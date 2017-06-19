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
		
		$("#payment_type").change(function() {
			if($(this).val() != "") {
				$("#payment_amount").removeAttr("disabled");
			} else {
				$("#payment_amount").attr("disabled","disabled");
			}
				
		
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
	if($type) {
		$parent = DB::table($type)->where('id',$type_id)->first();
		
		if($type == "purchase_order") {
			$ref = "ISFP".str_pad($type_id,6,"0",STR_PAD_LEFT);			
		} else if($type == "proforma_invoice") {
			$ref = "ISFI".str_pad($type_id,6,"0",STR_PAD_LEFT)."P";
		} else if($type == "invoice") {
			$ref = "ISFI".str_pad($type_id,6,"0",STR_PAD_LEFT);
		}
		
		$price = $parent->total_sale_price;	
			
		$related_history = DB::table('history')->where('child_id',$parent->deal_id)->where('child_type',"deal")->where('details','LIKE',$ref."%")->first();
		
	} else {
		$parent = null;
	}
	?>

<div class="tab-content">	

	  <div id="clientdetails" class="tab-pane fade in active">
	  	
	  
		@if ($type)
			<h3 style = "text-align:center;">You are adding a payment to the <b>{{ucwords($ref)}}</b> file.</h3>
			<input type = "hidden" id = "parent_price" name = "parent_price" value = "{{$price}}" />
		@endif
		
        {{ Form::open(array('route' => 'accounts.store', 'files' => true, 'role'=>'form', 'id'=>'accounts_form')) }}
        
        <input type = "hidden" id = "type" name = "type" value = "{{$type}}" />
        <input type = "hidden" id = "type_id" name = "type_id" value = "{{$type_id}}" />

        <div class = "formSection" style = "width:30%;">
			<fieldset>
				<legend>Payment Details</legend>
				
				<div class = "form-group">
					<label for = "payment_date">Payment Date :</label>
					<input type = "date" id = "payment_date" name = "payment_date" value = "{{date('Y-m-d')}}" class = "form-control" />
				</div>
				
				<div class = "form-group">
					<label for = "payment_type">Payment Type :</label>
					<select id = "payment_type" name = "payment_type" class="form-control">
						<option value = "">Choose</option>
						<option value = "full">Full</option>
						<option value = "deposit">Deposit</option>
						<option value = "partial">Partial</option>
					</select>
				</div>
				
				<div class="checkbox">
				  <label><input type="checkbox" value="" id = "payment_vat" name = "payment_vat">Is this payment subject to VAT?</label>
				</div>
			
				<div class = "form-group">
					<label for = "payment_amount">Amount ($):</label>
					<input type = "number" step ="0.01" id = "payment_amount" name = "payment_amount" class = "form-control" disabled = "disabled" />
				</div>
				

				
				
			
			</fieldset>
			
		</div>
		<div class = "formSection" style = "width:68%;padding:10px;">
			@if(is_object($parent))
				@if($related_history)
					<object width = "100%" height = "100%" type = "application/pdf" data="/data/{{$related_history->parent_type}}/{{$related_history->parent_id}}/{{$related_history->child_type}}/{{$related_history->child_id}}/{{$related_history->file}}">
						<p>Sorry, couldn't display PDF. Please inform IT.</p>
					</object>
				@endif
			@endif
		</div>
		
	</div>
    {{ Form::close() }}

</div>

	
@stop