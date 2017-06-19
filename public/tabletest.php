<script src="js/jquery-1.11.1.min.js"></script>
<script src="js/DataTables/datatables.min.js"></script>
<script src="js/moment.min.js"></script>
<script src="js/datetime-moment.js"></script>
<link rel = "stylesheet" type = "text/css" href = "js/DataTables/datatables.min.css">
<script>
$(document).ready(function() {
	
	$.fn.dataTable.moment('DD/MM/YY HH:mm:ss');
	
	
	var historyTable = $('#dateTable').DataTable({
			"bPaginate": true,			
			"scrollX": "97%",
			"bInfo" : false,
			"bAutoWidth" : false,
			"columnDefs" : [
				//{ type : 'date', targets : [0] }
			]
	});
			
	historyTable.page('last').draw('page')

});
</script>
<?php

$db = new PDO('mysql:host=localhost;dbname=intranet_isf_crm;charset=utf8','root','3l1tc4t14');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$begin = new DateTime('2017-01-01');
$end = new DateTime('2017-12-28');

$daterange = new DatePeriod($begin, new DateInterval('P1D'), $end);

$history = $db->query("SELECT * FROM history");
$history_results = $history->fetchAll(PDO::FETCH_ASSOC);

?>

<table class = "table" id = "dateTable">
	<thead>
		<th>Date</th>
		<th>Details</th>
		<th>User</th>
	</thead>
	
	<tbody>
		<?php
			foreach($history_results as $history) {
				$histDate = explode(" ",$history["date"]);
				$histTime = $histDate[1];
				$histDate = date("d/m/y", strtotime($histDate[0]));
				print '<tr>
							<td>'.$histDate.' '.$histTime.'</td>
							<td>'.$history["details"].'</td>
							<td>'.$history["user"].'</td>
						</tr>';
			}
		?>
	</tbody>
	
</table>
		