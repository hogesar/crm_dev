@extends('layouts.structure')

@section('header')
	<script>
	$(document).ready(function() {
		
		var historyTable = $('#historyTable').DataTable({
				"bPaginate": true,			
				"scrollX": "97%",
				"bInfo" : false,
				"bAutoWidth" : false,
				"columnDefs" : [
					{ type : 'date', targets : [0] }
				]
			});
				
		historyTable.page('last').draw('page');
		
		$('body').find("#client_menu").addClass("active");
		$('body').find("#client").removeClass("collapse").addClass("collapsed");
		
		$(".button_next").click(function(e) {
			e.preventDefault();
			var tab = $('.nav-tabs > .active').next('li').find('a');
			tab.trigger('click');
			
			var tabDiv = tab.attr("href");
			//$(':input[type="text"]:enabled:visible:first').focus();
			$(tabDiv+'-input:enabled:visible:first').focus();
		});
		
		$(".button_prev").click(function(e) {
			e.preventDefault();
			var tab = $('.nav-tabs > .active').prev('li').find('a');
			tab.trigger('click');
			
			var tabDiv = tab.attr("href");
			//$(':input[type="text"]:enabled:visible:first').focus();
			$(tabDiv+'-input:first').focus();
		});
		
		$('#history_form :input:enabled:visible:not([readonly]):first').focus();
		
		$('#schedule_diary').change(function () {
		
			if(this.checked == true) {
				console.log("true");
				$(".diaryInput").each(function() {
					console.log($(this).attr("readonly"));
					$(this).attr("readonly",false);
				});				
			} else {
				$(".diaryInput").attr("readonly",true);
			}
		});
		
		$(".dy_checkbox").each(function() {
			this.checked = true;
			$(this).trigger("change");
		});
		
		
	});
	</script>
	<style>
	
	.dy {
		display:none!important;
	}
	
	.se {
		display:none!important;
	}
	
	.sedy {
		display:block;
		margin:0 auto;
		width:70%;
	}
	
	.left {
		float:left;
	}
	
	.right {
		float:right;
	}
	
	.buttonHolder {
		width:120px;
		float:right;
		height:30px;
	}
	#sortable { list-style-type: none; margin: 0; padding: 0; width: 450px; }
  	#sortable li { margin: 3px 3px 3px 0; padding: 1px; float: left; width: 100px; height: 90px; font-size: 4em; text-align: center; }
	</style>
@stop


@section('content')
	<?php
	
	//if theres a child id we need to match that to filter the history properly i.e for an enquiry
	if($child_id != "0") {
		$parent_history = DB::table('history')->where('parent_id',$parent_id)->where('parent_type',$parent_type)->where('child_id',$child_id)->where('child_type',$child_type)->get();
		//grab the child
		$child = DB::table($child_type)->where('id',$child_id)->first();
	} else {
		$parent_history = DB::table('history')->where('parent_id',$parent_id)->where('parent_type',$parent_type)->get();
	}
	//grab the parent object (this is a client, supplier or bank)
	$parent = DB::table($parent_type)->where('id',$parent_id)->first();
	//grab the contacts for the parent
	$parent_contacts = DB::table('contact')->where('contact_type',$parent_type)->where('type_id',$parent->id)->get();
	
	if($parent_type == "client") {
		//client can also have bank contacts as a contact
		$bank_contacts = DB::table('contact')->where('contact_type','bank')->where('type_id',$parent->bank_id)->get();
	}

	
	$datetime = date('Y-m-d H:i:s');
	?>

<div class="tab-content">	

	  <div id="historydetails" class="tab-pane fade in active">
	  	@if ($child_type == "contact")
			<h3 style = "text-align:center;">You are adding a {{strtoupper($history_type)}} to the <b>{{ucwords($child->title." ".$child->firstname." ".$child->lastname)}}</b> file.</h3>
		@elseif ($parent_type == "client")
			<h3 style = "text-align:center;">You are adding a {{strtoupper($history_type)}} to the <b>{{ucwords($parent->client_name)}}</b> file.</h3>
		@elseif ($parent_type == "supplier")
			<h3 style = "text-align:center;">You are adding a {{strtoupper($history_type)}} to the <b>{{ucwords($parent->supplier_name)}}</b> file.</h3>
		@elseif ($parent_type == "bank")
			<h3 style = "text-align:center;">You are adding a {{strtoupper($history_type)}} to the <b>{{ucwords($parent->bank_name)}}</b> file.</h3>
		@endif
		
		<div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
		  <div class="panel panel-default">
			<div class="panel-heading" role="tab" id="headingOne">
			  <h4 class="panel-title">
				<a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
				  History
				</a>
			  </h4>
			</div>
			<div id="collapseOne" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingOne">
			  <div class="panel-body">
			  	<div class = "fullwidth_container">
					<table id="historyTable" class = "table table-striped" cellspacing="0" width="100%">
						<thead>
							<tr>
								<th>Date</th>
								<th>Type</th>
								<th>Details</th>
								@if($child_type != "client")
								<th>Client</th>
								@endif
								@if($child_type != "contact")
								<th>Contact</th>
								@endif
								@if($child_type != "enquiry" AND $child_type != "deal")
								<th>Enquiry / Order</th>
								@endif
								<th>User</th>
							</tr>
						</thead>
						
						<tbody>
						@if(is_array($parent_history))
						@foreach($parent_history as $history)
							<?php
								$this_client = DB::table($history->parent_type)->where('id', $history->parent_id)->first();

								$this_contact = DB::table('contact')->where('id', $history->contact_id)->first();
						
								if($history->child_type) {
									$this_child = DB::table($history->child_type)->where('id',$history->child_id)->first();
								} else {
									$this_child = null;
								}
						
								$histDate = explode(" ",$history->date);
								$histTime = $histDate[1];
								$histDate = date("d/m/y", strtotime($histDate[0]));
						
						
								?>
								<tr>
									<td>{{$histDate}} {{$histTime}}</td>
									<td>
										{{strtoupper($history->action_type)}}
										@if($history->file)
											@if($history->action_type == "SC" OR $history->action_type == "PO" OR $history->action_type == "PI" OR $history->action_type == "IN")
												<a href = "/data/{{$history->parent_type}}/{{$history->parent_id}}/{{$history->child_type}}/{{$history->child_id}}/{{$history->file}}" target = "_blank"><i class="fa fa-paperclip" aria-hidden="true"></i></a>
											@else
												<a href = "/data/{{$child_type}}/{{$parent->id}}/history/{{$history->id}}/{{$history->file}}" target = "_blank"><i class="fa fa-paperclip" aria-hidden="true"></i></a>
											@endif
										@endif 
									</td>
									<td>
										<div class = "detailsCell" title = "{{$history->details}}">
											{{$history->details}}
										</div>
									</td>
									@if($child_type != "client")
										<td>								
											@if(is_object($this_client))
												<a href = '../{{$history->parent_type}}/{{$this_client->id}}'>{{$this_client->{$history->parent_type.'_name'} }}</a>
											@endif								
										</td>
									@endif	
									@if($child_type != "contact")						
										<td>							
											@if(is_object($this_contact))
												<a href = '../contact/{{$this_contact->id}}'>{{ucwords($this_contact->firstname)}} {{ucwords($this_contact->lastname)}}</a>
											@endif								
										</td>
									@endif
									@if($child_type != "enquiry" AND $child_type != "order")
										<td>								
											@if(is_object($this_child))
												<a href = '../{{$history->child_type}}/{{$this_child->id}}'>{{ucwords($this_child->id)}}</a>
											@endif								
										</td>
									@endif
									<td>{{ucfirst($history->user)}}</td>
								</tr>
							@endforeach
						@endif
					</tbody>

					</table>
				</div>
			  </div>
			</div>
		  </div>
		</div>
		
		
		
        {{ Form::open(array('route' => 'history.store', 'files' => true, 'role'=>'form', 'id'=>'history_form')) }}
        
        <input type = "hidden" id = "parent_type" name = "parent_type" value = "{{$parent_type}}" />
        <input type = "hidden" id = "parent_id" name = "parent_id" value = "{{$parent_id}}" />
        <input type = "hidden" id = "child_id" name = "child_id" value = "{{$child_id}}" />
        <input type = "hidden" id = "child_type" name = "child_type" value = "{{$child_type}}" />
        <input type = "hidden" id = "history_type" name = "history_type" value = "{{$history_type}}" />
        
        
        @if($history_type == "se")
        	<div class = "formSection" style = "width:70%;margin:0 auto;display:block;">
					<div class = "form-group">
						<label for = "contact">Email To :</label>
						<select id = "email_to" name = "email_to" class="form-control">
							@if($parent)
								@if($parent->email1)
									<option value = "{{$parent->email1}}">{{$parent->email1}}</option>
								@endif
								@if($parent->email2)
									<option value = "{{$parent->email2}}">{{$parent->email2}}</option>
								@endif
							@endif
							@if($child_type)
								@if($child_type == "contact")
									@if($child->email1)
										<option value = "{{$child->email1}}">{{ucwords($child->firstname)}} - {{$child->email1}}</option>
									@endif
									@if($child->email2)
										<option value = "{{$child->email2}}">{{ucwords($child->firstname)}} - {{$child->email2}}</option>
									@endif						
								@else

									@foreach($parent_contacts as $contact)
										@if($contact->email1)
											<option value = "{{$contact->email1}}">{{ucwords($contact->firstname)}} - {{$contact->email1}}</option>
										@endif
										@if($contact->email2)
											<option value = "{{$contact->email2}}">{{ucwords($contact->firstname)}} - {{$contact->email2}}</option>
										@endif
									@endforeach
									@endif
							@else
								@foreach($parent_contacts as $contact)
									@if($contact->email1)
										<option value = "{{$contact->email1}}">{{ucwords($contact->firstname)}} - {{$contact->email1}}</option>
									@endif
									@if($contact->email2)
										<option value = "{{$contact->email2}}">{{ucwords($contact->firstname)}} - {{$contact->email2}}</option>
									@endif
								@endforeach
							@endif

						</select>
					</div>
					<div class = "form-group">
						<label for = "email_subject">Email Subject :</label>
						<input id = "email_subject" name = "email_subject" class = "form-control" />
					</div>
					<div class = "form-group">
						<label for = "email_attachment">Attach File From History :</label>
						<select id = "email_attachment" name = "email_attachment" class = "form-control">
							<option value = "">None</option>
							@if(is_array($parent_history))
								@foreach($parent_history as $history)
									@if($history->file !="")
										<option value = "{{$history->id}}" title = "{{$history->details}}">{{strtoupper($history->action_type)}} - {{substr($history->details,0,80)}}...</option>
									@endif
								@endforeach
							@endif
						</select>
					</div>
					<div class = "form-group">
						<label for = "custom_attachments">Attach Other Files :</label>
						<input id = "custom_attachments" name = "custom_attachments[]" type = "file" class = "form-control" multiple/>
					</div>
					<div class = "form-group">
						<label for = "email_content">Email Content :</label>
						<textarea id = "email_content" name = "email_content" class = "form-control"></textarea>
						    <script>
								CKEDITOR.replace( 'email_content' );
							</script>
					</div>
			</div>
		@endif
        

        <div class = "formSection {{$history_type}}">
			<fieldset>
				<legend>History Details</legend>
				
				<div class = "form-inline">
					<label for = "date_time">Date & Time :</label></br>
					<input style = "width:49%;" type = "date" id = "history_date" name = "history_date" value = "{{date('Y-m-d')}}" class = "form-control" readonly = "true"/>
					<input style = "width:49%;" type = "time" id = "history_time" name = "history_time" value = "{{date('H:i:s')}}" class = "form-control" readonly = "true"/>
				</div>
				<br>
				
				<div class = "form-group">
					<label for = "contact">Contact :</label>
					<select id = "history_contact" name = "history_contact" class="form-control">
						@if($child_type)
							@if($child_type == "contact")
								<option value = "{{$child->id}}">{{ucwords($child->firstname)}} {{ucwords($child->lastname)}}</option>
							
							@else
							<option value = "">None</option>
							@foreach($parent_contacts as $contact)
								<option value = "{{$contact->id}}">{{ucwords($contact->firstname)}} {{ucwords($contact->lastname)}}</option>
							@endforeach
							@endif
						@else
							<option value = "">None</option>
							@foreach($parent_contacts as $contact)
								<option value = "{{$contact->id}}">{{ucwords($contact->firstname)}} {{ucwords($contact->lastname)}}</option>
							@endforeach
						@endif
						@if(isset($bank_contacts))
							@foreach($bank_contacts as $contact)
								<option value = "{{$contact->id}}">{{ucwords($contact->firstname)}} {{ucwords($contact->lastname)}} (Bank)</option>
							@endforeach
						@endif

					</select>
				</div>
			
				<div class = "form-group">
					<label for = "history_details">Details :</label>
					<textarea id = "history_details" name = "history_details" class = "form-control" placeholder = "Please enter details in here"></textarea>
				</div>
				
				<div class = "form-group">
					<label for = "history_file">File :</label>
					<input type = "file" id = "history_file" name = "history_file" class = "form-control" />
				</div>
			
			</fieldset>
			
		</div>
		
		@if($parent_type != "product")
		<div class = "formSection {{$history_type}}dy">
			<fieldset>
				<legend>Diary Details</legend>
				
				<div class="checkbox">
				  <label><input type="checkbox" id = "schedule_diary" name = "schedule_diary" class = "{{$history_type}}_checkbox" value="schedule_diary">Schedule Diary?</label>
				</div>
				
				<div class = "form-inline">
					<label for = "diary_date_time">Diary Date & Time :</label></br>
					<input style = "width:49%;" type = "date" id = "diary_date" name = "diary_date" class = "form-control diaryInput" value = "{{date('Y-m-d')}}" readonly="true" />
					<input style = "width:49%;" type = "time" id = "diary_time" name = "diary_time" value = "{{date('H:i:s')}}" class = "form-control diaryInput" readonly = "true"/>
				</div>
				
				<div class = "form-group">
					<label for = "diary_type">Diary Type :</label>
					<select id = "diary_type" name = "diary_type" class="form-control diaryInput" readonly="true">
						<option value = "mo">Memo</option>
						<option value = "mc">Make Call</option>
						<option value = "se">Send Email</option>
						<option value = "st">Send Text</option>
						<option value = "re">Receive Email</option>
						<option value = "rt">Receive Text</option>
						<option value = "tc">Take Call</option>
					</select>
				</div>
				
				<div class = "form-group">
					<label for = "diary_Details">Diary Details :</label>
					<textarea id = "diary_details" name = "diary_details" class = "form-control diaryInput" placeholder = "Please enter details in here" readonly="true"></textarea>
				</div> 
		@endif 	
				
			
			<button class = "form-control btn btn-success styledButton">Submit</button>
			
		</fieldset>

		</div>
		
	</div>
    {{ Form::close() }}
    

</div>

	
@stop