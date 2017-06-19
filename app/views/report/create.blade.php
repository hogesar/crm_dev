@extends('layouts.structure')

@section('header')
	<script>

		
	</script>
	<style>
	
	
	.left {
		float:left;
	}
	
	.right {
		float:right;
	}
	
	.buttonHolder {
		width:120px;
		float:right;
		height:30px;
	}
	#sortable { list-style-type: none; margin: 0; padding: 0; width: 450px; }
  	#sortable li { margin: 3px 3px 3px 0; padding: 1px; float: left; width: 100px; height: 90px; font-size: 4em; text-align: center; }
	</style>
@stop

@section('content')

<div class="tab-content">

	<div id="divCreateForm" class="tab-pane fade in active">
		{{ Form::open(array(
			'url'=>'reports',
			'files' => true,
			'role'=>'form',
			'id'=>'frmCreateReport',
			'method' => 'post'
			))
		}}
		{{ Form::label('ticket_id','Tickt number') }}
		{{ Form::input('text','ticket_id') }}
		{{ Form::label('report_type_id','Report Type') }}
		{{ Form::select('reportType_id',$aReportTypes,array_keys($aReportTypes)[0]) }}
		{{ Form::submit() }}
		{{ Form::close() }}
	</div>

</div>
	
@stop