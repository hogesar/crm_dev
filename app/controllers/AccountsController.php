<?php

class AccountsController extends BaseController {

	protected $accounts;

	public function __construct(Accounts $accounts)
	{
		$this->Accounts = $accounts;
	}
	
	
	public function create($type = null,$type_id = null) {
		
		return View::make('accounts.create')->with('type',$type)->with('type_id',$type_id);		
	
	}
	
	public function create_in($type = null,$type_id = null) {
		
		return View::make('accounts.in.create')->with('type',$type)->with('type_id',$type_id);	
	
	}
	
	public function create_out($type = null,$type_id = null) {
		
		return View::make('accounts.out.create')->with('type',$type)->with('type_id',$type_id);	
	
	}
	
	public function actionDiary($diaryid) {
	
		$diary = DB::table('diary')->where('id',$diaryid)->first();
		
		return View::make('history.actiondiary')->with('diary',$diary);	
	}
	
	
	
	public function store() {
	
		$type = Input::get('type');
		$typeid = Input::get('type_id');
		
		if($type == "purchase_order" OR $type == "proforma_invoice" OR $type == "invoice") {
			
			$accountsInsertId = DB::table('accounts')->insertGetId(
				array('date_entered' => Input::get('payment_date'),
					'date_paid' => Input::get('payment_date'),
					'link_type' => Input::get('type'), 
					'link_id' => Input::get('type_id'),
					'method' => Input::get('payment_method'), 
					'ref' => Input::get('payment_type'),
					'type' => Input::get('payment_directions'),
					'amount' => Input::get('payment_amount'),
					'status' => 'paid',
					'notes' => Input::get('payment_notes')
					)
			);				
		}
		
		//create a directory for this accounts entry
		$dirPath = public_path().'/data/accounts/'.$accountsInsertId;
		$dirCreate = File::makeDirectory($dirPath, 0775, true);
		//declare empty filename for update method in case no image
		$filename = "";
		//check dircreate success
		if($dirCreate) {
				//check for file
			   if (Input::hasFile('payment_file')) {
					$file            = Input::file('payment_file');
					$destinationPath = $dirPath.'/';
					$filename        = str_random(6).'.'.$file->getClientOriginalExtension();
					$uploadSuccess   = $file->move($destinationPath, $filename);
					//update table with image filename
					DB::table('accounts')->where('id', $accountsInsertId)->update(['file' => $filename]);
				}
		}
		
		return Redirect::action(
			'HistoryController@autoAccountsHistory', array('accounts_id' => $accountsInsertId, 'type' => $type, 'type_id' => $typeid, 'filename' => $filename)
		);
	
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
				} else {
					$pathToFile = public_path()."/data/".$history->parent_type."/".$parent->id."/history/".$history->id."/".$history->file;
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