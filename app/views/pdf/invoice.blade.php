@extends('layouts.pdf_structure')

	@section('content')
		<?php
			//fetch the sales confirmation and products using the id passed to the controller
			$invoice = DB::table('invoice')->where('id',$invoice_id)->first();
			$invoiceProducts = DB::table('invoice_products')->where('invoice_id',$invoice_id)->get();
			//fetch the client it relates to
			$client = DB::table('client')->where('id',$invoice->client_id)->first();
			//set the format for displaying the confirmation id in the title
			$displayInvoiceId =  "ISFI".str_pad($invoice_id,6,"0",STR_PAD_LEFT);
			//declare the title for the pdf as its used in the pdf_styles file
			$pdfTitle = "Invoice :</span> ".$displayInvoiceId;
			//grab the terms for the sales confirmation, or if none set, display none
			$terms = DB::table('terms_conditions')->where('id',$invoice->terms)->first();
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
							<li><span class = "poInfoLabel">Proforma Invoice Number :</span> {{$displayInvoiceId}}</li>
							<li><span class = "poInfoLabel">Proforma Invoice Date :</span> {{date("d/m/Y", strtotime($invoice->invoice_date))}}</li>
							<li><span class = "poInfoLabel">Payment Date :</span> @if($invoice->payment_date != "0000-00-00"){{date("d/m/Y", strtotime($invoice->payment_date))}}@endif</li>
							<li><span class = "poInfoLabel">&nbsp;</span></li>
							<li><span class = "poInfoLabel">Invoiced To :</span> {{$invoice->invoiced_to}}</li>
						</ul>
					</div>
			
					<div class = "poInfoSplit">
						<ul>
							<li><span class = "poInfoLabel">Shipping Address :</span> {{$invoice->shipping_address}}</li>
							<li><span class = "poInfoLabel">&nbsp;</span> </li>
							<li><span class = "poInfoLabel">&nbsp;</span> </li>
							<li><span class = "poInfoLabel">&nbsp;</span></li>
							<li><span class = "poInfoLabel">Destination Country :</span> {{$invoice->destination_country}}</li>
							<li><span class = "poInfoLabel">Delivery Date (Est) :</span> @if($invoice->delivery_date != "0000-00-00"){{date("d/m/Y", strtotime($invoice->delivery_date))}}@endif</li>
							<li><span class = "poInfoLabel">Delivery Basis (Incoterms) :</span> {{$delivery_basis}}</li>
						</ul>						
					</div>
			
					<div class = "poInfoSplit">
						<ul>
							<li><span class = "poInfoLabel">Shipping To :</span></li>
							<li><span class = "poInfoLabel">Shipping Company :</span> {{ucwords($invoice->shipping_company)}}</li>
							<li><span class = "poInfoLabel">Shipping Method :</span> {{$invoice->shipping_method}}</li>
							<li><span class = "poInfoLabel">Shipping Reference :</span> {{$invoice->shipping_reference}}</li>
							<li><span class = "poInfoLabel">Shipping Contact Name :</span> {{ucwords($invoice->shipping_contact_name)}}</li>
							<li><span class = "poInfoLabel">Shipping Contact Number :</span> {{$invoice->shipping_contact_number}}</li>
							<li><span class = "poInfoLabel">Shipping Date (Est) :</span> @if($invoice->shipping_date != "0000-00-00"){{date("d/m/Y", strtotime($invoice->shipping_date))}}@endif</li>
							<li><span class = "poInfoLabel">Loading Date (Est) :</span> @if($invoice->loading_date != "0000-00-00"){{date("d/m/Y", strtotime($invoice->loading_date))}}@endif</li>
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
				
				@foreach($invoiceProducts as $iProduct)
					<?php
					//grab all product details
					$product = DB::table('product')->where('id',$iProduct->product_id)->first();
					?>
				
					<tr>
						<td>{{$itemCount}}</td>
						<td>{{strtoupper($product->prefix.$product->code)}}</td>
						<td>{{ucwords($product->name)}}</td>
						<td>As per attached (ISF_Global_{{strtoupper($product->prefix.$product->code)}})</td>										
						<td>${{$iProduct->unit_sale_price}}</td>
						<td>{{$iProduct->quantity}}</td>
						<td>{{ucwords($iProduct->quantity_type)}}</td>
						<td style = "text-align:right;">${{$iProduct->total_sale_price}}</td>
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
						<td style = "text-align:right;">${{$invoice->total_sale_price}}</td>
					</tr>
			

				
				</tbody>
			</table>
				
				{{$poSignature}}

			
				<div style = "page-break-before:always;">
				
					
					<div class = "poInfo">
						<div class = "poInfoSplit">
							<ul>
								<li><span class = "poInfoLabel">Proforma Invoice Number :</span> {{$displayInvoiceId}}</li>
								<li><span class = "poInfoLabel">Proforma Invoice Date :</span> {{date("d/m/Y", strtotime($invoice->invoice_date))}}</li>
								<li><span class = "poInfoLabel">Payment Date :</span> @if($invoice->payment_date != "0000-00-00"){{date("d/m/Y", strtotime($invoice->payment_date))}}@endif</li>
								<li><span class = "poInfoLabel">&nbsp;</span></li>
								<li><span class = "poInfoLabel">Invoiced To :</span> {{$invoice->invoiced_to}}</li>
							</ul>
						</div>
				
						<div class = "poInfoSplit">
							<ul>
								<li><span class = "poInfoLabel">Shipping Address :</span> {{$invoice->shipping_address}}</li>
								<li><span class = "poInfoLabel">&nbsp;</span> </li>
								<li><span class = "poInfoLabel">&nbsp;</span> </li>
								<li><span class = "poInfoLabel">&nbsp;</span></li>
								<li><span class = "poInfoLabel">Destination Country :</span> {{$invoice->destination_country}}</li>
								<li><span class = "poInfoLabel">Delivery Date (Est) :</span> @if($invoice->delivery_date != "0000-00-00"){{date("d/m/Y", strtotime($invoice->delivery_date))}}@endif</li>
								<li><span class = "poInfoLabel">Delivery Basis (Incoterms) :</span> {{$delivery_basis}}</li>
							</ul>						
						</div>
				
						<div class = "poInfoSplit">
							<ul>
								<li><span class = "poInfoLabel">Shipping To :</span></li>
								<li><span class = "poInfoLabel">Shipping Company :</span> {{ucwords($invoice->shipping_company)}}</li>
								<li><span class = "poInfoLabel">Shipping Method :</span> {{$invoice->shipping_method}}</li>
								<li><span class = "poInfoLabel">Shipping Reference :</span> {{$invoice->shipping_reference}}</li>
								<li><span class = "poInfoLabel">Shipping Contact Name :</span> {{ucwords($invoice->shipping_contact_name)}}</li>
								<li><span class = "poInfoLabel">Shipping Contact Number :</span> {{$invoice->shipping_contact_number}}</li>
								<li><span class = "poInfoLabel">Shipping Date (Est) :</span> @if($invoice->shipping_date != "0000-00-00"){{date("d/m/Y", strtotime($invoice->shipping_date))}}@endif</li>
								<li><span class = "poInfoLabel">Loading Date (Est) :</span> @if($invoice->loading_date != "0000-00-00"){{date("d/m/Y", strtotime($invoice->loading_date))}}@endif</li>
							</ul>
						</div>
					</div>
										
					<div class = "terms">
						<h3><b>Terms & Conditions</b></h3>
						<p>{{nl2br($termsconditions)}}</p>
					<br></br>																			
					{{$poSignature}}</div></body></html>
			@stop
	
