
DROP PROCEDURE IF EXISTS sp_amiba_data_profit;


CREATE PROCEDURE sp_amiba_data_profit(IN p_ent CHAR(200),IN p_purpose CHAR(200),IN p_period CHAR(200)) 
BEGIN

DECLARE v_last_period NVARCHAR(100);
DECLARE v_loop INT DEFAULT 1;
/*
计算结果汇总表经营台账
*/
DROP TEMPORARY TABLE IF EXISTS tml_amiba_result_profits;
CREATE TEMPORARY TABLE IF NOT EXISTS tml_amiba_result_profits(
  `id` NVARCHAR(100),
  `purpose_id` NVARCHAR(100),
  `period_id` NVARCHAR(100),
  `group_id` NVARCHAR(100),
  `is_init` BOOLEAN DEFAULT 0,
  `init_profit` DECIMAL(30,9) DEFAULT 0,
  `income` DECIMAL(30,9) DEFAULT 0,
  `cost`  DECIMAL(30,9) DEFAULT 0,
  `time_profit` DECIMAL(30,9) DEFAULT 0,
  `time_output` DECIMAL(30,9) DEFAULT 0,
  `time_total` DECIMAL(30,9) DEFAULT 0
);
/*查询上一个期间*/
SELECT l.id INTO v_last_period FROM `suite_cbo_period_accounts` AS l
INNER JOIN suite_cbo_period_accounts AS ll ON l. `calendar_id`=ll.`calendar_id` 
WHERE ll.id=p_period AND l.`from_date`<ll.`from_date`
ORDER BY l.`from_date` DESC LIMIT 1;


/*节点对应最末级的节点*/
DROP TEMPORARY TABLE IF EXISTS tml_data_childGroup;
CREATE TEMPORARY TABLE IF NOT EXISTS tml_data_childGroup(
  `id` NVARCHAR(100),
  `last_id` NVARCHAR(100),
  `loops` INT
);
DROP TEMPORARY TABLE IF EXISTS tml_data_childGroup1;
CREATE TEMPORARY TABLE IF NOT EXISTS tml_data_childGroup1(
  `id` NVARCHAR(100),
  `last_id` NVARCHAR(100),
  `loops` INT
);
SET v_loop=1;
/*末级阿米巴单元*/
INSERT INTO tml_data_childGroup(id,last_id,loops) 
SELECT DISTINCT g.id,g.id,v_loop
FROM suite_amiba_groups AS g
  LEFT JOIN suite_amiba_groups AS child ON child.parent_id=g.id
WHERE  g.purpose_id=p_purpose
   AND child.id IS NULL;
   
INSERT INTO tml_data_childGroup1(id,last_id,loops) SELECT id,last_id,loops FROM tml_data_childGroup;
WHILE v_loop>0 DO
  SET v_loop = v_loop + 1;
  -- 查询所有子最末级节点
  INSERT INTO tml_data_childGroup(id,last_id,loops)
  SELECT g.parent_id,d.last_id,v_loop
  FROM tml_data_childGroup1 AS d 
    INNER JOIN suite_amiba_groups AS g ON d.id=g.id
  WHERE g.parent_id!='';
  
  DELETE FROM tml_data_childGroup1;
  INSERT INTO tml_data_childGroup1(id,last_id,loops) SELECT id,last_id,loops FROM tml_data_childGroup WHERE loops=v_loop;
  
  IF NOT EXISTS(SELECT * FROM tml_data_childGroup1 WHERE loops=v_loop) THEN
    SET v_loop=0;
  END IF;
END WHILE;

DELETE FROM tml_data_childGroup1;
INSERT INTO tml_data_childGroup1(id,last_id,loops) SELECT id,last_id,loops FROM tml_data_childGroup;


/*计算期初开账*/
INSERT INTO tml_amiba_result_profits(purpose_id,period_id,group_id,init_profit)
SELECT l.purpose_id,l.period_id,g.id,SUM(ll.profit) AS profit
FROM tml_data_childGroup AS g 
  INNER JOIN suite_amiba_data_inits AS l ON 1=1
  INNER JOIN `suite_amiba_data_init_lines` AS ll ON l.id=ll.init_id AND g.last_id=ll.group_id
WHERE  l.`purpose_id`=p_purpose AND l.`period_id`=p_period
GROUP BY l.purpose_id,l.period_id,g.id;


/*计算期初*/
INSERT INTO tml_amiba_result_profits(purpose_id,period_id,group_id,init_profit)
SELECT l.purpose_id,p_period,l.group_id, l.bal_profit
FROM  `suite_amiba_result_profits` AS l
WHERE l.purpose_id=p_purpose AND l.period_id=v_last_period;


/*计算本期*/
INSERT INTO tml_amiba_result_profits(purpose_id,period_id,group_id,income,cost)
SELECT l.purpose_id,l.period_id,l.group_id,  
  (CASE WHEN e.type_enum='rcv' THEN l.money ELSE 0 END * CASE WHEN e.direction_enum='plus' THEN 1 ELSE 0-1 END) AS income,
  (CASE WHEN e.type_enum!='rcv' THEN l.money ELSE 0 END * CASE WHEN e.direction_enum='plus' THEN 1 ELSE 0-1 END) AS cost
FROM suite_amiba_result_accounts AS l
  INNER JOIN `suite_amiba_elements` AS e ON l.element_id=e.id
WHERE l.purpose_id=p_purpose AND l.period_id=p_period AND e.type_enum!='';

/*计算时间*/
INSERT INTO tml_amiba_result_profits(purpose_id,period_id,group_id,time_total)
SELECT l.purpose_id,l.period_id,g.id,SUM(ll.`nor_time`+ll.`over_time`) AS time_total
FROM tml_data_childGroup AS g 
  INNER JOIN `suite_amiba_data_time_lines` AS ll ON ll.group_id=g.last_id
  INNER JOIN `suite_amiba_data_times` AS l  ON l.id=ll.time_id
WHERE  l.`purpose_id`=p_purpose AND l.`period_id`=p_period
GROUP BY l.purpose_id,l.period_id,g.id;

-- 先删除
DELETE l FROM suite_amiba_result_profits AS l  WHERE l.ent_id=p_ent AND l.purpose_id=p_purpose AND l.period_id=p_period;

-- 插入数据

INSERT INTO `suite_amiba_result_profits`
(
 id,ent_id,purpose_id,period_id,group_id,is_init,init_profit,income,cost,bal_profit,time_total,time_profit,time_output
)
SELECT MD5(REPLACE(UUID(),'-','')) AS id,p_ent,l.purpose_id,l.period_id,l.group_id,
 MAX(l.is_init),SUM(l.init_profit),SUM(l.income),SUM(l.cost),SUM(l.init_profit+l.income-l.cost),
 SUM(l.time_total) AS time_total,
 CASE WHEN SUM(l.time_total)=0 THEN 0 ELSE SUM(l.init_profit+l.income-l.cost)/SUM(l.time_total) END AS time_profit,
 CASE WHEN SUM(l.time_total)=0 THEN 0 ELSE SUM(l.income)/SUM(l.time_total) END AS time_output
FROM tml_amiba_result_profits AS l
GROUP BY l.purpose_id,l.period_id,l.group_id;

END