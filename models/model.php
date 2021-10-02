<?php

class Model
{
    private $_db;
    private $_sql;
    private $_param;

    private $_insert_id;

    protected $_result;

    public function __construct()
    {
        $this->_db = Db::init();
    }

    /* set _sql property */
    protected function setSql( string $sql, bool $usecache = false )
    {
        if ( $usecache )
        {
            $sql = '/*' . 'qc=on' . '*/' . $sql;
        };
        $this->_sql = $sql;
    }

    /* set _params property */
    /**
     * $format = array(var1type,var2type,...,varntype)
     * types:
     * i    integer
     * d    double
     * s    string
     * b    blob
     */
    protected function setParam( array $data, array $format )
    {
        /* normalize format */
        $format = implode( '', $format );
        /* prepend $format onto $data */
        array_unshift( $data, $format );
        /* assign to _param property */
        $this->_param = $this->ref_values( $data );
    }

    /* run the query staged in _sql and _param properties. response is saved to _result property */
    protected function runQuery()
    {
        $this->_result = $this->getResult( $this->executeSql() );
        $this->clearQuery();
    }

    /* clear the query variables, leaving _db for reuse and _result */
    private function clearQuery()
    {
        $this->_sql = null;
        $this->_param = null;
    }

    /* returns an array by reference */
    private function ref_values( array $array )
    {
        $refs = array();
        foreach ( $array as $key => $value )
        {
            $refs[ $key ] = &$array[ $key ];
        }
        return $refs;
    }

    /* prepares, binds, and executes a sql query */
    private function executeSql()
    {
        /* if no sql query was set */
        if ( !$this->_sql )
        {
            throw new Exception("No SQL query!");
        }

        /* begin transaction */
        $this->_db->begin_transaction();

        /* loop through multiple statements */
        foreach ( explode(";;", $this->_sql ) as $i => &$part )
        {
            /* begin a prepared statement */
            $stmt = $this->_db->prepare( $part );
            if ( !$stmt )
            {
                throw new Exception( 'invalid SQL statement: ' . $part );
            }
            /* bind params if params are set */
            if ( ( $i == 0 ) && $this->_param ) { call_user_func_array( array( $stmt, 'bind_param' ), $this->_param ); }
            $stmt->execute();
        }

        /* commit */
        $this->_db->commit();

        /* make insert_id accessible via insert_id() */
        $this->_insert_id = $stmt->insert_id;

        return $stmt;
    }

    /* return insert_id if it exists */
    protected function insert_id()
    {
        return isset( $this->_insert_id ) ? $this->_insert_id : null;
    }

    /* Processes the executed msqli object, returning a row of data,           */
    /* an array of rows of data, or true/false if affected row response exists */
    private function getResult( $stmt )
    {
        /* get results, read into results[] and return */
        $result = $stmt->get_result();
        if ($result)
        {
            $results = null;
            while ($row = $result->fetch_object())
            {
                $results[] = $row;
            }
            return $results;
        }
        /* otherwise, check for successful insertion using affected rows
         * make sure affected_rows is not -1 (insert error)
         */

        elseif ($stmt->affected_rows && $stmt->affected_rows != -1)
        {
            return true;
        }
        /* finally, return $false */

        else
        {
            return false;
        };
    }

    /* attempt to create table using a class' static $_definition property */
    protected function _createTable(): void
    {
        /* check for table */
        $this->setSql(
            'SELECT COUNT(*) AS `exists`
            FROM  information_schema.tables
            WHERE table_schema = ?
            AND   table_name = ?
            LIMIT 1;'
        );
        $this->setParam( array( Db::$name, $this::$_definition['name'] ), array( 's','s' ) );
        $this->runQuery();

        if ( !$this->_result[0]->exists )
        {
            /* set create script */
            $this->setSql( $this::$_definition['create'] );
            /* execute on database */
            $this->runQuery();
        }
    }

    /* SQL results managed here */
    private array $_data = [];
    /* SQL filters are set here and compared upon second get */
    private $_filter;
    /* getter for data object */
    protected function _getData( array $filter = null ): array
    {
        /* if data is empty, or if we have data but with a different filter */
        if ( !$this->_data || $this->_filter != $filter )
        {
            /* collect variables from child class */
            $def = &$this::$_definition;
            $n = &$def['name'];
            $l = &$def['limit'];

            /* set empty "where" string, type, value */
            $ws = ''; $wt = []; $wv = [];
            /* create where string if we have a filter */
            if ( $filter ) { foreach ( $filter as $k => $v )
            {
                /* append string */
                $ws .= $ws ? ' AND ' : ' WHERE ';
                $ws .= '`' . $k . '` = ?';
                /* cast to value and do some limited type detection */
                if ( is_numeric( $v ) ) { array_push( $wt, 'i' ); array_push( $wv, ( (int) $v ) ); } else { array_push( $wt, 's' ); array_push( $wv, $v  ); }
            /* bind parameters to query */
            } $this->setParam( $wv, $wt ); }
            /* set select query to property, LIMIT will only be applied if $l is truthy */
            $this->setSql( 'SELECT * FROM `' . $n . '` ' . $ws . ( $l ? ' LIMIT ' . $l : '' ) . ';' );

            try   { $this->runQuery(); }
            /* if the query fails, pass to createTable method and then bail */
            catch ( Exception $e ) { $this->_createTable(); die; }
            /* save result to _data */
            $this->_data = $this->_result ?: [];
            /* save filter to be checked against on subsequent lookups */
            $this->_filter = $filter;
        }
        /* return property */
        return $this->_data;
    }

}
