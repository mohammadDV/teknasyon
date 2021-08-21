<?php


namespace System\View\Traits;

trait HasExtendsContent {
    private $extendContent;

    private function checkExtendsContent()
    {
        $layoutFilePath = $this->findExtends();
        if ($layoutFilePath) {
            $this->extendContent    = $this->viewLoader($layoutFilePath);
            $yieldsNamesArray        = $this->findYieldsNames();
            if (!empty($yieldsNamesArray)){
                foreach ($yieldsNamesArray as $yieldName) {
                    $this->initialYields($yieldName);
                }
            }
            $this->content = $this->extendContent;
        }
    }

    private function initialYields($yieldName){
        $string     = $this->content;
        $startWord  = "@section('" . $yieldName . "')";
        $endWord    = "@endsection";

        $startPos = strpos($string,$startWord);
        if (!$startPos){
            return $this->extendContent = str_replace("@yield('"  . $yieldName . "')","",$this->extendContent);
        }

        $startPos   += strlen($startWord);
        $endPos      = strpos($string,$endWord,$startPos);
        if (!$endPos){
            return $this->extendContent = str_replace("@yield('"  . $yieldName . "')","",$this->extendContent);
        }

        $length         = $endPos - $startPos;
        $sectionContent = substr($string,$startPos,$length);
        return $this->extendContent = str_replace("@yield('"  . $yieldName . "')",$sectionContent,$this->extendContent);
    }

    private function findYieldsNames(){
        $yieldsNamesArray  = [];
        $pattern        = "/@yield+\('([^)]+)'\)/";
        preg_match_all($pattern,$this->extendContent,$yieldsNamesArray);
        return isset($yieldsNamesArray[1]) ? $yieldsNamesArray[1] : false;
    }

    private function findExtends(){
        $filePathArray  = [];
        $pattern        = "/s*@extends+\('([^)]+)'\)/";
        preg_match($pattern,$this->content,$filePathArray);
        return isset($filePathArray[1]) ? $filePathArray[1] : false;
    }
}