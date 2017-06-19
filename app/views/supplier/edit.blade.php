@extends('layouts.structure')

@section('header')
	<script>
	$(document).ready(function() {
		$('#propertyTable').DataTable();
		$('body').find("#supplier_menu").addClass("active");;
		$('body').find("#supplier").removeClass("collapse").addClass("collapsed");
		$('body').find("#addsupplier").addClass("active");
		
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

	  <div id="supplierdetails" class="tab-pane fade in active">
	  	  <h3 style = "text-align:center;">You are amending the supplier <b>{{ucwords($supplier->supplier_name)}}</b> file.</h3>

          {{ Form::open(array('route' => ['supplier.update',$supplier->id], 'method' => 'PUT', 'files' => true, 'role'=>'form', 'id'=>'supplier_form')) }}

          <div class = "formSection">
	  	<fieldset>
	  		<legend>Supplier Details</legend>
	  		
	  		<div class = "form-group">
				<label for = "supplier_name">Supplier Name :</label>
				<input type = "text" id = "supplier_name" name = "supplier_name" class = "form-control" value = "{{$supplier->supplier_name}}" />
			</div>
			
			<div class = "form-group">
				<label for = "supplier_type">Supplier Type / Industry :</label>
				<select id = "supplier_type" name = "supplier_type" class="form-control">
					<option value = "{{$supplier->supplier_type}}">{{ucwords($supplier->supplier_type)}}</option>
					<option value = "food">Food Distribution</option>
					<option value = "restaurant">Restaurant</option>
					<option value = "misc">Misc</option>
				</select>
			</div>
			
			<div class = "form-group">
				<label for = "supplier_nationality">Nationality :</label>
				<select id = "supplier_nationality" name = "supplier_nationality" class = "form-control">
					<option value = "{{$supplier->nationality}}">{{$supplier->nationality}}</option>
					@foreach ($nationalities as $nationality)
						<option value = "{{$nationality->nationality}}">{{$nationality->nationality}}</option>
					@endforeach
				</select>
			</div>
			
			<div class = "form-group">
				<label for = "supplier_number">Supplier Company Number :</label>
				<input type = "text" id = "supplier_number" name = "supplier_number" class = "form-control" value = "{{$supplier->company_number}}" />
			</div>
			
			<div class = "form-group">
				<label for = "supplier_website">Supplier Website :</label>
				<input type = "text" id = "supplier_website" name = "supplier_website" class = "form-control" value = "{{$supplier->website}}" />
			</div>
			
			<div class = "form-group">
				<label for = "supplier_phone1">Phone 1 :</label>
				<input type = "text" id = "supplier_phone1" name = "supplier_phone1" class = "form-control" value = "{{$supplier->phone1}}" />
			</div>
			
			<div class = "form-group">
				<label for = "supplier_phone2">Phone 2 :</label>
				<input type = "text" id = "supplier_phone2" name = "supplier_phone2" class = "form-control" value = "{{$supplier->phone2}}" />
			</div>

			<div class = "form-group">
				<label for = "supplier_email1">Email 1 :</label>
				<input type = "email" id = "supplier_email1" name = "supplier_email1" class = "form-control" value = "{{$supplier->email1}}" />
			</div>
			
			<div class = "form-group">
				<label for = "supplier_email2">Email 2 :</label>
				<input type = "text" id = "supplier_email2" name = "supplier_email2" class = "form-control" value = "{{$supplier->email2}}" />
			</div>
			
		</fieldset>
			
		</div>
		<div class = "formSection">
	  	
	  	<fieldset>
	  		<legend>Supplier Address</legend>
		
		
			<div class = "form-group">
				<label for = "supplier_address1">Address 1 :</label>
				<input type = "text" id = "supplier_address1" name = "supplier_address1" class = "form-control" value = "{{$supplier->address1}}" />
			</div>

			<div class = "form-group">
				<label for = "supplier_address2">Address 2 :</label>
				<input type = "text" id = "supplier_address2" name = "supplier_address2" class = "form-control" value = "{{$supplier->address2}}" />
			</div>

			<div class = "form-group">
				<label for = "supplier_address3">Address 3 :</label>
				<input type = "text" id = "supplier_address3" name = "supplier_address3" class = "form-control" value = "{{$supplier->address3}}" />
			</div>

			<div class = "form-group">
				<label for = "supplier_address4">Address 4 :</label>
				<input type = "text" id = "supplier_address4" name = "supplier_address4" class = "form-control" value = "{{$supplier->address4}}" />
			</div>
			
			<div class = "form-group">
				<label for = "supplier_postcode">Postcode :</label>
				<input type = "text" id = "supplier_postcode" name = "supplier_postcode" class = "form-control" value = "{{$supplier->postcode}}" />
			</div>
			
			<div class = "form-group">
				<label for = "supplier_status">Supplier Status :</label>
				<select id = "supplier_status" name = "supplier_status" class="form-control">
					<option value = "{{$supplier->status}}">{{ucwords($supplier->status)}}</option>
					<option value = "lead">Lead</option>
					<option value = "prospect">Prospect</option>
					<option value = "supplier">Supplier</option>
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