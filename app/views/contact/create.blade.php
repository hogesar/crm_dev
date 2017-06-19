@extends('layouts.structure')

@section('header')
	<script>
	$(document).ready(function() {
		$('#propertyTable').DataTable();
		$('body').find("#contact_menu").addClass("active");;
		$('body').find("#contact").removeClass("collapse").addClass("collapsed");
		$('body').find("#addcontact").addClass("active");
		
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
		
		$('#contact_form :input:enabled:visible:first').focus();
		
		
	});
	</script>
	<style>
	
	
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
	$nationalities = DB::table('nationality')->get();
	?>

<div class="tab-content">	

	  <div id="clientdetails" class="tab-pane fade in active">
	  
		@if ($type == "client")
			<h3 style = "text-align:center;">You are adding a contact to the <b>{{ucwords($parent->client_name)}}</b> file.</h3>
		@elseif ($type == "supplier")
			<h3 style = "text-align:center;">You are adding a contact to the <b>{{ucwords($parent->supplier_name)}}</b> file.</h3>
		@elseif ($type == "bank")
			<h3 style = "text-align:center;">You are adding a contact to the <b>{{ucwords($parent->bank_name)}}</b> file.</h3>
		@endif
		
        {{ Form::open(array('route' => 'contact.store', 'files' => true, 'role'=>'form', 'id'=>'contact_form')) }}
        
        <input type = "hidden" id = "type" name = "type" value = "{{$type}}" />
        <input type = "hidden" id = "type_id" name = "type_id" value = "{{$parent->id}}" />

        <div class = "formSection">
			<fieldset>
				<legend>Contact Details</legend>
				
				<div class = "form-group">
					<label for = "contact_title">Contact Title :</label>
					<select id = "contact_title" name = "contact_title" class="form-control">
						<option value = "mr">Mr</option>
						<option value = "mrs">Mrs</option>
						<option value = "miss">Miss</option>
						<option value = "ms">Ms</option>
						<option value = "dr">Dr</option>
					</select>
				</div>
			
				<div class = "form-group">
					<label for = "contact_firstname">Firstname :</label>
					<input type = "text" id = "contact_firstname" name = "contact_firstname" class = "form-control" />
				</div>
				
				<div class = "form-group">
					<label for = "contact_lastname">Lastname :</label>
					<input type = "text" id = "contact_lastname" name = "contact_lastname" class = "form-control" />
				</div>
			
				<div class = "form-group">
					<label for = "contact_nationality">Nationality :</label>
					<select id = "contact_nationality" name = "contact_nationality" class = "form-control">
						@foreach ($nationalities as $nationality)
							<option value = "{{$nationality->nationality}}">{{$nationality->nationality}}</option>
						@endforeach
					</select>
				</div>
			
				<div class = "form-group">
					<label for = "contact_position">Job Position :</label>
					<input type = "text" id = "contact_position" name = "contact_position" class = "form-control" />
				</div>
				
				<div class = "form-group">
					<label for = "contact_address1">Address 1 :</label>
					<input type = "text" id = "contact_address1" name = "contact_address1" class = "form-control" />
				</div>
		
				<div class = "form-group">
					<label for = "contact_address2">Address 2 :</label>
					<input type = "text" id = "contact_address2" name = "contact_address2" class = "form-control" />
				</div>

				<div class = "form-group">
					<label for = "contact_address3">Address 3 :</label>
					<input type = "text" id = "contact_address3" name = "contact_address3" class = "form-control" />
				</div>
		
				<div class = "form-group">
					<label for = "contact_address4">Address 4 :</label>
					<input type = "text" id = "contact_address4" name = "contact_address4" class = "form-control" />
				</div>
				
				<div class = "form-group">
					<label for = "contact_postcode">Postcode :</label>
					<input type = "text" id = "contact_postcode" name = "contact_postcode" class = "form-control" />
				</div>
			
			
			</fieldset>
			
		</div>
		<div class = "formSection">
			<fieldset>
				<legend>&nbsp;</legend>
		
				<div class = "form-group">
					<label for = "contact_phone1">Phone 1 :</label>
					<input type = "text" id = "contact_phone1" name = "contact_phone1" class = "form-control" />
				</div>
		
				<div class = "form-group">
					<label for = "contact_phone2">Phone 2 :</label>
					<input type = "text" id = "contact_phone2" name = "contact_phone2" class = "form-control" />
				</div>

				<div class = "form-group">
					<label for = "contact_email1">Email 1 :</label>
					<input type = "email" id = "contact_email1" name = "contact_email1" class = "form-control" />
				</div>
		
				<div class = "form-group">
					<label for = "contact_email2">Email 2 :</label>
					<input type = "text" id = "contact_email2" name = "contact_email2" class = "form-control" />
				</div>
				
				<div class = "form-group">
					<label for = "contact_skype">Skype :</label>
					<input type = "text" id = "contact_skype" name = "contact_skype" class = "form-control" />
				</div>
		
				<div class = "form-group">
					<label for = "contact_whatsapp">Whatsapp :</label>
					<input type = "text" id = "contact_whatsapp" name = "contact_whatsapp" class = "form-control" />
				</div>
	  	
				<div class = "form-group">
					<label for = "contact_image">Image :</label>
					<input type = "file" id = "contact_image" name = "contact_image" class = "form-control" />
				</div>
			
			<button class = "form-control btn btn-success">Submit</button>
			
		</fieldset>

		</div>
		
	</div>
    {{ Form::close() }}

</div>

	
@stop