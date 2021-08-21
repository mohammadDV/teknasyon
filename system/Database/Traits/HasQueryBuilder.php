<?php

namespace System\Database\Traits;

use System\Database\DBConnection\Connection;


trait HasQueryBuilder {

    private $sql            = "";
    private $where          = [];
    private $orderBy        = [];
    private $limit          = [];
    private $values         = [];
    private $bindValues     = [];

    protected function setSql($query)
    {
        $this->sql = $query;

    }

    protected function getSql(){
        return $this->sql;
    }

    protected  function resetSql(){
        $this->sql = "";
    }

    protected function setWhere($operator,$condition ){

        $array = [
            "operator"  => $operator,
            "condition" => $condition
        ];
        array_push($this->where,$array);
    }

    protected function resetWhere(){
        $this->where = [];
    }

    protected function setOrderBy($name,$expression){
        array_push($this->orderBy,"`" . $name . "` " . $expression);
    }

    protected function resetOrderBy(){
        $this->orderBy = [];
    }

    protected function setLimit($number,$from = 0){
        $this->limit["from"]    = $from;
        $this->limit["number"]  = $number;
    }

    protected function resetLimit(){
        unset($this->limit["from"]);
        unset($this->limit["number"]);
    }

    function addValue($attribute,$value){
        $this->values[$attribute]   = $value;
        $this->bindValues[]         = $value;
    }
//my comment

    protected function removeValues(){
        $this->values       = [];
        $this->bindValues   = [];
    }

    protected function resetQuery(){
        $this->resetSql();
        $this->resetWhere();
        $this->resetOrderBy();
        $this->resetLimit();
        $this->removeValues();
    }

    protected function executeQuery()
    {
        $query  = "";
        $query .= $this->sql;
        if (!empty($this->where)){
            $whereString = "";
            foreach ($this->where as $where) {
                $whereString == "" ? $whereString .= $where["condition"] : $whereString .= " " . $where["operator"] . " " . $where["condition"];
            }

            $query .= " WHERE " . $whereString;
        }

        if (!empty($this->orderBy)){
            $query .= " ORDER BY " . implode(', ',$this->orderBy);
        }
        if (!empty($this->limit)){
            $query .= " LIMIT " . $this->limit["from"] . " , " . $this->limit["number"] . " ";
        }

        $query .= " ;";

//        echo var_dump($query) . " query <br>"; die();
        $instance   = Connection::instance();
        $statement  = $instance->prepare($query);
//        if (count($this->bindValues) > count($this->values)){
//            count($this->bindValues) > 0 ? $statement->execute($this->bindValues) : $statement->execute();
//        }else{
//            count($this->values) > 0 ? $statement->execute(array_values($this->values)) : $statement->execute();
//        }
        sizeof($this->bindValues) > 0 ? $statement->execute($this->bindValues) : $statement->execute();
        return $statement;
    }

    protected function getCount(){

        $query  = "";
        $query .= "SELECT COUNT(*) FROM " . $this->getTableName();
        if (!empty($this->where)){
            $whereString = "";
            foreach ($this->where as $where) {
                $whereString == "" ? $whereString .= $where["condition"] : $whereString . " " . $where["operator"] . " " . $where["condition"];
            }

            $query .= " WHERE " . $whereString;
        }

        $query .= " ;";

        $instance   = Connection::instance();
        $statement  = $instance->prepare($query);
        if (count($this->bindValues) > count($this->values)){
            count($this->bindValues) > 0 ? $statement->execute($this->bindValues) : $statement->execute();
        }else{
            count($this->values) > 0 ? $statement->execute(array_values($this->values)) : $statement->execute();
        }

        return $statement->fetchColumn();

    }

    protected function getTableName(){
        return " `" . $this->table . "`";
    }

    protected function getAttrName($attr)
    {
        return " `" . $this->table . "`.`" . $attr . "`";
    }
}