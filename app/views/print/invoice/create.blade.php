@extends('print.page_a4')
@section('content')
    @include('print.header')
    <div id="DocumentTitle">
        <h1>Invoice for Goods and Services</h1>
    </div>
    <div id="ProductionDate">20 Oct 2015</div>
    @include('print.invoice.detail')
    @include('print.invoice.remittance')
    @include('print.footer')
@stop