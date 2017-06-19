<?php

class ReportController extends \BaseController
{

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        // show all reports (and the tickets they came in on;

        $aReports = Report::all();

    }


    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        // get the list of avialable reports
        $aReportTypes = ValuesReportTypes::lists('name','id');
        // submit;
        return View::make('report.create',compact('aReportTypes'));
    }


    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store()
    {
        /* create a new report based on a ticker ID and save it to the database */

        $iTicketId = Input::get("ticket_id"); // the id of the ticket
        $iReportTypesId = Input::get("reportType_id"); //the report_type id associated with the report we want
        $sReportDoc = Input::get("report_doc") || null; //an existing document to go with this

        // find the ticket and reporttypes
        /** @var  $oTicket Ticket */
        $oTicket = Ticket::findOrFail($iTicketId);

        /** @var $oReportType ValuesReportTypes */
        $oReportType = ValuesReportTypes::findOrFail($iReportTypesId);

        // make a report based on a ticket;
        /** @var $oReport Report */
        $oReport = Report::createReportFromTicket($oTicket, $oReportType, $sReportDoc);

        // if there's an URL, store the object and tell the user;
        if ($oReport->save()) {
            // should also save the report object
            $sContents = $oReport->storage_url;
        } else { // something went wrong, debug me the object contents;
            $sContents = $oReport->toArray();
        }

        $iStatusCode = 200;
        $sContentType = "text/html";
        $oResponse = Response::make($sContents, $iStatusCode);
        $oResponse->header('ContentType', $sContentType);
        return $oResponse;
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return Response
     */
    public function show($id)
    {
        /** @var $oReport Report */
        $oReport = Report::findOrFail($id);

        $sContents = $oReport->get_sRep();

        $iStatusCode = 200;
        $sContentType = $oReport->sContentType;
        $oResponse = Response::make($sContents, $iStatusCode);
        $oResponse->header('ContentType', $sContentType);
        return $oResponse;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int $id
     * @return Response
     */
    public function update($id)
    {
        //
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return Response
     */
    public function destroy($id)
    {
        //
    }


}
