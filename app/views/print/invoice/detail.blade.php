<style>

    #divDetail {
        height : 25em;
    }

    .detail {
        width: 100%;
        border-collapse : collapse;
        height : 100%;
    }

    .detail thead {
        color : white;
        background-color: black;
        text-align : center;
        height : inherit;
    }

    .detail tbody {
        border-bottom : black solid thin;

    }

    .detail tbody tr {
    }

    .detail tbody tr:last-of-type {
        height : 100%;
    }
    .detail tbody td {
        text-align: right;
        padding-right : 0.5em;
        padding-left : 0.5em;
        border : 0;
        border-left : black solid thin;
        border-right : black solid thin;
    }

    .detail tfoot tr td {
        text-align: right;
    }

    .detail tfoot tr td:nth-of-type(2) {
        font-weight : bold;
        border-bottom : black solid thin;
    }

    .curr {
    }

    .multi {
    }

    .short {
    }

</style>

<div id="divDetail">
    <table class="detail">
       <colgroup>
            <col class="short">
            <col class="short">
            <col class="multi">
            <col class="curr">
            <col class="curr">
            <col class="short">
            <col class="curr">
            <col class="curr">
        </colgroup>

        <thead>
            <tr>
                <th>Line</th>
                <th>Qnt.</th>
                <th>Description</th>
                <th>Cost/Item</th>
                <th>Net</th>
                <th>Vat %</th>
                <th>Vat</th>
                <th>Gross</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($aDetailLines as $iKey=>$aLine)
                <tr>
                    <td>{{$iKey}}</td>
                    <td>{{$aLine["qnt"]}}</td>
                    <td>{{$aLine["description"] }}</td>
                    <td>{{$aLine["cost"]}}</td>
                    <td>{{$aLine["net"]}}</td>
                    <td>{{$aLine["vatpct"]}}</td>
                    <td>{{$aLine["vat"]}}</td>
                    <td>{{$aLine["gross"]}}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="5">Net</td>
                <td>{{$summary["net"]}}</td>
                <td colspan="2"></td>
            </tr>
            <tr>/
                <td colspan="6">Vat</td>
                <td>{{$summary["vat"]}}</td>
                <td></td>
            </tr>
            <tr>
                <td colspan="7">Gross</td>
                <td>{{$summary["gross"]}}</td>
            </tr>
        </tfoot>
    </table>
</div>