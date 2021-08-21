<?php

namespace System\Request\Traits;

trait HasFileValidationRules {

    public function fileValidation($name,$ruleArray)
    {
        foreach ($ruleArray as $rule) {
            if ($rule == 'required'){
                $this->fileRequired($name);
            }elseif (strpos("z" . $rule,"mimes:")){
                $rule       = str_replace('mimes:',"",$rule);
                $rule       = explode(",",$rule);
                $this->fileType($name,$rule);
            }elseif (strpos("z" . $rule,"max:")){
                $rule = str_replace('max:',"",$rule);
                $this->maxFile($name,$rule);
            }elseif (strpos("z" . $rule,"min:")){
                $rule = str_replace('min:',"",$rule);
                $this->minFile($name,$rule);
            }
        }
    }

    protected function maxFile($name,$size)
    {
        if ($this->checkFileExist($name) && $this->checkFirstError($name)) {
            $size = $size * 1024;
            if ($this->request[$name]["size"] > $size){
                $this->setError($name," size must be lower than ". ($size / 1024) . " kb.");
            }
        }
    }

    protected function minFile($name,$size)
    {
        if ($this->checkFileExist($name) && $this->checkFirstError($name)) {
            $size = $size * 1024;
            if ($this->request[$name]["size"] < $size){
                $this->setError($name," size must be upper than ". ($size / 1024) . " kb.");
            }
        }
    }

    protected function fileRequired($name){
        if ((!isset($this->files[$name]['name']) || empty($this->files[$name]['name'])) && $this->checkFirstError($name)){
            $this->setError($name,$name . " is required");
        }
    }

    protected function fileType($name,$typesArray)
    {
        if ($this->checkFileExist($name) && $this->checkFirstError($name)) {
            $currentFileType = explode("/",$this->files[$name]['type'][1]);
            if (!in_array($currentFileType,$typesArray)){
                $this->setError($name,$name . " type must be " . implode(", ",$typesArray));
            }
        }
    }
}
