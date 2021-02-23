<?php

class WorkModel extends Model
{

    /* table definition */
    protected static $_definition = [
        /* table name */
        'name'   => 'work',
        /* limit the # of records to include in select query */
        'limit'  => 0,
        /* create and populate the table if it does not exist */
        'create' =>
            "CREATE TABLE `work` (
                `id`           int NOT NULL AUTO_INCREMENT,
                `organization` varchar(255) DEFAULT NULL,
                `role`         varchar(255) DEFAULT NULL,
                `description`  text DEFAULT NULL,
                `startdate`    datetime DEFAULT NULL,
                `enddate`      datetime DEFAULT NULL,
                `timestamp`    timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB;;
            INSERT INTO `work` ( `organization`, `role`, `description`, `startdate`, `enddate` )
            SELECT 'AviWeb', 'Front End Developer', 'Lorem ipsum dolor sit amet. Praesentium magnam consectetur vel in deserunt aspernatur est reprehenderit sunt hic. Nulla tempora soluta ea et odio, unde doloremque repellendus iure, iste.', DATE_ADD(DATE_SUB(CURRENT_TIMESTAMP,INTERVAL 59 MONTH ), interval -DAY(DATE_SUB(CURRENT_TIMESTAMP,INTERVAL 59 MONTH ))+1 DAY), NULL UNION
            SELECT 'Bionix', 'Microbiologist', 'Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo.', DATE_ADD(DATE_SUB(CURRENT_TIMESTAMP,INTERVAL 90 MONTH ),interval -DAY(DATE_SUB(CURRENT_TIMESTAMP,INTERVAL 90 MONTH ))+1 DAY), DATE_ADD(DATE_SUB(CURRENT_TIMESTAMP,INTERVAL 60 MONTH ), interval -DAY(DATE_SUB(CURRENT_TIMESTAMP,INTERVAL 60 MONTH ))+1 DAY) UNION
            SELECT 'Ocalc', 'Statistician', 'Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem.', DATE_ADD(DATE_SUB(CURRENT_TIMESTAMP,INTERVAL 130 MONTH ),interval -DAY(DATE_SUB(CURRENT_TIMESTAMP,INTERVAL 60 MONTH ))+1 DAY), DATE_ADD(DATE_SUB(CURRENT_TIMESTAMP,INTERVAL 90 MONTH ), interval -DAY(DATE_SUB(CURRENT_TIMESTAMP,INTERVAL 90 MONTH ))+1 DAY);"
    ];

    /* constructor */
    public function __construct() { parent::__construct(); }

    /* works */
    public function getWorks(): array
    {
        /* create listmodel object to query  */
        $list = New ListModel();
        /* get data */
        $o = $this->_getData();
        /* get list values for each row */
        foreach ( $o as $row ) { $row->list = $list->getList( 'w', $row->id ); }
        /* return result */
        return $o;
    }

}
