<?php

class SkillModel extends Model
{

    /* table definition */
    protected static $_definition = [
        /* table name */
        'name'   => 'skill',
        /* limit the # of records to include in select query */
        'limit'  => 0,
        /* create and populate the table if it does not exist */
        'create' =>
            "CREATE TABLE `skill` (
                `id`         int NOT NULL AUTO_INCREMENT,
                `label`      varchar(255) DEFAULT NULL,
                PRIMARY KEY  (`id`)
            ) ENGINE=InnoDB;;
            INSERT INTO `skill` ( `label` )
            SELECT 'Adobe Photoshop' UNION
            SELECT 'Photography' UNION
            SELECT 'Illustrator' UNION
            SELECT 'Media';"
    ];

    /* constructor */
    public function __construct() { parent::__construct(); }

    /* skills */
    public function getSkills(): array
    {
        /* initiate output object */
        $o = [];
        /* push each result into output object */
        foreach ( $this->_getData() as $r ) { array_push( $o, $r->label ); };
        /* return result */
        return  $o;
    }

}
