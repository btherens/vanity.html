<?php

class EducationModel extends Model
{

    /* table definition */
    protected static $_definition = [
        /* table name */
        'name'   => 'education',
        /* limit the # of records to include in select query */
        'limit'  => 0,
        /* create and populate the table if it does not exist */
        'create' =>
            "CREATE TABLE `education` (
                `id`           int NOT NULL AUTO_INCREMENT,
                `organization` varchar(255) DEFAULT NULL,
                `subject`      varchar(255) DEFAULT NULL,
                `description`  text DEFAULT NULL,
                `startdate`    datetime DEFAULT NULL,
                `enddate`      datetime DEFAULT NULL,
                `timestamp`    timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB;;
            INSERT INTO `education` ( `organization`, `subject`, `description`, `startdate`, `enddate` )
            SELECT 'Union College','Medicine','Lorem ipsum dolor sit amet. Praesentium magnam consectetur vel in deserunt aspernatur est reprehenderit sunt hic. Nulla tempora soluta ea et odio, unde doloremque repellendus iure, iste.',DATE_ADD(MAKEDATE(year(DATE_SUB(CURRENT_TIMESTAMP,INTERVAL 60 MONTH )),245),INTERVAL - DAY( MAKEDATE(year(DATE_SUB(CURRENT_TIMESTAMP,INTERVAL 60 MONTH )),245) ) + 1 DAY),DATE_ADD(MAKEDATE(year(DATE_SUB(CURRENT_TIMESTAMP,INTERVAL 12 MONTH )),123),INTERVAL - DAY( MAKEDATE(year(DATE_SUB(CURRENT_TIMESTAMP,INTERVAL 12 MONTH )),122) ) + 1 DAY) UNION
            SELECT 'Northwestern University','Scandinavian Studies','Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem.',DATE_ADD(MAKEDATE(year(DATE_SUB(CURRENT_TIMESTAMP,INTERVAL 108 MONTH )),245),INTERVAL - DAY( MAKEDATE(year(DATE_SUB(CURRENT_TIMESTAMP,INTERVAL 108 MONTH )),245) ) + 1 DAY),DATE_ADD(MAKEDATE(year(DATE_SUB(CURRENT_TIMESTAMP,INTERVAL 60 MONTH )),123),INTERVAL - DAY( MAKEDATE(year(DATE_SUB(CURRENT_TIMESTAMP,INTERVAL 60 MONTH )),123) ) + 1 DAY);"
    ];

    /* constructor */
    public function __construct() { parent::__construct(); }

    /* educations */
    public function getEducations(): array
    {
        /* return entire result */
        return $this->_getData();
    }

}
