<?php

class BankController extends BaseController {

	protected $bank;

	public function __construct(Bank $bank)
	{
		$this->Bank = $bank;
	}
	
	//custom funcs
	
	public function index()
	{		
		return View::make('bank.index')->with('banks', $this->Bank->all());
	}
	
	public function show($id)
	{
		$bank = $this->Bank->whereId($id)->first();
	
		return View::make('bank.show')->withBank($bank);
	}
	
	public function create()
	{
		
		return View::make('bank.create');
	}
	
	public function edit($id)
	{
		$bank = $this->Bank->whereId($id)->first();
		
		return View::make('bank.edit')->withBank($bank);
	}
	
	public function update($id)
	{
		
		DB::table('bank')->where('id',$id)->update(
				array('bank_name' => Input::get('bank_name'), 
    			'nationality' => Input::get('bank_nationality'),
    			'website' => Input::get('bank_website'),
    			'address1' => Input::get('bank_address1'),
    			'address2' => Input::get('bank_address2'),
    			'address3' => Input::get('bank_address3'),
    			'address4' => Input::get('bank_address4'),
    			'postcode' => Input::get('bank_postcode'),
    			'phone1' => Input::get('bank_phone1'),
    			'phone2' => Input::get('bank_phone2'),
    			'email1' => Input::get('bank_email1'),
    			'email2' => Input::get('bank_email2'),
    			'swift_code' => Input::get('bank_swiftcode'),
    			'sort_code' => Input::get('bank_sortcode'),
    			'loc_relationship' => Input::get('bank_loc')
    			)
		);
		
		$bank = $this->Bank->whereId($id)->first();
		
		return View::make('bank.show')->withBank($bank);
	
	}
	
	
	public function store()
	{
		//get the input array
		$inputArray = Input::except('_token', '_method');
		
		//insert the bank whilst getting its newly created id
		$bankInsertId = DB::table('bank')->insertGetId(
    		array('bank_name' => Input::get('bank_name'), 
    			'nationality' => Input::get('bank_nationality'),
    			'website' => Input::get('bank_website'),
    			'address1' => Input::get('bank_address1'),
    			'address2' => Input::get('bank_address2'),
    			'address3' => Input::get('bank_address3'),
    			'address4' => Input::get('bank_address4'),
    			'postcode' => Input::get('bank_postcode'),
    			'phone1' => Input::get('bank_phone1'),
    			'phone2' => Input::get('bank_phone2'),
    			'email1' => Input::get('bank_email1'),
    			'email2' => Input::get('bank_email2'),
    			'swift_code' => Input::get('bank_swiftcode'),
    			'sort_code' => Input::get('bank_sortcode'),
    			'loc_relationship' => Input::get('bank_loc')
    			)
		);
		
		//create a directory for this bank
		$dirCreate = File::makeDirectory(public_path().'/data/bank/'.$bankInsertId, 0775, true);
		
		return Redirect::to('bank/'.$bankInsertId);
	}
	
	
	public function getTypeOptions($fieldname) {
			$casetypes = DB::table('options')->where('fieldname', '=', $fieldname)->get();
			return Response::json($casetypes);
		}

	}

?>