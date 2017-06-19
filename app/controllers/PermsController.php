<?php

class PermsController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		//
	}


	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		//
	}


	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		//
	}


	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		//
	}


	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		//
	}


	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		//
	}


	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}
	/**
	 * test perm
	 *
	 * @return Response
	 */

	public function test() {
	
		$user = User::where('username','=','charles')->first();
		
		echo "<br>Owner : " . $user->hasRole("Owner");    // false
		echo "<br>Admin : " . $user->hasRole("Admin");    // true
		echo "<br>Posts : " . $user->can("manage_posts"); // true
		echo "<br>Users : " . $user->can("manage_users"); // false	

	}

	public function prime() {
		$owner = new Role;
		$owner->name = 'Owner';
		$owner->save();
		
		$admin = new Role;
		$admin->name = 'Admin';
		$admin->save();

		$managePosts = new Permission;
		$managePosts->name = 'manage_posts';
		$managePosts->display_name = 'Manage Posts';
		$managePosts->save();

		$manageUsers = new Permission;
		$manageUsers->name = 'manage_users';
		$manageUsers->display_name = 'Manage Users';
		$manageUsers->save();

		$owner->perms()->sync(array($managePosts->id,$manageUsers->id));
		$admin->perms()->sync(array($managePosts->id));

		$user = User::where('username','=','charles')->first();

		/* role attach alias */
		$user->attachRole( $admin );
	}

}
