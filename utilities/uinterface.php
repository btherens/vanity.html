<?php

class UInterface extends DomDocument
{

    private $_group;

    /* read access to _group */
    private $_readonly = array('_group');
    public function __get(string $var)
    {
        return (in_array( $var, $this->_readonly ) && isset( $this->$var ) ) ? $this->$var : null;
    }

    private $_form;

    private $_table;

    /* property determines if javascript is used (default on) */
    private $_isactive;

    /* initial setup, set formatting preferences */
    public function __construct( string $group )
    {
        parent::__construct( '1.0', 'utf-8' );
        $this->formatOutput = true;
        $this->_group = $group;

        $this->_isactive = true;
    }

    /* magic method directed through saveHTML() */
    public function __toString()
    {
        return $this->saveHTML();
    }

    /* pass saveHTML to parent through html_entity_decode() -- currently disabled */
    public function saveHTML( $node = null )
    {
        return parent::saveHTML( $node );
        //return html_entity_decode(parent::saveHTML($node));
    }

    /* create form, save to _form property and add to object DOM */
    public function _setForm( string $name = null, string $action = null ): UIelement
    {
        $frm = $this->createElement('form');

        $this->_form = &$frm;

        $frm->setAttribute( 'name', $name ? $name : $this->_group );

        $frm->setAttribute( 'id', $this->_group . '-frm' );
        $frm->setAttribute( 'action', isset($action) ? $action : '' );
        $frm->setAttribute( 'method', 'post' );
        $this->appendChild( $frm );
        return $frm;
    }

    public function _createTable( array $headers, bool $invisiblecolumn = false ): UIelement
    {
        $tbl = $this->createElement( 'table' );
        $this->_table = &$tbl;

        /* set class style tags if invisible column needed */
        if ($invisiblecolumn)
        {
            $tbl->_setClass( 'invisible-column' );
        }

        /* add table headers */
        $tbl->_setTableHeader( $headers );

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
        $btn->_setSubmit( $submit );
        $btn->setAttribute( 'value', $key );
        $btn->appendChild( $this->createElement( 'span', $label ) );

        return $btn;
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
                if ( get_class( $valueObj ) == 'ValidVar' )
                {
                    /* if value exists, simply pass on to inherit type */
                    if ( $valueObj->value )
                    {
                        $value = $valueObj->value;
                    }
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
         * switch statement handles by type
         */
        /* detect type if we haven't already */
        $varTypestr = $varTypestr ?: gettype( $value );
        if ($varTypestr == 'object')
        {
            if (get_class($value) == 'DateTime')
            {
                $varTypestr = 'DateTime';
            }};
        switch ($varTypestr)
        {
            /* special handling, boolean value types will appear as checkboxes */
            case 'boolean':
                $container = $this->_createComplexElement('input', $name, $label, 1, $regexpattern, $class, true);
                if ($value)
                {
                    $container->setMainAttribute('checked');
                };
                break;
            case 'DateTime':
                /* cast date as string in format compatible with datetime picker */
                $value = $value ? $value->format('Y-m-d\TH:i:s') : null;
                $regexpattern = '^(\d{4}-\d{2}-\d{2})T(0[0-9]|1[0-9]|2[0-3]):([0-5][0-9])(:[0-5][0-9])?$';
            default:
                $container = $this->_createComplexElement('input', $name, $label, $value, $regexpattern, $class);
                break;
        }

        /* STEP 3
         * postprocess
         * assign type to input attribute, along with any other
         * post-processing a type requires
         */
        switch ($varTypestr)
        {
            /* special handling, boolean value types will appear as checkboxes */
            case 'boolean':
                $container->setMainAttribute('type', 'checkbox');
                $container->setMainAttribute('onchange', "processActive(this);");
                break;
            case 'DateTime':
                $container->setMainAttribute('type', 'datetime-local');
                $container->setMainAttribute('onchange', "processActive(this);");
                break;
            default:
                $container->setMainAttribute('type', 'text');
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
        if (!empty($valueObj))
        {
            if (gettype($valueObj) == 'object')
            {
                if (get_class($valueObj) == 'ValidVar')
                {
                    $value = $valueObj->value;
                    $regexpattern = $valueObj->pattern;
                }
                else { $value = $valueObj; }
            }
            else { $value = $valueObj; }
        }

        $container = $this->_createComplexElement('textarea', $name, $label, $value, $regexpattern, $class);

        /* customized attributes for input type */
        if ($value)
        {
            $container->lastChild->nodeValue = htmlspecialchars($value);
        }

        return $container;
    }

    /* function to create and pass a label/field div (not added to dom) */
    private function _createComplexElement(
        string $tag,
        string $name,
        string $label = null,
        string $value = null,
        string $regexpattern = null,
        string $class = null,
        /* use $reverse = true to reverse dom order of label/input (for checkboxes) */
        bool $reverse = false
    ): UIelement
    {
        $group = &$this->_group;
        $id = $group . '-' . $tag . '-' . $name;

        /* create elements */
        $container = $this->createElement('div');
        $element = $this->createElement($tag);

        /* save main element reference to container */
        $container->setMain($element);

        /* set attributes */
        $element->setAttribute('id', $id);
        $element->setAttribute('name', $group . '-' . $name);

        /* if a regex pattern is given, assign to the pattern attribute */
        if ($regexpattern)
        {
            $element->setAttribute('pattern', $regexpattern);
        }

        /* add js events and active css */
        if ($this->_isactive)
        {
            $element->_setClass('script-active');
            $element->setAttribute('onkeyup', "processActive(this);");
            //$element->setAttribute('origValue', $value);
        }
        /* set value attribute, if provided */
        $element->setAttribute('value', $value);

        /* set class, if provided */
        if ($class)
        {
            $element->_setClass($class);
        }

        /* if a label is defined, create the label element and link */
        if ($label)
        {
            /* label set to placeholder text */
            $element->setAttribute('placeholder', $label);
            /* label element is created! */
            $lbl = $this->createElement('label', $label);
            $lbl->setAttribute('for', $id);

            /* label is assigned after for checkboxes, before for others (inputs) */
            if ($reverse)
            {
                $container->appendChild($element);
                $container->appendChild($lbl);
            }
            else
            {
                $container->appendChild($lbl);
                $container->appendChild($element);
            }
        }
        else
        {
            $container->appendChild($element);
        }

        return $container;
    }

    /* overloading createElement to use the UIelement definition below */
    public function createElement($name, $value = null)
    {
        /* new sub-class object */
        $orphan = new UIelement($name, $value);
        /* by passing things through docFragment, the ownerDocument property is maintained */
        $docFragment = $this->createDocumentFragment();
        $docFragment->appendChild($orphan);
        $ret = $docFragment->removeChild($orphan);

        /* return the element here, it will be able to link to ownerdocument */
        return $ret;
    }
}

/* extending DomElement because the og class is too difficult to save as a fragment */
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
    private $_readonly = array('_mainAttribute');
    public function __get(string $var)
    {
        return (in_array($var, $this->_readonly) && isset($this->$var)) ? $this->$var : null;
    }

    /* pass details to parent constructor */
    public function __construct($name, $value = '', $namespaceURI = null)
    {
        parent::__construct($name, htmlspecialchars($value), $namespaceURI);

        /* by default, _mainAttribute references the UIelement */
        $this->setMain($this);
    }

    /* magic method directed through saveHTML() */
    public function __toString()
    {
        return $this->saveHTML();
    }

    /* saveHTML method accessible from the Element class! uses ownerDocument for rendering */
    public function saveHTML()
    {
        return $this->ownerDocument->saveHTML($this);
    }

    /* non-destructive method to set a class tag (appending if class exists) */
    public function _setClass(string $class)
    {
        $currentclass = $this->getAttribute('class');
        $this->setAttribute('class', !empty($currentclass) ? $currentclass . " $class" : "$class");
    }

    /* non-destructive method to remove a class tag */
    public function _removeClass(string $class)
    {
        $currentclass = $this->getAttribute('class');
        $newclass = str_replace($class, '', $currentclass);

        /* if new class is blank, remove class attribute entirely */
        if (ctype_space($newclass) || $newclass == '')
        {
            $this->removeAttribute('class');
        }
        /* otherwise, set new class attribute */

        else
        {
            $this->setAttribute('class', $newclass);
        }
    }

    /* a method to run a given key value against the class' key value, will perform alterations upon match */
    public function _setIfMatch($setkey, string $newname = null)
    {
        /* if there is an setkey */
        if ($setkey)
        {
            /* if the given setkey matches the button's value attribute */
            if ($setkey == $this->getAttribute('value'))
            {
                $this->_setState();
                $this->setAttribute('name', $this->ownerDocument->_group . '-' . $newname);
            }
        }
    }

    /* set the element's class to btn-set (determines if a button has been pressed) */
    public function _setState()
    {
        $this->_setClass('btn-set');
    }

    /* set submit status, accepts bool */
    public function _setSubmit(bool $dosubmit = true)
    {
        if ($this->nodeName == 'button')
        {
            /* add submit attribute if submit bool passed */
            if ($dosubmit)
            {
                $this->removeAttribute('formnovalidate');
                $this->_removeClass('alt1');
            }
            else
            {
                $this->setAttribute('formnovalidate', 'formnovalidate');
                $this->_setClass('alt1');
            }
        }
    }

    /* set the table header */
    public function _setTableHeader(array $headers)
    {
        if (!isset($this->_rows) && !isset($this->_columns))
        {
            $this->_settablerowGeneric($headers, 'th');
            /* save table dimension properties */
            $this->_rows = 1;
            $this->_columns = sizeof($headers);
        }
        else
        {
            throw new Exception('table headers exist');
        }
    }

    /* set a row to a table UIelement */
    public function _setTableRow(array $cells)
    {
        if (isset($this->_rows) && isset($this->_columns))
        {
            if ($this->_columns == sizeof($cells))
            {
                $this->_settablerowGeneric($cells, 'td');
                /* save table dimension properties */
                $this->_rows++;
            }
            else
            {
                throw new Exception('table dimension mismatch');
            }
        }
        else
        {
            throw new Exception('table not set!');
        }
    }

    /* generic function to add a table row to the UIelement */
    private function _settablerowGeneric(array $headers, string $tag = 'td')
    {
        /* create table row and add header cells */
        $tablerow = $this->ownerDocument->createElement('tr');
        foreach ($headers as $header)
        {
            if (gettype($header) == 'object')
            {
                if (get_class($header) == 'UIelement')
                {
                    $elmt = $this->ownerDocument->createElement($tag);
                    $elmt->appendChild($header);
                    $tablerow->appendChild($elmt);
                }
                else
                {
                    throw new Exception("unexpected class type in headers. class: " . get_class($header));
                }
            }
            else
            {
                $tablerow->appendChild($this->ownerDocument->createElement($tag, $header));
            }
        }
        /* add the table row to the table */
        $this->appendChild($tablerow);
    }

    /* create reference at _mainAttribute property to the specified mainattribute */
    public function setMain(&$arg1)
    {
        $this->_mainAttribute = &$arg1;
    }

    public function setMainAttribute($arg1, $arg2 = null)
    {
        if (is_null($arg2))
        {
            $arg2 = $arg1;
        };
        ($this->_mainAttribute) ? $this->_mainAttribute->setAttribute($arg1, $arg2) : $this->setAttribute($arg1, $arg2);
    }

    /* configure an event attribute to pass this mainattribute's value
     * to another UIelement's mainattribute value
     */
    public function setIdValueEvent(UIelement $elmt)
    {
        $id = $elmt->_mainAttribute->getAttribute('id');
        if ($id)
        {
            $this->setMainAttribute('onfocusout', "setIdValue(this,'{$id}');");
        }
    }
}
