<?php

class HomeController extends BaseController {

	/*
	|--------------------------------------------------------------------------
	| Default Home Controller
	|--------------------------------------------------------------------------
	|
	| You may wish to use controllers instead of, or in addition to, Closure
	| based routes. That's great! Here is an example controller method to
	| get you started. To route to this controller, just add the route:
	|
	|	Route::get('/', 'HomeController@showWelcome');
	|
	*/

	public function showWelcome()
	{
		return View::make('pages.dashboard');
	}
	
	public function dashboard() {
		if(Input::get('date_filter')) {
			$date_filter = Input::get('date_filter');
		} else {
			//set date filter default
			$date_filter = "this_month";
		}
		return View::make('pages.dashboard')->with('date_filter',$date_filter);
	}

}
