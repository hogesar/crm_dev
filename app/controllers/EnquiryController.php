<?php

class EnquiryController extends BaseController {

	protected $enquiry;

	public function __construct(Enquiry $enquiry)
	{
		$this->Enquiry = $enquiry;
	}
	
	//custom funcs
	
	public function index($date_filter = null, $type = null)
	{
		if($date_filter) {
			return View::make('enquiry.index')->with('date_filter', $date_filter)->with('type', $type);
		} else {
			return View::make('enquiry.index')->with('enquiries', $this->Enquiry->all());
		}
	}
	
	public function edit($case)
	{
		$case = $this->Cases->whereId($case)->first();
		$caseid = $case->id;
		$corr_fields = Correspondant::getFieldnames();
		
		return View::make('cases.edit')->withCase($case)->with('casefields', $this->Cases->getFieldnames())->with('corrfields', $corr_fields);
	}
	
	public function show($id)
	{
		$enquiry = $this->Enquiry->whereId($id)->first();
	
		return View::make('enquiry.show')->withEnquiry($enquiry);
	}
	
	public function create($id,$contactid = null)
	{		
		
		$client = DB::table('client')->where('id',$id)->first();
		
		return View::make('enquiry.create')->with('client',$client)->with('contactid',$contactid);
	}
	
	public function update()
	{

		return View::make('cases.showcase')->withCase($newcase);
	
	}
	

	
	public function store()
	{
		//get the input array
		$inputArray = Input::except('_token', '_method');
		
		//insert the contact whilst getting its newly created id
		$enquiryInsertId = DB::table('enquiry')->insertGetId(
    		array('client_id' => Input::get('client_id'), 
    			'contact_id' => Input::get('enquiry_contact'), 
    			'enquiry_date' => Input::get('enquiry_date'),
    			'payment_date' => Input::get('payment_date'),
    			'delivery_date' => Input::get('delivery_date'),
    			'shipping_from' => Input::get('shipping_from'),
    			'destination_country' => Input::get('destination_country'),
    			'shipping_method' => Input::get('shipping_method'),
    			'notes' => Input::get('notes'),
    			'status' => 'open'
    			)
		);
		
		$enquiryProductArray = explode(",",Input::get('product_count_array'));
		
		
		foreach($enquiryProductArray as $enquiryProductId) {
		
			$enquiryProductInsertId = DB::table('enquiry_products')->insertGetId(
				array('enquiry_id' => $enquiryInsertId,
					'product_id' => Input::get('product_id_'.$enquiryProductId),
					'product_prefix' => Input::get('product_prefix_'.$enquiryProductId),
					'quantity' => Input::get('product_quantity_est_'.$enquiryProductId),
					'quantity_type' => Input::get('product_quantity_type_'.$enquiryProductId),
					'frequency' => Input::get('product_quantity_frequency_'.$enquiryProductId),
					'notes' => Input::get('product_notes_'.$enquiryProductId)
					)
				);
				
		}
		

		
		//create a directory for this enquiry
		$dirCreate = File::makeDirectory(public_path().'/data/client/'.Input::get('client_id').'/enquiry/'.$enquiryInsertId, 0775, true);
		//declare empty filename for update method in case no image
		$filename = "";
		//check dircreate success
		if($dirCreate) {
				//check for file
			   if (Input::hasFile('enquiry_file')) {
					$file            = Input::file('enquiry_file');
					$destinationPath = public_path().'/data/client/'.Input::get('client_id').'/enquiry/'.$enquiryInsertId.'/';
					$filename        = str_random(6).'.'.$file->getClientOriginalExtension();
					$uploadSuccess   = $file->move($destinationPath, $filename);
				}
		}
		//update table with image filename
		DB::table('enquiry')->where('id', $enquiryInsertId)->update(['enquiry_file' => $filename]);
		
		return Redirect::to('enquiry/'.$enquiryInsertId);
	}
	
	
	public function getTypeOptions($fieldname) {
			$casetypes = DB::table('options')->where('fieldname', '=', $fieldname)->get();
			return Response::json($casetypes);
		}

	}

?>