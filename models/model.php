<?php

class Model
{
    /* connection to the database */
    private $_db;
    /* the query to be executed */
    private $_sql;
    /* array of parameters */
    private $_param;

    private string $_basepath;

    /* the insert_id returned by mysql query */
    private $_insert_id;
    /* insert_id getter */
    protected function insert_id() { return isset( $this->_insert_id ) ? $this->_insert_id : null; }
    /* the table result from mysql */
    protected $_result;

    /* constructor - initiate db static class */
    public function __construct()
    {
        $this->_db = Db::init();
        /* assign default / override basepath */
        $this->_basepath = ( defined( 'RESOURCEFILEPATH' ) ? RESOURCEFILEPATH : 'resource' ) . DS;
    }

    /* return the path used for document space */
    protected function basepath(): string { return $this->_basepath; }

    /* get file from application path */
    protected function getFile( string $name ): void { readfile( $this->basepath() . $name ); }

    /* get file from application path */
    protected function dropFile( string $name ): void { try { unlink( $this->basepath() . $name ); } catch ( Exception $e ) {  } }

    /* set _sql property */
    protected function setSql( string $sql, bool $usecache = false )
    {
        /* prepend cache statement if boolean set */
        if ( $usecache ) { $sql = '/*' . 'qc=on' . '*/' . $sql; };
        /* set value */
        $this->_sql = $sql;
    }

    /* set _params property */
    /**
     * $format = [ var1type, var2type, ..., varntype ]
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
        $this->_result = $this->executeSql();
        $this->clearQuery();
    }

    /* clear the query variables, leaving _db for reuse and _result */
    private function clearQuery()
    {
        $this->_sql = null;
        $this->_param = null;
    }

    /* sanitize an array for setting to params object */
    private function ref_values( array $array ): array
    {
        /* declare output array */
        $refs = [];
        /* step through input array and assign references by key to output object */
        foreach ( $array as $key => $value ) { $refs[ $key ] = &$array[ $key ]; }
        /* return sanitized array */
        return $refs;
    }

    /* prepares, binds, and executes a sql query */
    private function executeSql()
    {
        /* if no sql query was set */
        if ( !$this->_sql ) { throw new Exception( 'No SQL query!' ); }

        /* begin transaction */
        $this->_db->begin_transaction();

        /* loop through multiple statements */
        foreach ( explode(";;", $this->_sql ) as $i => &$part )
        {
            /* begin a prepared statement */
            $stmt = $this->_db->prepare( $part );
            if ( !$stmt ) { throw new Exception( 'invalid SQL statement: ' . $part ); }
            /* bind params if params are set */
            if ( ( $i == 0 ) && $this->_param ) { call_user_func_array( [ $stmt, 'bind_param' ], $this->_param ); }
            $stmt->execute();
        }
        /* make insert_id accessible via insert_id() */
        $this->_insert_id = $stmt->insert_id;
        /* process results */
        $result = $this->getResult( $stmt );
        /* commit statements if we've reached this point */
        $this->_db->commit();

        return $result;
    }

    /* Processes the executed msqli object, returning a row of data,           */
    /* an array of rows of data, or true/false if affected row response exists */
    private function getResult( $stmt )
    {
        /* get results, read into results[] and return */
        $result = $stmt->get_result();
        /* check for a result and cast rows to array */
        if ( $result )
        {
            $results = null; 
            /* save each row we find to a new array */
            while ( $row = $result->fetch_object() ) { $results[] = $row; }
            return $results;
        }
        /* otherwise, check for successful insertion using affected rows
         * make sure affected_rows is not -1 (insert error)
         */
        elseif ( $stmt->affected_rows && $stmt->affected_rows != -1 ) { return true; }
        /* finally, return $false */
        else   { return false; }
    }

    /* attempt to create table using a class' static $_definition property */
    protected function _createTable(): void
    {
        /* set sql query to return table record in schema */
        $this->setSql(
            'SELECT COUNT(*) AS `exists`
            FROM  information_schema.tables
            WHERE table_schema = ?
            AND   table_name = ?
            LIMIT 1;'
        );
        /* connect parameters */
        $this->setParam( [ Db::$name, $this::$_definition[ 'name' ] ], [ 's','s' ] );
        /* execute query */
        $this->runQuery();

        /* set create script and execute on database if table does not exist */
        if ( !$this->_result[0]->exists ) { $this->setSql( $this::$_definition[ 'create' ] ); $this->runQuery(); }
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
            $n = &$def[ 'name' ];
            $l = &$def[ 'limit' ];

            /* set empty "where" string, type, value */
            $ws = ''; $wt = []; $wv = [];
            /* create where string if we have a filter */
            if ( $filter ) { foreach ( $filter as $k => $v )
            {
                /* append string */
                $ws .= $ws ? ' AND ' : ' WHERE ';
                $ws .= '`' . $k . '` = ?';
                /* cast to value and do some limited type detection */
                if ( is_numeric( $v ) ) { array_push( $wt, 'i' ); array_push( $wv, ( ( int ) $v ) ); } else { array_push( $wt, 's' ); array_push( $wv, $v  ); }
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

    /* a simple web request for headers (response object returned if successful, false if not) */
    protected function getHeaders( string $url ) #: aray|false
    {
        /* set default stream context to head only */
        stream_context_set_default( [ 'http' => [ 'method' => 'HEAD' ] ] );
        /* attempt to get headers from html endpoint, returning false upon exception */
        if ( $url ) { try { $headers = get_headers( $url ); } catch ( Exception $e ) { $headers = false; } } else { $headers = false; }
        /* return result */
        return $headers;
    }

    /* execute CLI commands with parameters and get return value */
    protected function shellExec( string $cmd, array $param = null ): ?array { return $this->_callShell( $this->_buildShellCmd( $cmd, $param ) ); }

    /* return a shell command based on the given base command text, as well as any arguments passed in associative array $param */
    private function _buildShellCmd( string $cmd, array $param = null ): string
    {
        /* loop through parameters */
        if ( $param ) { foreach( $param as $k => $v )
        {
            /* append named arguments to command, or pass key as flag if value is null */
            if   ( gettype( $k ) == 'string' ) { $cmd = $cmd . ' -' . $k . ( is_null( $v ) ? '' : ' ' . escapeshellarg( $v ) ); }
            /* otherwise append arguments in order */
            else { $cmd = $cmd . ' ' . escapeshellarg( $v ); }
        } }
        return $cmd;
    }

    /* execute the given command and return output - method will throw upon shell exception */
    private function _callShell( $cmd ): ?array
    {
        /* open process and direct results to $stream array */
        $process = proc_open( $cmd, [ 0 => [ 'pipe', 'r' ], 1 => [ 'pipe', 'w' ], 2 => [ 'pipe', 'w' ] ], $stream );
        /* throw exception if we couldn't open the process */
        if ( !is_resource( $process ) ) { throw new Exception( 'error opening shell process!' ); }
        else
        {
            /* close input stream */
            fclose( $stream[ 0 ] );
            /* get output */
            $result = explode( PHP_EOL, trim( stream_get_contents( $stream[ 1 ] ) ) ); fclose( $stream[ 1 ] );
            /* get error stream */
            $stderr = stream_get_contents( $stream[ 2 ] ); fclose( $stream[ 2 ] );

            /* close process and record exit code */
            $exit = proc_close( $process );

            /* throw from shell execution */
            if     ( $exit )   { throw new Exception( 'shell exception: ' . $stderr ); }
            /* return result if we got one */
            elseif ( $result ) { return $result; }
        }
    }

}
