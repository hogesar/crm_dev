<?php

class PdfGen extends Eloquent  {

 	/** set TimeStamps to false, this will stop laravel expecting dateadded/updated fields */
    public $timestamps = true;

    /** Set validation required for a case, i.e whats necessary on creation and other validation */
    public static $rules = array(

    );

    /** placeholder var for validation messages i reckon */
    public $messages;
    
    protected $table = 'pdf_log';


}