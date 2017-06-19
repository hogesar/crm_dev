@extends('layouts.pdf_structure')

	@section('content')
		<?php
			//fetch the sales confirmation and products using the id passed to the controller
			$product = DB::table('product')->where('id',$product_id)->first();
			$product_description = DB::table('product_descriptions')->where('product_id',$product_id)->get();			
			$pdfTitle = "ISF Global Product Specification :</span> ".ucwords($product->name);
			//grab the terms for the sales confirmation, or if none set, display none
			
			include_once(public_path().'/resources/pdf/pdf_styles.php');
			
			$specificationGrade = implode('/',str_split($product->specification_grade));
			
			if(is_dir(public_path().'/products/images/'.strtolower($product->prefix).'/'.$product->code)) {
				$images = glob(public_path().'/products/images/'.strtolower($product->prefix).'/'.$product->code.'/*.{jpeg,gif,png,jpg}', GLOB_BRACE);
			} else {
				$images = glob(public_path().'/products/images/'.strtolower($product->prefix).'/*.{jpeg,gif,png,jpg,JPG,PNG,GIF,JPEG}', GLOB_BRACE);
			}
		?>
		
	<style>
	ul {
		list-style: none;
	}
	
	th { 
		text-align:left;
	}

	td { 
		text-align:left;
	}
	</style>

	
	<table class = "table" cellspacing="0" width="100%">
		<tr>
		  <th>Product Code</th>
			<td class = "pdfTitle">{{strtoupper($product->prefix.$product->code)}}</td>
		</tr>
		<tr>
		  <th>Description</th>
			<td>
				<ul style = "padding-left:0em;">
				@foreach($product_description as $description)
					<li>{{ucfirst($description->description)}}</li>
				@endforeach
				</ul>						
			</td>
		</tr>
		<tr>
		  <th>Category</th>
			<td>{{ucfirst($product->category)}}</td>
		</tr>
		<tr>
		  <th>Subcategory</th>
			<td>{{ucfirst($product->subcategory)}}</td>
		</tr>
		<tr>
		  <th>Variant</th>
			<td>{{ucfirst($product->variant)}}</td>
		</tr>
		<tr>
		  <th>Specification Grade</th>
			<td>{{strtoupper($specificationGrade)}}</td>
		</tr>
		<tr>
		  <th>Product Weight</th>
			<td>{{$product->weight_min." - ".$product->weight_max." ".$product->weight_unit}}
				<br>{{ucfirst($product->weight_text)}}</td>
		</tr>
		<tr>
		  <th>Container Weight</th>
			<td>{{$product->container_weight_min." - ".$product->container_weight_max." ".$product->container_weight_unit}}
				<br>{{ucfirst($product->container_weight_text)}}</td>
		</tr>
		<tr>
		  <th>Packaging</th>
			<td>{{ucfirst($product->packaging)}}</td>
		</tr>
		<tr>
		  <th>Label</th>
			<td>{{ucfirst($product->label)}}</td>
		</tr>
		<tr>
		  <th>Requirements</th>
			<td>{{ucfirst($product->requirements)}}</td>
		</tr>
	</table>
				
				
	@if(!empty($images))
	
		<div style = "page-break-before:always;">
			   
			<table class = "table" cellspacing="0" width="100%">
				<?php
				$imgCount = 0;
				?>
			   @foreach($images as $image) 
					@if($imgCount == 0)
						<tr>
					@endif
							<td style = "padding:10px;">
								<img src = "{{$image}}" style = "max-width:95%;min-width:170px!important;border-radius:0px;" />
							</td>
					@if($imgCount == 1)
						</tr>
						<?php
						$imgCount = 0;
						?>
					@endif
					<?php
					$imgCount++;
					?>
				@endforeach
			</table>
		</div>
	@endif
		
		
	@stop
	
