@extends('layouts.structure')

@section('header')
	<script>
	$(document).ready(function() {
		$('#propertyTable').DataTable();
		$('body').find("#property_menu").addClass("active");;
		$('body').find("#properties").removeClass("collapse").addClass("collapsed");
		$('body').find("#addProp").addClass("active");
		
		$('#existing_tenant').change(function() {
		
			if($(this).val() == "") {
				 $('#tenancy_form')[0].reset();
			} else {
		
				$.post( "../../../tenant/fetchTenantById", { tenantid : $(this).val() }).done(function( data ) {
					console.log( "Data Loaded: " + data );
					var tenantarray = JSON.parse(data);
					$("#title").val(tenantarray.title);
					$("#firstname").val(tenantarray.firstname);
					$("#lastname").val(tenantarray.lastname);
					$("#phone1").val(tenantarray.phone1);
					$("#phone2").val(tenantarray.phone2);
					$("#email1").val(tenantarray.email1);
					$("#email2").val(tenantarray.email2);
					$("#address1").val(tenantarray.address1);
					$("#address2").val(tenantarray.address2);
					$("#address3").val(tenantarray.address3);
					$("#address4").val(tenantarray.address4);
					$("#postcode").val(tenantarray.postcode);
					$("#nationality").val(tenantarray.nationality);
					if(tenantarray.sms_consent == "yes") {
						$('#sms_consent').prop('checked', true);
					}
					if(tenantarray.references_received == "yes") {
						$("#references_received").prop('checked',true);
					}
				});
			}
		
		});
		
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
		
		
		$("#prop_saletype").change(function() {
			if($(this).val() == "auction") {
				$(".prop_price").text("Reserve Price :");
			} else if($(this).val() == "let") {
				$(".prop_price").text("Rent PCM :");
			} else if($(this).val() == "sell") {
				$(".prop_price").text("Asking Price :");
			}
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
	$tenants = DB::table('tenant')->get();
	?>
	<ul class="nav nav-tabs">
	  <li class="active"><a data-toggle="tab" href="#tenantdetails" target = "tenantdetails">Tenant Details</a></li>
	  <li><a data-toggle="tab" href="#tenancydetails" target = "tenancydetails">Tenancy Details</a></li>
	</ul>
	
	<div class = "buttonHolder">
		<button type = "button" class = "button_prev btn btn-danger">Prev</button>
		<button type = "button" class = "button_next btn btn-success">Next</button>
	</div>

<div class="tab-content" style = "padding:10px;">

	

	  <div id="tenantdetails" class="tab-pane fade in active">
	  	{{ Form::open(array('url' => 'property/storetenancy/'.$property->id, 'files' => true, 'role'=>'form', 'id'=>'tenancy_form')) }}
	  	<div class = "formSection">
	  	<fieldset>
	  		<legend>Tenant Details</legend>
	  		
	  		<div class = "form-group">
				<label for = "existing_tenant">Select an Existing Tenant :</label>
				<select id = "existing_tenant" name = "existing_tenant" class="form-control">
					<option value = "">Select a Tenant</option>
					@if(is_array($tenants))
						@foreach($tenants as $tenant)
							<option value = "{{$tenant->id}}">{{$tenant->firstname}} {{$tenant->lastname}}</option>
						@endforeach
					@endif
				</select>
				<br>
				<label>Or create a new one now :</label>
			</div>
			
			<div class = "form-group">
				<label for = "title">Title :</label>
				<select id = "title" name = "title" class="form-control">
					<option value = "Mr">Mr</option>
					<option value = "Mrs">Mrs</option>
					<option value = "Ms">Ms</option>
					<option value = "Miss">Miss</option>
					<option value = "Dr">Dr</option>
				</select>
			</div>
			
			<div class = "form-group">
				<label for = "firstname">Firstname :</label>
				<input type = "text" id = "firstname" name = "firstname" class = "form-control" />
			</div>
			
			<div class = "form-group">
				<label for = "lastname">Lastname :</label>
				<input type = "text" id = "lastname" name = "lastname" class = "form-control" />
			</div>
			
			<div class = "form-group">
				<label for = "phone1">Phone 1 :</label>
				<input type = "text" id = "phone1" name = "phone1" class = "form-control" />
			</div>
			
			<div class = "form-group">
				<label for = "phone2">Phone 2 :</label>
				<input type = "text" id = "phone2" name = "phone2" class = "form-control" />
			</div>

			<div class = "form-group">
				<label for = "email1">Email 1 :</label>
				<input type = "email" id = "email1" name = "email1" class = "form-control" />
			</div>
			
			<div class = "form-group">
				<label for = "email2">Email 2 :</label>
				<input type = "text" id = "email2" name = "email2" class = "form-control" />
			</div>
			
		</fieldset>
			
		</div>
		<div class = "formSection">
	  	
	  	<fieldset>
	  		<legend>Tenant Address</legend>
			<div class = "form-group">
				<label for = "postcode">Postcode :</label>
				<div id="postcode_lookup"></div>
			</div>
		
		
			<div class = "form-group">
				<label for = "address1">Address 1 :</label>
				<input type = "text" id = "address1" name = "address1" class = "form-control" />
			</div>

			<div class = "form-group">
				<label for = "address2">Address 2 :</label>
				<input type = "text" id = "address2" name = "address2" class = "form-control" />
			</div>

			<div class = "form-group">
				<label for = "address3">Town :</label>
				<input type = "text" id = "address3" name = "address3" class = "form-control" />
			</div>

			<div class = "form-group">
				<label for = "address4">County :</label>
				<input type = "text" id = "address4" name = "address4" class = "form-control" />
			</div>
			
			<div class = "form-group">
				<label for = "postcode">Postcode :</label>
				<input type = "text" id = "postcode" name = "postcode" class = "form-control" />
			</div>
			
			<div class = "form-group">
				<label for = "nationality">Nationality :</label>
				<select id = "nationality" name = "nationality" class = "form-control">
						<option value = "British">British</option>
					@foreach ($nationalities as $nationality)
						<option value = "{{$nationality->nationality}}">{{$nationality->nationality}}</option>
					@endforeach
				</select>
			</div>
			
			<div class = "form-group">
				<label for = "checkbox">Tick where applies :</label>
				<div class="checkbox">
						<label style = "width:120px;font-size:13px;"><input type="checkbox" name = "tenantoptions[]" id = "sms_consent" value="sms_consent">Consents to SMS?</label>
						<label style = "width:120px;font-size:13px;"><input type="checkbox" name = "tenantoptions[]" id = "references_received" value="references_received">References received?</label>
				</div>
			</div>
		</fieldset>

		</div>
		
	</div>
	
	<div id="tenancydetails" class="tab-pane fade">
		<div class = "formSection">
			<fieldset>
				<legend>Tenancy Details</legend>
				
				<div class = "form-group">
					<label for = "start_date">Start Date :</label>
					<input type = "date" id = "start_date" name = "start_date" class = "form-control" />
				</div>
				
				<div class = "form-group">
					<label for = "end_date">End Date :</label>
					<input type = "date" id = "end_date" name = "end_date" class = "form-control" />
				</div>
				
				<div class = "form-group">
					<label for = "rent_amount">Rent: (&pound; Per Month) :</label>
					<input type = "number" id = "rent_amount" name = "rent_amount" class = "form-control" />
				</div>
				
				<div class = "form-group">
					<label for = "deposit_amount">Deposit: (&pound; Per Month) :</label>
					<input type = "number" id = "deposit_amount" name = "deposit_amount" class = "form-control" />
				</div>
				
				<button class = "form-control btn btn-success">Submit</button>
	</div>
	{{ Form::close() }}
</div>

	
@stop