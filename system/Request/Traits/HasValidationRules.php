<?php

namespace System\Request\Traits;

use System\Database\DBConnection\Connection;


trait HasValidationRules {

    public function normalValidation($name,$ruleArray)
    {
        foreach ($ruleArray as $rule) {
            if ($rule == 'required'){
                $this->required($name);
            }elseif (strpos("z" . $rule,"max:")){
                $rule = str_replace('max:',"",$rule);
                $this->maxStr($name,$rule);
            }elseif (strpos("z" . $rule,"min:")){
                $rule = str_replace('min:',"",$rule);
                $this->minStr($name,$rule);
            }elseif (strpos("z" . $rule,"exists:")){
                $rule = str_replace('exists:',"",$rule);
                $rule = explode(",",$rule);
                $key  = !isset($rule[1]) ? null : $rule[1];
                $this->existsIn($name,$rule[0],$key);
            }elseif ($rule == 'email'){
                $this->email();
            }elseif ($rule == 'date'){
                $this->date();
            }
        }
    }

    public function numberValidation($name,$ruleArray)
    {
        foreach ($ruleArray as $rule) {
            if ($rule == 'required'){
                $this->required($name);
            }elseif (strpos("z" . $rule,"max:")){
                $rule = str_replace('max:',"",$rule);
                $this->maxNumber($name,$rule);
            }elseif (strpos("z" . $rule,"min:")){
                $rule = str_replace('min:',"",$rule);
                $this->minNumber($name,$rule);
            }elseif (strpos("z" . $rule,"exists:")){
                $rule = str_replace('exists:',"",$rule);
                $rule = explode(",",$rule);
                $key  = !isset($rule[1]) ? null : $rule[1];
                $this->existsIn($name,$rule[0],$key);
            }elseif ($rule == 'number'){
                $this->number();
            }
        }
    }

    protected function maxStr($name,$count)
    {
        if ($this->checkFieldExist($name)){
            if (strlen($this->request[$name]) > $count && $this->checkFirstError($name)){
                $this->setError($name,"max length lower than ". $count . " character");
            }
        }
    }

    protected function minStr($name,$count)
    {
        if ($this->checkFieldExist($name)){
            if (strlen($this->request[$name]) < $count && $this->checkFirstError($name)){
                $this->setError($name,"min length Upper than ". $count . " character");
            }
        }
    }

    protected function maxNumber($name,$count)
    {
        if ($this->checkFieldExist($name)){
            if (strlen($this->request[$name]) < $count && $this->checkFirstError($name)){
                $this->setError($name,"max number Upper than ". $count . " number");
            }
        }
    }

    protected function minNumber($name,$count)
    {
        if ($this->checkFieldExist($name)){
            if ($this->request[$name] < $count && $this->checkFirstError($name)){
                $this->setError($name,"min number Upper than ". $count . " number");
            }
        }
    }

    protected function required($name){
        if (!isset($this->request[$name]) && $this->checkFirstError($name)){
            $this->setError($name,$name . " is required");
        }
    }

    protected function number($name){
        if ($this->checkFieldExist($name)){
            if (!is_numeric($name) && $this->checkFirstError($name)){
                $this->setError($name, $name . " must be number format");
            }
        }
    }

    protected function date($name)
    {
        if ($this->checkFieldExist($name)) {
            $pattern = "/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/";
            if (!preg_match($pattern,$name) && $this->checkFirstError($name)){
                $this->setError($name, $name . " must be date format");
            }
        }
    }

    protected function email($name)
    {
        if ($this->checkFieldExist($name)) {
            if (!filter_var($name,FILTER_VALIDATE_EMAIL)  && $this->checkFirstError($name)){
                $this->setError($name, $name . " must be email format");
            }
        }
    }

    protected function existsIn($name,$table,$field = "id"){
        if ($this->checkFieldExist($name) && $this->checkFirstError($name)) {
            $value      = $this->$name;
            $sql        = "SELECT COUNT(*) FROM {$table} WHERE {$field} = ? ";
            $statement  = Connection::instance()->prepare($sql);
            $statement->execute([$value]);
            $result     = $statement->fetchColumn();
            if ($result == 0 || $result === false){
                $this->setError($name, $name . " not already exist");
            }
        }
    }
}
