@extends('layouts.structure')

@section('header')
	<script>
	$(document).ready(function() {

		var supplierTable = $('#supplierTable').DataTable({
    			"order": [[ 0, "asc" ]],
				"bPaginate": true,
				
				"scrollX": "97%",
				"bInfo" : false,
				"bAutoWidth" : false
				});			
		
		$('body').find("#supplier_menu").addClass("active");;
		$('body').find("#supplier").removeClass("collapse").addClass("collapsed");
		$('body').find("#viewsupplier").addClass("active");
		localStorage.removeItem('lastTab');
		
		$('.supplier').contextmenu({
			target:'#supplier-menu', 
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
<!--<a href = '{{ url("supplier/create") }}' id = "newsupplier" class = "btn btn-success actionButton" >Add New supplier</a>-->
<table id="supplierTable" class = "table table-striped" cellspacing="0" width="100%">
        <thead>
            <tr>
                <th>Supplier Name</th>
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
                <th>supplier Name</th>
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
        	@foreach ($suppliers as $supplier)
        	
        		<!--create right click menu for supplier column-->
        		@if(is_object($supplier))
					<div id="supplier-menu">
						<ul class="dropdown-menu" role="menu">
							<li><a tabindex="-1">Email - {{$supplier->email1}}</a></li>
							<li><a tabindex="-1">Email - {{$supplier->email2}}</a></li>
							<li><a tabindex="-1">SMS - {{$supplier->phone1}}</a></li>
							<li><a tabindex="-1">SMS - {{$supplier->phone2}}</a></li>
							<li class="divider"></li>
							<li><a tabindex="-1">View supplier</a></li>
						</ul>
					</div>
				@endif
				
				<!-- Get Bank For supplier -->
				<?php
				$supplier_bank = DB::table('bank')->where('id', $supplier->bank_id)->get();
				?>
				
    			<tr onclick = "window.location='{{ url("supplier/$supplier->id") }}'" href = '{{ url("supplier/$supplier->id") }}' class = "historyAdd">
					<td>{{$supplier->supplier_name}}</td>
					<td>{{$supplier->supplier_type}}</td>
					<td>{{$supplier->nationality}}</td>
					<td class = "website">{{$supplier->website}}</td>
					<td>{{$supplier->address1}}, {{$supplier->postcode}}</td>
					<td class = "phone">{{$supplier->phone1}}</td>
					<td class = "phone">{{$supplier->phone2}}</td>
					<td class = "mail">{{$supplier->email1}}</td>
					<td class = "mail">{{$supplier->email2}}</td>
					<td>{{$supplier->bank_id}}</td>
					<td>{{ucfirst($supplier->status)}}</td>

            	</tr>
			@endforeach
            
        </tbody>
</table>


@stop