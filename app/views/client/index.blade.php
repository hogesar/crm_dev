@extends('layouts.structure')

@section('header')
	<script>
	$(document).ready(function() {
	
		var clientTable = $('#clientTable').DataTable({
    			"order": [[ 0, "asc" ]],
				"bPaginate": true,
				
				"scrollX": "97%",
				"bInfo" : false,
				"bAutoWidth" : false
				});			
		
		$('body').find("#client_menu").addClass("active");;
		$('body').find("#client").removeClass("collapse").addClass("collapsed");
		$('body').find("#viewClient").addClass("active");
		localStorage.removeItem('lastTab');
		
		$('.client').contextmenu({
			target:'#client-menu', 
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
<!--<a href = '{{ url("client/create") }}' id = "newclient" class = "btn btn-success actionButton" >Add New client</a>-->
<table id="clientTable" class = "table table-striped" cellspacing="0" width="100%">
        <thead>
            <tr>
                <th>Client Name</th>
                <th>Type</th>
                <th>Nationality</th>
                <th>Website</th>
                <th>Address</th>
                <th>Phone 1</th>
                <th>Phone 2</th>
                <th>Email 1</th>
                <th>Email 2</th>
                <th>Bank</th>
                <th>Status</th>
            </tr>
        </thead>
 
        <!--<tfoot>
            <tr>
                <th>Client Name</th>
                <th>Type</th>
                <th>Nationality</th>
                <th>Website</th>
                <th>Address</th>
                <th>Phone 1</th>
                <th>Phone 2</th>
                <th>Email 1</th>
                <th>Email 2</th>
                <th>Bank</th>
                <th>Status</th>
            </tr>
        </tfoot>-->
 
        <tbody>
        	@foreach ($clients as $client)
        	
        		<!--create right click menu for client column-->
        		@if(is_object($client))
					<div id="client-menu">
						<ul class="dropdown-menu" role="menu">
							<li><a tabindex="-1">Email - {{$client->email1}}</a></li>
							<li><a tabindex="-1">Email - {{$client->email2}}</a></li>
							<li><a tabindex="-1">SMS - {{$client->phone1}}</a></li>
							<li><a tabindex="-1">SMS - {{$client->phone2}}</a></li>
							<li class="divider"></li>
							<li><a tabindex="-1">View client</a></li>
						</ul>
					</div>
				@endif
				
				<!-- Get Bank For Client -->
				<?php
				$client_bank = DB::table('bank')->where('id', $client->bank_id)->first();
				?>
				
    			<tr onclick = "window.location='{{ url("client/$client->id") }}'" href = '{{ url("client/$client->id") }}' class = "historyAdd">
					<td>{{$client->client_name}}</td>
					<td>{{$client->client_type}}</td>
					<td>{{$client->nationality}}</td>
					<td class = "website">{{$client->website}}</td>
					<td>{{$client->address1}}, {{$client->postcode}}</td>
					<td class = "phone">{{$client->phone1}}</td>
					<td class = "phone">{{$client->phone2}}</td>
					<td class = "mail">{{$client->email1}}</td>
					<td class = "mail">{{$client->email2}}</td>
					<td>
						@if(is_object($client_bank))
							<a href = "/bank/{{$client_bank->id}}">{{$client_bank->bank_name}}</a>
						@endif
					</td>
					<td>{{ucfirst($client->status)}}</td>

            	</tr>
			@endforeach
            
        </tbody>
</table>


@stop