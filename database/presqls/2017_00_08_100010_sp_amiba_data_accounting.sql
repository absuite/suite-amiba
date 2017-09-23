

DROP PROCEDURE IF EXISTS sp_amiba_data_accounting;


CREATE PROCEDURE sp_amiba_data_accounting(IN p_ent CHAR(200),IN p_purpose CHAR(200),IN p_period CHAR(200)) 
BEGIN

/*
SELECT UUID() into v_uid;
对于末级阿米巴而言，直接数据、间接数据、调整数据，都是根据每笔发生直接写到“外部发生金额”。
1、	间接费用分配、考核调整单都是只能到末级阿米巴的，故这2部分数据都是直接写到“外部发生额”；不用考虑其他情况
2、	【核算数据表】中的直接数据，按阿米巴登记明细记录时，也是直接写到“外部发生额”，不需要区分内部、外部
3、	计算上级阿米巴的收入、支出时需要区分内外部

	先抓出该阿米巴下所有下级阿米巴的明细记录，
	再来看每张核算数据表上的“对方阿米巴”是否属于本阿米巴的下级，如果属于就放到“内部发生额”；如果不属于就放到“外部发生额”。
	逐张判断，汇总结果写到本级阿米巴的记录下；是否需要保存中间级的明细记录设计定；
	“内部发生额”需要提供可联查的明细记录；
4、	计算时需逐层计算，先算最底层，再逐步往上算，类似成本计算时需要按照低阶码算。UI上可以没有明确的低阶码推算过程、提示，系统后台能记清楚、算清楚就可以。每个核算目的中，阿米巴的定义只有一棵树，是明确的。


*/

DECLARE v_loop INT DEFAULT 1;

/*核算结果表*/
DROP TEMPORARY TABLE IF EXISTS tml_result_accounts;
CREATE TEMPORARY TABLE IF NOT EXISTS tml_result_accounts(
  `id` NVARCHAR(100),
  `purpose_id` NVARCHAR(100),
  `period_id` NVARCHAR(100),
  `group_id` NVARCHAR(100),
  `element_id` NVARCHAR(100),
  `type_enum` NVARCHAR(100),
  `is_init` BOOLEAN DEFAULT 0,
  `money` DECIMAL(30,9) DEFAULT 0,
  `src_id` NVARCHAR(500),
  `src_no` NVARCHAR(500)
);

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

/*核算 末级*/
INSERT INTO tml_result_accounts(purpose_id,period_id,group_id,element_id,type_enum,money)
SELECT d.purpose_id,d.period_id,g.id,d.element_id,
  d.use_type_enum,SUM(d.money) AS money
FROM tml_data_childGroup AS g 
INNER JOIN `suite_amiba_data_docs` AS d ON g.last_id=d.fm_group_id
WHERE  d.`purpose_id`=p_purpose AND d.`period_id`=p_period
  AND d.`fm_group_id`!=''
  AND g.loops=1
  AND d.use_type_enum IN ('direct','allocated')
GROUP BY d.`purpose_id`, d.`period_id`,d.`fm_group_id`,d.`element_id`,d.`use_type_enum`;


/*核算 非末级*/
/*外部直接汇总，间接费用直接汇总*/
INSERT INTO tml_result_accounts(purpose_id,period_id,group_id,element_id,type_enum,money)
SELECT 
  d.purpose_id,d.period_id,g.id,d.element_id,d.use_type_enum,
  SUM(d.money) AS money
FROM tml_data_childGroup AS g 
INNER JOIN `suite_amiba_data_docs` AS d ON g.last_id=d.fm_group_id
WHERE  d.`purpose_id`=p_purpose AND d.`period_id`=p_period
  AND d.`fm_group_id`!=''
  AND d.use_type_enum IN ('allocated')
  AND g.loops>1
GROUP BY d.purpose_id,d.period_id,g.id,d.element_id,d.use_type_enum;

INSERT INTO tml_result_accounts(purpose_id,period_id,group_id,element_id,type_enum,money)
SELECT 
  d.purpose_id,d.period_id,g.id,d.element_id,d.use_type_enum,
  SUM(d.money) AS money
FROM tml_data_childGroup AS g 
INNER JOIN `suite_amiba_data_docs` AS d ON g.last_id=d.fm_group_id
WHERE  d.`purpose_id`=p_purpose AND d.`period_id`=p_period
  AND d.`fm_group_id`!=''
  AND d.use_type_enum IN ('direct')
  AND g.loops>1
GROUP BY d.purpose_id,d.period_id,g.id,d.element_id,d.use_type_enum;

INSERT INTO tml_result_accounts(purpose_id,period_id,group_id,element_id,type_enum,money)
SELECT 
  d.purpose_id,d.period_id,g.id,d.element_id,d.use_type_enum,
  0-SUM(d.money) AS money
FROM tml_data_childGroup AS g 
INNER JOIN `suite_amiba_data_docs` AS d ON g.last_id=d.fm_group_id
WHERE  d.`purpose_id`=p_purpose AND d.`period_id`=p_period
  AND d.`fm_group_id`!=''
  AND d.use_type_enum IN ('direct')
  AND d.to_group_id IN (SELECT tg.last_id FROM tml_data_childGroup1 AS tg WHERE tg.id=g.id)
  AND g.loops>1
GROUP BY d.purpose_id,d.period_id,g.id,d.element_id,d.use_type_enum;

-- 先删除
DELETE l FROM suite_amiba_result_accounts AS l  WHERE l.ent_id=p_ent AND l.purpose_id=p_purpose AND l.period_id=p_period;

-- 插入数据

INSERT INTO `suite_amiba_result_accounts`(id,ent_id,purpose_id,period_id,group_id,element_id,type_enum,is_init,src_id,src_no,created_at,money)
SELECT MD5(REPLACE(UUID(),'-','')) AS id,
  p_ent,purpose_id,period_id,group_id,element_id,l.type_enum,is_init,src_id,src_no,NOW(),
  SUM(money)
FROM tml_result_accounts AS l
GROUP BY purpose_id,period_id,group_id,element_id,l.type_enum,is_init,src_id,src_no;

END