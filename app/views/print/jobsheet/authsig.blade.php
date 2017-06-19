<style>
    .authform {
        width: 100%;
        border-collapse: collapse;
        border : red solid thin;
    }

    .authform tr {
        vertical-align: top;
    }

    .authform td {
        padding-left: 1em;
        padding-bottom: 1em;
    }

    .authform input[type=text] {
        width : 100%;
        border : 3px;
        border-bottom: black solid thin;
    }
 </style>

<h2>Authorisation</h2>
<div id="divAuthSig">
    <table class="authform">
        <tr>
            <td><label for="inpSign">Signed</label><input id="inpSign" type="text" READONLY value=" "></td>
            <td><label for="inpPrint">Print</label><input id="inpPrint" type="text" READONLY value=" "></td>
        </tr>
    </table>
</div>