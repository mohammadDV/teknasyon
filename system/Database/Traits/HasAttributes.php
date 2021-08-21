<?php

namespace System\Database\Traits;

trait HasAttributes {

    private function registerAttr($object,string $attribute,$value) {
        $this->inCastAttr($attribute) == true ?
        $object->$attribute = $this->castDecodeValue($attribute,$value) :
        $object->$attribute = $value;

    }

    private function arrayToAttr($array,$object = null){

        if (empty($object)){
            $className = get_called_class();
            $object = new $className;
        }

        foreach ($array as $attr => $value) {
            if ($this->inHiddenAttr($attr))
                continue;

            $this->registerAttr($object,$attr,$value);

        }
        return $object;
    }

    protected function arrayToObj(array $array){
        $collection = [];

        foreach ($array as $item) {
            $object = $this->arrayToAttr($item);
            $collection[] = $object;
        }

        $this->collection = $collection;
    }

    private function inHiddenAttr($attr){
        return in_array($attr,$this->hidden);
    }

    private function inCastAttr($attr){
        return in_array($attr,array_keys($this->casts));
    }

    private function castDecodeValue($attr,$value){
        if ($this->casts[$attr] == "array" || $this->casts[$attr] == "object"){
            return unserialize($value);
        }
        return $value;
    }

    private function castEncodeValue($attr,$value){
        if ($this->casts[$attr] == "array" || $this->casts[$attr] == "object"){
            return serialize($value);
        }
        return $value;
    }

    private function arrayToCastEncodeValue($values){
        $newArray = [];

        foreach ($values as $attr => $value) {
            $this->inCastAttr($attr) == true ? $newArray[$attr] = $this->castEncodeValue($attr,$values) : $newArray[$attr] = $value;
        }

        return $newArray;
    }
}