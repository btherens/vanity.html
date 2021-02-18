<?php

class LanguageModel extends Model
{

    /* table definition */
    protected static $_definition = [
        /* table name */
        'name'   => 'language',
        /* limit the # of records to include in select query */
        'limit'  => 0,
        /* create and populate the table if it does not exist */
        'create' =>
            "CREATE TABLE `language` (
                `id`         int NOT NULL AUTO_INCREMENT,
                `label`      varchar(255) DEFAULT NULL,
                PRIMARY KEY  (`id`)
            ) ENGINE=InnoDB;;
            INSERT INTO `language` ( `label` )
            SELECT 'PHP' UNION
            SELECT 'JavaScript' UNION
            SELECT 'CSS' UNION
            SELECT 'MySQL';"
    ];

    /* constructor */
    public function __construct() { parent::__construct(); }

    /* languages */
    public function getLanguages(): array
    {
        /* initiate output object */
        $o = [];
        /* push each result into output object */
        foreach ( $this->_getData() as $row ) { array_push( $o, $row->label ); };
        /* return result */
        return  $o;
    }

}
