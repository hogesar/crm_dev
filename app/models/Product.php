<?php

class Product extends Eloquent
{
    /** Set validation required for a case, i.e whats necessary on creation and other validation */
    public static $rules = array();

    /** Can use either fillable or guarded to configure which fields can be mapped in insert qs post */
    //protected $fillable = ['added_date', 'type', 'status'];

    /** placeholder var for validation messages i reckon */
    public $messages;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'product';

    // not sure if this is necessary;
    public function correspondant()
    {
        // FKey in Tenant is property_id;
        return $this->hasMany('Tenants','property_id');
    }

    /** set TimeStamps to false, this will stop laravel expecting dateadded/updated fields */
    //public $timestamps = true;

    public function landlord()
    {
        // FKey in this table is landlord_id
        return $this->belongsTo('Landlord','landlord_id');
    }

    public function tickets()
    {
        // FKey in Ticket is property_id;
        return $this->hasMany('Ticket','property_id');
    }

    public function address()
    {
        // FKey in Address is property_id;
        return $this->hasOne('Address','property_id');
    }

    public function tenants()
    {
        // pivot table to Tenant is "property_tenancy"
        return $this->belongsToMany('Tenant','property_tenancy');
    }

    // fetches active tennants
    public function activeTenants() {

        return $this->tenants()->wherePivot('status','active');

    }

    public function isValid($data)
    {
        $validation = Validator::make($data, static::$rules);

        if ($validation->passes()) return true;

        //else
        $this->messages = $validation->messages();
        return false;

    }

    public function getFieldnames()
    {
        /*$tablename = "cases";
        $fields = array();

        $db = new PDO('mysql:host=localhost;dbname='.$_SESSION["database"].';charset=utf8', 'mysql', '7thseal');

        $stmt = $db->prepare("SELECT * FROM $tablename");
        $stmt->execute();
        /* Count the number of columns in the result set */
        /*$colcount = $stmt->columnCount();
        //we dont know what the column should be yet
        $column_type = "";

        for($i = 0; $i < $colcount; $i++) {
            $meta = $stmt->getColumnMeta($i);
            //get column type from metadata. This could change if its a special field
            $column_type = $meta["native_type"];
            $column_name = $meta["name"];
            $fields[$i]['column_type'] = $column_type;
            $fields[$i]['column_name'] = $column_name;
        }

        return $fields;*/
    }

}
