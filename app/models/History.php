<?php

use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableInterface;

class History extends Eloquent implements UserInterface, RemindableInterface {

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
	protected $table = 'history';

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
		//protected $hidden = array('password');

	/**
	 * Get the unique identifier for the user.
	 *
	 * @return mixed
	 */
	public function getAuthIdentifier()
	{
		return $this->getKey();
	}

	/**
	 * Get the password for the user.
	 *
	 * @return string
	 */
	public function getAuthPassword()
	{
		return $this->password;
	}

	/**
	 * Get the token value for the "remember me" session.
	 *
	 * @return string
	 */
	public function getRememberToken()
	{
		return $this->remember_token;
	}

	/**
	 * Set the token value for the "remember me" session.
	 *
	 * @param  string  $value
	 * @return void
	 */
	public function setRememberToken($value)
	{
		$this->remember_token = $value;
	}

	/**
	 * Get the column name for the "remember me" token.
	 *
	 * @return string
	 */
	public function getRememberTokenName()
	{
		return 'remember_token';
	}

	/**
	 * Get the e-mail address where password reminders are sent.
	 *
	 * @return string
	 */
	public function getReminderEmail()
	{
		return $this->email;
	}
	
	public function isValid($data)
	{
		$validation = Validator::make($data, static::$rules);
		
		if($validation->passes()) return true;
		
		//else
		$this->messages = $validation->messages();
		return false;
		
	}
	

}
