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
	?>

<div class="tab-content">	

	  <div id="clientbank" class="tab-pane fade in active">
	  
			<h3 style = "text-align:center;">You are associating a bank with the <b>{{ucwords($client->client_name)}}</b> file.</h3>
	
        	{{ Form::open(array('url' => 'client/bankstore', 'method' => 'POST', 'role'=>'form', 'id'=>'bank_form')) }}
        
        	<input type = "hidden" id = "client_id" name = "client_id" value = "{{$client->id}}" />

        	<div class = "form-group">
				<label for = "client_bank">Please select a bank :</label>
				<select id = "client_bank" name = "client_bank" class = "form-control">
					@foreach ($banks as $bank)
						<option value = "{{$bank->id}}">{{ucwords($bank->bank_name)}}</option>
					@endforeach
				</select>
			</div>
		
			
			<button class = "form-control btn btn-success">Submit</button>


		</div>
		
	</div>
    {{ Form::close() }}

</div>

	
@stop