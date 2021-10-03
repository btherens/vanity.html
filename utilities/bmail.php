<?php

#$bmail = new bmail();
#$bmail->to( 'link@lycos.com' );
#$bmail->subject( 'it is dangerous to go alone. take this!' );
#$bmail->body( "the hero of time <3 ʕ•ᴥ•ʔ" );
#$bmail->send();

class bmail
{

    /* header text included in server request */
    private $_headers;

    /* the body of the email */
    private $_body;

    /* initial constructor */
    public function __construct()
    {
        /* map some default headers */
        $this->_headers = "MIME-Version: 1.0" . "\r\n";
        $this->_headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $this->_headers .= 'FROM: skyhold.app <auto@skyhold.app>' . "\r\n";
    }

    /* addressee */
    private $_to;
    public function to( string $value )
    {
        if ($value)
        {
            $this->_to = $value;
        }
    }

    /* the email's subject */
    private $_subject;
    public function subject( string $value )
    {
        if ($value)
        {
            $this->_subject = $value;
        }
    }

    public function body( string $value )
    {
        if ($value)
        {
            $this->_body = $value;
        }
    }

    public function send(): bool
    {
        return mail( $this->_to, $this->_subject, $this->_body, $this->_headers ) ? true : false;
    }

}
