<?php


namespace App;

use System\Database\ORM\Model;

class Device extends Model {

    protected $table            = "devices";
    protected $fillable         = ["token","u_id","mobile","os","ip"];
    protected $hidden           = [];
    protected $casts            = [];


}