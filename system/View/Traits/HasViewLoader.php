<?php


namespace System\View\Traits;

use System\Lib\Exp;

trait HasViewLoader {

    private $viewNameArray = [];

    private function viewLoader($dir)
    {
        $dir = trim($dir," .");
        $dir = str_replace(".","/",$dir);
        $die_file = dirname(dirname(dirname(__DIR__))) . "/resources/view/{$dir}.blade.php";
        if (file_exists($die_file)){
            $this->registerView($dir);
            $content = file_get_contents(htmlentities($die_file));
            return $content;
        }else{
            throw new Exp("not found view",2020);
        }
    }

    private function registerView($view){
        array_push($this->viewNameArray,$view);
    }
}