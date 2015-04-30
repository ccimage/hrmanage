BEGIN TRANSACTION;
CREATE TABLE [hr_timerange] (
  [id] INTEGER NOT NULL, 
  [rangefrom] INTEGER, 
  [rangeto] INTEGER, 
  [rangetype] INTEGER);
INSERT INTO `hr_timerange` VALUES(1,0,15,1);
INSERT INTO `hr_timerange` VALUES(2,60,120,2);
INSERT INTO `hr_timerange` VALUES(3,15,30,1);
INSERT INTO `hr_timerange` VALUES(4,30,60,1);
INSERT INTO `hr_timerange` VALUES(5,120,180,2);
INSERT INTO `hr_timerange` VALUES(6,180,240,2);
CREATE TABLE [hr_setting] (
  [starttime] TIME, 
  [offtime] TIME);
INSERT INTO `hr_setting` VALUES('09:00:00','18:00:00');
CREATE TABLE "hr_employee" (
	`id`	INTEGER NOT NULL,
	`workernum`	INTEGER NOT NULL,
	`realname`	TEXT NOT NULL,
	`password`	TEXT
);

CREATE TABLE "hr_checkrecord" (
	`id`	INTEGER NOT NULL,
	`checktime`	TEXT,
	`checkdate`	TEXT,
	`workernum`	INTEGER,
	PRIMARY KEY(id)
);
COMMIT;
