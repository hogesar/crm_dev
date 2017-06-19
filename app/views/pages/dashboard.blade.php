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
	
	$('body').find("#dashboard_menu").addClass("active");
	$('body').find("#dashboard").removeClass("collapse").addClass("collapsed");
	$('body').find("#viewdashoard").addClass("active");
});
</script>
<style>
	.dashboard_widget_holder {
		width:45%;
		padding:1%;
		margin:1.5%;
		display:inline-block;
		vertical-align:top;
	}
	
	
	.panel-title {
		background-color:#{{Session::get('colour1')}}!important;
		color:{{Session::get('colour3')}}!important;
	}
	
	.panel-heading {
		background-color:#{{Session::get('colour1')}}!important;
		background-image:none!important;
	}
	
	.positive {
		color:#{{Session::get('colour1')}}!important;
	}
	
	.negative {
		color:red;
	}
</style>
@stop
@section('content')

<?php
//get date filter
if($date_filter == "this_month") {
	$date_from = date('Y-m-d', strtotime('first day of this month'));
	$date_to = date('Y-m-d', strtotime('last day of this month'));
} else if($date_filter == "last_month") {
	$date_from = date('Y-m-d', strtotime('first day of last month'));
	$date_to = date('Y-m-d', strtotime('last day of last month'));
} else if($date_filter == "last_week") {
	$previous_week = strtotime("-1 week +1 day");
	$date_from = date('Y-m-d', strtotime('last week monday'));
	$date_to = date('Y-m-d', strtotime('last week sunday'));
} else if($date_filter == "this_week") {
	$date_from = date('Y-m-d', strtotime('last monday', strtotime('tomorrow')));
	$date_to = date('Y-m-d', strtotime('+6 days', strtotime('last monday', strtotime('tomorrow'))));
}


//all enquiry arrays
$all_enquiries = DB::table('enquiry')->where('enquiry_date', '>=', $date_from)->where('enquiry_date', '<=', $date_to)->get();
$all_enquiries_count = sizeof($all_enquiries);

$open_enquiries = DB::table('enquiry')->where('status','open')->where('enquiry_date', '>=', $date_from)->where('enquiry_date', '<=', $date_to)->get();
$open_enquiries_count = sizeof($open_enquiries);

$successful_enquiries = DB::table('enquiry')->where('status','deal')->where('enquiry_date', '>=', $date_from)->where('enquiry_date', '<=', $date_to)->get();
$successful_enquiries_count = sizeof($successful_enquiries);

$cancelled_enquiries = DB::table('enquiry')->where('status','cancelled')->where('enquiry_date', '>=', $date_from)->where('enquiry_date', '<=', $date_to)->get();
$cancelled_enquiries_count = sizeof($cancelled_enquiries);

//all deals
$all_deals = DB::table('deal')->where('order_date', '>=', $date_from)->where('order_date', '<=', $date_to)->get();
$all_deals_count = sizeof($all_deals);

$open_deals = DB::table('deal')->where('status','open')->where('order_date', '>=', $date_from)->where('order_date', '<=', $date_to)->get();
$open_deals_count = sizeof($open_deals);

$completed_deals = DB::table('deal')->where('status','completed')->where('order_date', '>=', $date_from)->where('order_date', '<=', $date_to)->get();
$completed_deals_count = sizeof($completed_deals);

$cancelled_deals = DB::table('deal')->where('status','cancelled')->where('order_date', '>=', $date_from)->where('order_date', '<=', $date_to)->get();
$cancelled_deals_count = sizeof($cancelled_deals);

//all invoice arrays
$all_invoices = DB::table('invoice')->where('invoice_date', '>=', $date_from)->where('invoice_date', '<=', $date_to)->get();
$all_invoices_count = sizeof($all_invoices);
$all_invoices_balance = DB::table('invoice')->where('invoice_date', '>=', $date_from)->where('invoice_date', '<=', $date_to)->sum('total_sale_price');

$outstanding_invoices = DB::table('invoice')->where('status','awaiting payment')->where('invoice_date', '>=', $date_from)->where('invoice_date', '<=', $date_to)->get();
$outstanding_invoices_count = sizeof($outstanding_invoices);
$outstanding_invoices_balance = DB::table('invoice')->where('status','awaiting payment')->where('invoice_date', '>=', $date_from)->where('invoice_date', '<=', $date_to)->sum('total_sale_price');

$paid_invoices = DB::table('invoice')->where('status','paid')->where('invoice_date', '>=', $date_from)->where('invoice_date', '<=', $date_to)->get();
$paid_invoices_count = sizeof($paid_invoices);
$paid_invoices_balance = DB::table('invoice')->where('status','paid')->where('invoice_date', '>=', $date_from)->where('invoice_date', '<=', $date_to)->sum('total_sale_price');

$cancelled_invoices = DB::table('invoice')->where('status','cancelled')->where('invoice_date', '>=', $date_from)->where('invoice_date', '<=', $date_to)->get();
$cancelled_invoices_count = sizeof($cancelled_invoices);
$cancelled_invoices_balance = DB::table('invoice')->where('status','cancelled')->where('invoice_date', '>=', $date_from)->where('invoice_date', '<=', $date_to)->sum('total_sale_price');

//all purchase order arrays
$all_purchase_orders = DB::table('purchase_order')->where('order_date', '>=', $date_from)->where('order_date', '<=', $date_to)->get();
$all_purchase_orders_count = sizeof($all_purchase_orders);
$all_purchase_orders_balance = DB::table('purchase_order')->where('order_date', '>=', $date_from)->where('order_date', '<=', $date_to)->sum('total_cost_price');

$outstanding_purchase_orders = DB::table('purchase_order')->where('status','awaiting payment')->where('order_date', '>=', $date_from)->where('order_date', '<=', $date_to)->get();
$outstanding_purchase_orders_count = sizeof($outstanding_purchase_orders);
$outstanding_purchase_orders_balance = DB::table('purchase_order')->where('status','awaiting payment')->where('order_date', '>=', $date_from)->where('order_date', '<=', $date_to)->sum('total_cost_price');

$paid_purchase_orders = DB::table('purchase_order')->where('status','paid')->where('order_date', '>=', $date_from)->where('order_date', '<=', $date_to)->get();
$paid_purchase_orders_count = sizeof($paid_purchase_orders);
$paid_purchase_orders_balance = DB::table('purchase_order')->where('status','paid')->where('order_date', '>=', $date_from)->where('order_date', '<=', $date_to)->sum('total_cost_price');

$cancelled_purchase_orders = DB::table('purchase_order')->where('status','cancelled')->where('order_date', '>=', $date_from)->where('order_date', '<=', $date_to)->get();
$cancelled_purchase_orders_count = sizeof($cancelled_purchase_orders);
$cancelled_purchase_orders_balance = DB::table('purchase_order')->where('status','cancelled')->where('order_date', '>=', $date_from)->where('order_date', '<=', $date_to)->sum('total_cost_price');

?>
<div style = "text-align:center;width:45%;display:block;margin:0 auto;">
	<h3 style = "margin-top:0px;">{{date("d/m/y", strtotime($date_from))." - ".date("d/m/y", strtotime($date_to))}}</h3>
	{{ Form::open(array('url' => 'dashboard/filter', 'role'=>'form', 'id'=>'dashboard_filter', 'class'=>'form-inline')) }}	
		<div class = "form-group">
			<select id = "date_filter" name = "date_filter" class = "form-control form-inline">
				@if(isset($date_filter))
					<?php $date_filter_text = explode("_",$date_filter);?>
					<option value = "{{$date_filter}}">{{ucwords($date_filter_text[0]." ".$date_filter_text[1])}}</option>
				@endif
				<option value = "this_month">This Month</option>
				<option value = "this_week">This Week</option>
				<option value = "last_week">Last Week</option>
				<option value = "last_month">Last Month</option>
			</select>
		</div>
	
		<button class = "btn btn-submit styledButton">Filter</button>
	{{ Form::close()}}
</div>
<div class = "dashboard_widget_holder">
	<div class="panel-group" id="enquiryaccordion" role="tablist" aria-multiselectable="true">
	  <div class="panel panel-default">
		<div class="panel-heading" role="tab" id="enquiries_widget">
		  <h4 class="panel-title">
			<a role="button" data-toggle="collapse" data-parent="#accordion" href="#enquirycollapse" aria-expanded="true" aria-controls="enquirycollapse">
			  Enquiries
			</a>
		  </h4>
		</div>
		<div id="enquirycollapse" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="enquiries_widget">
		  <div class="panel-body">
		  	<table class = "table table-hover">
		  		<tr onclick = "window.location='{{ url("enquiry/$date_filter/open") }}'" href = '{{ url("enquiry/$date_filter/open") }}' class = "historyAdd">
		  			<th>Open Enquiries</th>
		  			<td>{{$open_enquiries_count}}</td>
		  		</tr>
		  		<tr onclick = "window.location='{{ url("enquiry/$date_filter/deal") }}'" href = '{{ url("enquiry/$date_filter/deal") }}' class = "historyAdd">
		  			<th>Successful Enquiries</th>
		  			<td>{{$successful_enquiries_count}}</td>
		  		</tr>
		  		<tr onclick = "window.location='{{ url("enquiry/$date_filter/cancelled") }}'" href = '{{ url("enquiry/$date_filter/cancelled") }}' class = "historyAdd">
		  			<th>Cancelled Enquiries</th>
		  			<td>{{$cancelled_enquiries_count}}</td>
		  		</tr>
		  		<tr onclick = "window.location='{{ url("enquiry") }}'" href = '{{ url("enquiry") }}' class = "historyAdd">
		  			<th>Total Enquiries</th>
		  			<td>{{$all_enquiries_count}}</td>
		  		</tr>
		  	</table>
			
		  </div>
		</div>
	  </div>
	</div>
</div>

<div class = "dashboard_widget_holder">
	<div class="panel-group" id="dealaccordion" role="tablist" aria-multiselectable="true">
	  <div class="panel panel-default">
		<div class="panel-heading" role="tab" id="deals_widget">
		  <h4 class="panel-title">
			<a role="button" data-toggle="collapse" data-parent="#accordion" href="#dealcollapse" aria-expanded="true" aria-controls="dealcollapse">
			  Deals
			</a>
		  </h4>
		</div>
		<div id="dealcollapse" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="deals_widget">
		  <div class="panel-body">
		  	<table class = "table table-hover">
		  		<tr onclick = "window.location='{{ url("deal/$date_filter/open") }}'" href = '{{ url("deal/$date_filter/open") }}' class = "historyAdd">
		  			<th>Open Deals</th>
		  			<td>{{$open_deals_count}}</td>
		  		</tr>
		  		<tr onclick = "window.location='{{ url("deal/$date_filter/completed") }}'" href = '{{ url("deal/$date_filter/completed") }}' class = "historyAdd">
		  			<th>Completed Deals</th>
		  			<td>{{$completed_deals_count}}</td>
		  		</tr>
		  		<tr onclick = "window.location='{{ url("deal/$date_filter/cancelled") }}'" href = '{{ url("deal/$date_filter/cancelled") }}' class = "historyAdd">
		  			<th>Cancelled Deals</th>
		  			<td>{{$cancelled_deals_count}}</td>
		  		</tr>
		  		<tr onclick = "window.location='{{ url("deal") }}'" href = '{{ url("deal") }}' class = "historyAdd">
		  			<th>Total Deals</th>
		  			<td>{{$all_deals_count}}</td>
		  		</tr>
		  	</table>
			
		  </div>
		</div>
	  </div>
	</div>
</div>

<div class = "dashboard_widget_holder">
	<div class="panel-group" id="poaccordion" role="tablist" aria-multiselectable="true">
	  <div class="panel panel-default">
		<div class="panel-heading" role="tab" id="po_widget">
		  <h4 class="panel-title">
			<a role="button" data-toggle="collapse" data-parent="#accordion" href="#pocollapse" aria-expanded="true" aria-controls="pocollapse">
			  Purchase Orders
			</a>
		  </h4>
		</div>
		<div id="pocollapse" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="po_widget">
		  <div class="panel-body">
		  	<table class = "table table-hover">
		  		<tr>
		  			<th>Outstanding Purchase Orders</th>
		  			<td>{{$outstanding_purchase_orders_count}}</td>
		  			<td class = "negative">${{$outstanding_purchase_orders_balance}}</td>
		  		</tr>
		  		<tr>
		  			<th>Paid Purchase Orders</th>
		  			<td>{{$paid_purchase_orders_count}}</td>
		  			<td class = "positive">${{$paid_purchase_orders_balance}}</td>
		  		</tr>
		  		<tr>
		  			<th>Cancelled Purchase Orders</th>
		  			<td>{{$cancelled_purchase_orders_count}}</td>
		  			<td class = "negative">${{$cancelled_purchase_orders_balance}}</td>
		  		</tr>
		  		<tr>
		  			<th>Total Purchase Orders</th>
		  			<td>{{$all_purchase_orders_count}}</td>
		  			<td class = "positive">${{$all_purchase_orders_balance}}</td>
		  		</tr>
		  	</table>
			
		  </div>
		</div>
	  </div>
	</div>
</div>

<div class = "dashboard_widget_holder">
	<div class="panel-group" id="invoiceaccordion" role="tablist" aria-multiselectable="true">
	  <div class="panel panel-default">
		<div class="panel-heading" role="tab" id="invoices_widget">
		  <h4 class="panel-title">
			<a role="button" data-toggle="collapse" data-parent="#accordion" href="#invoicecollapse" aria-expanded="true" aria-controls="invoicecollapse">
			  Invoices
			</a>
		  </h4>
		</div>
		<div id="invoicecollapse" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="invoices_widget">
		  <div class="panel-body">
		  	<table class = "table table-hover">
		  		<tr>
		  			<th>Outstanding Invoices</th>
		  			<td>{{$outstanding_invoices_count}}</td>
		  			<td class = "negative">${{$outstanding_invoices_balance}}</td>
		  		</tr>
		  		<tr>
		  			<th>Paid Invoices</th>
		  			<td>{{$paid_invoices_count}}</td>
		  			<td class = "positive">${{$paid_invoices_balance}}</td>
		  		</tr>
		  		<tr>
		  			<th>Cancelled Invoices</th>
		  			<td>{{$cancelled_invoices_count}}</td>
		  			<td class = "negative">${{$cancelled_invoices_balance}}</td>
		  		</tr>
		  		<tr>
		  			<th>Total Invoices</th>
		  			<td>{{$all_invoices_count}}</td>
		  			<td class = "positive">${{$all_invoices_balance}}</td>
		  		</tr>
		  	</table>
			
		  </div>
		</div>
	  </div>
	</div>
</div>

@stop