@extends('layouts.structure')

@section('header')
	<script>
	$(document).ready(function() {
		$('body').find("#client_menu").addClass("active");;
		$('body').find("#client").removeClass("collapse").addClass("collapsed");
		$('body').find("#addclient").addClass("active");
		
		$('#postcode_lookup').getAddress({
			//api_key: 'OhmPLAFXXUyTawOjrO-8og1374', 
			api_key: 'xt1Tv8-EwkyBB67aFT0jjQ1600',
			output_fields:{
				line_1: '#address1',
				line_2: '#address2',
				line_3: '',
				post_town: '#address3',
				county: '#address4',
				postcode: '#postcode'
			},                                                                                                         
			onLookupSuccess: function(data){
				$("#postcode_lookup").find("select").addClass("form-control");
				},
			onLookupError: function(){/* Your custom code */},
			onAddressSelected: function(elem,index){/* Your custom code */}
		});
		
		$("#postcode_lookup").find("input").addClass("form-control");
		
		 $("#multipleupload").uploadFile({
			url:"/packages/fileupload/upload.php?property="+$("#address1").val(),
			multiple:true,
			dragDrop:true,
			fileName:"propertyFile",
			formData: {property: $("#address1").val()},
			onSuccess:function(files,data,xhr,pd)
				{
					for(i=0;i<files.length;i++) {
						var sortHTML = $("#sortable").html();
						sortHTML = sortHTML + '<li class="ui-state-default" style = "background-image:url(\'/images/sort_both.png\');">1</li>';
					}
				},
		}); 
		
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
		
		$('#property_form :input:enabled:visible:first').focus();
		
		
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
	$banks = DB::table('bank')->get();
	?>

<div class="tab-content">	

	  <div id="clientdetails" class="tab-pane fade in active">
	  
	  	<h3 style = "text-align:center;">You are amending the client <b>{{ucwords($client->client_name)}}</b> file.</h3>
          {{ Form::open(array('route' => ['client.update', $client->id], 'method' => 'PUT', 'files' => true, 'role'=>'form', 'id'=>'client_form')) }}

          <div class = "formSection">
	  	<fieldset>
	  		<legend>Client Details</legend>
	  			  		
	  		<div class = "form-group">
				<label for = "client_name">Client Name :</label>
				<input type = "text" id = "client_name" name = "client_name" class = "form-control" value = "{{$client->client_name}}" />
			</div>
			
			<div class = "form-group">
				<label for = "client_type">Client Type / Industry :</label>
				<select id = "client_type" name = "client_type" class="form-control">
					<option value = "{{$client->client_type}}">{{ucwords($client->client_type)}}</option>
					<option value = "food">Food Distribution</option>
					<option value = "restaurant">Restaurant</option>
					<option value = "misc">Misc</option>
				</select>
			</div>
			
			<div class = "form-group">
				<label for = "client_nationality">Country :</label>
				<select id = "client_nationality" name = "client_nationality" class = "form-control">
					<option value = "{{$client->nationality}}">{{$client->nationality}}</option>
					@foreach ($nationalities as $nationality)
						<option value = "{{$nationality->nationality}}">{{$nationality->nationality}}</option>
					@endforeach
				</select>
			</div>
			
			<div class = "form-group">
				<label for = "client_number">Client Company Number :</label>
				<input type = "text" id = "client_number" name = "client_number" class = "form-control" value = "{{$client->company_number}}" />
			</div>
			
			<div class = "form-group">
				<label for = "client_website">Client Website :</label>
				<input type = "text" id = "client_website" name = "client_website" class = "form-control" value = "{{$client->website}}" />
			</div>
			
			<div class = "form-group">
				<label for = "client_phone1">Phone 1 :</label>
				<input type = "text" id = "client_phone1" name = "client_phone1" class = "form-control" value = "{{$client->phone1}}" />
			</div>
			
			<div class = "form-group">
				<label for = "client_phone2">Phone 2 :</label>
				<input type = "text" id = "client_phone2" name = "client_phone2" class = "form-control" value = "{{$client->phone2}}" />
			</div>

			<div class = "form-group">
				<label for = "client_email1">Email 1 :</label>
				<input type = "email" id = "client_email1" name = "client_email1" class = "form-control" value = "{{$client->email1}}" />
			</div>
			
			<div class = "form-group">
				<label for = "client_email2">Email 2 :</label>
				<input type = "email" id = "client_email2" name = "client_email2" class = "form-control" value = "{{$client->email2}}" />
			</div>
			
		</fieldset>
			
		</div>
		<div class = "formSection">
	  	
	  	<fieldset>
	  		<legend>Client Address</legend>
		
		
			<div class = "form-group">
				<label for = "client_address1">Address 1 :</label>
				<input type = "text" id = "client_address1" name = "client_address1" class = "form-control" value = "{{$client->address1}}" />
			</div>

			<div class = "form-group">
				<label for = "client_address2">Address 2 :</label>
				<input type = "text" id = "client_address2" name = "client_address2" class = "form-control" value = "{{$client->address2}}" />
			</div>

			<div class = "form-group">
				<label for = "client_address3">Address 3 :</label>
				<input type = "text" id = "client_address3" name = "client_address3" class = "form-control" value = "{{$client->address3}}" />
			</div>

			<div class = "form-group">
				<label for = "client_address4">Address 4 :</label>
				<input type = "text" id = "client_address4" name = "client_address4" class = "form-control" value = "{{$client->address4}}" />
			</div>
			
			<div class = "form-group">
				<label for = "client_postcode">Postcode :</label>
				<input type = "text" id = "client_postcode" name = "client_postcode" class = "form-control" value = "{{$client->postcode}}" />
			</div>
			
			<div class = "form-group">
				<label for = "client_status">Client Status :</label>
				<select id = "client_status" name = "client_status" class="form-control">
					<option value = "{{$client->status}}">{{ucfirst($client->status)}}</option>
					<option value = "lead">Lead</option>
					<option value = "prospect">Prospect</option>
					<option value = "client">Client</option>
					<option value = "dead">Dead</option>
				</select>
			</div>
			
			<button class = "form-control btn btn-success">Submit</button>
			
		</fieldset>

		</div>
		
	</div>
    {{ Form::close() }}

</div>

	
@stop