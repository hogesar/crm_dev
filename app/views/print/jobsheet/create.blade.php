@extends('print.page_a4')
@section('content')
    @include('print.header')

    <style>
        #divDocTitle {
            width: 100%;
            border-bottom: thin black solid;
        }

        .documentTitle {
            width:100%
        }

        .documentTitle td {
            vertical-align : middle;
        }

        #productionDate {
            text-align : right;
        }
    </style>
    <div id="divDocTitle">
        <table class="documentTitle">
                 <tr>
                    <td><h1>WorkSheet</h1></td>
                    <td id="productionDate"> Job : {{ str_pad($oTicket->id,6,"0",STR_PAD_LEFT) }} <br> {{  date('d M Y') }}</td>
                </tr>
        </table>
    </div>
    @include('print.jobsheet.detail')
    @include('print.jobsheet.authsig')

@stop