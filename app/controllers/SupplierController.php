<?php

class SupplierController extends BaseController {

	protected $supplier;

	public function __construct(Supplier $supplier)
	{
		$this->Supplier = $supplier;
	}
	
	//custom funcs
	

	public function index()
	{		
		return View::make('supplier.index')->with('suppliers', $this->Supplier->all());
	}
	
	public function show($id)
	{
		$supplier = $this->Supplier->whereId($id)->first();
	
		return View::make('supplier.show')->withSupplier($supplier);
	}
	
	public function create()
	{
		return View::make('supplier.create');
	}

	public function edit($id)
	{
		$supplier = $this->Supplier->whereId($id)->first();
		
		return View::make('supplier.edit')->withSupplier($supplier);
	}
	
	public function update($id)
	{
		
		DB::table('supplier')->where('id',$id)->update(
				array('supplier_name' => Input::get('supplier_name'),
    			'supplier_type' => Input::get('supplier_type'), 
    			'nationality' => Input::get('supplier_nationality'),
    			'company_number' => Input::get('supplier_number'),
    			'website' => Input::get('supplier_website'),
    			'address1' => Input::get('supplier_address1'),
    			'address2' => Input::get('supplier_address2'),
    			'address3' => Input::get('supplier_address3'),
    			'address4' => Input::get('supplier_address4'),
    			'postcode' => Input::get('supplier_postcode'),
    			'phone1' => Input::get('supplier_phone1'),
    			'phone2' => Input::get('supplier_phone2'),
    			'email1' => Input::get('supplier_email1'),
    			'email2' => Input::get('supplier_email2'),
    			'status' => Input::get('supplier_status')
    			)
		);
		
		$supplier = $this->Supplier->whereId($id)->first();
		
		return View::make('supplier.show')->withSupplier($supplier);
	
	}
	
	public function updatebank($id)
	{
		$client = $this->Client->whereId($id)->first();
		
		return View::make('client.updatebank')->withClient($client);
	
	}
	
	public function bankstore()
	{
		$inputArray = Input::except('_token', '_method');
		$client = $this->Client->whereId(Input::get('client_id'))->first();
		
		DB::table('client')->where('id', $client->id)->update(['bank_id' => Input::get('client_bank')]);
		
		return View::make('client.show')->withClient($client);
	}
	

	
	public function store()
	{
		$inputArray = Input::except('_token', '_method');
		
		
		$supplierInsertId = DB::table('supplier')->insertGetId(
    		array('supplier_name' => Input::get('supplier_name'),
    			'supplier_type' => Input::get('supplier_type'), 
    			'nationality' => Input::get('supplier_nationality'),
    			'company_number' => Input::get('supplier_number'),
    			'website' => Input::get('supplier_website'),
    			'address1' => Input::get('supplier_address1'),
    			'address2' => Input::get('supplier_address2'),
    			'address3' => Input::get('supplier_address3'),
    			'address4' => Input::get('supplier_address4'),
    			'postcode' => Input::get('supplier_postcode'),
    			'phone1' => Input::get('supplier_phone1'),
    			'phone2' => Input::get('supplier_phone2'),
    			'email1' => Input::get('supplier_email1'),
    			'email2' => Input::get('supplier_email2'),
    			'status' => Input::get('supplier_status')
    			)
		);
		
		$dirCreate = File::makeDirectory(public_path().'/data/supplier/'.$supplierInsertId, 0775, true);
		
		return Redirect::to('supplier/'.$supplierInsertId);
	}
	
	
	public function getTypeOptions($fieldname) {
			$casetypes = DB::table('options')->where('fieldname', '=', $fieldname)->get();
			return Response::json($casetypes);
		}

	}

?>