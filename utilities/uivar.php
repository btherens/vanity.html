<?php

/* custom class that contains a variable and validation logic */
class UIvar
{
    /* allow value to be null - defaults to true */
    public $allowNull = true;

    /* where the actual variable is stored */
    private $value;

    /* the regex validation pattern, if necessary */
    private $pattern;

    /* acceptable types
     * text
     * datetime-local
     * email
     * html
     */
    private $type;

    /* generic input validation pattern (don't accept any outside strings outside this boundary) */
    private $_default_ptrn = '^([\d\D]*)$';

    /* initial setup */
    public function __construct( $pattern = null )
    {
        $this->type = 'text';
        /* overrides for types based on the pattern passed to constructor */
        switch ( $pattern )
        {
            case 'datetime-local':
                $this->type = strtolower( $pattern );
                $this->pattern = '^(\d{4}-\d{2}-\d{2})T(0[0-9]|1[0-2]):([0-5][0-9])(:[0-5][0-9])?$';
                break;
            case 'boolean':
                $this->type = strtolower( $pattern );
                break;
            case 'email':
                $this->type = strtolower( $pattern );
                $this->pattern = '(?=.{5,255}$)^([^@]+@[A-z0-9À-ž_]+\.[A-z0-9À-ž_\.]+)$';
                break;
            case 'html':
                $this->type = strtolower( $pattern );
                $this->pattern = '^([\d\D]{0,255})$';
                break;
            case null:
                $this->pattern = $this->_default_ptrn;
                break;
            default:
                $this->pattern = $pattern;
                break;
        }
    }

    public function __get( string $var ) { return ( in_array( $var, [ 'value', 'pattern', 'type' ] ) && isset( $this->$var ) ) ? $this->$var : null; }

    /* clear the variable's value but leave object otherwise intact */
    public function clear(): void { unset( $this->value ); }

    /* a way of assigning the variable without evaluation (when source is trusted)
     * value is cast to bool based on condition
     */
    public function assign( $var ): void { $this->value = $this->type == 'boolean' ? ( (bool) $var ) : $var; }

    /* attempt to set value, returns success/fail status
     * setvalue = false to avoid saving value (only validate)
     */
    public function set( $value, $setvalue = true ): bool
    {
        /* result (success/fail) */
        $output = true;

        /* handling for strings of no length */
        $value = is_string( $value ) ? ( strlen( $value ) == 0 ? null : $value ) : $value;

        /* send non-null values through validation */
        if ( !is_null( $value ) )
        {
            try
            {
                switch ( $this->type )
                {
                    case 'datetime-local':
                        $result = $this->_processDate($value);
                        break;
                    case 'boolean':
                        $result = (bool) $value;
                        break;
                    case 'email':
                        $result = filter_var( $value, FILTER_VALIDATE_EMAIL ) ? $this->_processPattern( $value ) : null;
                        break;
                    case 'html':
                        $result = (bool) $this->_validateHTML($value);
                        break;
                    case 'text':
                        $result = $this->_processPattern($value);
                        break;
                }
            }
            catch ( Exception $e ) { $output = false; }
        }
        /* handle null values */
        elseif ( $this->allowNull ) { $result = null; } else { $output = false; }
        /* assign the value if we can */
        if ( $output && $setvalue ) { $this->value = $result; }
        /* return true/false on success/fail */
        return $output;
    }

    /* pass value through to string */
    public function __toString(): string { return !is_null( $this->value ) ? $this->value : ''; }

    /* sanitize string and pass on (exception on failed validation) */
    private function _processPattern( $value ): string
    {
        /* pass an normalized string  */
        if   ( $this->_isPattern( $value ) ) { return $this->_normalizeEOL( $value ); }
        /* throw exceptions */
        else { throw new Exception( 'invalid character in response!' ); }
    }

    /* sanitize date input and return if valid (exception upon failure) */
    private function _processDate( $value ): DateTime
    {
        /* return valid values as datetime objects */
        if ( $this->_isDatetime( $value ) ) { return new DateTime( $value ); }
        /* throw exceptions */
        else { throw new Exception( 'invalid character in response!' ); }
    }

    /* confirm that html validates using DOMdocument method
     * returns result as boolean
     */
    private function _validateHTML( string $value ): bool
    {
        /* open tag position */
        $start = strpos( $value, '<' );
        /* close tag position */
        $end = strrpos( $value, '>', $start );
        /* length of string */
        $len = strlen( $value );
        /* get substring */
        $value = $end !== false ? substr( $value, $start ) : substr( $value, $start, $len - $start );
        /* set error handling */
        libxml_use_internal_errors( true ); libxml_clear_errors();
        /* attempt to load string */
        $xml = simplexml_load_string( $value );
        return count( libxml_get_errors() ) == 0;
    }

    /* test for valid pattern */
    private function _isPattern( $value ): bool
    {
        /* use ~ as delimiter */
        $pattern = '~' . $this->pattern . '~';
        /* return results of pattern match */
        return ( bool ) preg_match( $pattern, $value );
    }

    /* test for valid date */
    private function _isDatetime( $value ): bool
    {
        /* attempt to create date object from string */
        $result = new DateTime( $value );
        /* return the result of the attempt */
        return !empty( $result );
    }

    /* replace all newline characters with unix-style LF */
    private function _normalizeEOL( $string, $to = "\n" ) { return preg_replace( "/\r\n|\r|\n/", $to, $string ); }

}
