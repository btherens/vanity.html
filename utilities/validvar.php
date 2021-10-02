<?php

/* custom class that contains a variable and its validating logic */
class ValidVar
{
    /* defaults to true */
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
        switch ( $pattern )
        {
            /* overrides for types */
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

    public function __get( string $var )
    {
        return (in_array( $var, array('value', 'pattern', 'type' ) ) && isset( $this->$var ) ) ? $this->$var : null;
    }

    public function clear()
    {
        unset( $this->value );
    }

    /* a way of assigning the variable without evaluation (when source is trusted)
     * value is cast to bool based on condition
     */
    public function assign( $var )
    {
        $this->value = $this->type == 'boolean' ? ( ( bool ) $var ) : $var;
    }

    /* attempt to set value, returns success/fail status
     * setvalue = false to avoid saving value (only validate)
     */
    public function set( $value, $setvalue = true ): bool
    {
        /* result (success/fail) */
        $output = true;

        /* handling for strings of no length */
        $value = is_string( $value ) ? ( strlen( $value ) == 0 ? null : $value ) : $value;
        /* accepted case for empty value */
        if ( is_null( $value ) && $this->allowNull )
        {
            $result = null;
        }
        /* rejected case for empty value */

        elseif ( is_null( $value ) )
        {
            $output = false;
        }
        /* send through validation */

        else
        {
            try
            {
                switch ( $this->type )
                {
                    case 'datetime-local':
                        $result = $this->processDate( $value );
                        break;
                    case 'boolean':
                        $result = (bool) $value;
                        break;
                    case 'email':
                        $result = filter_var( $value, FILTER_VALIDATE_EMAIL ) ? $this->_processPattern( $value ) : null; //(throw new Exception('email validation failed!'));
                        break;
                    case 'html':
                        $result = (bool) $this->_validateHTML( $value );
                        break;
                    case 'text':
                        $result = $this->_processPattern( $value );
                        break;
                }
            }
            catch (Exception $e)
            {
                $output = false;
            }
        }
        /* assign the value */
        if ( $output && $setvalue )
        {
            $this->value = $result;
        }
        return $output;
    }

    public function __toString()
    {
        return !is_null( $this->value ) ? $this->value : '';

    }

    /* sanitize string and pass on (exception on failed validation) */
    private function _processPattern( $value ): string
    {
        if ($this->isPattern( $value ) )
        {
            return $this->normalizeEOL( $value );
        }
        else
        {
            throw new Exception( 'invalid character in response!' );
        }
    }

    /* confirm that html validates using DOMdocument method
     * returns result as boolean
     */
    private function _validateHTML( string $value ): bool
    {
        $start = strpos( $value, '<' );
        $end = strrpos($value, '>', $start);

        $len = strlen( $value );

        if ( $end !== false )
        {
            $value = substr( $value, $start );
        }
        else
        {
            $value = substr( $value, $start, $len - $start );
        }
        libxml_use_internal_errors( true );
        libxml_clear_errors();
        $xml = simplexml_load_string( $value );
        return count(libxml_get_errors()) == 0;
    }

    /* sanitize date input and return if valid (exception upon failure) */
    private function processDate( $value ): DateTime
    {
        if ($this->isDatetime( $value ) )
        {
            $result = ( new DateTime( $value ) );
            return $result;
        }
        else
        {
            throw new Exception( 'invalid character in response!' );
        }
    }

    /* test for valid pattern */
    private function isPattern( $value ): bool
    {
        /* use ~ as delimiter, preg_quote to escape */
        #$pattern = '~' . preg_quote($this->pattern, '~') . '~';
        $pattern = '~' . $this->pattern . '~';
        return preg_match( $pattern, $value ) ? true : false;
    }

    /* test for valid date */
    private function isDatetime( $value ): bool
    {
        $result = ( new DateTime( $value ) );
        return !empty( $result ) ? true : false;
    }

    private function normalizeEOL( $string, $to = "\n" )
    {
        return preg_replace( "/\r\n|\r|\n/", $to, $string );
    }

}
