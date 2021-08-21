<?php

namespace System\Database\Traits;

trait HasSoftDelete {

    protected function deleteMethod($id = null){
        $object = $this;
        $this->resetQuery();
        if (!empty($id)){
            $object = $this->findMethod($id);
        }

        if ($object){
            $this->resetQuery();
            $object->setSql("UPDATE " . $object->getTableName() . " SET " . $this->getAttrName($this->deletedAt) . " = " . time());
            $object->setWhere("AND",$this->getAttrName($object->primaryKey . " = ?"));
            $object->addValue($object->primaryKey,$this->{$object->primaryKey});
            return $object->executeQuery();
        }
    }

    protected function allMethod(){
        $this->resetQuery();
        $this->setSQL("SELECT " . $this->getTableName() . ".* FROM" . $this->getTableName());
        $this->setWhere("AND",$this->getAttrName($this->deletedAt) . " IS NULL");
        $statement  = $this->executeQuery();
        $data       = $statement->fetchAll();
        if ($data){
            $this->arrayToObj($data);
            return $this->collection;
        }
        return [];
    }

    protected function findMethod($id){
        $this->resetQuery();
        $this->setSQL("SELECT " . $this->getTableName() . ".* FROM" . $this->getTableName());
        $this->setWhere("AND",$this->getAttrName($this->primaryKey) . " = ? ");
        $this->addValue($this->primaryKey,$id);
        $this->setWhere("AND",$this->getAttrName($this->deletedAt) . " IS NULL");
        $statement  = $this->executeQuery();
        $data       = $statement->fetch();
        $this->setAllowedMethods(['update','delete','find']);
        if ($data)
            return $this->arrayToAttr($data);
        return null;
    }


    protected function getMethod($array = []){

        if ($this->sql == ''){
            if (empty($array)){
                $fields = $this->getTableName() .  ".*";
            }else{
                foreach ($array as $key => $field) {
                    $array[$key] = $this->getAttrName($field);
                }
                $fields = implode(",",$array);
            }
            $this->setSql("SELECT  " . $fields . " FROM " . $this->getTableName());
        }
        $this->setWhere("AND",$this->getAttrName($this->deletedAt) . " IS NULL");
        $statement  = $this->executeQuery();
        $data       = $statement->fetchAll();
        if ($data){
            $this->arrayToObj($data);
            return $this->collection;
        }
        return [];

    }

    protected function paginateMethod($perPage)
    {

        $this->setWhere("AND",$this->getAttrName($this->deletedAt) . " IS NULL");
        $totalRows      = $this->getCount();
        $currentPage    = clearStr($_GET["page"]);
        if (empty($currentPage)){
            $currentPage = 1;
        }
        $totalPages = ceil($totalRows / $perPage);
        $currentPage = min($currentPage,$totalPages);
        $currentPage = max($currentPage,1);
        $currentRow     = ($currentPage - 1) * $perPage;
        $this->limitMethod($perPage,$currentRow);
        if (!empty($this->sql)){
            $this->setSql("SELECT " . $this->getTableName() . ".* FROM " . $this->getTableName());
        }

        $statement  = $this->executeQuery();
        $data       = $statement->fetchAll();
        if ($data){
            $this->arrayToObj($data);
            return $this->collection;
        }
        return [];
    }
}