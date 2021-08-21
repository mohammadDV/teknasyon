<?php

namespace System\Request;

use System\Request\Traits\HasFileValidationRules;
use System\Request\Traits\HasRunValidation;
use System\Request\Traits\HasValidationRules;

class Request {
    use HasFileValidationRules,HasRunValidation,HasValidationRules;

    // commit
    protected $errorExist               = false;
    protected $is_api                   = false;
    protected $request;
    protected $files                    = null;
    protected $errorVariablesName       = [];
    protected $apiMessage               = null;

    public function __construct($is_api = false)
    {
        $this->is_api = $is_api;
        if (isset($_POST)){
            $this->postAttr();
        }
        if (!empty($_FILES)){
            $this->files = $_FILES;
        }

        $rules = $this->rules();
        if(!empty($rules)){
            $this->run($rules);
        }

        $this->errorRedirect();
    }

    protected function rules(){
        return [];
    }

    protected function run($rules)
    {
        foreach ($rules as $attr => $value) {
            $ruleArray = explode("|",$value);
            if (in_array('file',$ruleArray)){
                unset($ruleArray[array_search('file',$ruleArray)] );
                $this->fileValidation($attr,$ruleArray);
            }elseif (in_array('number',$ruleArray)){
                $this->numberValidation($attr,$ruleArray);
            }else{
                $this->normalValidation($attr,$ruleArray);
            }

        }
    }

    public function file($name){
        return isset($this->files[$name]) ? $this->files[$name] : false;
    }

    protected function postAttr()
    {
        foreach ($_POST as $key => $value) {
            $this->$key             = htmlentities(addslashes($value));
            $this->request[$key]    = htmlentities(addslashes($value));
        }
    }

    public function all()
    {
        return $this->request;
    }
}