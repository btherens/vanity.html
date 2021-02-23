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
                `label`      varchar(255) DEFAULT NULL,
                PRIMARY KEY  (`id`)
            ) ENGINE=InnoDB;;
            INSERT INTO `method` ( `label` )
            SELECT 'PHP' UNION
            SELECT 'JavaScript' UNION
            SELECT 'CSS' UNION
            SELECT 'MySQL';"
    ];

    /* constructor */
    public function __construct() { parent::__construct(); }

    /* methods */
    public function getMethods(): array
    {
        /* initiate output object */
        $o = [];
        /* push each result into output object */
        foreach ( $this->_getData() as $r ) { array_push( $o, $r->label ); };
        /* return result */
        return  $o;
    }

}
