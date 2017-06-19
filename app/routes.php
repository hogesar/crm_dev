<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

Route::get('/', 'HomeController@dashboard');
Route::post('dashboard/filter', 'HomeController@dashboard');

Route::resource('product', 'ProductController');
Route::resource('client', 'ClientController');
Route::resource('contact', 'ContactController');
Route::resource('bank', 'BankController');
Route::resource('history', 'HistoryController');
Route::resource('enquiry', 'EnquiryController');
Route::resource('supplier', 'SupplierController');
Route::resource('deal', 'DealController');
Route::resource('accounts', 'AccountsController');

//custom accounts
Route::get('accounts/create/{type?}/{id?}', 'AccountsController@create');
Route::get('accounts/create/in/{type?}/{id?}', 'AccountsController@create_in');
Route::get('accounts/create/out/{type?}/{id?}', 'AccountsController@create_out');
Route::get('accounts/autoAccountsHistory/{accounts_id}/{type}/{type_id}/{file?}', 'HistoryController@autoAccountsHistory');
//custom client
Route::get('client/updatebank/{id}', 'ClientController@updatebank');
Route::post('client/bankstore', 'ClientController@bankstore');
//custom contact
Route::get('contact/create/{type}/{id}', 'ContactController@create');
//custom history
Route::get('history/{history_type}/{parent_type}/{parent_id}/{child_type?}/{child_id?}', 'HistoryController@create');
Route::get('history/actiondiary/{diary_id}', 'HistoryController@actionDiary');
//custom enquiry
Route::get('enquiry/create/{id}/{contactid?}', 'EnquiryController@create');
Route::get('enquiry/{date_filter?}/{type?}', 'EnquiryController@index');
//custom deal
Route::get('deal/create/{id}/{contactid?}', 'DealController@create');
Route::get('deal/{id}/sales_confirmation/create', 'DealController@create_sales_confirmation');
Route::post('deal/{id}/sales_confirmation/store', 'DealController@store_sales_confirmation');
Route::get('deal/{id}/purchase_order/create', 'DealController@create_purchase_order');
Route::post('deal/{id}/purchase_order/store', 'DealController@store_purchase_order');
Route::get('deal/{id}/proforma_invoice/create', 'DealController@create_proforma_invoice');
Route::post('deal/{id}/proforma_invoice/store', 'DealController@store_proforma_invoice');
Route::get('deal/{id}/invoice/create', 'DealController@create_invoice');
Route::post('deal/{id}/invoice/store', 'DealController@store_invoice');
Route::get('deal/autoDealHistory/{type}/{typeid}/{filename?}', 'HistoryController@autoDealHistory');
Route::get('deal/{date_filter?}/{type?}', 'DealController@index');
//custom pdf
Route::get('pdf/create/{type}/{id}', 'PdfGenController@create');

// Confide routes
Route::get('users/create', 'UsersController@create');
Route::post('users', 'UsersController@store');
Route::get('users/login', 'UsersController@login');
Route::post('users/login', 'UsersController@doLogin');
Route::get('users/confirm/{code}', 'UsersController@confirm');
Route::get('users/forgot_password', 'UsersController@forgotPassword');
Route::post('users/forgot_password', 'UsersController@doForgotPassword');
Route::get('users/reset_password/{token}', 'UsersController@resetPassword');
Route::post('users/reset_password', 'UsersController@doResetPassword');
Route::get('users/logout', 'UsersController@logout');
Route::get('perms/test','PermsController@test');
Route::get('perms/prime','PermsController@prime');
Route::get('scraper/{id}','ScraperController@show');

//report routes
Route::resource('reports','ReportController');
