@extends('layouts.structure')

@section('header')
	<script>
	$(document).ready(function() {

		var contactTable = $('#contactTable').DataTable({
			"order": [[ 0, "asc" ]],
			"bPaginate": true,
			
			"scrollX": "97%",
			"bInfo" : false,
			"bAutoWidth" : false
		});	
		
		
		$('body').find("#contact_menu").addClass("active");;
		$('body').find("#contact").removeClass("collapse").addClass("collapsed");
		$('body').find("#viewcontact").addClass("active");
		localStorage.removeItem('lastTab');
		
		$('.contact').contextmenu({
			target:'#contact-menu', 
			before: function(e,context) {
			// execute code before context menu if shown
			},
			onItem: function(context,e) {
			// execute on menu item selection
			}
		});
	});
	</script>
@stop




@section('content')
<!--<a href = '{{ url("contact/create") }}' id = "newcontact" class = "btn btn-success actionButton" >Add New contact</a>-->
<table id="contactTable" class="table">
        <thead>
            <tr>
                <th>Contact Name</th>
                <th>Employer</th>
                <th>Position</th>
                <th>Nationality</th>
                <th>Phone 1</th>
                <th>Phone 2</th>
                <th>Email 1</th>
                <th>Email 2</th>
                <th>Skype</th>
                <th>Whatsapp</th>
            </tr>
        </thead>
 
        <!--<tfoot>
            <tr>
                <th>Contact Name</th>
                <th>Employer</th>
                <th>Position</th>
                <th>Nationality</th>
                <th>Phone 1</th>
                <th>Phone 2</th>
                <th>Email 1</th>
                <th>Email 2</th>
                <th>Skype</th>
                <th>Whatsapp</th>
            </tr>
        </tfoot>-->
 
        <tbody>
        	@foreach ($contacts as $contact)
        	
        		<!--create right click menu for contact column-->
        		@if(is_object($contact))
					<div id="contact-menu">
						<ul class="dropdown-menu" role="menu">
							<li><a tabindex="-1">Email - {{$contact->email1}}</a></li>
							<li><a tabindex="-1">Email - {{$contact->email2}}</a></li>
							<li><a tabindex="-1">SMS - {{$contact->phone1}}</a></li>
							<li><a tabindex="-1">SMS - {{$contact->phone2}}</a></li>
							<li class="divider"></li>
							<li><a tabindex="-1">View contact</a></li>
						</ul>
					</div>
				@endif
				
				<!-- Get Bank For contact -->
				<?php
				$contact_parent = DB::table($contact->contact_type)->where('id', $contact->type_id)->first();
				?>
				
    			<tr onclick = "window.location='{{ url("contact/$contact->id") }}'" href = '{{ url("contact/$contact->id") }}' class = "historyAdd">
					<td>{{ucfirst($contact->title)}} {{ucfirst($contact->firstname)}} {{ucfirst($contact->lastname)}}</td>
					<td><a href = "/{{$contact->contact_type}}/{{$contact->type_id}}">{{$contact_parent->{$contact->contact_type.'_name'} }}</a></td>
					<td>{{$contact->position}}</td>
					<td>{{$contact->nationality}}</td>
					<td class = "phone">{{$contact->phone1}}</td>
					<td class = "phone">{{$contact->phone2}}</td>
					<td class = "mail">{{$contact->email1}}</td>
					<td class = "mail">{{$contact->email2}}</td>
					<td><a href="skype:{{$contact->skype}}?call">{{$contact->skype}}</a></td>
					<td>{{$contact->whatsapp}}</td>

            	</tr>
			@endforeach
            
        </tbody>
</table>


@stop