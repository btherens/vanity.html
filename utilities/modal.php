<?php

class Modal
{
    /*  */
    private static $_set;
    private static $_DOMbuilder;

    private static $_titleString;
    private static $_promptString;
    private static $_trueString;
    private static $_falseString;

    public static $_html;
    public static $_trueBtn;
    public static $_falseBtn;

    public static function init(string $group, string $form = null): UIelement
    {
        self::reset();

        /* build objects and content */
        self::$_DOMbuilder = new UInterface($group);
        self::$_html = self::$_DOMbuilder->_setForm($form);

        return self::$_html;
    }

    private static function reset()
    {
        /* reset object */
        self::$_set = false;
        self::$_DOMbuilder = null;
        self::$_html = null;
        self::$_trueBtn = null;
        self::$_falseBtn = null;

        /* initiate defaults/overrides */
        self::$_titleString = null;
        self::$_promptString = 'are you sure?';
        self::$_trueString = 'confirm';
        self::$_falseString = 'cancel';
    }

    public static function build( string $title = null, string $prompt = null, string $trueBtnText = null, string $falseBtnText = null )
    {
        self::$_titleString = $title ? $title : self::$_titleString;
        self::$_promptString = $prompt ? $prompt : self::$_promptString;
        self::$_trueString = $trueBtnText ? $trueBtnText : self::$_trueString;
        self::$_falseString = $falseBtnText ? $falseBtnText : self::$_falseString;

        if ( self::$_titleString ) {
            self::$_html->appendChild( self::$_DOMbuilder->createElement( 'h2', self::$_titleString ) );
        }
        self::$_html->appendChild( self::$_DOMbuilder->createElement( 'div', self::$_promptString ) );
        self::$_html->appendChild( self::$_DOMbuilder->createElement( 'br' ) );

        self::$_trueBtn = self::$_DOMbuilder->createButton( 'true', null, self::$_trueString, true );
        self::$_falseBtn = self::$_DOMbuilder->createButton( 'false', null, self::$_falseString, false );
        self::$_falseBtn->setAttribute( 'onclick', "modal_toggle(false);return false;" );

        self::$_html->appendChild( self::$_trueBtn );
        self::$_html->appendChild( self::$_falseBtn );

        self::$_set = true;
    }

    public static function setString(): string
    {
        return self::$_set ? self::$_html->saveHTML() : '';
    }

    public static function exists(): bool
    {
        return self::$_set ? true : false;
    }
}
