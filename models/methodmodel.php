<?php

class methodModel extends Model
{

    /* table definition */
    protected static $_definition = [
        /* table name */
        'name'   => 'method',
        /* limit the # of records to include in select query */
        'limit'  => 0,
        /* create and populate the table if it does not exist */
        'create' =>
            "CREATE TABLE `method` (
                `id`         int NOT NULL AUTO_INCREMENT,
                `label`      varchar(255) NULL,
                `aptitude`   decimal(2,2) NULL,
                PRIMARY KEY  (`id`)
            ) ENGINE=InnoDB;;
            INSERT INTO `method` ( `label`, `aptitude` )
            SELECT 'PHP', .8 UNION
            SELECT 'JavaScript', .55 UNION
            SELECT 'CSS', .65 UNION
            SELECT 'MySQL', .9;"
    ];

    /* constructor */
    public function __construct() { parent::__construct(); }

    /* return list of methods */
    public function getMethods(): array { return $this->_getData(); }

}
