<?php

/*
|--------------------------------------------------------------------------
| Application & Route Filters
|--------------------------------------------------------------------------
|
| Below you will find the "before" and "after" events for the application
| which may be used to do any work before or after a request into your
| application. Here you may also register your custom route filters.
|
*/

App::before(function($request)
{
		
		if(isset($_GET["system"])) {

			//manage session things here..
			Session::put('system', $_GET["system"]);
			Session::put('colour1', $_GET["colour1"]);
			Session::put('colour2', $_GET["colour2"]);
			Session::put('colour3', $_GET["colour3"]);
			Session::put('colour4', $_GET["colour4"]);
			Session::put('user', $_GET["user"]);
			Session::put('email', $_GET["emailaddress"]);
			Session::put('dbuser', $_GET["dbuser"]);
			Session::put('dbpassword', $_GET["dbpassword"]);
			Session::put('intranetdomain', $_GET["intranetdomain"]);
			Session::put('emaildomain', $_GET["emaildomain"]);
			
			//set any authorisation session vars
			Session::put('view_bank_details','0');
			
			//fetch auth from users
			$userAuth = DB::table('users')->where('username',$_GET["user"])->first();
			
			if(is_object($userAuth)) {
				//update any authorisation to allow access
				Session::put('view_bank_details',$userAuth->view_bank_details);
			}
			
			
			Session::save();
		}
});


App::after(function($request, $response)
{
	//
});

/*
|--------------------------------------------------------------------------
| Authentication Filters
|--------------------------------------------------------------------------
|
| The following filters are used to verify that the user of the current
| session is logged into this application. The "basic" filter easily
| integrates HTTP Basic authentication for quick, simple checking.
|
*/

Route::filter('auth', function()
{
	if (Auth::guest())
	{
		if (Request::ajax())
		{
			return Response::make('Unauthorized', 401);
		}
		else
		{
			return Redirect::guest('login');
		}
	}
});


Route::filter('auth.basic', function()
{
	return Auth::basic();
});

/*
|--------------------------------------------------------------------------
| Guest Filter
|--------------------------------------------------------------------------
|
| The "guest" filter is the counterpart of the authentication filters as
| it simply checks that the current user is not logged in. A redirect
| response will be issued if they are, which you may freely change.
|
*/

Route::filter('guest', function()
{
	if (Auth::check()) return Redirect::to('/');
});

/*
|--------------------------------------------------------------------------
| CSRF Protection Filter
|--------------------------------------------------------------------------
|
| The CSRF filter is responsible for protecting your application against
| cross-site request forgery attacks. If this special token in a user
| session does not match the one given in this request, we'll bail.
|
*/

Route::filter('csrf', function()
{
	if (Session::token() !== Input::get('_token'))
	{
		throw new Illuminate\Session\TokenMismatchException;
	}
});
