DELIMITER$$

DROP PROCEDURE IF EXISTS sp_amiba_data_modeling$$ 


CREATE PROCEDURE sp_amiba_data_modeling(IN p_ent CHAR(200),IN p_purpose CHAR(200),IN p_period CHAR(200)) 
BEGIN

DECLARE v_from_date DATETIME;
DECLARE v_to_date DATETIME;

/*
SELECT UUID() into v_uid;
*/

/*数据对应的要素*/
DROP TEMPORARY TABLE IF EXISTS tml_data_elementing;
CREATE TEMPORARY TABLE IF NOT EXISTS tml_data_elementing(
`purpose_id` NVARCHAR(100),
`period_id` NVARCHAR(100),
`def_group_id` NVARCHAR(100),/*模型头定义的巴*/
`m_fm_group_id` NVARCHAR(100),/*模型行按匹配方向取得的巴*/
`m_to_group_id` NVARCHAR(100),/*模型行按匹配方向取得的巴*/
`ml_id` NVARCHAR(100),/*模型行ID*/
`fm_group_id` NVARCHAR(100),/*业务数据来源对应的巴*/
`to_group_id` NVARCHAR(100),/*业务数据目标对应的巴*/
`element_id` NVARCHAR(100),

`data_id`  NVARCHAR(100),
`use_type_enum` NVARCHAR(100),

`value_type_enum` NVARCHAR(100),
`src_qty` DECIMAL(30,9),
`src_money` DECIMAL(30,9),
`adjust` NVARCHAR(100),
`qty` DECIMAL(30,9),
`money` DECIMAL(30,9),
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
`account_code` NVARCHAR(100),

`currency_id`  NVARCHAR(100),
`uom_id`  NVARCHAR(100)
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
`fm_group_id` NVARCHAR(100),
`to_group_id` NVARCHAR(100),
`element_id` NVARCHAR(100),
`money` DECIMAL(30,9),
`bizkey` NVARCHAR(500)
);
DROP TEMPORARY TABLE IF EXISTS tml_data_docLine;
CREATE TEMPORARY TABLE IF NOT EXISTS tml_data_docLine(
`id` NVARCHAR(100),
`doc_id` NVARCHAR(100),
`trader_id` NVARCHAR(100),
`item_category_id` NVARCHAR(100),
`item_id` NVARCHAR(100),
`project_id` NVARCHAR(100),
`mfc_id` NVARCHAR(100),
`account_code` NVARCHAR(100),
`qty` DECIMAL(30,9),
`price` DECIMAL(30,9),
`money` DECIMAL(30,9),
`bizkey` NVARCHAR(500)
);

/*期间的开始时间和结束时间*/
SELECT from_date,to_date INTO v_from_date,v_to_date FROM `suite_cbo_period_accounts` WHERE id=p_period;


INSERT INTO tml_data_elementing
(
  `purpose_id`,`period_id`,`def_group_id`,`ml_id`,`element_id`,
  `data_id`,`value_type_enum`,`src_qty`,`src_money`,`adjust`
)

SELECT 
  p_purpose,p_period,m.group_id,ml.id,ml.element_id,
  d.id AS data_id,ml.`value_type_enum`,
  CASE WHEN ml.`value_type_enum`='qty' THEN d.qty ELSE 0 END AS src_qty,
  CASE WHEN ml.`value_type_enum`='amt' THEN d.money ELSE 0 END AS src_money,
  ml.`adjust`
FROM `suite_amiba_doc_bizs` AS d 
  INNER JOIN  `suite_amiba_modelings` AS m ON m.purpose_id=p_purpose  
  INNER JOIN `suite_amiba_modeling_lines` AS ml ON m.`id`=ml.`modeling_id`
  LEFT JOIN `suite_cbo_doc_types` AS dt ON ml.doc_type_id=dt.id
  LEFT JOIN `suite_cbo_item_categories` AS ic ON ml.item_category_id=ic.id
  LEFT JOIN `suite_cbo_items` AS item ON ml.item_id=item.id
  LEFT JOIN `suite_cbo_traders` AS trader ON ml.trader_id=trader.id
WHERE ml.`biz_type_enum`=d.`biz_type` 
  AND d.doc_date BETWEEN v_from_date AND v_to_date
  AND (dt.`code` IS NULL OR(dt.`code` IS NOT NULL AND dt.`code`=d.`doc_type`))  
  AND (ic.`code` IS NULL OR(ic.`code` IS NOT NULL AND ic.`code`=d.`item_category`))
  AND (trader.`code` IS NULL OR(trader.`code` IS NOT NULL AND trader.`code`=d.`trader`))
  AND (item.`code` IS NULL OR(item.`code` IS NOT NULL AND item.`code`=d.`item`))
  AND (ml.`project_code` IS NULL OR(ml.`project_code` IS NOT NULL AND ml.`project_code`=d.`project`))
  AND (ml.`factor1` IS NULL OR(ml.`factor1` IS NOT NULL AND ml.`factor1`=d.`factor1`))
  AND (ml.`factor2` IS NULL OR(ml.`factor2` IS NOT NULL AND ml.`factor2`=d.`factor2`))
  AND (ml.`factor3` IS NULL OR(ml.`factor3` IS NOT NULL AND ml.`factor3`=d.`factor3`))
  AND (ml.`factor4` IS NULL OR(ml.`factor4` IS NOT NULL AND ml.`factor4`=d.`factor4`))
  AND (ml.`factor5` IS NULL OR(ml.`factor5` IS NOT NULL AND ml.`factor5`=d.`factor5`));
  
  UPDATE tml_data_elementing SET `qty`=(`src_qty`*`adjust`/100) WHERE adjust IS NOT NULL AND src_qty!=0;
  UPDATE tml_data_elementing SET `money`=(`src_money`*`adjust`/100) WHERE adjust IS NOT NULL AND src_money!=0;
  
  UPDATE tml_data_elementing AS l 
    INNER JOIN suite_amiba_doc_bizs AS d ON l.data_id=d.id
    LEFT JOIN `suite_cbo_orgs` AS fm_org ON fm_org.ent_id=p_ent AND fm_org.code=d.fm_org
    LEFT JOIN `suite_cbo_depts` AS fm_dept ON fm_dept.ent_id=p_ent AND fm_dept.code=d.fm_dept
    LEFT JOIN `suite_cbo_works` AS fm_work ON fm_work.ent_id=p_ent AND fm_work.code=d.fm_work
    LEFT JOIN `suite_cbo_teams` AS fm_team ON fm_team.ent_id=p_ent AND fm_team.code=d.fm_team
    
    LEFT JOIN `suite_cbo_orgs` AS to_org ON to_org.ent_id=p_ent AND to_org.code=d.to_org
    LEFT JOIN `suite_cbo_depts` AS to_dept ON to_dept.ent_id=p_ent AND to_dept.code=d.to_dept
    LEFT JOIN `suite_cbo_works` AS to_work ON to_work.ent_id=p_ent AND to_work.code=d.to_work
    LEFT JOIN `suite_cbo_teams` AS to_team ON to_team.ent_id=p_ent AND to_team.code=d.to_team
    
    LEFT JOIN  `suite_cbo_traders` AS trader ON trader.ent_id=p_ent AND trader.code=d.trader
    LEFT JOIN  `suite_cbo_items` AS item ON item.ent_id=p_ent AND item.code=d.item
    LEFT JOIN  `suite_cbo_item_categories` AS item_category ON item_category.ent_id=p_ent AND item_category.code=d.item_category
  SET
    l.fm_org_id=fm_org.id,
    l.fm_dept_id=fm_dept.id,
    l.fm_work_id=fm_work.id,
    l.fm_team_id=fm_team.id,
    
    l.to_org_id=to_org.id,
    l.to_dept_id=to_dept.id,
    l.to_work_id=to_work.id,
    l.to_team_id=to_team.id,
    
    l.trader_id=trader.id,
    l.item_id=item.id,
    l.item_category_id=item_category.id
  WHERE d.ent_id=p_ent;

  -- 如果模型中指定阿米巴，则直接取阿米巴
  UPDATE tml_data_elementing SET fm_group_id=def_group_id WHERE def_group_id IS NOT NULL;
  -- 依据阿米巴定义找来源阿米巴
  UPDATE tml_data_elementing AS l 
    INNER JOIN `suite_amiba_groups` AS g ON g.`purpose_id`=p_purpose
    INNER JOIN `suite_amiba_group_lines` AS gl ON g.id=gl.group_id 
      AND (
        (gl.`data_type`='Suite\Cbo\Models\Org' AND l.fm_org_id=gl.`data_id`)
        OR (gl.`data_type`='Suite\Cbo\Models\Dept' AND l.fm_dept_id=gl.`data_id`)
        OR (gl.`data_type`='Suite\Cbo\Models\Work' AND l.fm_work_id=gl.`data_id`)
        OR (gl.`data_type`='Suite\Cbo\Models\Team' AND l.fm_team_id=gl.`data_id`)
        OR (gl.`data_type`='Suite\Cbo\Models\Person' AND l.fm_person_id=gl.`data_id`)
      )
  SET l.fm_group_id=g.id
  WHERE g.ent_id=p_ent AND l.fm_group_id IS NULL; 
  -- 依据阿米巴定义找对方阿米巴
  UPDATE tml_data_elementing AS l 
    INNER JOIN `suite_amiba_groups` AS g ON g.`purpose_id`=p_purpose
    INNER JOIN `suite_amiba_group_lines` AS gl ON g.id=gl.group_id 
      AND (
        (gl.`data_type`='Suite\Cbo\Models\Org' AND l.to_org_id=gl.`data_id`)
        OR (gl.`data_type`='Suite\Cbo\Models\Dept' AND l.to_dept_id=gl.`data_id`)
        OR (gl.`data_type`='Suite\Cbo\Models\Work' AND l.to_work_id=gl.`data_id`)
        OR (gl.`data_type`='Suite\Cbo\Models\Team' AND l.to_team_id=gl.`data_id`)
        OR (gl.`data_type`='Suite\Cbo\Models\Person' AND l.to_person_id=gl.`data_id`)
      )
  SET l.to_group_id=g.id
  WHERE g.ent_id=p_ent AND g.id!=l.fm_group_id; 
  
  /*更新模型巴*/
  UPDATE tml_data_elementing AS l 
    INNER JOIN  `suite_amiba_modeling_lines` AS ml ON l.ml_id=ml.id
  SET l.m_fm_group_id=l.fm_group_id,l.m_to_group_id=l.to_group_id
  WHERE ml.match_direction_enum='fm' AND l.fm_group_id!='';
  UPDATE tml_data_elementing AS l 
    INNER JOIN  `suite_amiba_modeling_lines` AS ml ON l.ml_id=ml.id
  SET l.m_fm_group_id=l.to_group_id,l.m_to_group_id=l.fm_group_id
  WHERE ml.match_direction_enum='to' AND l.to_group_id!='';
  
  DELETE FROM tml_data_elementing WHERE m_fm_group_id!=def_group_id;
  
  -- 更新数据用途
  UPDATE tml_data_elementing SET use_type_enum='indirect' WHERE m_fm_group_id IS NULL;
  UPDATE tml_data_elementing SET use_type_enum='direct' WHERE m_fm_group_id IS NOT NULL;
  -- 更新业务主键
  UPDATE tml_data_elementing SET bizKey=MD5(CONCAT(purpose_id,period_id,IFNULL(m_fm_group_id,''),IFNULL(m_to_group_id,''),IFNULL(use_type_enum,''),IFNULL(element_id,'')));
  
  INSERT INTO tml_data_doc(`id`,`bizKey`,`doc_no`,`doc_date`,`purpose_id`,`period_id`,`use_type_enum`,`fm_group_id`,`to_group_id`,`element_id`,`money`)
  SELECT MD5(REPLACE(UUID(),'-','')) AS id,l.bizKey,DATE_FORMAT(v_to_date,'%Y%m%d'),v_to_date,l.purpose_id,l.period_id,l.use_type_enum,l.m_fm_group_id,l.m_to_group_id,l.element_id,SUM(l.money) AS money
  FROM tml_data_elementing AS l
  GROUP BY l.purpose_id,l.period_id,l.use_type_enum,l.m_fm_group_id,l.m_to_group_id,l.element_id,l.bizKey;
  
  
  INSERT INTO tml_data_docLine(`id`,`bizKey`,`trader_id`,`item_category_id`,`item_id`,`mfc_id`,`project_id`,`account_code`,`qty`,`money`)
  SELECT MD5(REPLACE(UUID(),'-','')) AS id,l.bizKey,
    l.`trader_id`,l.`item_category_id`,l.`item_id`,l.`mfc_id`,l.`project_id`,l.`account_code`,
    l.qty,l.money
  FROM tml_data_elementing AS l;
  
  UPDATE tml_data_docLine SET price=money/qty WHERE qty!=0;
  UPDATE tml_data_docLine AS dl INNER JOIN  tml_data_doc AS d ON d.bizkey=dl.bizkey
  SET dl.doc_id=d.id;

DELETE l FROM suite_amiba_data_doc_lines AS l
INNER JOIN suite_amiba_data_docs AS h ON l.doc_id=h.id
WHERE h.src_type_enum='interface' AND h.ent_id=p_ent AND h.purpose_id=p_purpose AND h.period_id=p_period;

DELETE h FROM suite_amiba_data_docs AS h 
WHERE h.src_type_enum='interface' AND h.ent_id=p_ent AND h.purpose_id=p_purpose AND h.period_id=p_period;

/*将数据更新到考核数据表*/
INSERT INTO `suite_amiba_data_docs`(`id`,`created_at`,`ent_id`,`src_type_enum`,`doc_no`,`doc_date`,`purpose_id`,`period_id`,`use_type_enum`,`fm_group_id`,`to_group_id`,`element_id`,`money`,`state_enum`)
SELECT `id`,NOW(),p_ent,'interface',`doc_no`,`doc_date`,`purpose_id`,`period_id`,`use_type_enum`,`fm_group_id`,`to_group_id`,`element_id`,`money`,'approved'
FROM tml_data_doc;

INSERT INTO`suite_amiba_data_doc_lines`(`id`,`created_at`,`ent_id`,`doc_id`,`trader_id`,`item_category_id`,`item_id`,`mfc_id`,`project_id`,`qty`,`price`,`money`)
SELECT `id`,NOW(),p_ent,`doc_id`,`trader_id`,`item_category_id`,`item_id`,`mfc_id`,`project_id`,`qty`,`price`,`money`
FROM tml_data_docLine;

END$$

DELIMITER ;