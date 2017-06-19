<?php

class ClientController extends BaseController {

	protected $client;

	public function __construct(Client $client)
	{
		$this->Client = $client;
	}
	
	//custom funcs
	
	public function fetchClient() 
	{
		$clientId = $_POST["clientid"];
		return json_encode(DB::table('client')->where('id', $clientId)->first());
	}

	public function index()
	{		
		return View::make('client.index')->with('clients', $this->Client->all());
	}
	
	
	
	public function show($id)
	{
		$client = $this->Client->whereId($id)->first();
	
		return View::make('client.show')->withClient($client);
	}
	
	public function create()
	{
		return View::make('client.create');
	}
	
	public function addproperty()
	{
		$values_property_sectors = ValuesPropertySectors::orderBy('value')->get();
		$values_property_types = ValuesPropertyTypes::orderBy('value')->get();
		$values_property_features = ValuesPropertyFeatures::orderBy('value')->get();
		$portals = Portals::orderBy('portal_name')->get();
		return View::make('property.addproperty')->with('values_property_sectors',$values_property_sectors)->with('values_property_features',$values_property_features)->with('values_property_types',$values_property_types)->with('portals',$portals);
	}
	
	public function addtenancy($id)
	{
		$property = $this->Property->whereId($id)->first();
		return View::make('property.addtenancy')->withProperty($property);
	}
	
	public function storetenancy($id)
	{
		$property = $this->Property->whereId($id)->first();
		
		$inputs = Input::except('_token', '_method');
		$smsconsent = "";
		$references = "";
		
		if($inputs["existing_tenant"] == "") {
			//NEW TENANT, ADD NEW TENANT TO TENANT TABLE NOW
		
			$tenantoptions = $inputs["tenantoptions"];
		
		
			if(is_array($tenantoptions))
				{
				   foreach($tenantoptions as $option) {
						if($option == "sms_consent") {
							$smsconsent = "yes";
						}
						else if($option == "references_received") {
							$references = "yes";
						}
				   }
				}
		
			$tenantid = DB::table('tenant')->insertGetId(
							array('property_id' => $id, 
							'title' => $inputs["title"],
							'firstname' => $inputs["firstname"],
							'lastname' => $inputs["lastname"],
							'address1' => $inputs["address1"],
							'address2' => $inputs["address2"],
							'address3' => $inputs["address3"],
							'address4' => $inputs["address4"],
							'postcode' => $inputs["postcode"],
							'phone1' => $inputs["phone1"],
							'phone2' => $inputs["phone2"],
							'email1' => $inputs["email1"],
							'email2' => $inputs["email2"],
							'sms_consent' => $smsconsent,
							'references_received' => $references,
							'nationality' => $inputs["nationality"],
							'status' => 'active')
			);
			
			DB::table('property_tenancy')->insert(
							array('property_id' => $id, 
							'tenant_id' => $tenantid,
							'start_date' => $inputs["start_date"],
							'end_date' => $inputs["end_date"],
							'duration' => '',
							'rent_amount' => $inputs["rent_amount"],
							'deposit_amount' => $inputs["deposit_amount"],
							'status' => 'active')
			);
			
		}else {
			//Existing Tenant, so we only need to add to the tenancy table boom
			DB::table('property_tenancy')->insert(
							array('property_id' => $id, 
							'tenant_id' => $inputs["existing_tenant"],
							'start_date' => $inputs["start_date"],
							'end_date' => $inputs["end_date"],
							'duration' => '',
							'rent_amount' => $inputs["rent_amount"],
							'deposit_amount' => $inputs["despoit_amount"],
							'status' => 'active')
			);
		}
		
		
		return View::make('property.show')->withProperty($property);
	}
	
	public function edit($id)
	{
		$client = $this->Client->whereId($id)->first();
		
		return View::make('client.edit')->withClient($client);
	}
	
	public function update($id)
	{
		
		DB::table('client')->where('id',$id)->update(
				array('client_name' => Input::get('client_name'),
    			'client_type' => Input::get('client_type'), 
    			'nationality' => Input::get('client_nationality'),
    			'company_number' => Input::get('client_number'),
    			'website' => Input::get('client_website'),
    			'address1' => Input::get('client_address1'),
    			'address2' => Input::get('client_address2'),
    			'address3' => Input::get('client_address3'),
    			'address4' => Input::get('client_address4'),
    			'postcode' => Input::get('client_postcode'),
    			'phone1' => Input::get('client_phone1'),
    			'phone2' => Input::get('client_phone2'),
    			'email1' => Input::get('client_email1'),
    			'email2' => Input::get('client_email2'),
    			'status' => Input::get('client_status')
    			)
    	);
		
		$client = $this->Client->whereId($id)->first();
		
		return View::make('client.show')->withClient($client);
	
	}
	
	public function updatebank($id)
	{
		$client = $this->Client->whereId($id)->first();
		
		return View::make('client.updatebank')->withClient($client);
	
	}
	
	public function bankstore()
	{		
		//set the clients bank
		DB::table('client')->where('id', Input::get('client_id'))->update(['bank_id' => Input::get('client_bank')]);
		
		//delete any existing bank details
		DB::table('client_bank_details')->where('client_id',Input::get('client_id'))->where('bank_id',Input::get('client_bank'))->delete();
		
		$clientBankInsertId = DB::table('client_bank_details')->insertGetId(
			array('client_id' => Input::get('client_id'),
				'bank_id' => Input::get('client_bank'),
				'account_name' => Input::get('account_name'),
				'iban_number' => Input::get('iban_number'),
				'account_number' => Input::get('account_number')
				)
		);
		
		$client = $this->Client->whereId(Input::get('client_id'))->first();
		
		return View::make('client.show')->withClient($client);
	}
	

	
	public function store()
	{
		$inputArray = Input::except('_token', '_method');
		
		
		$clientInsertId = DB::table('client')->insertGetId(
    		array('client_name' => Input::get('client_name'),
    			'client_type' => Input::get('client_type'), 
    			'nationality' => Input::get('client_nationality'),
    			'company_number' => Input::get('client_number'),
    			'website' => Input::get('client_website'),
    			'address1' => Input::get('client_address1'),
    			'address2' => Input::get('client_address2'),
    			'address3' => Input::get('client_address3'),
    			'address4' => Input::get('client_address4'),
    			'postcode' => Input::get('client_postcode'),
    			'phone1' => Input::get('client_phone1'),
    			'phone2' => Input::get('client_phone2'),
    			'email1' => Input::get('client_email1'),
    			'email2' => Input::get('client_email2'),
    			'status' => Input::get('client_status')
    			)
		);
		
		$dirCreate = File::makeDirectory(public_path().'/data/client/'.$clientInsertId, 0775, true);
		
		return Redirect::to('client/'.$clientInsertId);
	}
	
	
	public function getTypeOptions($fieldname) {
			$casetypes = DB::table('options')->where('fieldname', '=', $fieldname)->get();
			return Response::json($casetypes);
		}

	}

?>