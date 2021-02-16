<?php

class ProfileModel extends Model
{

    /* table definition */
    protected static $_definition = [
        /* table name */
        'name'   => 'profile',
        /* limit the # of records to include in select query */
        'limit'  => 1,
        /* create and populate the table if it does not exist */
        'create' =>
            "CREATE TABLE `profile` (
                `id`         int NOT NULL AUTO_INCREMENT,
                `name`       varchar(255) DEFAULT NULL,
                `occupation` varchar(255) DEFAULT NULL,
                `home`       varchar(255) DEFAULT NULL,
                PRIMARY KEY  (`id`)
            ) ENGINE=InnoDB;;
            INSERT INTO `profile` ( `name`, `occupation`, `home` )
            SELECT 'Bilbo Baggins', 'raconteur', 'Hobbiton, Shire';"
    ];

    public function __construct()
    {
        parent::__construct();
    }

    /* name */
    public function getName(): string
    { return $this->_getData()[0]->name; }

    /* occupation */
    public function getOccupation(): string
    { return $this->_getData()[0]->occupation; }

    /* occupation */
    public function getHome(): string
    { return $this->_getData()[0]->home; }

}
