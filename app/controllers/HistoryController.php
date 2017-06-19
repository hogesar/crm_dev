<?php

class HistoryController extends BaseController {

	protected $history;

	public function __construct(History $history)
	{
		$this->History = $history;
	}
	
	
	public function create($history_type,$parent_type,$parent_id,$child_type = "",$child_id = "0") {
		
		return View::make('history.create')->with('parent_type',$parent_type)->with('parent_id',$parent_id)
		->with('child_type',$child_type)->with('child_id',$child_id)->with('history_type',$history_type);
	
	}
	
	public function actionDiary($diaryid) {
	
		$diary = DB::table('diary')->where('id',$diaryid)->first();
		
		return View::make('history.actiondiary')->with('diary',$diary);	
	}
	
	public function autoAccountsHistory($accounts_id,$type,$type_id,$filename = "") {
	
		if($type == "purchase_order" OR $type == "proforma_invoice" OR $type == "invoice") {
			$thisParent = DB::table($type)->where('id',$type_id)->first();
			$thisAccounts = DB::table('accounts')->where('id',$accounts_id)->first();
			
			$historyInsertId = DB::table('history')->insertGetId(
				array('date' => date('Y-m-d H:i:s'),
					'parent_id' => $thisParent->client_id, 
					'parent_type' => "client",
					'child_id' => $thisParent->deal_id, 
					'child_type' => "deal",
					'contact_id' => $thisParent->contact_id,
					'accounts_id' => $thisAccounts->id,
					'action_type' => "AC",
					'details' => "Payment ".strtoupper($thisAccounts->type)." -  ".$thisAccounts->ref.". Amount : $".$thisAccounts->amount." - ".$thisAccounts->notes,
					'user' => Session::get('user'),
					'file' => $filename
					)
			);
			
			
			$deal = DB::table('deal')->where('id',$thisParent->deal_id)->first();
			return Redirect::route('deal.show' , $deal);
			
		} else {
			print "WTF";
		}
			
	
	
	}
	
	public function autoDealHistory($type,$typeid,$filename = null) {
		if($type == "sales_confirmation") {
			$thisConfirmation = DB::table('sales_confirmation')->where('id',$typeid)->first();

			$historyInsertId = DB::table('history')->insertGetId(
				array('date' => date('Y-m-d H:i:s'),
					'parent_id' => $thisConfirmation->client_id, 
					'parent_type' => "client",
					'child_id' => $thisConfirmation->deal_id, 
					'child_type' => "deal",
					'contact_id' => $thisConfirmation->contact_id,
					'action_type' => "SC",
					'details' => "ISFC".str_pad($thisConfirmation->id,6,"0",STR_PAD_LEFT)." - ".$thisConfirmation->notes,
					'user' => Session::get('user'),
					'file' => $filename
					)
			);
			
			$deal = DB::table('deal')->where('id',$thisConfirmation->deal_id)->first();
			return Redirect::route('deal.show' , $deal);
			
		} else if($type == "purchase_order") {
		
			$thisPurchaseOrder = DB::table('purchase_order')->where('id',$typeid)->first();

			$historyInsertId = DB::table('history')->insertGetId(
				array('date' => date('Y-m-d H:i:s'),
					'parent_id' => $thisPurchaseOrder->client_id, 
					'parent_type' => "client",
					'child_id' => $thisPurchaseOrder->deal_id, 
					'child_type' => "deal",
					'contact_id' => $thisPurchaseOrder->contact_id,
					'action_type' => "PO",
					'details' => "ISFP".str_pad($thisPurchaseOrder->id,6,"0",STR_PAD_LEFT)." - ".$thisPurchaseOrder->notes,
					'user' => Session::get('user'),
					'file' => $filename
					)
			);
			
			$deal = DB::table('deal')->where('id',$thisPurchaseOrder->deal_id)->first();
			return Redirect::route('deal.show' , $deal);
		
		} else if($type == "proforma_invoice") {
		
			$thisProforma = DB::table('proforma_invoice')->where('id',$typeid)->first();

			$historyInsertId = DB::table('history')->insertGetId(
				array('date' => date('Y-m-d H:i:s'),
					'parent_id' => $thisProforma->client_id, 
					'parent_type' => "client",
					'child_id' => $thisProforma->deal_id, 
					'child_type' => "deal",
					'contact_id' => $thisProforma->contact_id,
					'action_type' => "PI",
					'details' => "ISFI".str_pad($thisProforma->id,6,"0",STR_PAD_LEFT)."P - ".$thisProforma->notes,
					'user' => Session::get('user'),
					'file' => $filename
					)
			);
			
			$deal = DB::table('deal')->where('id',$thisProforma->deal_id)->first();
			return Redirect::route('deal.show' , $deal);
		
		}  else if($type == "invoice") {
		
			$thisInvoice = DB::table('invoice')->where('id',$typeid)->first();

			$historyInsertId = DB::table('history')->insertGetId(
				array('date' => date('Y-m-d H:i:s'),
					'parent_id' => $thisInvoice->client_id, 
					'parent_type' => "client",
					'child_id' => $thisInvoice->deal_id, 
					'child_type' => "deal",
					'contact_id' => $thisInvoice->contact_id,
					'action_type' => "IN",
					'details' => "ISFI".str_pad($thisInvoice->id,6,"0",STR_PAD_LEFT)." - ".$thisInvoice->notes,
					'user' => Session::get('user'),
					'file' => $filename
					)
			);
			
			$deal = DB::table('deal')->where('id',$thisInvoice->deal_id)->first();
			return Redirect::route('deal.show' , $deal);
		
		}
	}
	
	public function store() {
	
		$inputArray = Input::except('_token', '_method');
		
		if(Input::get('child_type') == "contact") {
			//contact is a bit different so we need to remove these as its against the contact anyway
			$child_type = "";
			$child_id = "0";
		} else {
			$child_type = Input::get('child_type');
			$child_id = Input::get('child_id');
		}
		
		if(Input::get('history_type') !="dy") {
		
			if(Input::get('history_type') == "se") {
				//insert the history whilst getting its newly created id
				$historyInsertId = DB::table('history')->insertGetId(
					array('date' => Input::get('history_date')." ".Input::get('history_time'),
						'parent_id' => Input::get('parent_id'), 
						'parent_type' => Input::get('parent_type'),
						'child_id' => $child_id, 
						'child_type' => $child_type,
						'contact_id' => Input::get('history_contact'),
						'action_type' => Input::get('history_type'),
						'details' => Input::get('email_subject'),
						'user' => Session::get('user')
						)
				);						
			} else {
				//insert the history whilst getting its newly created id
				$historyInsertId = DB::table('history')->insertGetId(
					array('date' => Input::get('history_date')." ".Input::get('history_time'),
						'parent_id' => Input::get('parent_id'), 
						'parent_type' => Input::get('parent_type'),
						'child_id' => $child_id, 
						'child_type' => $child_type,
						'contact_id' => Input::get('history_contact'),
						'action_type' => Input::get('history_type'),
						'details' => Input::get('history_details'),
						'user' => Session::get('user')
						)
				);
			}
		
			//create a directory for this history
			$dirPath = public_path().'/data/'.Input::get('parent_type').'/'.Input::get('parent_id').'/history/'.$historyInsertId;
			$dirCreate = File::makeDirectory($dirPath, 0775, true);
			//declare empty filename for update method in case no image
			$filename = "";
			//check dircreate success
			if($dirCreate) {
					//check for file
				   if (Input::hasFile('history_file')) {
						$file            = Input::file('history_file');
						$destinationPath = $dirPath.'/';
						$filename        = str_random(6).'.'.$file->getClientOriginalExtension();
						$uploadSuccess   = $file->move($destinationPath, $filename);
						//update table with image filename
						DB::table('history')->where('id', $historyInsertId)->update(['file' => $filename]);
					}
			}
			
			if(Input::get('diary_id')) {
				//we have actioned a diary so update the diary
				DB::table('diary')->where('id', Input::get('diary_id'))->update(['completed_by' => Session::get('user'), 'completed_date' => Input::get('history_date')." ".Input::get('history_time')]);		
			}
					
		}
		
		if(Input::get('schedule_diary')) {
			
			$diaryInsertId = DB::table('diary')->insertGetId(
				array('date' => Input::get('diary_date')." ".Input::get('diary_time'),
					'parent_id' => Input::get('parent_id'), 
					'parent_type' => Input::get('parent_type'),
					'child_id' => $child_id, 
					'child_type' => $child_type,
					'contact_id' => Input::get('history_contact'),
					'action_type' => Input::get('diary_type'),
					'details' => Input::get('diary_details'),
					'user' => Session::get('user'),
					'user_for' => Session::get('user')
					)
			);
			
			if(Input::get('history_type') !="dy") {
				//if its not just a diary we need to update and link the history
				DB::table('history')->where('id', $historyInsertId)->update(['diary_id' => $diaryInsertId, 'diary_date' => Input::get('diary_date')." ".Input::get('diary_time')]);			
			}
						
		}
		
		if(Input::get('history_type') == "se") {
			return $this->email($historyInsertId,$dirPath,Input::get('email_to'),Input::get('email_subject'),Input::get('email_attachment'),Input::file('custom_attachments'),Input::get('email_content'),Input::get('child_id'));
		}
		if(Input::get('child_id') != "0") {
			return Redirect::to(Input::get('child_type').'/'.Input::get('child_id'));
		} else {
			return Redirect::to(Input::get('parent_type').'/'.Input::get('parent_id'));
		}
	
	}
	
	public function email($historyId,$dirPath,$emailTo,$subject,$historyfile,$custom_attachments,$content,$child_id) {
	
		$data = array('emailTo' => $emailTo, 'historyfile' => $historyfile, 'subject' => $subject, 'custom_attachments' => $custom_attachments);
	
		Mail::send('email.custom', array('content' => $content), function($message) use ($data)
		{
			$message->from(Session::get('email'), "ISF Global - ".ucwords(Session::get('user')));

			if($data["emailTo"]) {
				$message->to($data["emailTo"])->subject($data["subject"]);
			} else {
				$message->to("callum.king@isf.global");
			}
			
			if($data["historyfile"] != "") {
				$history = DB::table('history')->where('id',$data["historyfile"])->first();
				
				if($history->action_type == "SC" OR $history->action_type == "PO" OR $history->action_type == "PI" OR $history->action_type == "IN") {
					$pathToFile = public_path()."/data/".$history->parent_type."/".$history->parent_id."/".$history->child_type."/".$history->child_id."/".$history->file;
				} else if($history->action_type == "AC") {
					$pathToFile = public_path()."/data/accounts/".$history->accounts_id."/".$history->file;				
				} else {
					$pathToFile = public_path()."/data/".$history->parent_type."/".$history->parent_id."/history/".$history->id."/".$history->file;
				}			
				$message->attach($pathToFile);
			}
			
			foreach($data["custom_attachments"] as $attachment) {
				if(!empty($attachment)) {
					$message->attach($attachment->getRealPath(), ['as' => 'ISF_'.$attachment->getClientOriginalName()]);
				}
			}

			
		});
		
		if (Mail::failures()) {
        	DB::table('history')->where('id',$historyId)->update(['details' => "Mail Failed"]);
    	} else {
    	
			//create history html file of email
			$filename = str_random(6).".html";
			$emailFile = fopen($dirPath."/".$filename, "w");
			fwrite($emailFile,"<b>Email To: </b>".$emailTo."<br></br><b>Email Subject: </b>".$subject."<br></br>".$content."<br></br><b>Attachments: </b></br><br>");
			
			if($data["historyfile"] != "") {
				$history = DB::table('history')->where('id',$data["historyfile"])->first();
				fwrite($emailFile,$history->file."<br>");
			}
			
			foreach($data["custom_attachments"] as $attachment) {
				if(!empty($attachment)) {
					fwrite($emailFile,"ISF_".$attachment->getClientOriginalName()."<br>");
				}
			}
    			
    			
    		fclose($emailFile);
    		DB::table('history')->where('id',$historyId)->update(['file' => $filename]);
    	}
		
		if(Input::get('child_id') != "0") {
			return Redirect::to(Input::get('child_type').'/'.Input::get('child_id'));
		} else {
			return Redirect::to(Input::get('parent_type').'/'.Input::get('parent_id'));
		}
		
	}
	


	public function storeOLD()
	{
		$historyid = DB::table('history')->insertGetId(
							array('date' => $_POST["date"]." ".$_POST["time"], 
							'property_id' => $_POST["property"],
							'correspondant_id' => $_POST["correspondant"],
							'correspondant_type' => $_POST["correspondant_type"],
							'action_type' => $_POST["action"],
							'details' => $_POST["details"],
							'user' => "callum")
			);
		
	}
	
	
	public function getTypeOptions($fieldname) {
			$casetypes = DB::table('options')->where('fieldname', '=', $fieldname)->get();
			return Response::json($casetypes);
		}

	}

?>