<?php

class Enquiry extends Eloquent {

	/** set TimeStamps to false, this will stop laravel expecting dateadded/updated fields */
	//public $timestamps = true;
	
	/** Set validation required for a case, i.e whats necessary on creation and other validation */
	public static $rules = array(
		
		);
		
	/** placeholder var for validation messages i reckon */
	public $messages;
	
	
	/** Can use either fillable or guarded to configure which fields can be mapped in insert qs post */
	//protected $fillable = ['added_date', 'type', 'status'];

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'enquiry';


	public function isValid($data)
	{
		$validation = Validator::make($data, static::$rules);
		
		if($validation->passes()) return true;
		
		//else
		$this->messages = $validation->messages();
		return false;
		
	}

}
