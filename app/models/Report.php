<?php

class Report extends Eloquent  {

    private $bRepChanged = false;
    private $sRep = null;
    private $sSummaryRep = null; //thumbnail/short text etc. each mimetype probably has one of these;

    public $sContentType = "application/pdf";
    
    /** set TimeStamps to false, this will stop laravel expecting dateadded/updated fields */
    public $timestamps = true;

    /** Set validation required for a case, i.e whats necessary on creation and other validation */
    public static $rules = array(

    );

    /** placeholder var for validation messages i reckon */
    public $messages;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'reports';

    
    public function ticket() {
        // FKey in this table to Ticket is ticket_id
        return $this->belongsTo("Ticket",'property_tickets_id');
    }

    public function reportType() {
        // FKey in this table to ValuesReportTypes is report_type_id
        return $this->belongsTo("ValuesReportTypes",'report_type_id');
    }

    public function isValid($data)
    {
        $validation = Validator::make($data, static::$rules);

        if($validation->passes()) return true;

        //else
        $this->messages = $validation->messages();
        return false;

    }


    /**
     * @param Ticket $oTicket
     * @param $oReportType $oReportType
     * @param string $sReportDoc
     * @param bool|true $bRender
     * @return Report
     */
    public static function createReportFromTicket($oTicket, $oReportType, $sReportDoc, $bRender = true ) {
        $oReport = new Report(); // create a blank Report model

        $oReport->ticket()->associate($oTicket);
        $oReport->reportType()->associate($oReportType);

        // if there isn't a report included, default to render one;
        if (!$sReportDoc && $bRender) {
//            $sReportDoc = $oReport->generateRep($oTicket,$oReportType);
            $sReportDoc = $oReport->generateRep();
        }
        $oReport->set_sRep($sReportDoc);

        return $oReport; // return the populated report.
    }


    // override save to also save the physical representation;

    /**
     * @param array $options
     * @return bool
     */
    public function save(array $options = Array()) {
        // if, for some reason we havn't rendered one by now, do it;
        if (empty($this->sRep)) {
            $this->generateRep($this->sContentType);
        }

        if (!$this->storeReportDoc()) {
            return false;
        }

        if (!parent::save($options)) {
            return false;
        };
        return true;
    }

    public function get_sRep() {
        if (empty($this->sRep)) {
            $this->fetch_sRep();
        }
        return $this->sRep;
    }

    public function set_sRep($sData) {
        $this->sRep = $sData;
        $this->bRepChanged = true;
    }

    private function fetch_sRep() {
       // get the property_id;
        $iPropertyId = $this->ticket->property_id;
        // set the storage path
        $pathDocStorage = public_path() . '/packages/properties/' . $iPropertyId;
        $pathDocRelative = $pathDocStorage . "/" . $this->id;

        if (File::exists($pathDocRelative)) {
            $this->set_sRep(File::get($pathDocRelative));
            $this->bRepChanged = false;
            return true;
        }
        return false;
    }


    /**
     * @param $oReportType ValuesReportTypes
     * @param null $sContentType
     * @return bool|\Illuminate\View\View|string
     */
    private function generateRep($sContentType = null) {

        if ($sContentType == null) {
            $sContentType = $this->sContentType;
        }
        $sReportName = $this->reportType->name;
        switch ($sReportName)	{
            case "invoice":
                // stuff invoice variables in here.
                $aData = array();
                break;

            case "jobsheet":
                $oTicket = $this->ticket;
                $oProperty = $this->ticket->property;
                $oAddress = $oProperty->address; //$oTicket->property->address;
                $aData = compact('oTicket','oProperty', 'oAddress');
                break;

            case "jobsheet_estimate":
                $oTicket = $this->ticket;
                $oProperty = $this->ticket->property;
                $oAddress = $oProperty->address; //$oTicket->property->address;
                $aData = compact('oTicket','oProperty', 'oAddress');
                break;

            default:
                $aData = array(); // default;
        }

        $sView = 'print.' . $sReportName . '.create';

        // replace with proper factory (and possibly a wrapper interface for each doctype).
        switch ($sContentType) {
            case "text/html" : // this wont happen unless we change stuff;
                return View::make($sView,$aData);
                break;
            case $this->sContentType :
                $pdfDoc = App::make('dompdf');
                return $pdfDoc->loadView($sView,$aData)->output();
                break;
            default:
                return false;
        }
    }

    /**
     *
     * @return bool
     */
    public function storeReportDoc() {

        // get the property_id;
        $iPropertyId = $this->ticket->property_id;

        // set the storage path
        $pathDocStorage= public_path() . '/packages/properties/' . $iPropertyId;
        // make it if doesn't exist;
        if (!File::exists($pathDocStorage)) {
            File::makeDirectory($pathDocStorage, $mode = 0750, true, true);
        }

        // generate a filename
        $uuidDoc = Uuid::generate();
        //save it;
        $pathDocRelative = $pathDocStorage . "/" . $uuidDoc . ".pdf";

        if (File::put($pathDocRelative,$this->get_sRep())) {
            //return us where we saved it;
            $this->storage_url = $pathDocRelative;
            return true;
        } else {
            // or some error code. or exception. or something.
            return false;
        }
    }

}


