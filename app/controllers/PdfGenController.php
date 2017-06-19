<?php

class PdfGenController extends BaseController {

	protected $pdfgen;
	
	public function __construct(PdfGen $pdfgen)
	{	
		$this->PdfGen = $pdfgen;	
	}
	
	
	public function create($type,$typeid) {
	
		if($type == "product_specification") {
		
			$thisProduct = DB::table('product')->where('id',$typeid)->first();

			$thisPDF = PDF::loadView('pdf.product_specification', array('product_id' => $typeid));
			$filename = str_random(6).'.pdf';
			$thisPDF->setPaper('a4')
					->setOrientation('portrait');
					
			return	$thisPDF->download("ISF_Global_".$thisProduct->prefix.$thisProduct->code.".pdf");
			
		} else if($type == "sales_confirmation") {
			$thisConfirmation = DB::table('sales_confirmation')->where('id',$typeid)->first();

			$thisPDF = PDF::loadView('pdf.sales_confirmation', array('sales_confirmation_id' => $typeid));
			//$displayConfirmationId =  "ISFC".str_pad($typeid,6,"0",STR_PAD_LEFT);
			$filename = str_random(6).'.pdf';
			$fileLocation = public_path().'/data/client/'.$thisConfirmation->client_id.'/deal/'.$thisConfirmation->deal_id.'/'.$filename;
			$thisPDF->setPaper('a4')
					->setOrientation('landscape')
					->save($fileLocation);

			return Redirect::action(
				'HistoryController@autoDealHistory', array('type' => $type, 'type_id' => $typeid, 'filename' => $filename)
			);
		
		}else if($type == "purchase_order") {
		
			$thisPurchaseOrder = DB::table('purchase_order')->where('id',$typeid)->first();

			$thisPDF = PDF::loadView('pdf.purchase_order', array('purchase_order_id' => $typeid));
			//$displayConfirmationId =  "ISFC".str_pad($typeid,6,"0",STR_PAD_LEFT);
			$filename = str_random(6).'.pdf';
			$fileLocation = public_path().'/data/client/'.$thisPurchaseOrder->client_id.'/deal/'.$thisPurchaseOrder->deal_id.'/'.$filename;
			$thisPDF->setPaper('a4')
					->setOrientation('landscape')
					->save($fileLocation);

			return Redirect::action(
				'HistoryController@autoDealHistory', array('type' => $type, 'type_id' => $typeid, 'filename' => $filename)
			);
			
		
		}else if($type == "proforma_invoice") {
		
			$thisProformaInvoice = DB::table('proforma_invoice')->where('id',$typeid)->first();

			$thisPDF = PDF::loadView('pdf.proforma_invoice', array('proforma_invoice_id' => $typeid));
			//$displayConfirmationId =  "ISFC".str_pad($typeid,6,"0",STR_PAD_LEFT);
			$filename = str_random(6).'.pdf';
			$fileLocation = public_path().'/data/client/'.$thisProformaInvoice->client_id.'/deal/'.$thisProformaInvoice->deal_id.'/'.$filename;
			$thisPDF->setPaper('a4')
					->setOrientation('landscape')
					->save($fileLocation);

			return Redirect::action(
				'HistoryController@autoDealHistory', array('type' => $type, 'type_id' => $typeid, 'filename' => $filename)
			);
		
		
		}else if($type == "invoice") {
		
			$thisInvoice = DB::table('invoice')->where('id',$typeid)->first();

			$thisPDF = PDF::loadView('pdf.invoice', array('invoice_id' => $typeid));
			//$displayConfirmationId =  "ISFC".str_pad($typeid,6,"0",STR_PAD_LEFT);
			$filename = str_random(6).'.pdf';
			$fileLocation = public_path().'/data/client/'.$thisInvoice->client_id.'/deal/'.$thisInvoice->deal_id.'/'.$filename;
			$thisPDF->setPaper('a4')
					->setOrientation('landscape')
					->save($fileLocation);

			return Redirect::action(
				'HistoryController@autoDealHistory', array('type' => $type, 'type_id' => $typeid, 'filename' => $filename)
			);
		
		
		}
	
	}
	
	

}

?>