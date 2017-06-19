@extends('layouts.structure')

@section('header')
	<script>
	$(document).ready(function() {
		//remembering tabs
		// for bootstrap 3 use 'shown.bs.tab', for bootstrap 2 use 'shown' in the next line
		$('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
			// save the latest tab; use cookies if you like 'em better:
			localStorage.setItem('lastTab', $(this).attr('href'));
		});

		// go to the latest tab, if it exists:
		var lastTab = localStorage.getItem('lastTab');
		if (lastTab) {
			$('[href="' + lastTab + '"]').tab('show');
		}
		
		$('body').find("#contact_menu").addClass("active");
		$('body').find("#contact").removeClass("collapse").addClass("collapsed");
		$('body').find("#viewcontact").addClass("active");
		
		var historyTable = $('#historyTable').DataTable({
				"bPaginate": true,			
				"scrollX": "97%",
				"bInfo" : false,
				"bAutoWidth" : false,
				"columnDefs" : [
					{ type : 'date', targets : [0] }
				]
			});
				
		historyTable.page('last').draw('page');
		
		var diaryTable = $('#diaryTable').DataTable({
				"bPaginate": true,				
				"scrollX": "97%",
				"bInfo" : false,
				"bAutoWidth" : false,
				"columnDefs" : [
					{ type : 'date', targets : [0] }
				] 
			});	
					
		diaryTable.page('last').draw('page');
		
		
		$('.contact_client').contextmenu({
			target:'#client-menu', 
			before: function(e,context) {
			// execute code before context menu if shown
			},
			onItem: function(context,e) {
			// execute on menu item selection
			}
		});
		
		$(".salesConfirm").click(function(e) {
			e.preventDefault;
			$("#salesConfirm").trigger("click");
		});
		
		$(".purchaseOrderLink").click(function(e) {
			e.preventDefault;
			var order_id = $(this).attr("data-id");
			$("#purchaseorder_"+order_id).trigger("click");
		});
		
		$(".proformaLink").click(function(e) {
			e.preventDefault;
			var proforma_id = $(this).attr("data-id");
			$("#proformainvoice_"+proforma_id).trigger("click");
		});
		
		$(".invoiceLink").click(function(e) {
			e.preventDefault;
			var invoice_id = $(this).attr("data-id");
			$("#invoice"+invoice_id).trigger("click");
		});

		
	});
	</script>
@stop

@section('content')

	<?php
	$child_type = "deal";
	$parent_type = "client";
	
	$parent = DB::table($parent_type)->where('id', $deal->client_id)->first();
	$deal_history = DB::table('history')->where('parent_id', $parent->id)->where('parent_type',$parent_type)->where('child_id',$deal->id)->where('child_type',$child_type)->get();
	$deal_diary = DB::table('diary')->where('parent_id', $parent->id)->where('parent_type',$parent_type)->where('child_id',$deal->id)->where('child_type',$child_type)->where('completed_by','')->get();
	$deal_contact = DB::table('contact')->where('id',$deal->contact_id)->first();
	
	$sales_confirmation = DB::table('sales_confirmation')->where('deal_id',$deal->id)->first();
	//sales confirmation product with no purchase order
	if(is_object($sales_confirmation)) {
		$sales_confirmation_products = DB::table('sales_confirmation_products')->where('confirmation_id',$sales_confirmation->id)->where('purchase_order_created','no')->get();
	} else {
		$sales_confirmation_products = 0;
	}
	
	//grab all the purchase orders
	$purchase_orders = DB::table('purchase_order')->where('deal_id',$deal->id)->get();
	
	//use the purchase order products to build an array of prods that are yet to be invoiced
	$nonProformaInvoicedProducts = array();
	foreach($purchase_orders as $purchase_order) {
		$nonInvoicedProds = DB::table('purchase_order_products')->where('purchase_order_id',$purchase_order->id)->where('proforma_invoice_created','no')->get();
		foreach($nonInvoicedProds as $nonInvoiced) {
			$nonInvoicedProducts[] = $nonInvoiced;
		}
	}
	
	$nonInvoicedProducts = array();
	foreach($purchase_orders as $purchase_order) {
		$nonInvoicedProds = DB::table('purchase_order_products')->where('purchase_order_id',$purchase_order->id)->where('invoice_created','no')->get();
		foreach($nonInvoicedProds as $nonInvoiced) {
			$nonInvoicedProducts[] = $nonInvoiced;
		}
	}
	
	//for creating tabs
	$purchase_order_count = 1;
	$proforma_count = 1;
	$invoice_count = 1;
	
	$proforma_invoices = DB::table('proforma_invoice')->where('deal_id',$deal->id)->get();
	$invoices = DB::table('invoice')->where('deal_id',$deal->id)->get();
	
	?>
	
	<!-- hidden input with ID of current object for history / diary -->
	<input type = "hidden" id = "parent_id" name = "parent_id" value = "{{$parent->id}}" />
	<input type = "hidden" id = "parent_type" name = "parent_type" value = "{{$parent_type}}" />
	<input type = "hidden" id = "child_id" name = "child_id" value = "{{$deal->id}}" />
	<input type = "hidden" id = "child_type" name = "child_type" value = "{{$child_type}}" />
  	
  	@if(is_object($parent))
	<div id="client-menu">
		<ul class="dropdown-menu" role="menu">
			<li><a tabindex="-1">Email - {{$parent->email1}}</a></li>
			<li><a tabindex="-1">Email - {{$parent->email2}}</a></li>
			<li><a tabindex="-1">SMS - {{$parent->phone1}}</a></li>
			<li><a tabindex="-1">SMS - {{$parent->phone2}}</a></li>
			<li class="divider"></li>
			<li><a tabindex="-1">View contact</a></li>
		</ul>
  	</div>
  	@endif
	
	<ul class="nav nav-tabs">
	  <li class="active"><a data-toggle="tab" href="#overview">Overview</a></li>
	  <li><a data-toggle="tab" href="#history">History</a></li>
	  <li><a data-toggle="tab" href="#diary">Diary</a></li>
	  @if($sales_confirmation)
	  	<li><a data-toggle="tab" href="#salesconfirmation" id = "salesConfirm">{{ucwords("ISFC".str_pad($sales_confirmation->id,6,"0",STR_PAD_LEFT))}}</a></li>
	  @endif
	  @if(!empty($purchase_orders))
	  	@foreach($purchase_orders as $purchase_order)
	  		<li><a data-toggle="tab" href="#purchaseordertab_{{$purchase_order->id}}" id = "purchaseorder_{{$purchase_order->id}}">{{ucwords("ISFP".str_pad($purchase_order->id,6,"0",STR_PAD_LEFT))}}</a></li>
	  	@endforeach
	  @endif
	  @if(!empty($proforma_invoices))
	  	@foreach($proforma_invoices as $proforma_invoice)
	  		<li><a data-toggle="tab" href="#proformainvoicetab_{{$proforma_invoice->id}}" id = "proformainvoice_{{$proforma_invoice->id}}">{{ucwords("ISFI".str_pad($proforma_invoice->id,6,"0",STR_PAD_LEFT)."P")}}</a></li>
	  	@endforeach
	  @endif
	  @if(!empty($invoices))
	  	@foreach($invoices as $invoice)
	  		<li><a data-toggle="tab" href="#invoicetab_{{$invoice->id}}" id = "invoice{{$invoice->id}}">{{ucwords("ISFI".str_pad($invoice->id,6,"0",STR_PAD_LEFT))}}</a></li>
	  	@endforeach
	  @endif
  
	</ul>

<div class="tab-content">
	<div id="overview" class="tab-pane fade in active">
		<div class = "fullwidth_container">
			<fieldset>
				<legend>Deal {{ucwords("ISFD".str_pad($deal->id,6,"0",STR_PAD_LEFT))}} for {{ucwords($parent_type)}} <b>{{ucwords($parent->{$parent_type.'_name'})}}</b></legend>
				
				<div class = "halfContentSection">
					<h4><u>Deal Details</u></h4>
					
					<label for = "">Client:</label>
						@if(is_object($parent))
						<a href = '{{ url("client/$parent->id") }}' class = "historyAdd" id = "client">{{ucwords($parent->{$parent_type.'_name'})}}</a>
						@endif
						</br>
					<label for = "contact">Contact:</label>
						@if(is_object($deal_contact))
						<a href = '{{ url("contact/$deal_contact->id") }}' class = "historyAdd" id = "contact">{{ucwords($deal_contact->title." ".$deal_contact->firstname." ".$deal_contact->lastname)}}</a>
						@else
							None
						@endif
						</br>
					<label for = "order_date">Deal Start Date:</label>
						<tag class = "order_date">{{date("d/m/y", strtotime($deal->order_date))}}</tag><br>
					<label for = "notes">Notes:</label>
						<tag class = "notes">{{$deal->notes}}</tag> </br>				
				</div>
				<div class = "halfContentSection" style = "text-align:right;">
				<!--map stuff-->
				<img src = "/images/handshake.jpg" style = "width:50%;border-radius:5px;">
				</div>
						
			</fieldset>
			
			<div>	
				<table id = "dealTable" class = "table table-striped" cellspacing="0" width="100%">
						<caption>Deal Timeline</caption>
						<tr>
							<th>Sales Confirmation</th>
							<td></td>
							<td></td>
						</tr>
						<tr>
							<th></th>
							<td>
								@if(is_object($sales_confirmation))
									<?php
									$theseProducts = DB::table('sales_confirmation_products')->where('confirmation_id',$sales_confirmation->id)->get();
									?>
									<a class = "salesConfirm">
										{{"ISFC".str_pad($sales_confirmation->id,6,"0",STR_PAD_LEFT)}} created on {{date("d/m/y", strtotime($sales_confirmation->confirmation_date))}} by {{ucfirst($sales_confirmation->user)}} 
										(@foreach($theseProducts as $product){{$product->product_code}}, @endforeach)
									</a>
								@else
									<button class = "btn btn-danger"><a href = "/deal/{{$deal->id}}/sales_confirmation/create">Create</a></button>
								@endif						
							</td>
							<td></td>
						</tr>
						<tr>
							<th>Purchase Order(s)</th>
							<td></td>
							<td></td>
						</tr>
						@if(!empty($purchase_orders))
							@foreach($purchase_orders as $purchase_order)
								<?php
								$theseProducts = DB::table('purchase_order_products')->where('purchase_order_id',$purchase_order->id)->get();
								$theseAccounts = DB::table('accounts')->where('link_type','purchase_order')->where('link_id',$purchase_order->id)->get();
								$outstanding_amount = floatVal($purchase_order->total_cost_price);
								
								if(is_array($theseAccounts)) {
									foreach($theseAccounts as $accountItem) {
										if($accountItem->type == "out") {
											//its an outgoing payment against the purchase order so deduct from the cost price
											$outstanding_amount = $outstanding_amount - floatVal($accountItem->amount);
										}
									}
								}
								
								
								?>
								<tr>
									<th></th>
									<td>								
										<a class = "purchaseOrderLink" data-id = "{{$purchase_order->id}}">{{"ISFP".str_pad($purchase_order->id,6,"0",STR_PAD_LEFT)}} created on {{date("d/m/y", strtotime($purchase_order->order_date))}} by {{ucfirst($purchase_order->user)}}
											(@foreach($theseProducts as $product){{$product->product_code}}, @endforeach)
										</a>
									</td>
									<td>
										@if($outstanding_amount != 0)
										<button class = "btn btn-danger confirmPayment" data-type = "purchase_order" data-id = "{{$purchase_order->id}}"><a href ="/accounts/create/purchase_order/{{$purchase_order->id}}">Confirm Payment (${{$outstanding_amount}} remaining)</a></button>
										@else
										<button class = "btn btn-success styledButton">Paid</button>
										@endif
									</td>
								</tr>
							@endforeach
							
							@if(!empty($sales_confirmation_products))
								<tr>
									<th></th>
									<td>
										<button class = "btn btn-danger">
											<a href = "/deal/{{$deal->id}}/purchase_order/create">
											@foreach($theseProducts as $product){{$product->product_code}}, @endforeach product(s) need Purchase Order
											</a>
										</button>
									</td>
									<td></td>
								</tr>
							@endif

						
						@else
							<tr>
								<th></th>
								<td>
									<button class = "btn btn-danger"><a href = "/deal/{{$deal->id}}/purchase_order/create">Create</a></button>
								</td>
								<td></td>
							</tr>						
						@endif		

						<tr>
							<th>ProForma Invoice(s)</th>
							<td></td>
							<td></td>
						</tr>
								@if(!empty($proforma_invoices))
									@foreach($proforma_invoices as $proforma)
									<tr>
										<th></th>
										<td>
											<?php
											$theseProducts = DB::table('proforma_invoice_products')->where('proforma_invoice_id',$proforma->id)->get();
											$theseAccounts = DB::table('accounts')->where('link_type','proforma_invoice')->where('link_id',$proforma->id)->get();
											$outstanding_amount = floatVal($proforma->total_sale_price);
								
											if(is_array($theseAccounts)) {
												foreach($theseAccounts as $accountItem) {
													if($accountItem->type == "in") {
														//its an incoming payment against the proforma invoice so deduct from the invoice total
														$outstanding_amount = $outstanding_amount - floatVal($accountItem->amount);
													}
												}
											}
											
											
											?>
											<a class = "proformaLink" data-id = "{{$proforma->id}}">{{"ISFI".str_pad($proforma->id,6,"0",STR_PAD_LEFT)."P"}} created on {{date("d/m/y", strtotime($proforma->proforma_invoice_date))}} by {{ucfirst($proforma->user)}}
												(@foreach($theseProducts as $product){{$product->product_code}}, @endforeach)
											</a>
										</td>
										<td>
											@if($outstanding_amount != 0)
											<button class = "btn btn-danger confirmPayment""><a href ="/accounts/create/proforma_invoice/{{$proforma->id}}">Confirm Payment (${{$outstanding_amount}} remaining)</a></button>
											@else
											<button class = "btn btn-success styledButton">Paid</button>
											@endif
										</td>
									</tr>
									@endforeach
									
									@if(!empty($nonProformaInvoicedProducts))
									<tr>
										<th></th>
										<td>
											<button class = "btn btn-danger"><a href = "/deal/{{$deal->id}}/proforma_invoice/create">
											@foreach($nonProformaInvoicedProducts as $product){{$product->product_code}}, @endforeach product(s) need Proforma Invoice
											</a></button>
										</td>
										<td></td>
									</tr>
									@endif
									

								@else
									<tr>
										<th></th>
										<td>
											<button class = "btn btn-danger"><a href = "/deal/{{$deal->id}}/proforma_invoice/create">Create</a></button>
										</td>
										<td></td>
								@endif						
							</td>
						</tr>
						<tr>
							<th>Invoice(s)</th>
							<td></td>
							<td></td>
						</tr>
						
						@if(!empty($invoices))
							@foreach($invoices as $invoice)
								<?php
								$theseProducts = DB::table('invoice_products')->where('invoice_id',$invoice->id)->get();
								$outstanding_amount = floatVal($invoice->total_sale_price);
								$theseAccounts = DB::table('accounts')->where('link_type','invoice')->where('link_id',$invoice->id)->get();
								
								if(is_array($theseAccounts)) {
									foreach($theseAccounts as $accountItem) {
										if($accountItem->type == "in") {
											//its an incoming payment against the proforma invoice so deduct from the invoice total
											$outstanding_amount = $outstanding_amount - floatVal($accountItem->amount);
										}
									}
								}
								?>
								<tr>
									<th></th>
									<td>								
										<a class = "invoiceLink" data-id = "{{$invoice->id}}">{{"ISFI".str_pad($invoice->id,6,"0",STR_PAD_LEFT)}} created on {{date("d/m/y", strtotime($invoice->invoice_date))}} by {{ucfirst($invoice->user)}}
											(@foreach($theseProducts as $product){{$product->product_code}}, @endforeach)
										</a>
									</td>
									<td>
										@if($outstanding_amount != 0)
										<button class = "btn btn-danger confirmPayment""><a href ="/accounts/create/invoice/{{$invoice->id}}">Confirm Payment (${{$outstanding_amount}} remaining)</a></button>
										@else
										<button class = "btn btn-success styledButton">Paid</button>
										@endif
									</td>
								</tr>
							@endforeach
							
							@if(!empty($nonInvoicedProducts))
								<tr>
									<th></th>
									<td>
										<button class = "btn btn-danger"><a href = "/deal/{{$deal->id}}/invoice/create">
										@foreach($nonInvoicedProducts as $product){{$product->product_code}}, @endforeach product(s) need Invoice
										</a></button>
									</td>
									<td></td>
								</tr>
							@endif
							

						@else
							<tr>
								<th></th>
								<td>
									<button class = "btn btn-danger"><a href = "/deal/{{$deal->id}}/invoice/create">Create</a></button>
								</td>
								<td></td>
						@endif							
					</td>
				</tr>
					</table>
				</div>
				
				
				
		</div>
	</div>
	<div id = "history" class = "tab-pane fade">
		<div class = "clientTools">
			<div class="btn-group">
			  <button type="button" class="btn btn-primary styledButton" title = "Memo"><a href = '{{ url("history/mo/$child_type/$parent->id") }}' >MO</a></button>
			  <button type="button" class="btn btn-primary styledButton" title = "Make Call"><a href = '{{ url("history/mc/$child_type/$parent->id") }}' >MC</a></button>
			  <button type="button" class="btn btn-primary styledButton" title = "Take Call"><a href = '{{ url("history/tc/$child_type/$parent->id") }}' >TC</a></button>
			  <button type="button" class="btn btn-primary styledButton" title = "Send Email"><a href = '{{ url("history/se/$child_type/$parent->id") }}' >SE</a></button>
			  <button type="button" class="btn btn-primary styledButton" title = "Receive Email"><a href = '{{ url("history/re/$child_type/$parent->id") }}'>RE</a></button>
			  <button type="button" class="btn btn-primary styledButton" title = "Send Text"><a href = '{{ url("history/st/$child_type/$parent->id") }}' >ST</a></button>
			  <button type="button" class="btn btn-primary styledButton" title = "Receive Text"><a href = '{{ url("history/rt/$child_type/$parent->id") }}' >RT</a></button>
			  <button type="button" class="btn btn-primary styledButton" title = "Schedule Diary"><a href = '{{ url("history/dy/$child_type/$parent->id") }}' >DY</a></button>
			  <button type="button" class="btn btn-primary styledButton" title = "Upload Contract"><a href = '{{ url("history/cn/$child_type/$parent->id") }}' >CN</a></button>
			</div>
		</div>
		
		<div class = "fullwidth_container">
				<table id="historyTable" class = "table table-striped" width="100%">
					<thead>
						<tr>
							<th>Date</th>
							<th>Type</th>
							<th>Details</th>
							@if($child_type != "client")
							<th>Client</th>
							@endif
							@if($child_type != "contact")
							<th>Contact</th>
							@endif
							@if($child_type != "order" AND $child_type != "order")
							<th>Order / Order</th>
							@endif
							<th>User</th>
						</tr>
					</thead>
					
					<tbody>
					@if(is_array($deal_history))
						@foreach($deal_history as $history)
							<?php
							$this_client = DB::table($history->parent_type)->where('id', $history->parent_id)->first();

							$this_contact = DB::table('contact')->where('id', $history->contact_id)->first();
						
							if($history->child_type) {
								$this_child = DB::table($history->child_type)->where('id',$history->child_id)->first();
							} else {
								$this_child = null;
							}
						
							$histDate = explode(" ",$history->date);
							$histTime = date("H:i",strtotime($histDate[1]));
							$histDate = date("d/m/y", strtotime($histDate[0]));
						
						
							?>
							<tr>
								<td>{{$histDate}} {{$histTime}}</td>
								<td>
									{{strtoupper($history->action_type)}}
									@if($history->file)
										@if($history->action_type == "SC" OR $history->action_type == "PO" OR $history->action_type == "PI" OR $history->action_type == "IN")
											<a href = "/data/{{$history->parent_type}}/{{$history->parent_id}}/{{$history->child_type}}/{{$history->child_id}}/{{$history->file}}" target = "_blank"><i class="fa fa-paperclip" aria-hidden="true"></i></a>
										@elseif($history->action_type == "AC")
											<a href = "/data/{{$history->accounts_id}}/{{$history->file}}" target = "_blank"><i class="fa fa-paperclip" aria-hidden="true"></i></a>										
										@else
											<a href = "/data/{{$history->parent_type}}/{{$parent->id}}/history/{{$history->id}}/{{$history->file}}" target = "_blank"><i class="fa fa-paperclip" aria-hidden="true"></i></a>
										@endif
									@endif 
								</td>
								<td>
									<div class = "detailsCell" title = "{{$history->details}}">
										{{$history->details}}
									</div>
								</td>
								@if($child_type != "client")
									<td>								
										@if(is_object($this_client))
											<a href = '../{{$history->parent_type}}/{{$this_client->id}}'>{{$this_client->{$history->parent_type.'_name'} }}</a>
										@endif								
									</td>
								@endif	
								@if($child_type != "contact")						
									<td>							
										@if(is_object($this_contact))
											<a href = '../contact/{{$this_contact->id}}'>{{ucwords($this_contact->firstname)}} {{ucwords($this_contact->lastname)}}</a>
										@endif								
									</td>
								@endif
								@if($child_type != "order" AND $child_type != "order")
									<td>								
										@if(is_object($this_child))
											<a href = '../{{$history->child_type}}/{{$this_child->id}}'>{{ucwords($this_child->id)}}</a>
										@endif								
									</td>
								@endif
								<td>{{ucfirst($history->user)}}</td>
							</tr>
						@endforeach
					@endif
					</tbody>
 
			</table>
		</div>
  	</div>
  	<div id = "diary" class = "tab-pane fade">
  	
		<div class = "clientTools">
			<div class="btn-group">
			  <button type="button" class="btn btn-primary styledButton" title = "Schedule Diary"><a href = '{{ url("history/dy/$child_type/$parent->id") }}' >DY</a></button>
			</div>
		</div>

  		<div class = "fullwidth_container">
			 <table id="diaryTable" class = "table table-striped" width="100%">
					<thead>
						<tr>
							<th>Details</th>
							<th>Client</th>
							<th>Contact</th>
							<th>Scheduled By</th>
							<th>Scheduled For</th>
							<th>Scheduled Date</th>
						</tr>
					</thead>
				
					<tbody>
					@if(is_array($deal_diary))
					@foreach($deal_diary as $diary)
						<?php
						$this_contact = DB::table('contact')->where('id', $diary->contact_id)->first();
						$this_parent = DB::table($diary->parent_type)->where('id', $diary->parent_id)->first();
						$diaryDate = explode(" ",$diary->date);
						$diaryTime = $diaryDate[1];
						$diaryDate = date("d/m/y", strtotime($diaryDate[0]));							
						?>
						<tr onclick = "window.location='{{ url("history/actiondiary/$diary->id") }}'">
							<td><b>{{$diaryDate}} {{$diaryTime}} - {{strtoupper($diary->action_type)}}</b> - {{$diary->details}}</td>
							<td>
								@if(is_object($this_parent))
									<a href = '../{{$diary->parent_type}}/{{$this_parent->id}}'>{{$this_parent->{$diary->parent_type.'_name'} }}</a>
								@endif
							</td>	
							<td>
								@if(is_object($this_contact))
								<a href = '/contact/{{$this_contact->id}}'>{{ucwords($this_contact->firstname)}} {{ucwords($this_contact->lastname)}}</a>
								@endif
							</td>
							<td>{{ucfirst($diary->user)}}</td>
							<td>{{ucfirst($diary->user_for)}}</td>
							<td>{{$diaryDate}}</td>
						</tr>
					@endforeach
				@endif
				</tbody>	
			</table>
		</div>
  	
  	</div>
  	@if($sales_confirmation)
    <div id = "salesconfirmation" class = "tab-pane fade">
    	<?php
    	$confirmation_products = DB::table('sales_confirmation_products')->where('confirmation_id',$sales_confirmation->id)->get();
    	$related_history = DB::table('history')->where('child_id',$deal->id)->where('child_type',"deal")->where('details','LIKE',"ISFC".str_pad($sales_confirmation->id,6,"0",STR_PAD_LEFT)."%")->first();
    	?>
    	

  		 <div class = "fullwidth_container">
  		 	 <legend>Sales Confirmation <b>{{ucwords("ISFC".str_pad($sales_confirmation->id,6,"0",STR_PAD_LEFT))}}</b></legend>

			 <table id="salesConfProducts" class = "table table-striped" width="100%">
					<thead>
						<tr>
							<th>Product Code</th>
							<th>Unit Cost Price ($)</th>
							<th>Unit Sale Price ($)</th>
							<th>Quantity</th>
							<th>Q Type</th>
							<th>Frequency</th>
							<th>Total Cost ($)</th>
							<th>Total Sale ($)</th>
							<th>Margin ($)</th>
						</tr>
					</thead>
				
					<tbody>
						@foreach($confirmation_products as $confirmation_product)
							<tr>
								<td>{{strtoupper($confirmation_product->product_code)}}</td>
								<td>${{$confirmation_product->unit_cost_price}}</td>
								<td>${{$confirmation_product->unit_sale_price}}</td>
								<td>{{$confirmation_product->quantity}}</td>
								<td>{{strtoupper($confirmation_product->quantity_type)}}</td>
								<td>{{ucwords($confirmation_product->frequency)}}</td>
								<td>${{$confirmation_product->total_cost_price}}</td>
								<td>${{$confirmation_product->total_sale_price}}</td>
								<td>${{$confirmation_product->total_sale_price - $confirmation_product->total_cost_price}}</td>
							</tr>
						@endforeach
					</tbody>	
			</table>
			
			<br></br>
			
			<div class = "dealFileTableHolder">
				<table id = "salesConf" class = "table table-striped" style = "display:inline-block;width:100%;">
					<tr>
						<th>Confirmation Date</th>
						<td>{{date("d/m/y", strtotime($sales_confirmation->confirmation_date))}}</td>
					</tr>
					<tr>
						<th>Payment Date</th>
						<td>
							@if($sales_confirmation->payment_date != "0000-00-00")
								{{date("d/m/y", strtotime($sales_confirmation->payment_date))}}
							@endif
						</td>
					</tr>
					<tr>
						<th>Delivery Date</th>
						<td>
							@if($sales_confirmation->delivery_date != "0000-00-00")
								{{date("d/m/y", strtotime($sales_confirmation->delivery_date))}}
							@endif
						</td>
					</tr>
					<tr>
						<th>Destination Country</th>
						<td>{{ucwords($sales_confirmation->destination_country)}}</td>
					</tr>
					<tr>
						<th>Shipping From</th>
						<td>{{ucwords($sales_confirmation->shipping_from)}}</td>
					</tr>
					<tr>
						<th>Shipping Address</th>
						<td>{{ucwords($sales_confirmation->shipping_address)}}</td>
					</tr>
					<tr>
						<th>Shipping Method</th>
						<td>{{ucwords($sales_confirmation->shipping_method)}}</td>
					</tr>
					<tr>
						<th>Total Cost Price</th>
						<td>${{ucwords($sales_confirmation->total_cost_price)}}</td>
					</tr>
					<tr>
						<th>Total Sale Price</th>
						<td>${{ucwords($sales_confirmation->total_sale_price)}}</td>
					</tr>
					<tr>
						<th>Notes</th>
						<td>{{ucwords($sales_confirmation->notes)}}</td>
					</tr>
				</table>
			</div>
			<div class = "dealFilePDFHolder">
				@if($related_history)
					<object width = "100%" height = "100%" type = "application/pdf" data="/data/{{$related_history->parent_type}}/{{$related_history->parent_id}}/{{$related_history->child_type}}/{{$related_history->child_id}}/{{$related_history->file}}">
						<p>Sorry, couldn't display PDF. Please inform IT.</p>
					</object>
				@endif
			</div>
		</div>
  	
  	</div>
  	@endif
  	@if(is_array($purchase_orders))
	  	@foreach($purchase_orders as $purchase_order)
			<div id = "purchaseordertab_{{$purchase_order->id}}" class = "tab-pane fade">
					<?php
					$purchase_order_products = DB::table('purchase_order_products')->where('purchase_order_id',$purchase_order->id)->get();
					$term = DB::table('terms_conditions')->where('id',$purchase_order->terms)->first();
    				$related_history = DB::table('history')->where('child_id',$deal->id)->where('child_type',"deal")->where('details','LIKE',"ISFP".str_pad($purchase_order->id,6,"0",STR_PAD_LEFT)."%")->first();

					?>

					 <div class = "fullwidth_container">
					 	 <legend>Purchase Order <b>{{ucwords("ISFP".str_pad($purchase_order->id,6,"0",STR_PAD_LEFT))}}</b></legend>

						 <table class = "table table-striped" width="100%">
								<thead>
									<tr>
										<th>Product Code</th>
										<th>Unit Cost Price ($)</th>
										<th>Unit Sale Price ($)</th>
										<th>Quantity</th>
										<th>Q Type</th>
										<th>Frequency</th>
										<th>Total Cost ($)</th>
										<th>Total Sale ($)</th>
										<th>Margin ($)</th>
									</tr>
								</thead>
				
								<tbody>
									@foreach($purchase_order_products as $purchase_order_product)
										<tr>
											<td>{{strtoupper($purchase_order_product->product_code)}}</td>
											<td>${{$purchase_order_product->unit_cost_price}}</td>
											<td>${{$purchase_order_product->unit_sale_price}}</td>
											<td>{{$purchase_order_product->quantity}}</td>
											<td>{{strtoupper($purchase_order_product->quantity_type)}}</td>
											<td>{{ucwords($purchase_order_product->frequency)}}</td>
											<td>${{$purchase_order_product->total_cost_price}}</td>
											<td>${{$purchase_order_product->total_sale_price}}</td>
											<td>${{$purchase_order_product->total_sale_price - $purchase_order_product->total_cost_price}}</td>
										</tr>
									@endforeach
								</tbody>	
						</table>
			
						<br></br>
			
						<div class = "dealFileTableHolder">
							<table class = "table table-striped" style = "display:inline-block;width:100%;">
								<tr>
									<th>Purchase Order Date</th>
									<td>{{date("d/m/y", strtotime($purchase_order->order_date))}}</td>
								</tr>
								<tr>
									<th>Payment Date</th>
									<td>
										@if($purchase_order->payment_date != "0000-00-00")
											{{date("d/m/y", strtotime($purchase_order->payment_date))}}
										@endif
									</td>
								</tr>
								<tr>
									<th>Delivery Date</th>
									<td>
										@if($purchase_order->delivery_date != "0000-00-00")
											{{date("d/m/y", strtotime($purchase_order->delivery_date))}}
										@endif
									</td>
								</tr>
								<tr>
									<th>Invoiced To</th>
									<td>{{ucwords($purchase_order->invoiced_to)}}</td>
								</tr>
								<tr>
									<th>Destination Country</th>
									<td>{{ucwords($purchase_order->destination_country)}}</td>
								</tr>
								<tr>
									<th>Shipping From</th>
									<td>{{ucwords($purchase_order->shipping_from)}}</td>
								</tr>
								<tr>
									<th>Shipping Address</th>
									<td>{{ucwords($purchase_order->shipping_address)}}</td>
								</tr>
								<tr>
									<th>Shipping Method</th>
									<td>{{ucwords($purchase_order->shipping_method)}}</td>
								</tr>
								<tr>
									<th>Shipping Company</th>
									<td>{{ucwords($purchase_order->shipping_company)}}</td>
								</tr>
								<tr>
									<th>Shipping Contact Name</th>
									<td>{{ucwords($purchase_order->shipping_contact_name)}}</td>
								</tr>
								<tr>
									<th>Shipping Contact Number</th>
									<td>{{ucwords($purchase_order->shipping_contact_number)}}</td>
								</tr>
								<tr>
									<th>Delivery Terms</th>
									<td>@if($term){{ucwords($term->type)}}@endif</td>
								</tr>
								<tr>
									<th>Total Cost Price</th>
									<td>${{ucwords($purchase_order->total_cost_price)}}</td>
								</tr>
								<tr>
									<th>Total Sale Price</th>
									<td>${{ucwords($purchase_order->total_sale_price)}}</td>
								</tr>
					
								<tr>
									<th>Notes</th>
									<td>{{ucwords($purchase_order->notes)}}</td>
								</tr>
							</table>
						</div>
						
						<div class = "dealFilePDFHolder">
							@if($related_history)
								<object width = "100%" height = "100%" type = "application/pdf" data="/data/{{$related_history->parent_type}}/{{$related_history->parent_id}}/{{$related_history->child_type}}/{{$related_history->child_id}}/{{$related_history->file}}">
									<p>Sorry, couldn't display PDF. Please inform IT.</p>
								</object>
							@endif
						</div>
						
					</div>
	
				</div>
  		@endforeach
	  @endif
	@if(is_array($proforma_invoices))
	  	@foreach($proforma_invoices as $proforma)
			<div id = "proformainvoicetab_{{$proforma->id}}" class = "tab-pane fade">
					<?php
					$proforma_invoice_products = DB::table('proforma_invoice_products')->where('proforma_invoice_id',$proforma->id)->get();
					$term = DB::table('terms_conditions')->where('id',$proforma->terms)->first();
					$related_history = DB::table('history')->where('child_id',$deal->id)->where('child_type',"deal")->where('details','LIKE',"ISFI".str_pad($proforma->id,6,"0",STR_PAD_LEFT)."P%")->first();

					?>
					
					 <div class = "fullwidth_container">
					 	<legend>Proforma Invoice <b>{{ucwords("ISFI".str_pad($proforma->id,6,"0",STR_PAD_LEFT)."P")}}</b></legend>

						 <table class = "table table-striped" width="100%">
								<thead>
									<tr>
										<th>Product Code</th>
										<th>Unit Cost Price ($)</th>
										<th>Unit Sale Price ($)</th>
										<th>Quantity</th>
										<th>Q Type</th>
										<th>Frequency</th>
										<th>Total Cost ($)</th>
										<th>Total Sale ($)</th>
										<th>Margin ($)</th>
									</tr>
								</thead>
				
								<tbody>
									@foreach($proforma_invoice_products as $proforma_invoice_product)
										<tr>
											<td>{{strtoupper($proforma_invoice_product->product_code)}}</td>
											<td>${{$proforma_invoice_product->unit_cost_price}}</td>
											<td>${{$proforma_invoice_product->unit_sale_price}}</td>
											<td>{{$proforma_invoice_product->quantity}}</td>
											<td>{{strtoupper($proforma_invoice_product->quantity_type)}}</td>
											<td>{{ucwords($proforma_invoice_product->frequency)}}</td>
											<td>${{$proforma_invoice_product->total_cost_price}}</td>
											<td>${{$proforma_invoice_product->total_sale_price}}</td>
											<td>${{$proforma_invoice_product->total_sale_price - $proforma_invoice_product->total_cost_price}}</td>
										</tr>
									@endforeach
								</tbody>	
						</table>
			
						<br></br>
			
						<div class = "dealFileTableHolder">
							<table class = "table table-striped" style = "display:inline-block;width:100%;">
								<tr>
									<th>Purchase Order Date</th>
									<td>{{date("d/m/y", strtotime($proforma->proforma_invoice_date))}}</td>
								</tr>
								<tr>
									<th>Payment Date</th>
									<td>
										@if($proforma->payment_date != "0000-00-00")
											{{date("d/m/y", strtotime($proforma->payment_date))}}
										@endif
									</td>
								</tr>
								<tr>
									<th>Delivery Date</th>
									<td>
										@if($proforma->delivery_date != "0000-00-00")
											{{date("d/m/y", strtotime($proforma->delivery_date))}}
										@endif
									</td>
								</tr>
								<tr>
									<th>Invoiced To</th>
									<td>{{ucwords($proforma->invoiced_to)}}</td>
								</tr>
								<tr>
									<th>Destination Country</th>
									<td>{{ucwords($proforma->destination_country)}}</td>
								</tr>
								<tr>
									<th>Shipping From</th>
									<td>{{ucwords($proforma->shipping_from)}}</td>
								</tr>
								<tr>
									<th>Shipping Address</th>
									<td>{{ucwords($proforma->shipping_address)}}</td>
								</tr>
								<tr>
									<th>Shipping Method</th>
									<td>{{ucwords($proforma->shipping_method)}}</td>
								</tr>
								<tr>
									<th>Shipping Company</th>
									<td>{{ucwords($proforma->shipping_company)}}</td>
								</tr>
								<tr>
									<th>Shipping Contact Name</th>
									<td>{{ucwords($proforma->shipping_contact_name)}}</td>
								</tr>
								<tr>
									<th>Shipping Contact Number</th>
									<td>{{ucwords($proforma->shipping_contact_number)}}</td>
								</tr>
								<tr>
									<th>Delivery Terms</th>
									<td>@if($term){{ucwords($term->type)}}@endif</td>
								</tr>
								<tr>
									<th>Total Cost Price</th>
									<td>${{ucwords($proforma->total_cost_price)}}</td>
								</tr>
								<tr>
									<th>Total Sale Price</th>
									<td>${{ucwords($proforma->total_sale_price)}}</td>
								</tr>
					
								<tr>
									<th>Notes</th>
									<td>{{ucwords($proforma->notes)}}</td>
								</tr>
							</table>
						</div>
						
						<div class = "dealFilePDFHolder">
							@if($related_history)
								<!--<span style = "text-align:center;" class = "goFullscreen"><i class="fa fa-arrows-alt" aria-hidden="true"></i> Go Fullscreen</span>-->
								<object class = "docPDF" width = "100%" height = "100%" type = "application/pdf" data="/data/{{$related_history->parent_type}}/{{$related_history->parent_id}}/{{$related_history->child_type}}/{{$related_history->child_id}}/{{$related_history->file}}">
									<p>Sorry, couldn't display PDF. Please inform IT.</p>
								</object>
							@endif
							
						</div>
						
					</div>
	
				</div>
  		@endforeach
	  @endif
	  @if(is_array($invoices))
	  	@foreach($invoices as $invoice)
			<div id = "invoicetab_{{$invoice->id}}" class = "tab-pane fade">
					<?php
					$invoice_products = DB::table('invoice_products')->where('invoice_id',$invoice->id)->get();
					$term = DB::table('terms_conditions')->where('id',$invoice->terms)->first();
					$related_history = DB::table('history')->where('child_id',$deal->id)->where('child_type',"deal")->where('details','LIKE',"ISFI".str_pad($invoice->id,6,"0",STR_PAD_LEFT)."%")->first();

					?>

					 <div class = "fullwidth_container">
					 	 <legend>Invoice <b>{{ucwords("ISFI".str_pad($invoice->id,6,"0",STR_PAD_LEFT))}}</b></legend>
						 <table class = "table table-striped" width="100%">
								<thead>
									<tr>
										<th>Product Code</th>
										<th>Unit Cost Price ($)</th>
										<th>Unit Sale Price ($)</th>
										<th>Quantity</th>
										<th>Q Type</th>
										<th>Frequency</th>
										<th>Total Cost ($)</th>
										<th>Total Sale ($)</th>
										<th>Margin ($)</th>
									</tr>
								</thead>
				
								<tbody>
									@foreach($invoice_products as $invoice_product)
										<tr>
											<td>{{strtoupper($invoice_product->product_code)}}</td>
											<td>${{$invoice_product->unit_cost_price}}</td>
											<td>${{$invoice_product->unit_sale_price}}</td>
											<td>{{$invoice_product->quantity}}</td>
											<td>{{strtoupper($invoice_product->quantity_type)}}</td>
											<td>{{ucwords($invoice_product->frequency)}}</td>
											<td>${{$invoice_product->total_cost_price}}</td>
											<td>${{$invoice_product->total_sale_price}}</td>
											<td>${{$invoice_product->total_sale_price - $invoice_product->total_cost_price}}</td>
										</tr>
									@endforeach
								</tbody>	
						</table>
			
						<br></br>
						
						<div class = "dealFileTableHolder">
							<table class = "table table-striped" style = "display:inline-block;width:100%;">
								<tr>
									<th>Invoice Date</th>
									<td>{{date("d/m/y", strtotime($invoice->invoice_date))}}</td>
								</tr>
								<tr>
									<th>Payment Date</th>
									<td>
										@if($invoice->payment_date != "0000-00-00")
											{{date("d/m/y", strtotime($invoice->payment_date))}}
										@endif
									</td>
								</tr>
								<tr>
									<th>Delivery Date</th>
									<td>
										@if($invoice->delivery_date != "0000-00-00")
											{{date("d/m/y", strtotime($invoice->delivery_date))}}
										@endif
									</td>
								</tr>
								<tr>
									<th>Invoiced To</th>
									<td>{{ucwords($invoice->invoiced_to)}}</td>
								</tr>
								<tr>
									<th>Destination Country</th>
									<td>{{ucwords($invoice->destination_country)}}</td>
								</tr>
								<tr>
									<th>Shipping From</th>
									<td>{{ucwords($invoice->shipping_from)}}</td>
								</tr>
								<tr>
									<th>Shipping Address</th>
									<td>{{ucwords($invoice->shipping_address)}}</td>
								</tr>
								<tr>
									<th>Shipping Method</th>
									<td>{{ucwords($invoice->shipping_method)}}</td>
								</tr>
								<tr>
									<th>Shipping Company</th>
									<td>{{ucwords($invoice->shipping_company)}}</td>
								</tr>
								<tr>
									<th>Shipping Contact Name</th>
									<td>{{ucwords($invoice->shipping_contact_name)}}</td>
								</tr>
								<tr>
									<th>Shipping Contact Number</th>
									<td>{{ucwords($invoice->shipping_contact_number)}}</td>
								</tr>
								<tr>
									<th>Delivery Terms</th>
									<td>@if($term){{ucwords($term->type)}}@endif</td>
								</tr>
								<tr>
									<th>Total Cost Price</th>
									<td>${{ucwords($invoice->total_cost_price)}}</td>
								</tr>
								<tr>
									<th>Total Sale Price</th>
									<td>${{ucwords($invoice->total_sale_price)}}</td>
								</tr>
					
								<tr>
									<th>Notes</th>
									<td>{{ucwords($invoice->notes)}}</td>
								</tr>
							</table>
						</div>
						
						<div class = "dealFilePDFHolder">
							@if($related_history)
								<object width = "100%" height = "100%" type = "application/pdf" data="/data/{{$related_history->parent_type}}/{{$related_history->parent_id}}/{{$related_history->child_type}}/{{$related_history->child_id}}/{{$related_history->file}}">
									<p>Sorry, couldn't display PDF. Please inform IT.</p>
								</object>
							@endif
						</div>
						
					</div>
	
				</div>
  		@endforeach
	  @endif
  	
</div>
@stop