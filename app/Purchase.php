<?php


namespace App;

use System\Database\ORM\Model;

class Purchase extends Model {

    protected $table            = "purchases";
    protected $fillable         = ["token","device_id","receipt_code","status","expire_date"];
    protected $hidden           = [];
    protected $casts            = [];


}