<?php

/* BaseUInterface - base class that manages form and annotations */
class BaseUInterface extends DomDocument
{

    /* group name for annotating elements */
    protected $_group;

    /* read access to _group */
    private $_readonly = [ '_group' ];
    public function __get( string $var ) { return ( in_array( $var, $this->_readonly ) && isset( $this->$var ) ) ? $this->$var : null; }

    protected $_table;

    /* property determines if javascript is used (default on) */
    protected $_isactive;

    /* initial setup, set formatting preferences */
    public function __construct( string $group )
    {
        parent::__construct( '1.0', 'utf-8' );
        $this->formatOutput = true;
        $this->_group = $group;

        $this->_isactive = true;
    }

    /* magic method directed through saveHTML() */
    public function __toString() { return $this->saveHTML(); }

    /* pass saveHTML to parent */
    public function saveHTML( $node = null ): string|false { return parent::saveHTML( $node ); }

    /* overloading createElement to use the UIelement definition below */
    public function createElement( $name, $value = null ): UIelement
    {
        /* new sub-class object */
        $orphan = new UIelement( $name, $value );
        /* by passing things through docFragment, the ownerDocument property is maintained */
        $docFragment = $this->createDocumentFragment();
        $docFragment->appendChild( $orphan );
        $ret = $docFragment->removeChild( $orphan );

        /* return the element here, it will be able to link to ownerdocument */
        return $ret;
    }

    /* */
    protected $_form;

    /* create form, save to _form property and add to object DOM */
    public function setForm( string $name = null, string $action = null ): UIelement
    {
        $frm = $this->createElement( 'form' );

        $this->_form = &$frm;

        $frm->setAttribute( 'name', $name ? $name : $this->_group );

        $frm->setAttribute( 'id', $this->_group . '-frm' );
        $frm->setAttribute( 'action', isset( $action ) ? $action : '' );
        $frm->setAttribute( 'method', 'post' );
        $this->appendChild( $frm );
        return $frm;
    }

    /* function to create and pass a label/field div (not added to dom) */
    protected function _createComplexElement(
        string $tag,
        string $name,
        string $label = null,
        string $value = null,
        string $regexpattern = null,
        string $class = null,
        /* use $reverse = true to reverse dom order of label/input (for checkboxes) */
        bool $reverse = false,
        bool $isactive = null
    ): UIelement
    {
        /* active settings - use object property by default */
        if ( is_null( $isactive ) ) { $isactive = $this->_isactive; }
        $group = &$this->_group;
        /* generate id */
        $id = $group . '-' . $tag . '-' . $name;

        /* create elements */
        $container = $this->createElement( 'div' );
        $element = $this->createElement( $tag );

        /* save main element reference to container */
        $container->setMain( $element );

        /* set attributes */
        $element->setAttribute( 'id', $id ); $element->setAttribute( 'name', $group . '-' . $name );

        /* if a regex pattern is given, assign to the pattern attribute */
        if ( $regexpattern ) { $element->setAttribute( 'pattern', $regexpattern ); }

        /* add js events and active css */
        if ( $isactive ) { $element->setClass( 'script-active' ); $element->setAttribute( 'onkeyup', 'processActive(this);' ); }
        /* set value attribute, if provided */
        $element->setAttribute( 'value', !is_null( $value ) ? $value : '' );

        /* set class, if provided */
        if ( $class ) { $element->setClass($class); }

        /* if a label is defined, create the label element and link */
        if ( $label )
        {
            /* label set to placeholder text */
            $element->setAttribute( 'placeholder', $label );
            /* label element is created! */
            $lbl = $this->createElement( 'label', $label );
            $lbl->setAttribute( 'for', $id );

            /* label is assigned after for checkboxes, before for others (inputs) */
            if   ( $reverse ) { $container->appendChild( $element ); $container->appendChild( $lbl ); }
            else { $container->appendChild( $lbl ); $container->appendChild( $element ); }
        }
        else { $container->appendChild( $element ); }

        return $container;
    }

}

/* Extend BaseUInterface class with complex input and other element build methods */
class UInterface extends BaseUInterface
{
    /* constructor */
    public function __construct( string $group ) { parent::__construct( $group ); }

    public function createTable( array $headers, bool $invisiblecolumn = false ): UIelement
    {
        $tbl = $this->createElement( 'table' );
        $this->_table = &$tbl;

        /* set class style tags if invisible column needed */
        if ( $invisiblecolumn ) { $tbl->setClass( 'invisible-column' ); }

        /* add table headers */
        $tbl->setTableHeader( $headers );

        /* add the table to the dom and return table variable */
        $this->appendChild( $tbl );
        return $tbl;
    }

    /* function to create and pass a button (not added to dom) */
    public function createButton( string $key, $name, string $label, bool $submit = true ): UIelement
    {
        $group = &$this->_group;
        $id = $group . '-btn-' . $key;

        $btn = $this->createElement( 'button' );

        $btn->setAttribute( 'id', $id );
        $btn->setAttribute( 'form', $this->_form->getAttribute( 'id' ) );
        $btn->setAttribute( 'name', $name ? $group . '-' . $name : $group );
        $btn->setAttribute( 'type', 'submit' );
        $btn->setSubmit( $submit );
        $btn->setAttribute( 'value', $key );
        $btn->appendChild( $this->createElement( 'span', $label ) );

        return $btn;
    }

    /* create a simple div with id and name tags */
    public function createDiv( string $key = '' ): UIelement
    {
        /* create div */
        $div = $this->createElement( 'div' );
        /* set attributes */
        $div->setAttribute( 'id', $this->_group . '-div-' . $key );
        $div->setAttribute( 'name', $this->_group . '-' . $key );

        return $div;
    }

    /* function to create and pass an input (not added to dom) */
    public function createInput(
        string $name,
        string $label = null,
        $valueObj = null,
        string $class = null
    ): UIelement
    {
        /* initial values */
        $value = null;
        $regexpattern = null;
        $varTypestr = null;

        /* STEP 1
         * processing types
         * properly handle data in $valueObj
         */
        if ( !empty( $valueObj ) )
        {
            if ( gettype( $valueObj ) == 'object' )
            {
                if ( get_class( $valueObj ) == 'UIvar' )
                {
                    /* if value exists, simply pass on to inherit type */
                    if ( $valueObj->value ) { $value = $valueObj->value; }
                    /* for null cases, attempt to create a type in other fussier ways */
                    else
                    {
                        switch ( $valueObj->type )
                        {
                            case 'datetime-local':
                                /* set vartype to datetime */
                                $varTypestr = 'DateTime';
                                $value = null;
                                break;
                            case 'boolean':
                                /* false is a good initiated value */
                                $value = false;
                                break;
                            case 'email':
                            /* tbd */
                            case 'text':
                                $value = null;
                                break;
                        }
                    }
                    $regexpattern = $valueObj->pattern;
                }
                else
                {
                    $value = $valueObj;
                    $regexpattern = '^([\d\D]*)$';
                }
            }
            else
            {
                $value = $valueObj;
                $regexpattern = '^([\d\D]*)$';
            }
        }

        /* STEP 2
         * create the complex element
         * some types may get special validation patterns
         */
        /* detect type if we haven't already */
        $varTypestr = $varTypestr ?: gettype( $value );
        /* get object types from class name */
        if ( $varTypestr == 'object' ) { $varTypestr = get_class( $value ); }
        /* type dependent switch */
        switch ( $varTypestr )
        {
            /* special handling, boolean value types will appear as checkboxes */
            case 'boolean':
                $container = $this->_createComplexElement( 'input', $name, $label, 1, $regexpattern, $class, true );
                if ( $value ) { $container->setMainAttribute( 'checked' ); };
                break;
            case 'DateTime':
                /* cast date as string in format compatible with datetime picker */
                $value = $value ? $value->format( 'Y-m-d\TH:i:s' ) : null;
                /* apply custom regex pattern to date */
                $regexpattern = '^(\d{4}-\d{2}-\d{2})T(0[0-9]|1[0-9]|2[0-3]):([0-5][0-9])(:[0-5][0-9])?$';
            default:
                $container = $this->_createComplexElement( 'input', $name, $label, $value, $regexpattern, $class );
                break;
        }

        /* STEP 3
         * postprocess
         * assign type to input attribute, along with any other
         * post-processing a type requires
         */
        switch ( $varTypestr )
        {
            /* special handling, boolean value types will appear as checkboxes */
            case 'boolean':
                $container->setMainAttribute( 'type', 'checkbox');
                $container->setMainAttribute( 'onchange', 'processActive(this);' );
                break;
            case 'DateTime':
                $container->setMainAttribute( 'type', 'datetime-local' );
                $container->setMainAttribute( 'onchange', 'processActive(this);' );
                break;
            default:
                $container->setMainAttribute( 'type', 'text' );
                break;
        }

        return $container;
    }

    /* function to create and pass an input (not added to dom) */
    public function createTextArea(
        string $name,
        string $label = null,
        $valueObj = null,
        string $class = null
    ): UIelement
    {
        $value = null;
        $regexpattern = '^([\d\D]*)$';

        /* processing types */
        if ( !empty( $valueObj ) )
        {
            if ( gettype( $valueObj ) == 'object' )
            {
                if   ( get_class( $valueObj ) == 'UIvar' ) { $value = $valueObj->value; $regexpattern = $valueObj->pattern; }
                else { $value = $valueObj; }
            }
            else { $value = $valueObj; }
        }
        /* create the element */
        $container = $this->_createComplexElement( 'textarea', $name, $label, $value, $regexpattern, $class );

        /* customized attributes for input type */
        if ( $value ) { $container->lastChild->nodeValue = htmlspecialchars( $value ); }

        return $container;
    }

    /* function to create and pass a text preview display */
    public function createTextPreview(
        string $name,
        string $label = null,
        string $value = null,
        string $class = ''
    ): UIelement
    {
        /* add textpreview css to passed class string */
        $class = 'textpreview update-no' . ( $class ? ' ' . $class : $class );
        /* create element */
        $container = $this->_createComplexElement( 'div', $name, $label, null, null, $class, false );
        /* apply inner value if we can */
        if ( $value ) { $container->setInnerHTML( $value ); };
        return $container;
    }

}

/* UIelement - DomElement-based class with rendering, event handling, class management */
class UIelement extends DomElement
{
    /* table properties */
    private $_rows;
    private $_columns;

    /* reference to mainattribute UIelement, for easy manipulation
     * read-only, public set interface is setMain();
     */
    private $_mainAttribute;

    /* read only properties */
    private $_readonly = array( '_mainAttribute' );
    public function __get( string $var ) { return ( in_array( $var, $this->_readonly ) && isset( $this->$var ) ) ? $this->$var : null; }

    /* assign javascript events with debounce (set to false to disable debounce in subsequent event assignments) */
    public bool $eventdebounce = true;

    /* pass details to parent constructor */
    /* string $qualifiedName, ?string $value = null, string $namespace = "" */
    public function __construct( string $name, ?string $value = null, string $namespaceURI = '' )
    {
        parent::__construct( $name, is_null( $value ) ? null : htmlspecialchars( $value ), $namespaceURI );

        /* by default, _mainAttribute references the UIelement */
        $this->setMain( $this );
    }

    /* magic method directed through saveHTML() */
    public function __toString() { return $this->saveHTML(); }

    /* saveHTML method accessible from the Element class! uses ownerDocument for rendering */
    public function saveHTML() { return $this->ownerDocument->saveHTML( $this ); }

    /* set this DOMElement's innerHTML */
    public function setInnerHTML( ?string $html ): void
    {
        if ( $html ) { try
        {
            /* load html into a temporary DOMDocument object */
            $tmpDOM = new DOMDocument(); $tmpDOM->loadHTML( $html );
            /* get the content */
            $node = $tmpDOM->getElementsByTagName( 'body' )->item( 0 );
            /* load node into current DOM and update reference */
            $node = $this->ownerDocument->importNode( $node, true );
        }
        /* throw html parse exceptions */
        catch ( Exception $e ) { throw new Exception( 'unrecoverable errors in html!' ); } }

        /* remove childnodes from this element */
        while ( $this->_mainAttribute->hasChildNodes() ) { $this->_mainAttribute->removeChild( $this->firstChild ); }
        /* set html to element if we have any */
        if    ( isset( $node ) ) { $this->_mainAttribute->appendChild( $node ); }
    }

    /* non-destructive method to set a class tag (appending if class exists) */
    public function setClass( string $class ): void
    {
        $currentclass = $this->getAttribute( 'class' );
        $this->setAttribute( 'class', !empty( $currentclass ) ? $currentclass . " $class" : "$class" );
    }

    /* non-destructive method to remove a class tag */
    public function removeClass( string $class ): void
    {
        $currentclass = $this->getAttribute('class');
        $newclass = str_replace( $class, '', $currentclass );

        /* if new class is blank, remove class attribute entirely */
        if   ( ctype_space( $newclass ) || $newclass == '' ) { $this->removeAttribute( 'class' ); }
        /* otherwise, set new class attribute */
        else { $this->setAttribute( 'class', $newclass ); }
    }

    /* a method to run a given key value against the class' key value, will perform alterations upon match */
    public function setBtnSetName( bool $isSet = true, ?string $newname = null ): void
    {
        /* remove / add btn-set */
        $isSet ? $this->setClass( 'btn-set' ) : $this->removeClass( 'btn-set' );
        /* set new name attribute */
        $this->setAttribute( 'name', $this->ownerDocument->_group . '-' . $newname );
    }

    /* set submit status, accepts bool */
    public function setSubmit( bool $dosubmit = true ): void
    {
        if ($this->nodeName == 'button')
        {
            /* add submit attribute if submit bool passed */
            if ( $dosubmit )
            {
                $this->removeAttribute( 'formnovalidate' );
                $this->removeClass( 'alt1' );
            }
            else
            {
                $this->setAttribute( 'formnovalidate', 'formnovalidate' );
                $this->setClass( 'alt1' );
            }
        }
    }

    /* set the table header */
    public function setTableHeader( array $headers ): void
    {
        if ( !isset( $this->_rows ) && !isset( $this->_columns ) )
        {
            $this->_settablerowGeneric( $headers, 'th' );
            /* save table dimension properties */
            $this->_rows = 1;
            $this->_columns = sizeof( $headers );
        }
        else { throw new Exception('table headers exist'); }
    }

    /* set a row to a table UIelement */
    public function setTableRow( array $cells ): void
    {
        if ( isset( $this->_rows ) && isset( $this->_columns ) )
        {
            if ( $this->_columns == sizeof( $cells ) )
            {
                $this->_settablerowGeneric( $cells, 'td' );
                /* save table dimension properties */
                $this->_rows++;
            }
            else { throw new Exception( 'table dimension mismatch' ); }
        }
        else { throw new Exception( 'table not set!' ); }
    }

    /* generic function to add a table row to the UIelement */
    private function _settablerowGeneric( array $headers, string $tag = 'td' ): void
    {
        /* create table row and add header cells */
        $tablerow = $this->ownerDocument->createElement( 'tr' );
        foreach ( $headers as $header )
        {
            if ( gettype( $header ) == 'object' )
            {
                if ( get_class( $header ) == 'UIelement' )
                {
                    $elmt = $this->ownerDocument->createElement( $tag );
                    $elmt->appendChild( $header );
                    $tablerow->appendChild( $elmt );
                }
                else
                {
                    throw new Exception( 'unexpected class type in headers. class: ' . get_class( $header ) );
                }
            }
            else
            {
                $tablerow->appendChild( $this->ownerDocument->createElement( $tag, $header ) );
            }
        }
        /* add the table row to the table */
        $this->appendChild( $tablerow );
    }

    /* create reference at _mainAttribute property to the specified mainattribute */
    public function setMain( &$arg1 ): void { $this->_mainAttribute = &$arg1; }

    public function setMainAttribute( $arg1, $arg2 = null ): void
    {
        if ( is_null( $arg2 ) ) { $arg2 = $arg1; }
        if ( $this->_mainAttribute ) { $this->_mainAttribute->setAttribute( $arg1, $arg2 ); } else { $this->setAttribute( $arg1, $arg2 ); }
    }

    /* comma delimit the results of argument array passed through javascript process function */
    protected static function _JsArgs( ...$args ): string
    {
        /* comma delimit the results of argument array passed through process function */
        $str = implode( ',', array_map( function( $val )
        {
            /* convert numeric values to string */
            if   ( is_numeric( $val ) ) { $result = strval( $val ); }
            /* switch based on type / class */
            else { switch ( gettype( $val ) == 'object' ? get_class( $val ) : gettype( $val ) )
            {
                /* get ids from UIelement types */
                case 'UIelement': $result = 'document.getElementById(\'' . $val->_mainAttribute->getAttribute( 'id' ) . '\')'; break;
                /* sanitize strings */
                default: $result = "'" . htmlspecialchars( $val ) . "'"; break;
            } }
            /* return result to array_map */
            return $result;
        }, $args ) );
        return $str;
    }

    /* set up a live link for this element with a given jsfunction name
     * pass arguments to javascript function call in $args (UIelements will be converted to element reference)
     * debounce function is used by default, disable at boolean property $this->eventdebounce
     */
    public function setEvent( string $event, string $jsfunction, ...$args ): void
    {
        /* define script tag */
        $script = $this->ownerDocument->createElement( 'script',
            /* declare event assigned to this uielement */
            'document.getElementById(\'' . $this->_mainAttribute->getAttribute( 'id' ) . '\').addEventListener("' . htmlspecialchars( $event ) . '",' .
            /* pass event through debounce function if enabled */
            ( $this->eventdebounce ? 'debounce(' : '' ) .
                /* call sanitized function name with arguments */
                '() => ' . preg_replace( '/[^A-Za-z0-9 ]/', '', $jsfunction ) . '(' . self::_JsArgs( ...$args ) . ')' .
            ( $this->eventdebounce ? ')' : '' ) . ');' );
        /* apply required attributes to script tag */
        $script->setAttribute( 'language', 'javascript' ); $script->setAttribute( 'type', 'text/javascript' );
        /* append the script tag to parentNode */
        if   ( $this->parentNode ) { $this->parentNode->appendChild( $script ); }
        /* throw exceptions if this UIelement has not been added to DOM */
        else { throw new exception( 'cannot connect event to tags before being added to DOM' ); }
    }
}
