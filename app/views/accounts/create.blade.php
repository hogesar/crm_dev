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
			var parent_price = parseFloat($("#parent_price").val());
			
			if($(this).val() != "") {
				$("#payment_amount").removeAttr("disabled");
				if($(this).val() == "deposit") {
					var percentage = parseFloat($("option:selected", this).attr("data-percent"));
					var deposit_amount = parent_price * percentage;
					$("#payment_amount").val(deposit_amount);
				} else if($(this).val() == "full") {
					$("#payment_amount").val(parent_price);
				} else {
					$("#payment_amount").val($(this).val());
				}
			} else {
				$("#payment_amount").attr("disabled","disabled");
			}
				
		
		});
		
		if($("#type").val() == "purchase_order") {
			$("#payment_direction_debit").prop("checked",true);
			$("#payment_direction_debit").button('toggle');
			$("#payment_direction_debit").parent().addClass("active");
		} else {
			$("#payment_direction_credit").prop("checked",true);
			$("#payment_direction_credit").button('toggle');
			$("#payment_direction_credit").parent().addClass("active");
		
		}
		
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
		$price = "";
		$balance = "";
		
		if($type == "purchase_order") {
			$ref = "ISFP".str_pad($type_id,6,"0",STR_PAD_LEFT);	
			$price = floatVal($parent->total_cost_price);		
		} else if($type == "proforma_invoice") {
			$ref = "ISFI".str_pad($type_id,6,"0",STR_PAD_LEFT)."P";
			$price = floatVal($parent->total_sale_price);	
		} else if($type == "invoice") {
			$ref = "ISFI".str_pad($type_id,6,"0",STR_PAD_LEFT);
			$price = floatVal($parent->total_sale_price);	
		}
		
		$balance = $price;
		
		$objectPayments = DB::table('accounts')->where('link_type',$type)->where('link_id',$type_id)->get();
		foreach($objectPayments as $payment) {
				$balance = $balance - floatVal($payment->amount);				
		}
			
		
		
			
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

        <div class = "formSection" style = "width:35%;">
			<fieldset>
				<legend>Payment Details</legend>
				
				<div class="btn-group" data-toggle="buttons" style = "text-align:center;">
				  <label class="btn btn-success styledButton">
					<input type="radio" name="payment_directions" id="payment_direction_credit" autocomplete="off" value = "in">In
				  </label>
				  <label class="btn btn-success styledButton">
					<input type="radio" name="payment_directions" id="payment_direction_debit" autocomplete="off" value = "out">Out
				  </label>
				</div>
				
				<div class = "form-group">
					<label for = "payment_date">Payment Date :</label>
					<input type = "date" id = "payment_date" name = "payment_date" value = "{{date('Y-m-d')}}" class = "form-control" />
				</div>
				
				<div class = "form-group">
					<label for = "payment_type">Payment Type :</label>
					<select id = "payment_type" name = "payment_type" class="form-control">
						<option value = "">Choose</option>
						@if(isset($balance))
							<option value = "{{$balance}}">Remaining Balance (${{$balance}})</option>
						@endif
						<option value = "full">Full (100%)</option>
						<option value = "deposit" data-percent = "0.1">Deposit (10%)</option>
						<option value = "deposit" data-percent = "0.2">Deposit (20%)</option>
						<option value = "deposit" data-percent = "0.3">Deposit (30%)</option>
						<option value = "deposit" data-percent = "0.4">Deposit (40%)</option>
						<option value = "deposit" data-percent = "0.5">Deposit (50%)</option>
						<option value = "deposit" data-percent = "0.6">Deposit (60%)</option>
						<option value = "deposit" data-percent = "0.7">Deposit (70%)</option>
						<option value = "deposit" data-percent = "0.8">Deposit (80%)</option>
						<option value = "deposit" data-percent = "0.9">Deposit (90%)</option>
						<option value = "partial">Partial</option>
					</select>
				</div>
				
				<div class="checkbox">
				  <label><input type="checkbox" value="" id = "payment_vat" name = "payment_vat">Is this payment subject to VAT?</label>
				</div>
			
				<div class = "form-group">
					<label for = "payment_amount">Amount inc VAT (if applicable) ($) :</label>
					<input type = "number" step ="0.01" id = "payment_amount" name = "payment_amount" class = "form-control" disabled = "disabled" />
				</div>
				
				<div class = "form-group">
					<label for = "payment_method">Payment Method :</label>
					<select id = "payment_method" name = "payment_method" class="form-control">
						<option value = "bacs">BACS</option>
						<option value = "cheque">Cheque</option>
						<option value = "cash">Cash</option>
						<option value = "loc">Letter of Credit</option>
					</select>
				</div>
				
				<div class = "form-group">
					<label for = "payment_file">Upload History File :</label>
					<input type = "file" id = "payment_file" name = "payment_file" class = "form-control"/>
				</div>
				
				<div class = "form-group">
					<label for = "payment_notes">Notes :</label>
					<textarea class = "form-control" id = "payment_notes" name = "payment_notes"></textarea>
				</div>
				
				<button class = "form-control btn btn-success styledButton">Submit</button>

				
				
			
			</fieldset>
			
		</div>
		<div class = "formSection" style = "width:64%;padding:10px;">
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