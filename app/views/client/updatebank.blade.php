@extends('layouts.structure')

@section('header')
	<script>
	$(document).ready(function() {
		$('body').find("#client_menu").addClass("active");;
		$('body').find("#client").removeClass("collapse").addClass("collapsed");
		$('body').find("#client").addClass("active");
		
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
		
		$('#bank_form :input:enabled:visible:first').focus();
		
		
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
	
	//empty placeholders to make form values work even if no results
	$account_name = "";
	$iban_number = "";
	$account_number = "";
	
	//if client has bank details already bring them in
	$client_bank_details = DB::table('client_bank_details')->where('client_id',$client->id)->first();
	
	if(is_object($client_bank_details)) {
		$account_name = $client_bank_details->account_name;
		$iban_number = $client_bank_details->iban_number;
		$account_number = $client_bank_details->account_number;
	}
	
	
	
	
	
	
	//if client has bank set that as bank
	$client_bank = DB::table('bank')->where('id',$client->bank_id)->first();
	?>

<div class="tab-content">	

	  <div id="clientbank" class="tab-pane fade in active">
	  
			<h3 style = "text-align:center;">You are associating a bank with the <b>{{ucwords($client->client_name)}}</b> file.</h3>
			
			<p>If the client's bank is not listed, please add it first by <a href = "/bank/create">clicking here</a>.</p>
	
        	{{ Form::open(array('url' => 'client/bankstore', 'method' => 'POST', 'role'=>'form', 'id'=>'bank_form')) }}
        
        	<input type = "hidden" id = "client_id" name = "client_id" value = "{{$client->id}}" />

        	<div class = "form-group">
				<label for = "client_bank">Please select a bank :</label>
				<select id = "client_bank" name = "client_bank" class = "form-control">
					@if(is_object($client_bank))
						<option value = "{{$client_bank->id}}">{{ucwords($client_bank->bank_name." - ".$client_bank->nationality)}}</option>
					@endif
					@foreach ($banks as $bank)
						<option value = "{{$bank->id}}">{{ucwords($bank->bank_name." - ".$bank->nationality)}}</option>
					@endforeach
				</select>
			</div>
			
        	<div class = "form-group">
				<label for = "client_bank">Account Name / Holder :</label>
				<input class = "form-control" id = "account_name" name = "account_name" value = "{{$account_name}}" />
			</div>
			
        	<div class = "form-group">
				<label for = "client_bank">IBAN Number :</label>
				<input class = "form-control" id = "iban_number" name = "iban_number" value = "{{$iban_number}}"/>
			</div>
			
        	<div class = "form-group">
				<label for = "client_bank">Account Number :</label>
				<input class = "form-control" id = "account_number" name = "account_number" value = "{{$account_number}}" />
			</div>
		
			
			<button class = "form-control btn btn-success">Submit</button>


		</div>
		
	</div>
    {{ Form::close() }}

</div>

	
@stop