DELIMITER $$
DROP PROCEDURE IF EXISTS sp_amiba_data_modeling$$


CREATE PROCEDURE sp_amiba_data_modeling(IN p_ent CHAR(200),IN p_purpose CHAR(200),IN p_period CHAR(200),IN p_model NVARCHAR(500)) 
sp_amiba_data_modeling:BEGIN
DECLARE v_from_date DATETIME;
DECLARE v_to_date DATETIME;
DECLARE v_id_start BIGINT;
DECLARE v_id_count BIGINT;

/*
SELECT UUID_SHORT() into v_uid;
*/

/*数据对应的要素*/
DROP TEMPORARY TABLE IF EXISTS tml_data_elementing;
CREATE TEMPORARY TABLE IF NOT EXISTS tml_data_elementing(
`purpose_id` NVARCHAR(100),
`period_id` NVARCHAR(100),
`def_fm_group_id` NVARCHAR(100),/*模型头定义的巴*/
`def_to_group_id` NVARCHAR(100),/*模型头定义的巴*/

`m_fm_group_id` NVARCHAR(100),/*模型行按匹配方向取得的巴*/
`m_to_group_id` NVARCHAR(100),/*模型行按匹配方向取得的巴*/

`modeling_id` NVARCHAR(100),/*模型行ID*/
`modeling_line_id` NVARCHAR(100),/*模型行ID*/
`match_direction_enum` NVARCHAR(100),
`match_group_id` NVARCHAR(100),

`fm_group_id` NVARCHAR(100),/*业务数据来源对应的巴*/
`to_group_id` NVARCHAR(100),/*业务数据目标对应的巴*/
`element_id` NVARCHAR(100),
`data_id`  NVARCHAR(100),
`data_type`  NVARCHAR(100),

`data_fm_org`  NVARCHAR(100),
`data_fm_dept`  NVARCHAR(100),
`data_fm_work`  NVARCHAR(100),
`data_fm_team`  NVARCHAR(100),
`data_to_org`  NVARCHAR(100),
`data_to_dept`  NVARCHAR(100),
`data_to_work`  NVARCHAR(100),
`data_to_team`  NVARCHAR(100),
`data_trader`  NVARCHAR(100),
`data_item`  NVARCHAR(100),
`data_uom`  NVARCHAR(100),
`data_item_category`  NVARCHAR(100),
 
    
`use_type_enum` NVARCHAR(100),
`value_type_enum` NVARCHAR(100),
`src_qty` DECIMAL(30,2) DEFAULT 0,
`src_money` DECIMAL(30,2) DEFAULT 0,
`adjust` NVARCHAR(100),
`qty` DECIMAL(30,2) DEFAULT 0,
`money` DECIMAL(30,2) DEFAULT 0,
`bizkey` NVARCHAR(500),
`fm_org_id`  NVARCHAR(100),
`fm_dept_id`  NVARCHAR(100),
`fm_work_id`  NVARCHAR(100),
`fm_team_id`  NVARCHAR(100),
`fm_wh_id`  NVARCHAR(100),
`fm_person_id`  NVARCHAR(100),
`to_org_id`  NVARCHAR(100),
`to_dept_id`  NVARCHAR(100),
`to_work_id`  NVARCHAR(100),
`to_team_id`  NVARCHAR(100),
`to_wh_id`  NVARCHAR(100),
`to_person_id`  NVARCHAR(100),
`trader_id`  NVARCHAR(100),
`item_id`  NVARCHAR(100),
`item_category_id`  NVARCHAR(100),
`project_id`  NVARCHAR(100),
`mfc_id`  NVARCHAR(100),
`lot_id`  NVARCHAR(100),
`expense_code` NVARCHAR(100),
`account_code` NVARCHAR(100),
`currency_id`  NVARCHAR(100),
`unit_id`  NVARCHAR(100)
);
/*考核数据表*/
DROP TEMPORARY TABLE IF EXISTS tml_data_doc;
CREATE TEMPORARY TABLE IF NOT EXISTS tml_data_doc(
`id` NVARCHAR(100),
`doc_no` NVARCHAR(100),
`doc_date`  DATETIME,
`purpose_id` NVARCHAR(100),
`period_id` NVARCHAR(100),
`use_type_enum` NVARCHAR(100),
`src_type_enum` NVARCHAR(100),
`modeling_id` NVARCHAR(100),
`fm_group_id` NVARCHAR(100),
`to_group_id` NVARCHAR(100),
`element_id` NVARCHAR(100),
`currency_id` NVARCHAR(100),
`money` DECIMAL(30,2),
`bizkey` NVARCHAR(500)
);
DROP TEMPORARY TABLE IF EXISTS tml_data_docLine;
CREATE TEMPORARY TABLE IF NOT EXISTS tml_data_docLine(
`id` NVARCHAR(100),
`doc_id` NVARCHAR(100),
`modeling_id` NVARCHAR(100),
`modeling_line_id` NVARCHAR(100),
`trader_id` NVARCHAR(100),
`item_category_id` NVARCHAR(100),
`item_id` NVARCHAR(100),
`project_id` NVARCHAR(100),
`mfc_id` NVARCHAR(100),
`unit_id` NVARCHAR(100),
`expense_code` NVARCHAR(100),
`account_code` NVARCHAR(100),
`qty` DECIMAL(30,2),
`price` DECIMAL(30,2),
`money` DECIMAL(30,2),
`bizkey` NVARCHAR(500)
);


  
/*期间的开始时间和结束时间*/
SELECT from_date,to_date INTO v_from_date,v_to_date FROM `suite_cbo_period_accounts` WHERE id=p_period;
/*业务数据*/
INSERT INTO tml_data_elementing
(
  `purpose_id`,`period_id`,`def_fm_group_id`,`def_to_group_id`,`modeling_id`,`modeling_line_id`,`match_direction_enum`,`match_group_id`,`element_id`,
  `data_id`,`data_type`,`value_type_enum`,`src_qty`,`src_money`,`adjust`
  ,`data_fm_org`,`data_fm_dept`,`data_fm_work` ,`data_fm_team` ,`data_to_org`,`data_to_dept`,`data_to_work`,`data_to_team`
  ,`data_trader`,`data_item`,`data_uom` ,`data_item_category` 
)
SELECT DISTINCT 
  p_purpose,p_period,m.group_id,ml.to_group_id,m.id,ml.id,ml.match_direction_enum,ml.match_group_id,ml.element_id,
  d.id AS data_id,'biz' AS data_type,ml.`value_type_enum`,
  d.qty AS src_qty,
  d.money AS src_money,
  ml.`adjust`
  ,d.`fm_org`,d.`fm_dept`,d.`fm_work` ,d.`fm_team` ,d.`to_org`,d.`to_dept`,d.`to_work`,d.`to_team`
  ,d.`trader`,d.`item`,d.`uom` ,d.`item_category` 
FROM `suite_amiba_doc_bizs` AS d 
  LEFT JOIN `suite_cbo_items` AS d_item ON d_item.code=d.item AND d_item.ent_id=d.ent_id
  INNER JOIN  `suite_amiba_modelings` AS m ON m.`purpose_id`=p_purpose  AND d.ent_id=m.ent_id
  INNER JOIN `suite_amiba_modeling_lines` AS ml ON m.`id`=ml.`modeling_id`
  LEFT JOIN `suite_cbo_doc_types` AS dt ON ml.`doc_type_id`=dt.`id` 
  LEFT JOIN `suite_cbo_item_categories` AS ic ON ml.`item_category_id`=ic.`id`
  LEFT JOIN `suite_cbo_items` AS item ON ml.`item_id`=item.`id`
  LEFT JOIN `suite_cbo_traders` AS trader ON ml.`trader_id`=trader.`id`
WHERE 
  d.ent_id=p_ent
  AND ml.`biz_type_enum`=d.`biz_type` 
  AND d.`doc_date` BETWEEN v_from_date AND v_to_date
  AND (IFNULL(p_model,'')='' OR (FIND_IN_SET(m.id , p_model)>0))
  AND (dt.`code` IS NULL OR(dt.`code` IS NOT NULL AND dt.`code`=d.`doc_type`))  
  AND (ic.`code` IS NULL OR(ic.`code` IS NOT NULL AND ic.id=d_item.category_id))
  AND (trader.`code` IS NULL OR(trader.`code` IS NOT NULL AND trader.`code`=d.`trader`))
  AND (item.`code` IS NULL OR(item.`code` IS NOT NULL AND item.`code`=d.`item`))
  AND (ml.`project_code` IS NULL OR(ml.`project_code` IS NOT NULL AND ml.`project_code`=d.`project`))
  AND (ml.`factor1` IS NULL OR(ml.`factor1` IS NOT NULL AND ml.`factor1`=d.`factor1`))
  AND (ml.`factor2` IS NULL OR(ml.`factor2` IS NOT NULL AND ml.`factor2`=d.`factor2`))
  AND (ml.`factor3` IS NULL OR(ml.`factor3` IS NOT NULL AND ml.`factor3`=d.`factor3`))
  AND (ml.`factor4` IS NULL OR(ml.`factor4` IS NOT NULL AND ml.`factor4`=d.`factor4`))
  AND (ml.`factor5` IS NULL OR(ml.`factor5` IS NOT NULL AND ml.`factor5`=d.`factor5`));
/*财务数据*/
INSERT INTO tml_data_elementing
(
  `purpose_id`,`period_id`,`def_fm_group_id`,`def_to_group_id`,`modeling_id`,`modeling_line_id`,`match_direction_enum`,`match_group_id`,`element_id`,
  `data_id`,`data_type`,`value_type_enum`,`src_qty`,`src_money`,`adjust`,
  `account_code`,`expense_code`
  ,`data_fm_org`,`data_fm_dept`,`data_fm_work` ,`data_fm_team` 
  ,`data_trader`
)
SELECT DISTINCT 
  p_purpose,p_period,m.group_id,ml.to_group_id,m.id,ml.id,ml.match_direction_enum,ml.match_group_id,ml.element_id,
  d.id AS data_id,'fi' AS data_type,ml.`value_type_enum`,
  0 AS src_qty,
  CASE WHEN ml.value_type_enum='debit' THEN d.`debit_money` ELSE d.`credit_money` END AS src_money,
  ml.`adjust`,
  d.account,d.`project`
  ,d.`fm_org`,d.`fm_dept`,d.`fm_work` ,d.`fm_team`
  ,d.`trader`
FROM `suite_amiba_doc_fis` AS d 
  INNER JOIN  `suite_amiba_modelings` AS m ON m.purpose_id=p_purpose  
  INNER JOIN `suite_amiba_modeling_lines` AS ml ON m.`id`=ml.`modeling_id`
  LEFT JOIN `suite_cbo_doc_types` AS dt ON ml.doc_type_id=dt.id
  LEFT JOIN `suite_cbo_traders` AS trader ON ml.trader_id=trader.id
WHERE ml.`biz_type_enum`=d.`biz_type` 
  AND d.doc_date BETWEEN v_from_date AND v_to_date
  AND (IFNULL(p_model,'')='' OR (FIND_IN_SET(m.id , p_model)>0))
  AND (dt.`code` IS NULL OR(dt.`code` IS NOT NULL AND dt.`code`=d.`doc_type`))  
  AND (trader.`code` IS NULL OR(trader.`code` IS NOT NULL AND trader.`code`=d.`trader`))
  AND (ml.`project_code` IS NULL OR(ml.`project_code` IS NOT NULL AND ml.`project_code`=d.`project`))
  AND (ml.`account_code` IS NULL OR(ml.`account_code` IS NOT NULL AND ml.`account_code`=d.`account`))
  AND (ml.`factor1` IS NULL OR(ml.`factor1` IS NOT NULL AND ml.`factor1`=d.`factor1`))
  AND (ml.`factor2` IS NULL OR(ml.`factor2` IS NOT NULL AND ml.`factor2`=d.`factor2`))
  AND (ml.`factor3` IS NULL OR(ml.`factor3` IS NOT NULL AND ml.`factor3`=d.`factor3`))
  AND (ml.`factor4` IS NULL OR(ml.`factor4` IS NOT NULL AND ml.`factor4`=d.`factor4`))
  AND (ml.`factor5` IS NULL OR(ml.`factor5` IS NOT NULL AND ml.`factor5`=d.`factor5`));
  
  UPDATE tml_data_elementing SET `qty`=(`src_qty`*`adjust`/100) WHERE adjust IS NOT NULL AND src_qty!=0;
  UPDATE tml_data_elementing SET `money`=(`src_money`*`adjust`/100) WHERE adjust IS NOT NULL AND src_money!=0;
  
  UPDATE tml_data_elementing SET `money`=0 WHERE value_type_enum='qty';
  
  UPDATE tml_data_elementing AS l 
    INNER JOIN `suite_cbo_orgs` AS d ON  d.code=l.data_fm_org
  SET l.fm_org_id=d.id
  WHERE d.ent_id=p_ent AND l.data_fm_org IS NOT NULL AND l.data_fm_org!='';
  
  UPDATE tml_data_elementing AS l 
    INNER JOIN `suite_cbo_depts` AS d ON  d.code=l.data_fm_dept
  SET l.fm_dept_id=d.id
  WHERE d.ent_id=p_ent AND l.data_fm_dept IS NOT NULL AND l.data_fm_dept!='';
  
  UPDATE tml_data_elementing AS l 
    INNER JOIN  `suite_cbo_works` AS d ON  d.code=l.data_fm_work
  SET l.fm_work_id=d.id
  WHERE d.ent_id=p_ent AND l.data_fm_work IS NOT NULL AND l.data_fm_work!='';
  
  UPDATE tml_data_elementing AS l 
    INNER JOIN  `suite_cbo_teams` AS d ON  d.code=l.data_fm_team
  SET l.fm_team_id=d.id
  WHERE d.ent_id=p_ent AND l.data_fm_team IS NOT NULL AND l.data_fm_team!='';
  
  UPDATE tml_data_elementing AS l 
    INNER JOIN  `suite_cbo_orgs` AS d ON  d.code=l.data_to_org
  SET l.to_org_id=d.id
  WHERE d.ent_id=p_ent AND l.data_to_org IS NOT NULL AND l.data_to_org!='';
  
  UPDATE tml_data_elementing AS l 
    INNER JOIN  `suite_cbo_depts` AS d ON  d.code=l.data_to_dept
  SET l.to_dept_id=d.id
  WHERE d.ent_id=p_ent AND l.data_to_dept IS NOT NULL AND l.data_to_dept!='';
  
  UPDATE tml_data_elementing AS l 
    INNER JOIN  `suite_cbo_works` AS d ON  d.code=l.data_to_work
  SET l.to_work_id=d.id
  WHERE d.ent_id=p_ent AND l.data_to_work IS NOT NULL AND l.data_to_work!='';
  
  UPDATE tml_data_elementing AS l 
    INNER JOIN  `suite_cbo_teams` AS d ON  d.code=l.data_to_team
  SET l.to_team_id=d.id
  WHERE d.ent_id=p_ent AND l.data_to_team IS NOT NULL AND l.data_to_team!='';
  
  UPDATE tml_data_elementing AS l 
    INNER JOIN  `suite_cbo_traders` AS d ON  d.code=l.data_trader
  SET l.trader_id=d.id
  WHERE d.ent_id=p_ent AND l.data_trader IS NOT NULL AND l.data_trader!='';
  
  UPDATE tml_data_elementing AS l 
    INNER JOIN  `suite_cbo_items` AS d ON  d.code=l.data_item
  SET l.item_id=d.id
  WHERE d.ent_id=p_ent AND l.data_item IS NOT NULL AND l.data_item!='';
  
  UPDATE tml_data_elementing AS l 
    INNER JOIN  `suite_cbo_units` AS d ON  d.code=l.data_uom
  SET l.unit_id=d.id
  WHERE d.ent_id=p_ent AND l.data_uom IS NOT NULL AND l.data_uom!='';
  
  UPDATE tml_data_elementing AS l 
    INNER JOIN  `suite_cbo_item_categories` AS d ON  d.code=l.data_item_category
  SET l.item_category_id=d.id
  WHERE d.ent_id=p_ent AND l.data_item_category IS NOT NULL AND l.data_item_category!='';
  

  -- 依据阿米巴定义找来源阿米巴
  UPDATE tml_data_elementing AS l 
    INNER JOIN `suite_amiba_groups` AS g ON g.`purpose_id`=p_purpose
    INNER JOIN `suite_amiba_group_lines` AS gl ON g.id=gl.group_id 
      AND (
        (gl.`data_type`='Suite\\Cbo\\Models\\Org' AND l.fm_org_id=gl.`data_id`)
        OR (gl.`data_type`='Suite\\Cbo\\Models\\Dept' AND l.fm_dept_id=gl.`data_id`)
        OR (gl.`data_type`='Suite\\Cbo\\Models\\Work' AND l.fm_work_id=gl.`data_id`)
        OR (gl.`data_type`='Suite\\Cbo\\Models\\Team' AND l.fm_team_id=gl.`data_id`)
        OR (gl.`data_type`='Suite\\Cbo\\Models\\Person' AND l.fm_person_id=gl.`data_id`)
      )
  SET l.fm_group_id=g.id
  WHERE l.fm_group_id IS NULL; 
  -- 依据阿米巴定义找对方阿米巴  
  UPDATE tml_data_elementing AS l 
    INNER JOIN `suite_amiba_groups` AS g ON g.`purpose_id`=p_purpose
    INNER JOIN `suite_amiba_group_lines` AS gl ON g.id=gl.group_id 
      AND (
        (gl.`data_type`='Suite\\Cbo\\Models\\Org' AND l.to_org_id=gl.`data_id`)
        OR (gl.`data_type`='Suite\\Cbo\\Models\\Dept' AND l.to_dept_id=gl.`data_id`)
        OR (gl.`data_type`='Suite\\Cbo\\Models\\Work' AND l.to_work_id=gl.`data_id`)
        OR (gl.`data_type`='Suite\\Cbo\\Models\\Team' AND l.to_team_id=gl.`data_id`)
        OR (gl.`data_type`='Suite\\Cbo\\Models\\Person' AND l.to_person_id=gl.`data_id`)
      )
  SET l.to_group_id=g.id
  WHERE l.to_group_id IS NULL;
  
  /*数据巴的来源和去向相同时，则需要删除*/
  DELETE FROM tml_data_elementing WHERE IFNULL(to_group_id,'')!='' AND IFNULL(fm_group_id,'')!='' AND (to_group_id=fm_group_id);
  
  
  /*
  匹配方：与原始业务数据中的巴进行匹配，并将匹配结果作为表头巴的建模成果。
  交易方：标识表头巴的交易对方巴是谁，当交易方为空时，使用原始业务数据匹配的巴，如果指定了，则直接使用交易巴。  
  更新模型巴
  如果模型行定义了匹配方，则按匹配方更新巴
  */
  UPDATE tml_data_elementing AS l 
  SET l.m_fm_group_id=l.fm_group_id,l.m_to_group_id=l.to_group_id
  WHERE l.match_direction_enum='fm' AND l.fm_group_id!='' AND IFNULL(l.match_group_id,'')!='';
  
  UPDATE tml_data_elementing AS l 
  SET l.m_fm_group_id=l.to_group_id,l.m_to_group_id=l.fm_group_id
  WHERE l.match_direction_enum='to' AND l.to_group_id!='' AND IFNULL(l.match_group_id,'')!='';
    
  /*更新交易方*/
  UPDATE tml_data_elementing AS l SET l.m_to_group_id=l.def_to_group_id
  WHERE IFNULL(l.m_fm_group_id,'')!='' AND IFNULL(l.def_to_group_id,'')!='';
   
  /*如果模型行定义了巴：1、与数据巴不对应时，需要删除，2、数据为空时，需要删除*/
  DELETE FROM tml_data_elementing WHERE IFNULL(match_group_id,'')!='' AND (m_fm_group_id!=match_group_id OR m_fm_group_id IS NULL);
  
  /*更新本方巴*/
  UPDATE tml_data_elementing AS l SET l.m_fm_group_id=l.def_fm_group_id
  WHERE IFNULL(l.m_fm_group_id,'')!='' AND IFNULL(l.def_fm_group_id,'')!='';
  
  /*数据巴的来源和去向相同时，则需要删除*/
  DELETE FROM tml_data_elementing WHERE IFNULL(match_group_id,'')!='' AND (m_fm_group_id=m_to_group_id);

  /*如果取数量，则需要从价表里取单价*/
  -- 来源巴+去向巴+物料
  UPDATE `suite_amiba_prices` AS p
    INNER JOIN `suite_amiba_price_lines` AS pl ON p.id=pl.price_id
    INNER JOIN tml_data_elementing AS l ON p.group_id=l.m_fm_group_id AND pl.group_id=l.m_to_group_id AND pl.item_id=l.item_id
  SET l.money=l.qty*pl.cost_price
  WHERE p.purpose_id=p_purpose AND l.value_type_enum='qty' AND l.qty!=0 AND l.money=0;
  
  UPDATE `suite_amiba_prices` AS p
    INNER JOIN `suite_amiba_price_lines` AS pl ON p.id=pl.price_id
    INNER JOIN tml_data_elementing AS l ON pl.group_id=l.m_fm_group_id AND p.group_id=l.m_to_group_id AND pl.item_id=l.item_id
  SET l.money=l.qty*pl.cost_price
  WHERE p.purpose_id=p_purpose AND l.value_type_enum='qty' AND l.qty!=0 AND l.money=0;
  -- 来源巴+物料
  UPDATE `suite_amiba_prices` AS p
    INNER JOIN `suite_amiba_price_lines` AS pl ON p.id=pl.price_id
    INNER JOIN tml_data_elementing AS l ON p.group_id=l.m_fm_group_id AND IFNULL(pl.group_id,'')='' AND pl.item_id=l.item_id
  SET l.money=l.qty*pl.cost_price
  WHERE p.purpose_id=p_purpose  AND l.value_type_enum='qty' AND l.qty!=0 AND l.money=0;  
  
  UPDATE `suite_amiba_prices` AS p
    INNER JOIN `suite_amiba_price_lines` AS pl ON p.id=pl.price_id
    INNER JOIN tml_data_elementing AS l ON p.group_id=l.m_to_group_id AND IFNULL(pl.group_id,'')='' AND pl.item_id=l.item_id
  SET l.money=l.qty*pl.cost_price
  WHERE p.purpose_id=p_purpose  AND l.value_type_enum='qty' AND l.qty!=0 AND l.money=0; 
  
  -- 来源巴
  UPDATE `suite_amiba_prices` AS p
    INNER JOIN `suite_amiba_price_lines` AS pl ON p.id=pl.price_id
    INNER JOIN tml_data_elementing AS l ON p.group_id=l.m_fm_group_id AND IFNULL(pl.group_id,'')='' AND IFNULL(pl.item_id,'')=''
  SET l.money=l.qty*pl.cost_price
  WHERE p.purpose_id=p_purpose AND l.value_type_enum='qty' AND l.qty!=0 AND l.money=0;
  
  UPDATE `suite_amiba_prices` AS p
    INNER JOIN `suite_amiba_price_lines` AS pl ON p.id=pl.price_id
    INNER JOIN tml_data_elementing AS l ON p.group_id=l.m_to_group_id AND IFNULL(pl.group_id,'')='' AND IFNULL(pl.item_id,'')=''
  SET l.money=l.qty*pl.cost_price
  WHERE p.purpose_id=p_purpose AND l.value_type_enum='qty' AND l.qty!=0 AND l.money=0;
  

  -- 更新数据用途
  UPDATE tml_data_elementing SET use_type_enum='indirect' WHERE m_fm_group_id IS NULL;
  UPDATE tml_data_elementing SET use_type_enum='direct' WHERE m_fm_group_id IS NOT NULL;
  -- 更新业务主键
  UPDATE tml_data_elementing SET bizKey=MD5(CONCAT(modeling_id,purpose_id,period_id,IFNULL(m_fm_group_id,''),IFNULL(m_to_group_id,''),IFNULL(use_type_enum,''),IFNULL(element_id,'')));


  SET @len=1; 
  SELECT COUNT(0) INTO @len FROM tml_data_elementing AS l;
  CALL sp_gmf_sys_uid('suite.amiba.data.doc',@len);
  
  INSERT INTO tml_data_doc(`id`,`bizKey`,`doc_no`,`src_type_enum`,`modeling_id`,
	`doc_date`,`purpose_id`,`period_id`,`use_type_enum`,`fm_group_id`,`to_group_id`,`element_id`,`money`)
  SELECT MD5(REPLACE(UUID_SHORT(),'-','')) AS id,l.bizKey,(@rownum := @rownum+1),'interface',l.modeling_id,
	v_to_date,l.purpose_id,l.period_id,l.use_type_enum,l.m_fm_group_id,l.m_to_group_id,l.element_id,SUM(l.money) AS money
  FROM tml_data_elementing AS l,(SELECT @rownum:=@len) r
  GROUP BY l.purpose_id,l.period_id,l.use_type_enum,l.m_fm_group_id,l.m_to_group_id,l.element_id,l.bizKey,l.modeling_id;

  INSERT INTO tml_data_docLine(`id`,`bizKey`,`modeling_id`,`modeling_line_id`,`trader_id`,`item_category_id`,`item_id`,`mfc_id`,`project_id`,`account_code`,`expense_code`,`unit_id`,`qty`,`money`)
  SELECT MD5(REPLACE(UUID_SHORT(),'-','')) AS id,l.bizKey,l.modeling_id,l.modeling_line_id,
    l.`trader_id`,l.`item_category_id`,l.`item_id`,l.`mfc_id`,l.`project_id`,l.`account_code`,l.expense_code,l.unit_id,
    SUM(l.qty),SUM(l.money)
  FROM tml_data_elementing AS l
  GROUP BY l.bizKey,l.modeling_id,l.modeling_line_id,l.`trader_id`,l.`item_category_id`,l.`item_id`,l.`mfc_id`,l.`project_id`,l.`account_code`,l.expense_code,l.unit_id;
  
  UPDATE tml_data_docLine SET price=money/qty WHERE qty!=0;
  UPDATE tml_data_docLine AS dl INNER JOIN  tml_data_doc AS d ON d.bizkey=dl.bizkey
  SET dl.doc_id=d.id;
  
  
/*将数据更新到考核数据表*/

DELETE l FROM suite_amiba_data_doc_lines AS l
INNER JOIN suite_amiba_data_docs AS h ON l.doc_id=h.id
WHERE h.src_type_enum='interface' AND h.ent_id=p_ent AND h.purpose_id=p_purpose AND h.period_id=p_period AND ((IFNULL(p_model,'')='' OR (FIND_IN_SET(h.modeling_id , p_model)>0)));

DELETE h FROM suite_amiba_data_docs AS h 
WHERE h.src_type_enum='interface' AND h.ent_id=p_ent AND h.purpose_id=p_purpose AND h.period_id=p_period AND ((IFNULL(p_model,'')='' OR (FIND_IN_SET(h.modeling_id , p_model)>0)));


INSERT INTO `suite_amiba_data_docs`(`id`,`created_at`,`ent_id`,`src_type_enum`,`modeling_id`,`doc_no`,`doc_date`,`purpose_id`,`period_id`,`use_type_enum`,`fm_group_id`,`to_group_id`,`element_id`,`money`,`state_enum`)
SELECT `id`,NOW(),p_ent,src_type_enum,`modeling_id`,`doc_no`,`doc_date`,`purpose_id`,`period_id`,`use_type_enum`,`fm_group_id`,`to_group_id`,`element_id`,IFNULL(`money`,0),'approved'
FROM tml_data_doc;

INSERT INTO`suite_amiba_data_doc_lines`(`id`,`created_at`,`ent_id`,`doc_id`,`modeling_id`,`modeling_line_id`,`trader_id`,`item_category_id`,`item_id`,`mfc_id`,`project_id`,`account_code`,`qty`,`price`,`money`)
SELECT `id`,NOW(),p_ent,`doc_id`,`modeling_id`,`modeling_line_id`,`trader_id`,`item_category_id`,`item_id`,`mfc_id`,`project_id`,`account_code`,IFNULL(`qty`,0),IFNULL(`price`,0),IFNULL(`money`,0)
FROM tml_data_docLine;


END$$

DELIMITER ;