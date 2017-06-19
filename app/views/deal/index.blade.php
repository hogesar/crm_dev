@extends('layouts.structure')

@section('header')
	<script>
	$(document).ready(function() {

		var dealTable = $('#dealTable').DataTable({
				"bPaginate": true,				
				"scrollX": "97%",
				"bInfo" : false,
				"bAutoWidth" : false,
				"columnDefs" : [
					{ type : 'date', targets : [0] }
				]
				});		
				
		dealTable.page('last').draw('page');	
		
		$('body').find("#contact_menu").addClass("active");;
		$('body').find("#contact").removeClass("collapse").addClass("collapsed");
		$('body').find("#viewcontact").addClass("active");
		localStorage.removeItem('lastTab');
		
		$('.contact').contextmenu({
			target:'#contact-menu', 
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

<?php
if(isset($date_filter)) {
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
	
	$deals = DB::table('deal')->where('status',$type)->where('order_date', '>=', $date_from)->where('order_date', '<=', $date_to)->get();
	
}
?>



@section('content')

@if(isset($date_filter))
	<div style = "text-align:center;width:45%;display:block;margin:0 auto;">
		<button class = "btn btn-submit styledButton" onclick = "window.location='{{ url("deal") }}'" href = '{{ url("deal") }}' class = "historyAdd">Remove Filter (View all)</button>
	</div>
@endif
<!--<a href = '{{ url("contact/create") }}' id = "newcontact" class = "btn btn-success actionButton" >Add New contact</a>-->
<table id="dealTable" class = "table table-striped" cellspacing="0" width="100%">
	<thead>
		<tr>
			<th>ID</th>
			<th>Date</th>
			<th>Client</th>
			<th>Contact</th>
			<th>Destination Country</th>
			<th>Notes</th>
		</tr>
	</thead>
	
	<tbody>

	@foreach($deals as $deal)
		<?php
		$order_date = date("d/m/y", strtotime($deal->order_date));
		$deal_client = DB::table('client')->where('id',$deal->client_id)->first();
		$deal_contact = DB::table('contact')->where('id',$deal->contact_id)->first();
		
		$displayDealId = ucwords("ISFD".str_pad($deal->id,6,"0",STR_PAD_LEFT));
		?>
		<tr onclick = "window.location='{{ url("deal/$deal->id") }}'" href = '{{ url("deal/$deal->id") }}' class = "historyAdd">
			<td>{{$displayDealId}}</td>
			<td>{{$order_date}}</td>
			<td>
				@if(is_object($deal_client))
					<a href = "/client/{{$deal->client_id}}">{{ucwords($deal_client->client_name)}}</a>
				@endif
			</td>
			<td>
				@if(is_object($deal_contact))
					<a href = "/contact/{{$deal_contact->id}}">{{ucwords($deal_contact->firstname." ".$deal_contact->lastname)}}</a>
				@endif
			</td>
			<td>{{ucwords($deal->destination_country)}}</td>
			<td>{{$deal->notes}}</td>
		</tr>
	@endforeach

	</tbody>

</table>


@stop