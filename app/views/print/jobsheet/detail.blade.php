<style>

    .detail {
        width: 100%;
        border-collapse : collapse;
      }

    .detail tr {
        vertical-align: top;
    }

    .detail td {
        padding-left : 1em;
        padding-bottom : 1em;
    }

    .detail tr td:first-of-type {
        font-weight     :   bold;
        text-align      :   right;
        border-right    :   black solid thin;
        padding-right : 1em;
    }

    #accessContact {
        height : 2em;
    }

    #workDescription {
        height : 10em;
    }

</style>
<div id="divDetail">
    <table class="detail">
        <tr>
            <td class="c1" >Contractor</td>
            <td> </td>
        </tr>
        <tr>
            <td class="c1" >Property</td>
            <td>{{ $oTicket->property->address->Display_Address }}</td>
        </tr>
        <tr>
            <td class="c1" >Billing Name</td>
            <td >
                {{ join(" ",array($oProperty->landlord->title,$oProperty->landlord->firstname,$oProperty->landlord->lastname)) }}
                @if(!empty($oContact->phone1))
                    ( {{ $Contact->phone1 }} )
                @endif
                @if(!empty($oContact->email1))
                    - {{$oContact->email1}}
                @endif
            </td>
        </tr>
        <tr>
            <td class="c1" >Date reported</td>
            <td>{{ $oTicket->date_raised }}</td>
        </tr>
        <tr>
            <td class="c1" >Preferred Start Date</td>
            <td>{{ $oTicket->date_raised }}</td>
        </tr>
        <tr>
            <td class="c1" >Estimate Cost</td>
            <td>{{ $oTicket->estimated_cost }}</td>
        </tr>
        <tr>
            <td class="c1">Access Contact</td>
            <td id="accessContact">
                @foreach ($oProperty->activeTenants as $oContact)
                    {{ join(" ",array($oContact->title,$oContact->firstname,$oContact->lastname)) }}
                    @if(!empty($oContact->phone1))
                        ( {{ $oContact->phone1 }} )
                    @endif
                    @if(!empty($oContact->email1))
                        - {{ $oContact->email1 }}
                    @endif
                    <br />
                @endforeach
            </td>
        </tr>
        <tr>
            <td>Description of Work</td>
            <td id="workDescription"> {{ $oTicket->description }}</td>
        </tr>
    </table>
</div>