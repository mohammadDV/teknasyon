<?php


namespace System\Database\ORM;

use System\Database\Traits\HasCRUD;
use System\Database\Traits\HasAttributes;
use System\Database\Traits\HasMethodCaller;
use System\Database\Traits\HasQueryBuilder;
use System\Database\Traits\HasRelation;
//use System\Database\Traits\HasSoftDelete;


abstract class Model {
    use HasCRUD,HasAttributes,HasMethodCaller,HasQueryBuilder,HasRelation;

    protected $table;
    protected $profile          = "main";
    protected $fillable         = [];
    protected $hidden           = [];
    protected $casts            = [];
    protected $primaryKey       = "id";
    protected $createdAt        = "created_at";
    protected $updatesAt        = "updated_at";
    protected $deletedAt        = null;
    protected $collection       = [];
}