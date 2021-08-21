<?php


namespace System\View\Traits;

trait HasIncludesContent {

    private function checkIncludesContent()
    {
        while (true){
            $includesNamesArray = $this->findIncludesNames();
            if (!empty($includesNamesArray)){
                foreach ($includesNamesArray as $IncludeName) {
                    $this->initialIncludes($IncludeName);
                }
            }else{
                break;
            }
        }
    }

    private function initialIncludes($includeName){
        return $this->content = str_replace("@include('"  . $includeName . "')",$this->viewLoader($includeName),$this->content);
    }

    private function findIncludesNames(){
        $includesNamesArray  = [];
        $pattern        = "/@include\('([^)]+)'\)/";
        preg_match_all($pattern,$this->content,$includesNamesArray);
        return isset($includesNamesArray[1]) ? $includesNamesArray[1] : false;
    }

}