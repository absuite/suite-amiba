DELIMITER $$
DROP PROCEDURE IF EXISTS sp_amiba_allot_level$$


CREATE PROCEDURE sp_amiba_allot_level(IN p_ent CHAR(200),IN p_purpose CHAR(200),IN p_period CHAR(200)) 
BEGIN

DECLARE v_loop INT DEFAULT 1;

/*分配标准*/
DROP TEMPORARY TABLE IF EXISTS tml_result_allocated;
CREATE TEMPORARY TABLE IF NOT EXISTS tml_result_allocated(
  `fm_group_id` NVARCHAR(100),
  `fm_bizkey` NVARCHAR(500),
  `to_group_id` NVARCHAR(100),
  `to_bizkey` NVARCHAR(500)
);


/*阿米巴层级*/
DROP TEMPORARY TABLE IF EXISTS tml_data_fm_group;
CREATE TEMPORARY TABLE IF NOT EXISTS tml_data_fm_group(
  `group_id` NVARCHAR(100),
  `bizkey` NVARCHAR(500),
  `level` INT
);

DROP TEMPORARY TABLE IF EXISTS tml_data_to_group;
CREATE TEMPORARY TABLE IF NOT EXISTS tml_data_to_group(
  `group_id` NVARCHAR(100),
  `bizkey` NVARCHAR(500),
  `level` INT
);

UPDATE suite_amiba_allot_rules SET bizkey=CONCAT(IFNULL(group_id,'0'),'|',IFNULL(`element_id`,'0'));

UPDATE suite_amiba_allot_rule_lines SET bizkey=CONCAT(IFNULL(group_id,'0'),'|',IFNULL(`element_id`,'0'));


INSERT INTO tml_result_allocated(fm_group_id,fm_bizkey,to_group_id,to_bizkey)
SELECT DISTINCT h.`group_id`,h.bizkey,l.`group_id`,l.bizkey
FROM `suite_amiba_allot_rules` AS h 
INNER JOIN `suite_amiba_allot_rule_lines` AS l ON h.id=l.`rule_id`
WHERE h.`purpose_id`=p_purpose AND h.`ent_id`=p_ent;

/**/
INSERT INTO tml_data_fm_group(group_id,bizkey,LEVEL)
SELECT DISTINCT fm_group_id,fm_bizkey,1 
FROM tml_result_allocated;


INSERT INTO tml_data_fm_group(group_id,bizkey,LEVEL)
SELECT DISTINCT to_group_id,to_bizkey,2
FROM tml_result_allocated AS l
WHERE NOT EXISTS(SELECT * FROM tml_data_fm_group AS d WHERE d.bizkey=l.to_bizkey);

INSERT INTO tml_data_to_group(group_id,bizkey,LEVEL) SELECT group_id,bizkey,LEVEL FROM tml_data_fm_group;


loop_example : LOOP

UPDATE tml_data_fm_group AS f
INNER JOIN tml_result_allocated AS d ON f.bizkey=d.fm_bizkey
INNER JOIN tml_data_to_group AS t ON t.bizkey=d.to_bizkey
SET t.LEVEL=f.LEVEL+1
WHERE t.LEVEL<=f.LEVEL;
IF ROW_COUNT()=0 THEN
  LEAVE loop_example;
END IF;

UPDATE tml_data_fm_group AS f
INNER JOIN tml_data_to_group AS t ON t.bizkey=f.bizkey
SET f.LEVEL=t.LEVEL;


SET v_loop = v_loop + 1;

IF v_loop> 10 THEN
  LEAVE loop_example;
END IF;

END LOOP;



DELETE FROM suite_amiba_allot_levels WHERE ent_id=p_ent AND purpose_id=p_purpose AND period_id=p_period;

INSERT INTO `suite_amiba_allot_levels`(`ent_id`,`purpose_id`,`period_id`,`group_id`,`bizkey`,`level`,`created_at`)
SELECT p_ent,p_purpose,p_period,l.group_id,l.bizkey,l.level,NOW()
FROM tml_data_to_group AS l;

END$$

DELIMITER ;