@extends('layouts.structure')

@section('header')
	<script>
	$(document).ready(function() {
		//var historyTable = $('#diaryTable').DataTable();
		//historyTable.page('last').draw('page');
		
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
	//grab the parent object (this is a client, supplier or bank)
	$parent = DB::table($diary->parent_type)->where('id',$diary->parent_id)->first();
	//grab the contacts for the parent
	$parent_contacts = DB::table('contact')->where('contact_type',$diary->parent_type)->where('type_id',$diary->parent_id)->get();
	//history type is whatever was defined in the diary
	$history_type = $diary->action_type;
	//parent type and id is whatever was defined in the diary
	$parent_type = $diary->parent_type;
	$parent_id = $diary->parent_id;
	//history item
	$history = DB::table('history')->where('diary_id',$diary->id)->first();
	
	$datetime = date('Y-m-d H:i:s');
	?>

<div class="tab-content">	

	  <div id="historydetails" class="tab-pane fade in active">
	  	@if ($parent_type == "client")
			<h3 style = "text-align:center;">You are completing a diary ({{strtoupper($history_type)}}) on the <b>{{ucwords($parent->client_name)}}</b> file.</h3>
		@elseif ($parent_type == "supplier")
			<h3 style = "text-align:center;">You are completing a diary ({{strtoupper($history_type)}}) on the <b>{{ucwords($parent->supplier_name)}}</b> file.</h3>
		@elseif ($parent_type == "bank")
			<h3 style = "text-align:center;">You are completing a diary ({{strtoupper($history_type)}}) on the <b>{{ucwords($parent->bank_name)}}</b> file.</h3>
		@endif
		
		<?php
		$diaryDate = explode(" ",$diary->date);
		$diaryTime = $diaryDate[1];
		$diaryDate = date("d/m/y", strtotime($diaryDate[0]));
		?>	
		
		<div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
		  <div class="panel panel-default">
			<div class="panel-heading" role="tab" id="headingOne">
			  <h4 class="panel-title">
				<a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
				  Diary Item
				</a>
			  </h4>
			</div>
			<div id="collapseOne" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne">
			  <div class="panel-body">
			  	<div class = "fullwidth_container">
					<table id="diaryTable" class = "table table-striped" cellspacing="0" width="100%">
						<tbody>
							<tr>
								<th>{{ucfirst($parent_type)}}</th>
								<td>
								<a href = "/{{$parent_type}}/{{$parent->id}}">
									@if ($parent_type == "client")
										{{ucwords($parent->client_name)}}
									@elseif ($parent_type == "supplier")
										{{ucwords($parent->supplier_name)}}
									@elseif ($parent_type == "bank")
										{{ucwords($parent->bank_name)}}
									@endif
								</a>
								</td>
							</tr>
							<tr>
								<th>Diary Date</th>
								<td>{{$diaryDate." ".$diaryTime}}</td>
							</tr>
							<tr>
								<th>Details</th>
								<td>{{$diary->details}}</td>
							</tr>
							<tr>
								<th>Scheduled By</th>
								<td>{{ucfirst($diary->user)}}</td>
							</tr>
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
        <input type = "hidden" id = "history_type" name = "history_type" value = "{{$history_type}}" />
        <input type = "hidden" id = "diary_id" name = "diary_id" value = "{{$diary->id}}" />
        <input type = "hidden" id = "child_type" name = "child_type" value = "{{$diary->child_type}}" />
        <input type = "hidden" id = "child_id" name = "child_id" value = "{{$diary->child_id}}" />

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
						@if(isset($child_type))
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
		<div class = "formSection">
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
				
			
			<button class = "form-control btn btn-success">Submit</button>
			
		</fieldset>

		</div>
		
	</div>
    {{ Form::close() }}

</div>

	
@stop