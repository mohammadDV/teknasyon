<?php

namespace System\Request\Traits;

use System\Database\DBConnection\Connection;

trait HasRunValidation {
    protected function errorRedirect()
    {
        if ($this->errorExist == false){
            return $this->request;
        }

        if ($this->is_api) {
            if (!empty($this->apiMessage)){
                echo json_encode(["status" => 0 ,"message" => $this->apiMessage]);
            }else{
                echo json_encode(["status" => 0 ,"message" => __tr("your api request is wrong")]);
            }
            exit();
        }

        return back();
    }

    private function checkFirstError($name){
        if (!errorExists($name) && !in_array($name,$this->errorVariablesName)){
            return true;
        }
        return false;
    }

    private function checkFieldExist($name)
    {
        return (isset($this->request[$name]) && !empty($this->request[$name])) ? true : false;
    }

    private function checkFileExist($name)
    {
        return (isset($this->files[$name]['name']) && !empty($this->files[$name]['name'])) ? true : false;
    }

    private function setError($name,$message){
        $this->errorVariablesName[] = $name;
        $this->apiMessage           = $message;
        $this->errorExist           = true;
        error($name,$message);
    }
}