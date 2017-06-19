<?php

class ContactController extends BaseController {

	protected $contact;

	public function __construct(Contact $contact)
	{
		$this->Contact = $contact;
	}
	
	//custom funcs
	
	public function index()
	{		
		return View::make('contact.index')->with('contacts', $this->Contact->all());
	}
	
	public function show($id)
	{
		$contact = $this->Contact->whereId($id)->first();
	
		return View::make('contact.show')->withContact($contact);
	}
	
	public function create($type,$id)
	{
		if($type == "client") {		
			$parent = DB::table('client')->where('id', '=', $id)->first();
		} else if($type == "supplier") {
			$parent = DB::table('supplier')->where('id', '=', $id)->first();
		} else if($type == "bank") {
			$parent = DB::table('bank')->where('id', '=', $id)->first();
		}
		print_r($type);
		
		return View::make('contact.create')->with('parent',$parent)->with('type',$type);
	}
	
	public function edit($id)
	{
		$contact = $this->Contact->whereId($id)->first();
		
		return View::make('contact.edit')->withContact($contact);
	}
	
	public function update($id)
	{
		
		DB::table('contact')->where('id',$id)->update(
				array('position' => Input::get('contact_position'),
    			'title' => Input::get('contact_title'),
    			'firstname' => Input::get('contact_firstname'),
    			'lastname' => Input::get('contact_lastname'),
    			'nationality' => Input::get('contact_nationality'),
    			'address1' => Input::get('contact_address1'),
    			'address2' => Input::get('contact_address2'),
    			'address3' => Input::get('contact_address3'),
    			'address4' => Input::get('contact_address4'),
    			'postcode' => Input::get('contact_postcode'),
    			'phone1' => Input::get('contact_phone1'),
    			'phone2' => Input::get('contact_phone2'),
    			'email1' => Input::get('contact_email1'),
    			'email2' => Input::get('contact_email2'),
    			'whatsapp' => Input::get('contact_whatsapp'),
    			'skype' => Input::get('contact_skype')
    			)
		);
		
		$contact = $this->Contact->whereId($id)->first();
		
		if (Input::hasFile('contact_image')) {
			$file            = Input::file('contact_image');
			$destinationPath = public_path().'/data/'.$contact->contact_type.'/'.$contact->type_id.'/contact/'.$id.'/';
			$filename        = str_random(6).'.'.$file->getClientOriginalExtension();
			$uploadSuccess   = $file->move($destinationPath, $filename);
			DB::table('contact')->where('id', $id)->update(['image' => $filename]);
		}
		
			
		
		$contact = $this->Contact->whereId($id)->first();	
		
		return View::make('contact.show')->withContact($contact);
	
	}
	
	
	public function store()
	{
		//get the input array
		$inputArray = Input::except('_token', '_method');
		
		//insert the contact whilst getting its newly created id
		$contactInsertId = DB::table('contact')->insertGetId(
    		array('contact_type' => Input::get('type'),
    			'type_id' => Input::get('type_id'), 
    			'position' => Input::get('contact_position'),
    			'title' => Input::get('contact_title'),
    			'firstname' => Input::get('contact_firstname'),
    			'lastname' => Input::get('contact_lastname'),
    			'nationality' => Input::get('contact_nationality'),
    			'address1' => Input::get('contact_address1'),
    			'address2' => Input::get('contact_address2'),
    			'address3' => Input::get('contact_address3'),
    			'address4' => Input::get('contact_address4'),
    			'postcode' => Input::get('contact_postcode'),
    			'phone1' => Input::get('contact_phone1'),
    			'phone2' => Input::get('contact_phone2'),
    			'email1' => Input::get('contact_email1'),
    			'email2' => Input::get('contact_email2'),
    			'whatsapp' => Input::get('contact_whatsapp'),
    			'skype' => Input::get('contact_skype')
    			)
		);
		
		//create a directory for this contact
		$dirCreate = File::makeDirectory(public_path().'/data/'.Input::get('type').'/'.Input::get('type_id').'/contact/'.$contactInsertId, 0775, true);
		//declare empty filename for update method in case no image
		$filename = "";
		//check dircreate success
		if($dirCreate) {
				//check for file
			   if (Input::hasFile('contact_image')) {
					$file            = Input::file('contact_image');
					$destinationPath = public_path().'/data/'.Input::get('type').'/'.Input::get('type_id').'/contact/'.$contactInsertId.'/';
					$filename        = str_random(6).'.'.$file->getClientOriginalExtension();
					$uploadSuccess   = $file->move($destinationPath, $filename);
				}
		}
		//update table with image filename
		DB::table('contact')->where('id', $contactInsertId)->update(['image' => $filename]);
		
		return Redirect::to('contact/'.$contactInsertId);
	}
	
	
	public function getTypeOptions($fieldname) {
			$casetypes = DB::table('options')->where('fieldname', '=', $fieldname)->get();
			return Response::json($casetypes);
		}

	}

?>