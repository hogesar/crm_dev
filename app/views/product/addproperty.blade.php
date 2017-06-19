@extends('layouts.structure')

@section('header')
	<script>
	$(document).ready(function() {
		$('#propertyTable').DataTable();
		$('body').find("#property_menu").addClass("active");;
		$('body').find("#properties").removeClass("collapse").addClass("collapsed");
		$('body').find("#addProp").addClass("active");
		
		$('#postcode_lookup').getAddress({
			//api_key: 'OhmPLAFXXUyTawOjrO-8og1374', 
			api_key: 'flWtf6av_06hffcjNnSWmg1878',
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
		
		$( "#sortable" ).sortable();
    	$( "#sortable" ).disableSelection();
		
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
	<ul class="nav nav-tabs">
	  <li class="active"><a data-toggle="tab" href="#details" target = "details">Details</a></li>
	  <!--<li><a data-toggle="tab" href="#documents" target = "documents">Documents / Media</a></li>-->
	  <li><a data-toggle="tab" href="#marketing" target = "marketing">Pricing & Marketing</a></li>
	</ul>
	
	<div class = "buttonHolder">
		<button type = "button" class = "button_prev btn btn-danger">Prev</button>
		<button type = "button" class = "button_next btn btn-success">Next</button>
	</div>
	
<!--OPEN THE FORM -->
{{ Form::open(array('route' => 'property.store', 'files' => true, 'role'=>'form', 'id'=>'property_form')) }}

<div class="tab-content" style = "padding:10px;">

	  <div id="details" class="tab-pane fade in active">
	  	
	  	<div class = "formSection">
	  	<fieldset>
	  		<legend>Address Details</legend>
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
		</fieldset>

		</div>
		
		<div class = "formSection">
	  	<fieldset>
	  		<legend>Property Details</legend>
		
			<div class = "form-group">
				<label for = "prop_sector">Property Sector :</label>
				<select id = "prop_sector" name = "prop_sector" class = "form-control">
					<option value = "">Choose</option>
					@foreach($values_property_sectors as $propertysector)
						<option value = "{{$propertysector->value}}">{{ucfirst($propertysector->value)}}</option>
					@endforeach
				</select>
			</div>
			

			<div class = "form-group">
				<label for = "prop_type">Property Type :</label>
				<select id = "prop_type" name = "prop_type" class = "form-control">
					<option value = "">Choose</option>
					@foreach($values_property_types as $propertytypes)
						<option value = "{{$propertytypes->value}}">{{ucfirst($propertytypes->value)}}</option>
					@endforeach
				</select>
			</div>

			<div class = "form-group">
				<label for = "checkbox">Tick where applies :</label>
				<div class="checkbox">
					@foreach($values_property_features as $propertyfeature)
						<label style = "width:120px;font-size:13px;"><input type="checkbox" name = "propertyfeatures[]" value="{{$propertyfeature->value}}" feature_id = "{{$propertyfeature->id}}">{{ucfirst($propertyfeature->value)}}</label>
					@endforeach
				</div>
			</div>

			<div class = "form-group">
				<button class = "form-control button_next btn btn-success">Next</button>
			</div>
		</fieldset>

		</div>
	  </div>
	  
	<!--  <div id="documents" class="tab-pane fade">
	  
	  	<br></br>
	  	This is easy! Simply click the browse button, or drag and drop files into the box below to upload them! Make sure you
	  	upload everything - floorplans, photos, documents...you can sort them all <b>once</b> they're uploaded. <br></br>
		
		<div class = "formSection">
			<div id = "multipleupload">
			</div>
		</div>
		
		<div class = "formSection">
			<ul id="sortable">

			</ul>
		</div>
	  </div>-->
	  
	  <div id="marketing" class="tab-pane fade">
		<div class = "formSection">
	  	<fieldset>
	  		<legend>Pricing Details</legend>
			<div class = "form-group">
				<label for = "prop_saletype">Type of Sale :</label>
				<select id = "prop_saletype" name = "prop_saletype" class = "form-control">
					<option value = "">Choose</option>
					<option value = "auction">Auction</option>
					<option value = "let">Let</option>
					<option value = "sell">Regular Sale</option>
				</select>	
			</div>
		
		
			<div class = "form-group">
				<label for = "prop_price" class = "prop_price">Price :</label>
				&pound;<input type = "text" id = "prop_price" name = "prop_price" class = "form-control" />
			</div>

			<div class = "form-group">
				<label for = "prop_startdate">Date Available :</label>
				<input type = "date" id = "prop_startdate" name = "prop_startdate" class = "form-control" />
			</div>

		</fieldset>

		</div>
		
		<div class = "formSection">
	  	<fieldset>
	  		<legend>Marketing Details</legend>
			<div class = "form-group">
				<label for = "checkbox">Where will you be sharing this property ?</label>
				<div class="checkbox">
					@foreach($portals as $portal)
						<label><input type="checkbox" value="{{$portal->id}}" portal_id = "{{$portal->id}}">{{ucfirst($portal->portal_name)}}</label></br>
					@endforeach
				</div>
			</div>
			
			<button class = "form-control btn btn-success">Submit</button>
		

		</fieldset>

		</div>
	  </div>
	  
	  
	{{ Form::close() }}
</div>

	
@stop