@extends('layouts.structure')

@section('header')
	<script>
	$(document).ready(function() {

		var banktable = $('#bankTable').DataTable({
    			"order": [[ 0, "asc" ]],
				"bPaginate": true,
				
				"scrollX": "97%",
				"bInfo" : false,
				"bAutoWidth" : false
				});			
		
		$('body').find("#bank_menu").addClass("active");;
		$('body').find("#bank").removeClass("collapse").addClass("collapsed");
		$('body').find("#viewbank").addClass("active");
		localStorage.removeItem('lastTab');
		
		$('.bank').contextmenu({
			target:'#bank-menu', 
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
<!--<a href = '{{ url("bank/create") }}' id = "newbank" class = "btn btn-success actionButton" >Add New bank</a>-->
<table id="bankTable" class = "table table-striped" cellspacing="0" width="100%">
        <thead>
            <tr>
                <th>Bank Name</th>
                <th>Nationality</th>
                <th>Address</th>
                <th>Phone 1</th>
                <th>Phone 2</th>
                <th>Email 1</th>
                <th>Email 2</th>
                <th>Website</th>
            </tr>
        </thead>
 
        <tfoot>
            <tr>
                <th>Bank Name</th>
                <th>Nationality</th>
                <th>Address</th>
                <th>Phone 1</th>
                <th>Phone 2</th>
                <th>Email 1</th>
                <th>Email 2</th>
                <th>Website</th>
            </tr>
        </tfoot>
 
        <tbody>
        	@foreach ($banks as $bank)
        	
        		<!--create right click menu for bank column-->
        		@if(is_object($bank))
					<div id="bank-menu">
						<ul class="dropdown-menu" role="menu">
							<li><a tabindex="-1">Email - {{$bank->email1}}</a></li>
							<li><a tabindex="-1">Email - {{$bank->email2}}</a></li>
							<li><a tabindex="-1">SMS - {{$bank->phone1}}</a></li>
							<li><a tabindex="-1">SMS - {{$bank->phone2}}</a></li>
							<li class="divider"></li>
							<li><a tabindex="-1">View bank</a></li>
						</ul>
					</div>
				@endif
				
    			<tr onclick = "window.location='{{ url("bank/$bank->id") }}'" href = '{{ url("bank/$bank->id") }}' class = "historyAdd">
					<td>{{ucwords($bank->bank_name)}}</td>
					<td>{{$bank->nationality}}</td>
					<td>{{$bank->address1}}, {{$bank->postcode}}</td>				
					<td class = "phone">{{$bank->phone1}}</td>
					<td class = "phone">{{$bank->phone2}}</td>
					<td class = "mail">{{$bank->email1}}</td>
					<td class = "mail">{{$bank->email2}}</td>
					<td class = "website">{{$bank->website}}</td>

            	</tr>
			@endforeach
            
        </tbody>
</table>


@stop