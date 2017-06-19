@extends('layouts.pdf_structure')

	@section('content')
		<?php
			//fetch the sales confirmation and products using the id passed to the controller
			$salesConfirmation = DB::table('sales_confirmation')->where('id',$sales_confirmation_id)->first();
			$salesConfirmationProducts = DB::table('sales_confirmation_products')->where('confirmation_id',$sales_confirmation_id)->get();
			//fetch the client it relates to
			$client = DB::table('client')->where('id',$salesConfirmation->client_id)->first();
			//set the format for displaying the confirmation id in the title
			$displayConfirmationId =  "ISFC".str_pad($sales_confirmation_id,6,"0",STR_PAD_LEFT);
			//declare the title for the pdf as its used in the pdf_styles file
			$pdfTitle = "Sales Confirmation :</span> ".$displayConfirmationId;
			//grab the terms for the sales confirmation, or if none set, display none
			$terms = DB::table('terms_conditions')->where('id',$salesConfirmation->terms)->first();
			if(is_object($terms)) {
				$termsconditions = $terms->content;
			} else {
				$termsconditions = "";
			}
			include_once(public_path().'/resources/pdf/pdf_styles.php');

			$itemCount = 1;
		?>
	
		
			<div class = "poInfo">
					<div class = "poInfoSplit">
						<ul>
							<li><span class = "poInfoLabel">Confirmation Number :</span> {{$displayConfirmationId}}</li>
							<li><span class = "poInfoLabel">Confirmation Date :</span> {{date("d/m/Y", strtotime($salesConfirmation->confirmation_date))}}</li>
							<li><span class = "poInfoLabel">Payment Date :</span> {{date("d/m/Y", strtotime($salesConfirmation->payment_date))}}</li>
							<li><span class = "poInfoLabel">&nbsp;</span></li>
							<li><span class = "poInfoLabel">To :</span> {{ucwords($client->client_name)}}</li>
						</ul>
					</div>
				
					<div class = "poInfoSplit">
						<ul>
							<li><span class = "poInfoLabel">Shipping Address :</span> {{$salesConfirmation->shipping_address}}</li>
							<li><span class = "poInfoLabel">&nbsp;</span> </li>
							<li><span class = "poInfoLabel">&nbsp;</span> </li>
							<li><span class = "poInfoLabel">&nbsp;</span></li>
							<li><span class = "poInfoLabel">Destination Country :</span> {{$salesConfirmation->destination_country}}</li>
							<li><span class = "poInfoLabel">Delivery Date (Est) :</span> {{date("d/m/Y", strtotime($salesConfirmation->delivery_date))}}</li>
							<li><span class = "poInfoLabel">Delivery Basis (Incoterms) :</span> {{$salesConfirmation->delivery_basis}}</li>
						</ul>						
					</div>
				
					<div class = "poInfoSplit">
						<ul>
							<li><span class = "poInfoLabel">Shipping To :</span></li>
							<li><span class = "poInfoLabel">Shipping Company :</span></li>
							<li><span class = "poInfoLabel">Shipping Method :</span> {{$salesConfirmation->shipping_method}}</li>
							<li><span class = "poInfoLabel">Shipping Reference :</span> N/A</li>
							<li><span class = "poInfoLabel">Shipping Contact Name :</span> N/A</li>
							<li><span class = "poInfoLabel">Shipping Contact Number :</span> N/A</li>
							<li><span class = "poInfoLabel">Shipping Date (Est) :</span></li>
							<li><span class = "poInfoLabel">Loading Date (Est) :</span></li>
						</ul>
					</div>
				</div>
			
				<table class = "table" style = "width:100%!important;">
					<thead>
						<tr style = "white-space: nowrap!important;">
							<th>#</th>
							<th>Product Code</th>
							<th>Description</th>
							<th>Specification</th>
							<th>Unit Price ($)</th>
							<th>Quantity</th>	
							<th>Quantity Type</th>						
							<th>Total Price</th>
						</tr>
					</thead>
					
					<tbody>
				
				@foreach($salesConfirmationProducts as $cProduct)
					<?php
					//grab all product details
					$product = DB::table('product')->where('id',$cProduct->product_id)->first();
					?>
				
					<tr>
						<td>{{$itemCount}}</td>
						<td>{{strtoupper($product->prefix.$product->code)}}</td>
						<td>{{ucwords($product->name)}}</td>
						<td>As per attached (ISF_Global_{{strtoupper($product->prefix.$product->code)}})</td>										
						<td>${{$cProduct->unit_sale_price}}</td>
						<td>{{$cProduct->quantity}}</td>
						<td>{{ucwords($cProduct->quantity_type)}}</td>
						<td style = "text-align:right;">${{$cProduct->total_sale_price}}</td>
					</tr>
				
					<?php			
						$itemCount++;
					?>
				@endforeach
				
					<tr>
						<td></td>
						<td></td>
						<td></td>
						<td></td>										
						<td></td>
						<td></td>
						<td>Total</td>
						<td style = "text-align:right;">${{$salesConfirmation->total_sale_price}}</td>
					</tr>
			

				
				</tbody>
			</table>
				
				{{$poSignature}}

			
				<div style = "page-break-before:always;">
				
					
					<div class = "poInfo">
						<div class = "poInfoSplit">
							<ul>
								<li><span class = "poInfoLabel">Confirmation Number :</span> {{$displayConfirmationId}}</li>
								<li><span class = "poInfoLabel">Confirmation Date :</span> {{date("d/m/Y", strtotime($salesConfirmation->confirmation_date))}}</li>
								<li><span class = "poInfoLabel">Payment Date :</span> {{date("d/m/Y", strtotime($salesConfirmation->payment_date))}}</li>
								<li><span class = "poInfoLabel">&nbsp;</span></li>
								<li><span class = "poInfoLabel">To :</span> {{ucwords($client->client_name)}}</li>
							</ul>
						</div>
				
						<div class = "poInfoSplit">
							<ul>
								<li><span class = "poInfoLabel">Shipping Address :</span> {{$salesConfirmation->shipping_address}}</li>
								<li><span class = "poInfoLabel">&nbsp;</span> </li>
								<li><span class = "poInfoLabel">&nbsp;</span> </li>
								<li><span class = "poInfoLabel">&nbsp;</span></li>
								<li><span class = "poInfoLabel">Destination Country :</span> {{$salesConfirmation->destination_country}}</li>
								<li><span class = "poInfoLabel">Delivery Date (Est) :</span> {{date("d/m/Y", strtotime($salesConfirmation->delivery_date))}}</li>
								<li><span class = "poInfoLabel">Delivery Basis (Incoterms) :</span> {{$salesConfirmation->delivery_basis}}</li>
							</ul>						
						</div>
				
						<div class = "poInfoSplit">
							<ul>
								<li><span class = "poInfoLabel">Shipping To :</span></li>
								<li><span class = "poInfoLabel">Shipping Company :</span></li>
								<li><span class = "poInfoLabel">Shipping Method :</span> {{$salesConfirmation->shipping_method}}</li>
								<li><span class = "poInfoLabel">Shipping Reference :</span> N/A</li>
								<li><span class = "poInfoLabel">Shipping Contact Name :</span> N/A</li>
								<li><span class = "poInfoLabel">Shipping Contact Number :</span> N/A</li>
								<li><span class = "poInfoLabel">Shipping Date (Est) :</span></li>
								<li><span class = "poInfoLabel">Loading Date (Est) :</span></li>
							</ul>
						</div>
					</div>
										
					<div class = "terms">
						<h3><b>Terms & Conditions</b></h3>
						<p>{{nl2br($termsconditions)}}</p>
					<br></br>																			
					{{$poSignature}}</div></body></html>
			@stop
	
