DELIMITER $$
DROP PROCEDURE IF EXISTS sp_amiba_data_distribute$$


CREATE PROCEDURE sp_amiba_data_distribute(IN p_ent CHAR(200),IN p_purpose CHAR(200),IN p_period CHAR(200),IN p_level INT) 
BEGIN

DECLARE v_loop INT DEFAULT 1;

/*待分配数据*/
DROP TEMPORARY TABLE IF EXISTS tml_result_allocated;
CREATE TEMPORARY TABLE IF NOT EXISTS tml_result_allocated(
  `purpose_id` NVARCHAR(100),
  `period_id` NVARCHAR(100),
  `group_id` NVARCHAR(100),
  `element_id` NVARCHAR(100),
  `method_id` NVARCHAR(100),
  `currency_id` NVARCHAR(100),
  `money` DECIMAL(30,9) DEFAULT 0
);

DROP TEMPORARY TABLE IF EXISTS suite_amiba_allot_total;
CREATE TEMPORARY TABLE IF NOT EXISTS suite_amiba_allot_total(
  `purpose_id` NVARCHAR(100),
  `group_id` NVARCHAR(100),
  `element_id` NVARCHAR(100),
  `total` DECIMAL(30,9) DEFAULT 0
);
DROP TEMPORARY TABLE IF EXISTS suite_amiba_allot_line;
CREATE TEMPORARY TABLE IF NOT EXISTS suite_amiba_allot_line(
  `purpose_id` NVARCHAR(100),
  `group_id` NVARCHAR(100),
  `element_id` NVARCHAR(100),
  `to_group_id` NVARCHAR(100),
  `to_element_id` NVARCHAR(100),
  `total` DECIMAL(30,9) DEFAULT 0,
  `radix` DECIMAL(30,9) DEFAULT 0,
  `rate` DECIMAL(30,9) DEFAULT 0
);

SET v_loop=1;


/*
核算 待分配数据
*/
INSERT INTO tml_result_allocated(purpose_id,period_id,group_id,element_id,currency_id,money)
SELECT d.purpose_id,d.period_id,d.fm_group_id,d.element_id,MAX(currency_id),SUM(d.money) AS money
FROM `suite_amiba_data_docs` AS d 
WHERE  d.`purpose_id`=p_purpose AND d.`period_id`=p_period
  AND d.src_type_enum IN ('interface','manual')
  AND EXISTS (SELECT ar.id FROM suite_amiba_allot_rules AS ar 
	INNER JOIN suite_amiba_allot_levels AS le ON ar.purpose_id=le.purpose_id AND ar.`group_id`=le.`group_id`  
  WHERE d.purpose_id=ar.purpose_id AND d.fm_group_id=ar.group_id AND d.element_id=ar.element_id
    AND le.`period_id`=d.period_id
    AND ar.bizkey=le.bizkey
    AND le.`level`=p_level 
  )
GROUP BY d.purpose_id,d.period_id,d.fm_group_id,d.element_id;

/*
计算分配比例
*/
INSERT INTO suite_amiba_allot_line(purpose_id,group_id,element_id,to_group_id,to_element_id,radix)
SELECT r.purpose_id,r.group_id,r.element_id,rl.group_id,rl.`element_id`,SUM(ml.rate) AS radix
FROM suite_amiba_allot_rules AS r
  INNER JOIN `suite_amiba_allot_rule_lines` AS rl ON r.id=rl.rule_id
  INNER JOIN `suite_amiba_allot_methods` AS m ON r.method_id=m.id
  INNER JOIN `suite_amiba_allot_method_lines` AS ml ON m.id=ml.method_id AND ml.group_id=rl.group_id
WHERE  r.purpose_id=p_purpose AND m.purpose_id=p_purpose
GROUP BY r.purpose_id,r.group_id,r.element_id,rl.group_id,rl.`element_id`;

INSERT INTO suite_amiba_allot_total(purpose_id,group_id,element_id,total)
SELECT l.purpose_id,l.group_id,l.element_id,SUM(radix) FROM suite_amiba_allot_line AS l GROUP BY l.purpose_id,l.group_id,l.element_id;

UPDATE suite_amiba_allot_line AS l INNER JOIN suite_amiba_allot_total AS t ON l.purpose_id=t.purpose_id AND l.element_id=t.element_id AND l.group_id=t.group_id
SET l.total=t.total,l.rate=CASE WHEN t.total=0 THEN 0 ELSE l.radix/t.total END;


/*删除之前分配的数据*/
DELETE l FROM suite_amiba_data_docs AS l  WHERE l.ent_id=p_ent AND l.purpose_id=p_purpose AND l.period_id=p_period AND src_type_enum ='allot';


-- 插入数据
INSERT INTO `suite_amiba_data_docs`(id,ent_id,doc_no,doc_date,src_type_enum,purpose_id,period_id,fm_group_id,element_id,currency_id,state_enum,money)
SELECT MD5(REPLACE(UUID_SHORT(),'-','')) AS id,p_ent,CONCAT('allot',DATE_FORMAT(NOW(),'%Y%m%d'),(@rownum:=@rownum+1)+1000),NOW() AS doc_date,'allot',
  a.purpose_id,p_period AS period_id,a.group_id,IFNULL(a.to_element_id,l.element_id) AS element_id,l.currency_id,'approved',
  SUM(l.money*a.rate) AS money
FROM tml_result_allocated AS l
INNER JOIN  suite_amiba_allot_line AS a ON l.purpose_id=a.purpose_id AND l.element_id=a.element_id,
(SELECT @rownum:=0) AS r
GROUP BY a.purpose_id,a.group_id,IFNULL(a.to_element_id,l.element_id),l.currency_id;

END$$

DELIMITER ;