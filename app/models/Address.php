<?php

class Address extends Eloquent
{
    /** Set validation required for a case, i.e whats necessary on creation and other validation */
    public static $rules = array();

    /** placeholder var for validation messages i reckon */
    public $messages;

    protected $table = 'property_address';

    public function property() {
        //FKey to property is property_id
        return $this->belongsTo('Property','property_id');

    }

    public function isValid($data)
    {
        $validation = Validator::make($data, static::$rules);

        if ($validation->passes()) return true;

        //else
        $this->messages = $validation->messages();
        return false;

    }

    public function getFieldnames() {

    }
}
