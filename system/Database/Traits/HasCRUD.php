<?php


namespace System\Database\Traits;

use System\Database\DBConnection\Connection;

trait HasCRUD {

    protected function createMethod($values)
    {
        $values = $this->arrayToCastEncodeValue($values);
        $this->arrayToAttr($values,$this);
        return $this->saveMethod();

    }

    protected function updateMethod($values)
    {
        $values = $this->arrayToCastEncodeValue($values);
        $this->arrayToAttr($values,$this);
        return $this->saveMethod();
    }

    protected function deleteMethod($id = null){
        $object = $this;
        $this->resetQuery();
        if (!empty($id)){
            $object = $this->findMethod($id);
            $this->resetQuery();
        }
        $object->setSql("DELETE FROM " . $object->getTableName());
        $object->setWhere("AND",$this->getAttrName($this->primaryKey . " = ?"));
        $object->addValue($this->primaryKey,$this->{$this->primaryKey});
        return $object->executeQuery();
    }

    protected function allMethod(){
        $this->setSQL("SELECT * FROM " . $this->getTableName());
        $statement  = $this->executeQuery();
        $data       = $statement->fetchAll();
        if ($data){
            $this->arrayToObj($data);
            return $this->collection;
        }
        return [];
    }

    protected function findMethod($id){
        $this->setSQL("SELECT * FROM " . $this->getTableName());
        $this->setWhere("AND",$this->getAttrName($this->primaryKey) . " = ? ");
        $this->addValue($this->primaryKey,$id);
        $statement  = $this->executeQuery();
        $data       = $statement->fetch();
        $this->setAllowedMethods(['update','delete','find']);
        if ($data)
            return $this->arrayToAttr($data);
        return null;
    }

    protected function whereMethod($attr,$first,$second = null){

        if ($second === null){
            $condition = $this->getAttrName($attr) . " = ? ";
            $this->addValue($attr,$first);
        }else{
            $condition = $this->getAttrName($attr) . " " . $first .  " ? ";
            $this->addValue($attr,$second);
        }

        $operator = 'AND';

        $this->setWhere($operator,$condition);
        $this->setAllowedMethods(['where','whereOr','whereIn','whereNull','whereNotNull','limit','orderBy','get','first','paginate']);
        return $this;
    }

    protected function whereOrMethod($attr,$first,$second = null){

        if ($second === null){
            $condition = $this->getAttrName($attr) . " = ? ";
            $this->addValue($attr,$first);
        }else{
            $condition = $this->getAttrName($attr) . " " . $first .  " ? ";
            $this->addValue($attr,$second);
        }

        $operator = 'OR';

        $this->setWhere($operator,$condition);
        $this->setAllowedMethods(['where','whereOr','whereIn','whereNull','whereNotNull','limit','orderBy','get','first','paginate']);
        return $this;
    }

    protected function whereNullMethod($attr){

        $condition = $this->getAttrName($attr) . " IS NULL ";
        $operator = 'AND';
        $this->setWhere($operator,$condition);
        $this->setAllowedMethods(['where','whereOr','whereIn','whereNull','whereNotNull','limit','orderBy','get','first','paginate']);
        return $this;
    }

    protected function whereNotNullMethod($attr){

        $condition = $this->getAttrName($attr) . " IS NOT NULL ";
        $operator = 'AND';
        $this->setWhere($operator,$condition);
        $this->setAllowedMethods(['where','whereOr','whereIn','whereNull','whereNotNull','limit','orderBy','get','first','paginate']);
        return $this;
    }

    protected function whereInMethod($attr,array $array){
        if (!empty($array)){
            $valuesArray    = [];
            foreach ($array as $item) {
                $this->addValue($attr,$item);
                $valuesArray[] = '?';
            }
            $condition  = $this->getAttrName($attr) . " IN (" . implode(",",$valuesArray) . ") ";
            $operator   = 'AND';
            $this->setWhere($operator,$condition);
        }
    }

    protected function limitMethod($number,$from = 0){
        $this->setLimit($number,$from);
        $this->setAllowedMethods(['orderBy','get','first','paginate']);
        return $this;
    }

    protected function orderByMethod($attr,$expression){
        $this->setOrderBy($attr,$expression);
        $this->setAllowedMethods(['limit','orderBy','get','first','paginate']);
        return $this;
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
        $statement  = $this->executeQuery();
        $data       = $statement->fetchAll();
        if ($data){
            $this->arrayToObj($data);
            return $this->collection;
        }
        return [];

    }

    protected function firstMethod($array = []){

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
        $this->limitMethod(1);
        $statement  = $this->executeQuery();
        $data       = $statement->fetch();
        if ($data)
            return $this->arrayToAttr($data);
        return null;

    }

    protected function paginateMethod($perPage)
    {
        $totalRows      = $this->getCount();
        $currentPage    = clearStr(isset($_GET["page"]) ? $_GET["page"] : 1);
        if (empty($currentPage)){
            $currentPage = 1;
        }
        $totalPages     = ceil($totalRows / $perPage);
        $currentPage    = min($currentPage,$totalPages);
        $currentPage    = max($currentPage,1);
        $currentRow     = ($currentPage - 1) * $perPage;

        $this->limitMethod($perPage,$currentRow);

        if (empty($this->sql)){
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

    protected function saveMethod(){
        $fillString = $this->fill();

        if (!isset($this->{$this->primaryKey})){
            $this->setSql("INSERT INTO " . $this->getTableName() . " SET " . $fillString ." , " . $this->getAttrName($this->createdAt) . " = " . time() );
        }else{
            $this->setSql("UPDATE  " . $this->getTableName() . " SET " . $fillString . " , " . $this->getAttrName($this->updatesAt) . " = " . time() );
            $this->setWhere("AND",$this->getAttrName($this->primaryKey) . " = ? ");
            $this->addValue($this->primaryKey,$this->{$this->primaryKey});
        }
        $this->executeQuery();
        $this->resetQuery();

        if (!isset($this->{$this->primaryKey})){
            $object         = $this->find(Connection::insertID());
            $defaultVars    = get_class_vars(get_called_class());
            $allVars        = get_object_vars($object);
            $diffVars       = array_diff(array_keys($allVars),array_keys($defaultVars));
            foreach ($diffVars as $attr) {
                if ($this->inCastAttr($attr) == true) {
                    $this->registerAttr($this,$attr,$this->castEncodeValue($attr,$object->$attr));
                }else{
                    $this->registerAttr($this,$attr,$object->$attr);
                }
            }
        }
        $this->resetQuery();
        $this->setAllowedMethods(['update','delete','find']);
        return $this;
    }

    protected function fill(){

        $fillArray = [];

        foreach ($this->fillable as $attr) {
            if (isset($this->$attr)){
                $fillArray[] = $this->getAttrName($attr) . " = ?";
                if($this->inCastAttr($attr) == true){
                    $this->addValue($attr,$this->castEncodeValue($attr,$this->$attr));
                }else{
                    $this->addValue($attr,$this->$attr);
                }
            }
        }

        $fillString = implode(",",$fillArray);
        return $fillString;
    }



}