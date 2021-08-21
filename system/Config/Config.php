<?php

namespace System\Config;

use System\Lib\Exp;

class Config {
    private static $instance;
    private $config_nested_array   = [];
    private $config_dot_array      = [];

    private function __construct()
    {
        $this->initialConfigArrays();
    }
    
    private function initialConfigArrays(){
        $configPath = dirname(dirname(__DIR__)) . "/config/";

        foreach (glob($configPath . '*.php') as $filename) {
            $config = require $filename;
            $key    = str_replace($configPath,"",$filename);
            $key    = str_replace(".php","",$key);

            $this->config_nested_array[$key] = $config;

        }

        $this->initialDefaultValues();
        $this->config_dot_array = $this->array_dot($this->config_nested_array);
        
    }

    private function initialDefaultValues()
    {
        $tem = str_replace($this->config_nested_array['app']['BASE_URL'],'',explode("?",$_SERVER["REQUEST_URI"])[0]);
        $tem === "/" ? $tem = "" : $tem = substr($tem,1);
        $this->config_nested_array['app']['CURRENT_ROUTE'] = $tem;
    }

    private function array_dot($array,$return_array = [],$return_key = "")
    {
        foreach ($array as $key => $value) {
            if (is_array($value)){
                $return_array = array_merge($return_array,$this->array_dot($value,$return_array,$return_key . $key . "."));
            }else{
                if (is_numeric($key))
                    $return_array[substr($return_key,0,-1)][] = $value;
                else
                    $return_array[$return_key . $key] = $value;

            }
        }

        return $return_array;
    }

    private static function getInstance(){
        if (empty(self::$instance)){
            self::$instance = new self();
        }
        return self::$instance;
    }

    public static function get($key){
        $instance = self::getInstance();
        if (isset($instance->config_dot_array[$key])){
            return $instance->config_dot_array[$key];
        }else{
            throw new Exp("not found config array " . $key);
        }
    }
}