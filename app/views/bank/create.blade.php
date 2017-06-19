@extends('layouts.structure')

@section('header')
	<script>
	$(document).ready(function() {
		$('body').find("#bank_menu").addClass("active");;
		$('body').find("#bank").removeClass("collapse").addClass("collapsed");
		$('body').find("#addbank").addClass("active");
		
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
	?>

<div class="tab-content">	

	  <div id="bankdetails" class="tab-pane fade in active">
	  
	  {{ Form::open(array('route' => 'bank.store', 'files' => false, 'role'=>'form', 'id'=>'bank_form')) }}
        
        <div class = "formSection">
			<fieldset>
				<legend>Bank Details</legend>
				
				<div class = "form-group">
					<label for = "bank_title">Bank Name :</label>
					<input type = "text" id = "bank_name" name = "bank_name" class = "form-control" />
				</div>
			
				<div class = "form-group">
					<label for = "bank_nationality">Nationality :</label>
					<select id = "bank_nationality" name = "bank_nationality" class = "form-control">
						@foreach ($nationalities as $nationality)
							<option value = "{{$nationality->nationality}}">{{$nationality->nationality}}</option>
						@endforeach
					</select>
				</div>
			
				<div class = "form-group">
					<label for = "bank_website">Website :</label>
					<input type = "text" id = "bank_website" name = "bank_website" class = "form-control" />
				</div>
				
				<div class = "form-group">
					<label for = "bank_address1">Address 1 :</label>
					<input type = "text" id = "bank_address1" name = "bank_address1" class = "form-control" />
				</div>
		
				<div class = "form-group">
					<label for = "bank_address2">Address 2 :</label>
					<input type = "text" id = "bank_address2" name = "bank_address2" class = "form-control" />
				</div>

				<div class = "form-group">
					<label for = "bank_address3">Address 3 :</label>
					<input type = "text" id = "bank_address3" name = "bank_address3" class = "form-control" />
				</div>
		
				<div class = "form-group">
					<label for = "bank_address4">Address 4 :</label>
					<input type = "text" id = "bank_address4" name = "bank_address4" class = "form-control" />
				</div>
				
				<div class = "form-group">
					<label for = "bank_postcode">Postcode :</label>
					<input type = "text" id = "bank_postcode" name = "bank_postcode" class = "form-control" />
				</div>
			
			
			</fieldset>
			
		</div>
		<div class = "formSection">
			<fieldset>
				<legend>&nbsp;</legend>
		
				<div class = "form-group">
					<label for = "bank_phone1">Phone 1 :</label>
					<input type = "text" id = "bank_phone1" name = "bank_phone1" class = "form-control" />
				</div>
		
				<div class = "form-group">
					<label for = "bank_phone2">Phone 2 :</label>
					<input type = "text" id = "bank_phone2" name = "bank_phone2" class = "form-control" />
				</div>

				<div class = "form-group">
					<label for = "bank_email1">Email 1 :</label>
					<input type = "email" id = "bank_email1" name = "bank_email1" class = "form-control" />
				</div>
		
				<div class = "form-group">
					<label for = "bank_email2">Email 2 :</label>
					<input type = "text" id = "bank_email2" name = "bank_email2" class = "form-control" />
				</div>
				
				<div class = "form-group">
					<label for = "bank_swiftcode">Swift Code :</label>
					<input type = "text" id = "bank_swiftcode" name = "bank_swiftcode" class = "form-control" />
				</div>
				
				<div class = "form-group">
					<label for = "bank_sortcode">Sort Code :</label>
					<input type = "text" id = "bank_sortcode" name = "bank_sortcode" class = "form-control" />
				</div>
				
				<div class = "form-group">
					<label for = "bank_loc">Letter of Credit Relationship :</label>
					<select id = "bank_loc" name = "bank_loc" class = "form-control">
						<option value = "no">No</option>
						<option value = "yes">Yes</option>
					</select>
				</div>
				
			
			<button class = "form-control btn btn-success">Submit</button>
			
		</fieldset>

		</div>
		
	</div>
    {{ Form::close() }}

</div>

	
@stop