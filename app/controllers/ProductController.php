<?php

class ProductController extends BaseController {

	protected $product;

	public function __construct(Product $product)
	{
		$this->Product = $product;
	}

	public function index()
	{
		/*$cases = $this->Cases->all();
	
		foreach($cases as $case) {
			if($_SESSION["database"] == "dynamicCRM" OR $_SESSION["database"] == "dynamicMSCRM") {
				$corrId = $case->company;
				}
			else {
				$corrId = $case->prop;
				}
			$corr = Correspondant::find($corrId);
			$order = Order::where('case_id', '=', $case->id)->first();
			$case->corres = $corr;
			$case->order = $order;
		}*/
		
		
		return View::make('product.index')->with('products', $this->Product->all());
	}
	
	public function edit($case)
	{
		$case = $this->Cases->whereId($case)->first();
		$caseid = $case->id;
		$corr_fields = Correspondant::getFieldnames();
		
		return View::make('cases.edit')->withCase($case)->with('casefields', $this->Cases->getFieldnames())->with('corrfields', $corr_fields);
	}
	
	public function export()
	{
		return View::make('cases.export');
	}
	
	public function show($product)
	{
		$product = $this->Product->whereId($product)->first();
		
		return View::make('product.show')->withProduct($product);
	}
	
	public function create()
	{
		return View::make('property.create');
	}
	
	public function addproperty()
	{
		$values_property_sectors = ValuesPropertySectors::orderBy('value')->get();
		$values_property_types = ValuesPropertyTypes::orderBy('value')->get();
		$values_property_features = ValuesPropertyFeatures::orderBy('value')->get();
		$portals = Portals::orderBy('portal_name')->get();
		return View::make('property.addproperty')->with('values_property_sectors',$values_property_sectors)->with('values_property_features',$values_property_features)->with('values_property_types',$values_property_types)->with('portals',$portals);
	}
	
	public function getCorrHistory()
	{
		$property_id = $_POST["property_id"];
		$correspondant_id = $_POST["correspondant_id"];
		$correspondant_type = $_POST["correspondant_type"];
		$historys = DB::table('history')->where('property_id',$property_id)->where('correspondant_id',$correspondant_id)->where('correspondant_type',$correspondant_type)->get();
		
		if(is_array($historys)) {
			foreach($historys as $history) {
				$historyDate = explode(" ",$history->date);
				$historyDate = explode("-",$historyDate[0]);
				$historyDate = $historyDate[2]."/".$historyDate[1]."/".$historyDate[0];
				
				print "<tr><td>".$historyDate."</td><td>".$history->details."</td><td>".$history->user."</td></tr>";
			}
		}
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
	
	public function update()
	{

		return View::make('cases.showcase')->withCase($newcase);
	
	}
	
	public function addFiles()
	{
		$filename = $_POST["filename"];
		$mediatype = $_POST["mediatype"];
		$propertyid = $_POST["propertyid"];
		
		$media_id = DB::table('property_media')->insertGetId(
    		array('property_id' => $propertyid, 'Media_Type' => $mediatype, 'Media_URL' => $filename, 'Active' => 'yes', 'Sort_Order' => '0')
		);
		
		return $media_id;
	}
	
	public function deleteFiles()
	{
		$deleted = "false";
		$mediaid = $_POST["mediaid"];
		$media_item = DB::table('property_media')->where('id', $mediaid)->first();
		
		$property_id = $_POST["propertyid"];
		if(unlink("packages/properties/".$property_id."/".$media_item->Media_URL)) {
			DB::table('property_media')->where('id', $mediaid)->delete();
			$deleted = "true";
		}
		
		return $deleted;
	}
	
	public function orderFiles()
	{
		$positions = $_POST["positionArray"];
		$positions = explode(";",$positions);
		
		foreach($positions as $position) {
			$position = explode("=",$position);
			$mediaid = $position[0];
			$orderpos = $position[1];
			
			DB::table('property_media')->where('id', $mediaid)->update(['Sort_Order' => $orderpos]);
		}
		
		return "Updated";
	}
	
	public function addToPortal()
	{
		$propertyid = $_POST["propertyid"];
		$portalid = $_POST["portalid"];
		$rightnow = date('Y-m-d H:i:s');
		
		$portal = DB::table('portals')->where('id',$portalid)->first();
		$link = $portal->portal_link;
		
		$propertyportalid = DB::table('property_portals')->insertGetId(
    		array('property_id' => $propertyid, 'portal_id' => $portalid, 'uploaded_by' => 'callum')
		);
		
		
		
		$resultArray = array($rightnow,'callum',$link,$propertyportalid);
		
		return json_encode($resultArray);
	
	}
	
	public function removeFromPortal()
	{
		$portalid = $_POST["propertyportalid"];
		$rightnow = date('Y-m-d H:i:s');
	
		DB::table('property_portals')->where('id', $portalid)->delete();
		
		$resultArray = array('N/A','N/A','N/A');
		
		return json_encode($resultArray);
	}
	
	public function getPropertyAssociates()
	{
		$propertyid = $_POST["propertyid"];
		
		$tenants = DB::table('tenant')->where('property_id',$propertyid)->get();
		if(is_array($tenants)) {
			foreach($tenants as $tenant) {
				$tenant->type = "tenant";
			}
		}	
	
		$landlords = DB::table('landlord')->where('property_id',$propertyid)->get();
		if(is_array($landlords)) {
			foreach($landlords as $landlord) {
				$landlord->type = "landlord";
			}
		}
		
		$associates = array_merge($tenants,$landlords);
		
		return json_encode($associates);		
	
	}

	
	public function store()
	{
		$inputArray = Input::except('_token', '_method');

		$id = DB::table('property')->insertGetId(
    		array('Property_Type' => Input::get('prop_type'), 'Date_Available' => Input::get('prop_startdate'))
		);
		
		if($id) {
			//create directory where associated files can be stored
			mkdir("packages/properties/".$id,"0777");
		}
		
		$postcode = explode(" ",Input::get('postcode'));
		
		$addressid = DB::table('property_address')->insertGetId(
    		array('property_id' => $id,
    			'House_Name_Number' => Input::get('address1'), 
    			'Address_2' => Input::get('address2'),
    			'Address_3' => Input::get('address3'),
    			'Town' => Input::get('address4'),
    			'Postcode_1' => $postcode[0],
    			'Postcode_2' => $postcode[1],
    			'Display_Address' => Input::get('address1').", ".Input::get('postcode'))
		);
		
		$property_features = Input::get('propertyfeatures');
		
			if(is_array($property_features))
			{
			   foreach($property_features as $feature) {
			   		DB::table('property_features')->insert(
						array('property_id' => $id, 'feature_name' => $feature)
					);
			   }
			}
		$newProperty = Property::find($id);
		
		return Redirect::to('property/'.$id);
	}
	
	
	public function getTypeOptions($fieldname) {
			$casetypes = DB::table('options')->where('fieldname', '=', $fieldname)->get();
			return Response::json($casetypes);
		}

	}

?>