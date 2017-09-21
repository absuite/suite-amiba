DELIMITER $$

DROP PROCEDURE IF EXISTS sp_amiba_data_doc_money $$ 


CREATE PROCEDURE sp_amiba_data_doc_money(IN p_doc CHAR(200)) 
BEGIN

DECLARE v_has_lines INT DEFAULT 0;
DECLARE v_period_fromdate DATETIME;
DECLARE v_lines_money DECIMAL(30,2) DEFAULT 0;

SELECT COUNT(0),ac.from_date INTO v_has_lines,v_period_fromdate FROM suite_amiba_data_doc_lines AS dl
INNER JOIN suite_amiba_data_docs AS d ON dl.doc_id=d.id
INNER JOIN suite_cbo_period_accounts AS ac ON ac.id=d.period_id
WHERE dl.doc_id=p_doc;

DROP TEMPORARY TABLE IF EXISTS tml_amiba_data_doc_price;
CREATE TEMPORARY TABLE IF NOT EXISTS tml_amiba_data_doc_price(
  `doc_id` NVARCHAR(100),
  `purpose_id` NVARCHAR(100),
  `period_id` NVARCHAR(100),
  `fm_group_id` NVARCHAR(100),
  `to_group_id` NVARCHAR(100),
  `docline_id` NVARCHAR(100),
  `item_id` NVARCHAR(100),  
  `qty` DECIMAL(30,9) DEFAULT 0,
  `price` DECIMAL(30,9) DEFAULT 0,
  `money` DECIMAL(30,9) DEFAULT 0
);

DROP TEMPORARY TABLE IF EXISTS tml_amiba_price;
CREATE TEMPORARY TABLE IF NOT EXISTS tml_amiba_price(
  `purpose_id` NVARCHAR(100),
  `fm_group_id` NVARCHAR(100),
  `to_group_id` NVARCHAR(100),
  `item_id` NVARCHAR(100),  
  `price` DECIMAL(30,9) DEFAULT 0
);

IF v_has_lines>0 THEN

/*查询需要取价的数据*/
INSERT INTO tml_amiba_data_doc_price(doc_id,purpose_id,period_id,fm_group_id,to_group_id,docline_id,item_id,qty)
SELECT d.id,d.purpose_id,d.period_id,d.fm_group_id,d.to_group_id,dl.id,dl.item_id,dl.qty
FROM  suite_amiba_data_docs AS d
 INNER JOIN suite_amiba_data_doc_lines AS dl ON d.id=dl.doc_id
WHERE d.id=p_doc AND dl.price=0 AND dl.qty!=0 AND IFNULL(dl.item_id,'')!='';

/*获取可能取到的价表数据，最后一个期间*/
INSERT INTO tml_amiba_price(purpose_id,fm_group_id,to_group_id,item_id,price)
SELECT rd.purpose_id,rd.fm_group_id,rd.to_group_id,rd.item_id,rpl.cost_price
FROM
(
	SELECT p.purpose_id,IFNULL(p.group_id,'') AS fm_group_id,IFNULL(pl.group_id,'') AS to_group_id,pl.item_id,MAX(ac.from_date) AS from_date 
	FROM  (
		SELECT DISTINCT purpose_id,item_id FROM tml_amiba_data_doc_price
	) AS d
	 INNER JOIN suite_amiba_prices AS p ON d.purpose_id=p.purpose_id
	 INNER JOIN suite_amiba_price_lines AS pl ON p.id=pl.price_id  AND d.item_id=pl.item_id
	 INNER JOIN suite_cbo_period_accounts AS ac ON p.period_id=ac.id
	WHERE pl.cost_price!=0 AND ac.from_date<v_period_fromdate
	GROUP BY p.purpose_id,IFNULL(p.group_id,''),IFNULL(pl.group_id,''),pl.item_id
) AS rd
INNER JOIN suite_amiba_prices AS rp ON rd.purpose_id=rp.purpose_id AND rd.fm_group_id=IFNULL(rp.group_id,'')
INNER JOIN suite_amiba_price_lines AS rpl ON rp.id=rpl.price_id AND rd.to_group_id=IFNULL(rpl.group_id,'') AND rd.item_id=rpl.item_id
INNER JOIN suite_cbo_period_accounts AS rac ON rp.period_id=rac.id
WHERE rd.from_date=rac.from_date;


/*来源巴+目标巴*/
UPDATE  tml_amiba_data_doc_price AS d
 INNER JOIN tml_amiba_price AS p ON d.purpose_id=p.purpose_id  AND d.item_id=p.item_id AND d.fm_group_id=p.fm_group_id AND d.to_group_id=p.to_group_id
SET d.money=d.qty*p.price,d.price=p.price
WHERE d.price=0;

UPDATE  tml_amiba_data_doc_price AS d
 INNER JOIN tml_amiba_price AS p ON d.purpose_id=p.purpose_id  AND d.item_id=p.item_id AND d.fm_group_id=p.fm_group_id
SET d.money=d.qty*p.price,d.price=p.price
WHERE d.price=0;

UPDATE  tml_amiba_data_doc_price AS d
 INNER JOIN tml_amiba_price AS p ON d.purpose_id=p.purpose_id  AND d.item_id=p.item_id
SET d.money=d.qty*p.price,d.price=p.price
WHERE d.price=0;

UPDATE  suite_amiba_data_doc_lines AS dl
INNER JOIN tml_amiba_data_doc_price AS p ON dl.id=p.docline_id
SET dl.money=p.money,dl.price=p.price
WHERE dl.price=0;


/*汇总头上金额*/
SELECT SUM(money) INTO v_lines_money FROM suite_amiba_data_doc_lines WHERE doc_id=p_doc;

UPDATE suite_amiba_data_docs SET money=v_lines_money WHERE id=p_doc;

END IF;

END $$

DELIMITER ;