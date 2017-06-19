<?php

class DealController extends BaseController {

	protected $deal;

	public function __construct(Deal $deal)
	{
		$this->Deal = $deal;
	}
	
	//custom funcs
	
	public function index($date_filter = null, $type = null)
	{		
	
		if($date_filter) {
			return View::make('deal.index')->with('date_filter', $date_filter)->with('type', $type);
		} else {
			return View::make('deal.index')->with('deals', $this->Deal->all());
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
		$deal = $this->Deal->whereId($id)->first();
	
		return View::make('deal.show')->withDeal($deal);
	}
	
	public function create($id,$contactid = null)
	{		
		
		$client = DB::table('client')->where('id',$id)->first();
		
		return View::make('deal.create')->with('client',$client)->with('contactid',$contactid);
	}
	
	public function create_sales_confirmation($id,$contactid = null)
	{		
		
		$deal = DB::table('deal')->where('id',$id)->first();
		
		return View::make('deal.sales_confirmation.create')->with('deal',$deal)->with('contactid',$contactid);
	}
	
	public function store_sales_confirmation($id,$contactid = null)
	{		
		
		//insert the confirmation whilst getting its newly created id
		$confirmationId = DB::table('sales_confirmation')->insertGetId(
    		array('client_id' => Input::get('client_id'), 
    			'deal_id' => Input::get('deal_id'), 
    			'user' => Session::get('user'), 
    			'confirmation_date' => Input::get('confirmation_date'),
    			'payment_date' => Input::get('payment_date'),
    			'delivery_date' => Input::get('delivery_date'),
    			'shipping_from' => Input::get('shipping_from'),
    			'destination_country' => Input::get('destination_country'),
    			'shipping_method' => Input::get('shipping_method'),
    			'notes' => Input::get('notes'),
    			'terms' => Input::get('terms'),
    			'total_cost_price' => Input::get('cost_total'),
    			'total_sale_price' => Input::get('sale_total'),
    			'status' => 'complete'
    			)
		);
		
		$confirmationProductArray = explode(",",Input::get('product_count_array'));
		
		
		foreach($confirmationProductArray as $confirmationProductId) {
		
			$enquiryProductInsertId = DB::table('sales_confirmation_products')->insertGetId(
				array('confirmation_id' => $confirmationId,
					'product_id' => Input::get('product_id_'.$confirmationProductId),
					'product_code' => Input::get('product_prefix_'.$confirmationProductId),
					'quantity' => Input::get('product_quantity_'.$confirmationProductId),
					'quantity_type' => Input::get('product_quantity_type_'.$confirmationProductId),
					'frequency' => Input::get('product_quantity_frequency_'.$confirmationProductId),
					'unit_cost_price' => Input::get('product_unit_cost_'.$confirmationProductId),
					'unit_sale_price' => Input::get('product_unit_sale_'.$confirmationProductId),
					'total_cost_price' => Input::get('product_total_cost_'.$confirmationProductId),
					'total_sale_price' => Input::get('product_total_sale_'.$confirmationProductId)
					)
				);
				
		}
		
		return Redirect::to('pdf/create/sales_confirmation/'.$confirmationId);
	}
	
	public function create_purchase_order($id,$contactid = null)
	{		
		
		$deal = DB::table('deal')->where('id',$id)->first();
		$sales_confirmation = DB::table('sales_confirmation')->where('deal_id',$id)->first();
		
		return View::make('deal.purchase_order.create')->with('deal',$deal)->with('sales_confirmation',$sales_confirmation)->with('contactid',$contactid);
	}
	
	public function store_purchase_order($id,$contactid = null)
	{		
		
		//insert the confirmation whilst getting its newly created id
		$purchaseOrderId = DB::table('purchase_order')->insertGetId(
    		array('client_id' => Input::get('client_id'), 
    			'deal_id' => Input::get('deal_id'), 
    			'user' => Session::get('user'), 
    			'order_date' => Input::get('po_date'),
    			'payment_date' => Input::get('payment_date'),
    			'delivery_date' => Input::get('delivery_date'),
    			'shipping_date' => Input::get('shipping_date'),
    			'loading_date' => Input::get('loading_date'),
    			'shipping_from' => Input::get('shipping_from'),
    			'destination_country' => Input::get('destination_country'),
    			'shipping_method' => Input::get('shipping_method'),
    			'shipping_contact_name' => Input::get('shipping_contact_name'),
    			'shipping_contact_number' => Input::get('shipping_contact_number'),
    			'shipping_address' => Input::get('shipping_address1').", ".Input::get('shipping_address2').", ".Input::get('shipping_address3').", ".Input::get('shipping_address4'),
     			'invoiced_to' => Input::get('invoiced_to1').", ".Input::get('invoiced_to2').", ".Input::get('invoiced_to3').", ".Input::get('invoiced_to4'),
    			'notes' => Input::get('notes'),
    			'terms' => Input::get('terms'),
    			'total_cost_price' => Input::get('cost_total'),
    			'total_sale_price' => Input::get('sale_total'),
    			'status' => 'complete'
    			)
		);
		
		$PoProductArray = explode(",",Input::get('product_count_array'));
		
		
		foreach($PoProductArray as $PoProductId) {
		
			$poProductInsertId = DB::table('purchase_order_products')->insertGetId(
				array('purchase_order_id' => $purchaseOrderId,
					'product_id' => Input::get('product_id_'.$PoProductId),
					'product_code' => Input::get('product_prefix_'.$PoProductId),
					'quantity' => Input::get('product_quantity_'.$PoProductId),
					'quantity_type' => Input::get('product_quantity_type_'.$PoProductId),
					'frequency' => Input::get('product_quantity_frequency_'.$PoProductId),
					'unit_cost_price' => Input::get('product_unit_cost_'.$PoProductId),
					'unit_sale_price' => Input::get('product_unit_sale_'.$PoProductId),
					'total_cost_price' => Input::get('product_total_cost_'.$PoProductId),
					'total_sale_price' => Input::get('product_total_sale_'.$PoProductId),
					'purchase_order_created' => 'yes'
					)
				);
				
			DB::table('sales_confirmation_products')->where('id', Input::get('sales_confirmation_product_id_'.$PoProductId))->update(['purchase_order_created' => 'yes']);		

				
		}
		
		return Redirect::to('pdf/create/purchase_order/'.$purchaseOrderId);
	}
	
	public function create_proforma_invoice($id,$contactid = null)
	{		
		
		$deal = DB::table('deal')->where('id',$id)->first();
		$purchase_orders = DB::table('purchase_order')->where('deal_id',$id)->get();
		$sales_confirmation = DB::table('sales_confirmation')->where('deal_id',$id)->first();
		
		return View::make('deal.proforma_invoice.create')->with('deal',$deal)->with('sales_confirmation',$sales_confirmation)->with('purchase_orders',$purchase_orders)->with('contactid',$contactid);
	}
	
	public function store_proforma_invoice($id,$contactid = null)
	{		
		
		//insert the confirmation whilst getting its newly created id
		$proformaId = DB::table('proforma_invoice')->insertGetId(
    		array('client_id' => Input::get('client_id'), 
    			'deal_id' => Input::get('deal_id'), 
    			'user' => Session::get('user'), 
    			'proforma_invoice_date' => Input::get('proforma_date'),
    			'payment_date' => Input::get('payment_date'),
    			'delivery_date' => Input::get('delivery_date'),
    			'shipping_date' => Input::get('shipping_date'),
    			'loading_date' => Input::get('loading_date'),
    			'shipping_from' => Input::get('shipping_from'),
    			'destination_country' => Input::get('destination_country'),
    			'shipping_method' => Input::get('shipping_method'),
    			'shipping_contact_name' => Input::get('shipping_contact_name'),
    			'shipping_contact_number' => Input::get('shipping_contact_number'),
    			'shipping_address' => Input::get('shipping_address1').", ".Input::get('shipping_address2').", ".Input::get('shipping_address3').", ".Input::get('shipping_address4'),
     			'invoiced_to' => Input::get('invoiced_to1').", ".Input::get('invoiced_to2').", ".Input::get('invoiced_to3').", ".Input::get('invoiced_to4'),
    			'notes' => Input::get('notes'),
    			'terms' => Input::get('terms'),
    			'total_cost_price' => Input::get('cost_total'),
    			'total_sale_price' => Input::get('sale_total'),
    			'status' => 'created'
    			)
		);
		
		$proformaProductArray = explode(",",Input::get('product_count_array'));
		
		
		foreach($proformaProductArray as $pProductId) {
		
			$poProductInsertId = DB::table('proforma_invoice_products')->insertGetId(
				array('proforma_invoice_id' => $proformaId,
					'purchase_order_id' => Input::get('purchase_order_product_id_'.$pProductId),
					'product_id' => Input::get('product_id_'.$pProductId),
					'product_code' => Input::get('product_prefix_'.$pProductId),
					'quantity' => Input::get('product_quantity_'.$pProductId),
					'quantity_type' => Input::get('product_quantity_type_'.$pProductId),
					'frequency' => Input::get('product_quantity_frequency_'.$pProductId),
					'unit_cost_price' => Input::get('product_unit_cost_'.$pProductId),
					'unit_sale_price' => Input::get('product_unit_sale_'.$pProductId),
					'total_cost_price' => Input::get('product_total_cost_'.$pProductId),
					'total_sale_price' => Input::get('product_total_sale_'.$pProductId),
					'purchase_order_created' => 'yes',
					'proforma_invoice_created' => 'yes'
					)
				);
				
			DB::table('purchase_order_products')->where('id', Input::get('purchase_order_product_id_'.$pProductId))->update(['proforma_invoice_created' => 'yes']);		

				
		}
		
		return Redirect::to('pdf/create/proforma_invoice/'.$proformaId);
	}
	
	public function create_invoice($id,$contactid = null)
	{		
		
		$deal = DB::table('deal')->where('id',$id)->first();
		$purchase_orders = DB::table('purchase_order')->where('deal_id',$id)->get();
		$sales_confirmation = DB::table('sales_confirmation')->where('deal_id',$id)->first();
		
		return View::make('deal.invoice.create')->with('deal',$deal)->with('sales_confirmation',$sales_confirmation)->with('purchase_orders',$purchase_orders)->with('contactid',$contactid);
	}
	
	public function store_invoice($id,$contactid = null)
	{		
		
		//insert the confirmation whilst getting its newly created id
		$invoiceId = DB::table('invoice')->insertGetId(
    		array('client_id' => Input::get('client_id'), 
    			'deal_id' => Input::get('deal_id'), 
    			'user' => Session::get('user'), 
    			'invoice_date' => Input::get('invoice_date'),
    			'payment_date' => Input::get('payment_date'),
    			'delivery_date' => Input::get('delivery_date'),
    			'shipping_date' => Input::get('shipping_date'),
    			'loading_date' => Input::get('loading_date'),
    			'shipping_from' => Input::get('shipping_from'),
    			'destination_country' => Input::get('destination_country'),
    			'shipping_method' => Input::get('shipping_method'),
    			'shipping_contact_name' => Input::get('shipping_contact_name'),
    			'shipping_contact_number' => Input::get('shipping_contact_number'),
    			'shipping_address' => Input::get('shipping_address1').", ".Input::get('shipping_address2').", ".Input::get('shipping_address3').", ".Input::get('shipping_address4'),
     			'invoiced_to' => Input::get('invoiced_to1').", ".Input::get('invoiced_to2').", ".Input::get('invoiced_to3').", ".Input::get('invoiced_to4'),
    			'notes' => Input::get('notes'),
    			'terms' => Input::get('terms'),
    			'total_cost_price' => Input::get('cost_total'),
    			'total_sale_price' => Input::get('sale_total'),
    			'status' => 'awaiting payment'
    			)
		);
		
		$invoiceProductArray = explode(",",Input::get('product_count_array'));
		
		
		foreach($invoiceProductArray as $iProductId) {
		
			$iProductInsertId = DB::table('invoice_products')->insertGetId(
				array('invoice_id' => $invoiceId,
					'purchase_order_id' => Input::get('purchase_order_product_id_'.$iProductId),
					'product_id' => Input::get('product_id_'.$iProductId),
					'product_code' => Input::get('product_prefix_'.$iProductId),
					'quantity' => Input::get('product_quantity_'.$iProductId),
					'quantity_type' => Input::get('product_quantity_type_'.$iProductId),
					'frequency' => Input::get('product_quantity_frequency_'.$iProductId),
					'unit_cost_price' => Input::get('product_unit_cost_'.$iProductId),
					'unit_sale_price' => Input::get('product_unit_sale_'.$iProductId),
					'total_cost_price' => Input::get('product_total_cost_'.$iProductId),
					'total_sale_price' => Input::get('product_total_sale_'.$iProductId),
					'purchase_order_created' => 'yes',
					'proforma_invoice_created' => 'yes',
					'invoice_created' => 'yes'
					)
				);
				
			DB::table('purchase_order_products')->where('id', Input::get('purchase_order_product_id_'.$iProductId))->update(['invoice_created' => 'yes']);		

				
		}
		
		return Redirect::to('pdf/create/invoice/'.$invoiceId);
	}
	
	public function update()
	{

		return View::make('cases.showcase')->withCase($newcase);
	
	}
	

	
	public function store()
	{
		
		//insert the contact whilst getting its newly created id
		$orderInsertId = DB::table('deal')->insertGetId(
    		array('client_id' => Input::get('client_id'), 
    			'contact_id' => Input::get('order_contact'), 
    			'order_date' => Input::get('order_date'),
    			'notes' => Input::get('notes'),
    			'status' => 'open'
    			)
		);
		
		
		//create a directory for this enquiry
		$dirCreate = File::makeDirectory(public_path().'/data/client/'.Input::get('client_id').'/deal/'.$orderInsertId, 0775, true);
		//declare empty filename for update method in case no image
		/*$filename = "";
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
		DB::table('enquiry')->where('id', $enquiryInsertId)->update(['enquiry_file' => $filename]);*/
		
		return Redirect::to('deal/'.$orderInsertId);
	}
	
	
	public function getTypeOptions($fieldname) {
			$casetypes = DB::table('options')->where('fieldname', '=', $fieldname)->get();
			return Response::json($casetypes);
		}

	}

?>