@extends('layouts.pdf_structure')

	@section('content')
		<?php
			//fetch the sales confirmation and products using the id passed to the controller
			$proforma = DB::table('proforma_invoice')->where('id',$proforma_invoice_id)->first();
			$proformaProducts = DB::table('proforma_invoice_products')->where('proforma_invoice_id',$proforma_invoice_id)->get();
			//fetch the client it relates to
			$client = DB::table('client')->where('id',$proforma->client_id)->first();
			//set the format for displaying the confirmation id in the title
			$displayProformaInvoiceId =  "ISFI".str_pad($proforma_invoice_id,6,"0",STR_PAD_LEFT)."P";
			//declare the title for the pdf as its used in the pdf_styles file
			$pdfTitle = "Proforma Invoice :</span> ".$displayProformaInvoiceId;
			//grab the terms for the sales confirmation, or if none set, display none
			$terms = DB::table('terms_conditions')->where('id',$proforma->terms)->first();
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
							<li><span class = "poInfoLabel">Proforma Invoice Number :</span> {{$displayProformaInvoiceId}}</li>
							<li><span class = "poInfoLabel">Proforma Invoice Date :</span> {{date("d/m/Y", strtotime($proforma->proforma_invoice_date))}}</li>
							<li><span class = "poInfoLabel">Payment Date :</span> @if($proforma->payment_date != "0000-00-00"){{date("d/m/Y", strtotime($proforma->payment_date))}}@endif</li>
							<li><span class = "poInfoLabel">&nbsp;</span></li>
							<li><span class = "poInfoLabel">Invoiced To :</span> {{$proforma->invoiced_to}}</li>
						</ul>
					</div>
				
					<div class = "poInfoSplit">
						<ul>
							<li><span class = "poInfoLabel">Shipping Address :</span> {{$proforma->shipping_address}}</li>
							<li><span class = "poInfoLabel">&nbsp;</span> </li>
							<li><span class = "poInfoLabel">&nbsp;</span> </li>
							<li><span class = "poInfoLabel">&nbsp;</span></li>
							<li><span class = "poInfoLabel">Destination Country :</span> {{$proforma->destination_country}}</li>
							<li><span class = "poInfoLabel">Delivery Date (Est) :</span> @if($proforma->delivery_date != "0000-00-00"){{date("d/m/Y", strtotime($proforma->delivery_date))}}@endif</li>
							<li><span class = "poInfoLabel">Delivery Basis (Incoterms) :</span> {{$delivery_basis}}</li>
						</ul>						
					</div>
				
					<div class = "poInfoSplit">
						<ul>
							<li><span class = "poInfoLabel">Shipping To :</span></li>
							<li><span class = "poInfoLabel">Shipping Company :</span> {{ucwords($proforma->shipping_company)}}</li>
							<li><span class = "poInfoLabel">Shipping Method :</span> {{$proforma->shipping_method}}</li>
							<li><span class = "poInfoLabel">Shipping Reference :</span> {{$proforma->shipping_reference}}</li>
							<li><span class = "poInfoLabel">Shipping Contact Name :</span> {{ucwords($proforma->shipping_contact_name)}}</li>
							<li><span class = "poInfoLabel">Shipping Contact Number :</span> {{$proforma->shipping_contact_number}}</li>
							<li><span class = "poInfoLabel">Shipping Date (Est) :</span> @if($proforma->shipping_date != "0000-00-00"){{date("d/m/Y", strtotime($proforma->shipping_date))}}@endif</li>
							<li><span class = "poInfoLabel">Loading Date (Est) :</span> @if($proforma->loading_date != "0000-00-00"){{date("d/m/Y", strtotime($proforma->loading_date))}}@endif</li>
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
				
				@foreach($proformaProducts as $pProduct)
					<?php
					//grab all product details
					$product = DB::table('product')->where('id',$pProduct->product_id)->first();
					?>
				
					<tr>
						<td>{{$itemCount}}</td>
						<td>{{strtoupper($product->prefix.$product->code)}}</td>
						<td>{{ucwords($product->name)}}</td>
						<td>As per attached (ISF_Global_{{strtoupper($product->prefix.$product->code)}})</td>										
						<td>${{$pProduct->unit_sale_price}}</td>
						<td>{{$pProduct->quantity}}</td>
						<td>{{ucwords($pProduct->quantity_type)}}</td>
						<td style = "text-align:right;">${{$pProduct->total_sale_price}}</td>
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
						<td style = "text-align:right;">${{$proforma->total_sale_price}}</td>
					</tr>
			

				
				</tbody>
			</table>
				
				{{$poSignature}}

			
				<div style = "page-break-before:always;">
				
					
					<div class = "poInfo">
					<div class = "poInfoSplit">
						<ul>
							<li><span class = "poInfoLabel">Proforma Invoice Number :</span> {{$displayProformaInvoiceId}}</li>
							<li><span class = "poInfoLabel">Proforma Invoice Date :</span> {{date("d/m/Y", strtotime($proforma->proforma_invoice_date))}}</li>
							<li><span class = "poInfoLabel">Payment Date :</span> @if($proforma->payment_date != "0000-00-00"){{date("d/m/Y", strtotime($proforma->payment_date))}}@endif</li>
							<li><span class = "poInfoLabel">&nbsp;</span></li>
							<li><span class = "poInfoLabel">Invoiced To :</span> {{$proforma->invoiced_to}}</li>
						</ul>
					</div>
				
					<div class = "poInfoSplit">
						<ul>
							<li><span class = "poInfoLabel">Shipping Address :</span> {{$proforma->shipping_address}}</li>
							<li><span class = "poInfoLabel">&nbsp;</span> </li>
							<li><span class = "poInfoLabel">&nbsp;</span> </li>
							<li><span class = "poInfoLabel">&nbsp;</span></li>
							<li><span class = "poInfoLabel">Destination Country :</span> {{$proforma->destination_country}}</li>
							<li><span class = "poInfoLabel">Delivery Date (Est) :</span> @if($proforma->delivery_date != "0000-00-00"){{date("d/m/Y", strtotime($proforma->delivery_date))}}@endif</li>
							<li><span class = "poInfoLabel">Delivery Basis (Incoterms) :</span> {{$delivery_basis}}</li>
						</ul>						
					</div>
				
					<div class = "poInfoSplit">
						<ul>
							<li><span class = "poInfoLabel">Shipping To :</span></li>
							<li><span class = "poInfoLabel">Shipping Company :</span> {{ucwords($proforma->shipping_company)}}</li>
							<li><span class = "poInfoLabel">Shipping Method :</span> {{$proforma->shipping_method}}</li>
							<li><span class = "poInfoLabel">Shipping Reference :</span> {{$proforma->shipping_reference}}</li>
							<li><span class = "poInfoLabel">Shipping Contact Name :</span> {{ucwords($proforma->shipping_contact_name)}}</li>
							<li><span class = "poInfoLabel">Shipping Contact Number :</span> {{$proforma->shipping_contact_number}}</li>
							<li><span class = "poInfoLabel">Shipping Date (Est) :</span> @if($proforma->shipping_date != "0000-00-00"){{date("d/m/Y", strtotime($proforma->shipping_date))}}@endif</li>
							<li><span class = "poInfoLabel">Loading Date (Est) :</span> @if($proforma->loading_date != "0000-00-00"){{date("d/m/Y", strtotime($proforma->loading_date))}}@endif</li>
						</ul>
					</div>
					</div>
										
					<div class = "terms">
						<h3><b>Terms & Conditions</b></h3>
						<p>{{nl2br($termsconditions)}}</p>
					<br></br>																			
					{{$poSignature}}</div></body></html>
			@stop
	
