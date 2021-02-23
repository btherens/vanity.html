<?php

class ListModel extends Model
{

    /* table definition */
    protected static $_definition = [
        /* table name */
        'name'   => 'list',
        /* limit the # of records to include in select query */
        'limit'  => 0,
        /* create and populate the table if it does not exist */
        'create' =>
            "CREATE TABLE `list` (
                `id`         int NOT NULL AUTO_INCREMENT,
                `type`       char(1) NULL,
                `fid`        int NULL,
                `text`       text DEFAULT NULL,
                `timestamp`  timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB;;"
    ];

    /* constructor */
    public function __construct() { parent::__construct(); }

    /* list */
    public function getList( string $t, int $i ): array
    {
        /* initiate output object */
        $o = [];
        /* push each result into output object */
        foreach ( $this->_getData() as $row ) { array_push( $o, $row->text ); };
        /* return result */
        return  $o;
    }

}
