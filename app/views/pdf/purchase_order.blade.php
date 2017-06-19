@extends('layouts.pdf_structure')

	@section('content')
		<?php
			//fetch the sales confirmation and products using the id passed to the controller
			$purchaseOrder = DB::table('purchase_order')->where('id',$purchase_order_id)->first();
			$purchaseOrderProducts = DB::table('purchase_order_products')->where('purchase_order_id',$purchase_order_id)->get();
			//fetch the client it relates to
			$client = DB::table('client')->where('id',$purchaseOrder->client_id)->first();
			//set the format for displaying the confirmation id in the title
			$displayPurchaseOrderId =  "ISFP".str_pad($purchase_order_id,6,"0",STR_PAD_LEFT);
			//declare the title for the pdf as its used in the pdf_styles file
			$pdfTitle = "Purchase Order :</span> ".$displayPurchaseOrderId;
			//grab the terms for the sales confirmation, or if none set, display none
			$terms = DB::table('terms_conditions')->where('id',$purchaseOrder->terms)->first();
			if(is_object($terms)) {
				$termsconditions = $terms->content;
				$delivery_basis = $terms->type;
			} else {
				$termsconditions = "";
				$delivery_basis = "";
			}
			include_once(public_path().'/resources/pdf/pdf_styles.php');

			$itemCount = 1;
		?>
	
		
			<div class = "poInfo">
					<div class = "poInfoSplit">
						<ul>
							<li><span class = "poInfoLabel">Purchase Order Number :</span> {{$displayPurchaseOrderId}}</li>
							<li><span class = "poInfoLabel">Purchase Order Date :</span> {{date("d/m/Y", strtotime($purchaseOrder->order_date))}}</li>
							<li><span class = "poInfoLabel">Payment Date :</span> {{date("d/m/Y", strtotime($purchaseOrder->payment_date))}}</li>
							<li><span class = "poInfoLabel">&nbsp;</span></li>
							<li><span class = "poInfoLabel">Invoiced To :</span> {{$purchaseOrder->invoiced_to}}</li>
						</ul>
					</div>
				
					<div class = "poInfoSplit">
						<ul>
							<li><span class = "poInfoLabel">Shipping Address :</span> {{$purchaseOrder->shipping_address}}</li>
							<li><span class = "poInfoLabel">&nbsp;</span> </li>
							<li><span class = "poInfoLabel">&nbsp;</span> </li>
							<li><span class = "poInfoLabel">&nbsp;</span></li>
							<li><span class = "poInfoLabel">Destination Country :</span> {{$purchaseOrder->destination_country}}</li>
							<li><span class = "poInfoLabel">Delivery Date (Est) :</span> {{date("d/m/Y", strtotime($purchaseOrder->delivery_date))}}</li>
							<li><span class = "poInfoLabel">Delivery Basis (Incoterms) :</span> {{$delivery_basis}}</li>
						</ul>						
					</div>
				
					<div class = "poInfoSplit">
						<ul>
							<li><span class = "poInfoLabel">Shipping To :</span></li>
							<li><span class = "poInfoLabel">Shipping Company :</span> {{ucwords($purchaseOrder->shipping_company)}}</li>
							<li><span class = "poInfoLabel">Shipping Method :</span> {{$purchaseOrder->shipping_method}}</li>
							<li><span class = "poInfoLabel">Shipping Reference :</span> {{$purchaseOrder->shipping_reference}}</li>
							<li><span class = "poInfoLabel">Shipping Contact Name :</span> {{ucwords($purchaseOrder->shipping_contact_name)}}</li>
							<li><span class = "poInfoLabel">Shipping Contact Number :</span> {{$purchaseOrder->shipping_contact_number}}</li>
							<li><span class = "poInfoLabel">Shipping Date (Est) :</span> {{date("d/m/Y", strtotime($purchaseOrder->shipping_date))}}</li>
							<li><span class = "poInfoLabel">Loading Date (Est) :</span> {{date("d/m/Y", strtotime($purchaseOrder->loading_date))}}</li>
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
				
				@foreach($purchaseOrderProducts as $pProduct)
					<?php
					//grab all product details
					$product = DB::table('product')->where('id',$pProduct->product_id)->first();
					?>
				
					<tr>
						<td>{{$itemCount}}</td>
						<td>{{strtoupper($product->prefix.$product->code)}}</td>
						<td>{{ucwords($product->name)}}</td>
						<td>As per attached (ISF_Global_{{strtoupper($product->prefix.$product->code)}})</td>										
						<td>${{$pProduct->unit_cost_price}}</td>
						<td>{{$pProduct->quantity}}</td>
						<td>{{ucwords($pProduct->quantity_type)}}</td>
						<td style = "text-align:right;">${{$pProduct->total_cost_price}}</td>
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
						<td style = "text-align:right;">${{$purchaseOrder->total_sale_price}}</td>
					</tr>
			

				
				</tbody>
			</table>
				
				{{$poSignature}}

			
				<div style = "page-break-before:always;">
				
					
					<div class = "poInfo">
						<div class = "poInfoSplit">
							<ul>
								<li><span class = "poInfoLabel">Purchase Order Number :</span> {{$displayPurchaseOrderId}}</li>
								<li><span class = "poInfoLabel">Purchase Order Date :</span> {{date("d/m/Y", strtotime($purchaseOrder->order_date))}}</li>
								<li><span class = "poInfoLabel">Payment Date :</span> {{date("d/m/Y", strtotime($purchaseOrder->payment_date))}}</li>
								<li><span class = "poInfoLabel">&nbsp;</span></li>
								<li><span class = "poInfoLabel">Invoiced To :</span> {{$purchaseOrder->invoiced_to}}</li>
							</ul>
						</div>
				
						<div class = "poInfoSplit">
							<ul>
								<li><span class = "poInfoLabel">Shipping Address :</span> {{$purchaseOrder->shipping_address}}</li>
								<li><span class = "poInfoLabel">&nbsp;</span> </li>
								<li><span class = "poInfoLabel">&nbsp;</span> </li>
								<li><span class = "poInfoLabel">&nbsp;</span></li>
								<li><span class = "poInfoLabel">Destination Country :</span> {{$purchaseOrder->destination_country}}</li>
								<li><span class = "poInfoLabel">Delivery Date (Est) :</span> {{date("d/m/Y", strtotime($purchaseOrder->delivery_date))}}</li>
								<li><span class = "poInfoLabel">Delivery Basis (Incoterms) :</span> {{$delivery_basis}}</li>
							</ul>						
						</div>
				
						<div class = "poInfoSplit">
							<ul>
								<li><span class = "poInfoLabel">Shipping To :</span></li>
								<li><span class = "poInfoLabel">Shipping Company :</span> {{ucwords($purchaseOrder->shipping_company)}}</li>
								<li><span class = "poInfoLabel">Shipping Method :</span> {{$purchaseOrder->shipping_method}}</li>
								<li><span class = "poInfoLabel">Shipping Reference :</span> {{$purchaseOrder->shipping_reference}}</li>
								<li><span class = "poInfoLabel">Shipping Contact Name :</span> {{ucwords($purchaseOrder->shipping_contact_name)}}</li>
								<li><span class = "poInfoLabel">Shipping Contact Number :</span> {{$purchaseOrder->shipping_contact_number}}</li>
								<li><span class = "poInfoLabel">Shipping Date (Est) :</span> {{date("d/m/Y", strtotime($purchaseOrder->shipping_date))}}</li>
								<li><span class = "poInfoLabel">Loading Date (Est) :</span> {{date("d/m/Y", strtotime($purchaseOrder->loading_date))}}</li>
							</ul>
						</div>
					</div>
										
					<div class = "terms">
						<h3><b>Terms & Conditions</b></h3>
						<p>{{nl2br($termsconditions)}}</p>
					<br></br>																			
					{{$poSignature}}</div></body></html>
			@stop
	
