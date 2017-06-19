@extends('layouts.structure')

@section('header')
	<script>
	$(document).ready(function() {
		$('#propertyTable').DataTable({
			"columnDefs": [
            		{
            			"targets": [ 0 ],
                		"visible": false,
                		"searchable": false
            		}]
		
		});
		$('body').find("#landlord_menu").addClass("active");
		$('body').find("#landlords").removeClass("collapse").addClass("collapsed");
		$('body').find("#viewLandlord").addClass("active");
		localStorage.removeItem('lastTab');
		
		
		
	});
	</script>
@stop


@section('content')

<table id="propertyTable" class="table display" cellspacing="0" width="100%">
				<thead>
					<th>ID</th>
					<th>Name</th>
					<th>Category</th>
					<th>Subcategory</th>
					<th>Variant</th>
					<th>Grade</th>
					<th>Product Weight</th>
					<th>Packaging</th>
					<th>Product Code</th>
				</thead>
				
 
        <tbody>
        	@foreach ($products as $product)
        		<?php 
        			$displayProductId = str_pad($product->id,7,"0",STR_PAD_LEFT);
        		 ?>
    			<tr onclick = "window.location='{{ url("product/$product->id") }}'" class = "historyAdd" href = '{{ url("product/$product->id") }}'>	
    			<td>{{$product->id}}</td>		
				<td>{{ucfirst($product->name)}}</td>
				<td>{{ucfirst($product->category)}}</td>
				<td>{{ucfirst($product->subcategory)}}</td>
				<td>{{ucfirst($product->variant)}}</td>
				<td>{{ucfirst($product->specification_grade)}}</td>
				<td>{{$product->weight_min}} - {{$product->weight_max.$product->weight_unit}}</td>
				<td>{{ucfirst($product->packaging)}}</td>
				<td>{{strtoupper($product->prefix).strtoupper($product->code)}}</td>
            	</tr>
			@endforeach
            
        </tbody>
</table>


@stop