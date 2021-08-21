<?php


namespace System\Database\Traits;

trait HasRelation {

    protected function hasOne($model,$foreignKey,$localKey)
    {

        if (!empty($this->{$this->primaryKey})){
            $modelObject = new $model();
            return $modelObject->getHasOneRelation($this->table,$foreignKey,$localKey,$this->$localKey);
        }

    }

    protected function getHasOneRelation($table,$foreignKey,$otherKey,$otherKeyValue)
    {
        // sql = "SELECT posts.* FROM categories JOIN  posts ON categories.id = posts.id"
        $this->setSql("SELECT `b`.* FROM `{$table}` AS `a` JOIN " . $this->getTableName() . " AS `b` ON `a`.`{$otherKey}` = `b`.`{$foreignKey}` ");
        $this->setWhere("AND","`a`.`$otherKey` = ? ");
        $this->table = "b";
        $this->addValue($otherKey,$otherKeyValue);
        $statement  = $this->executeQuery();
        $data       = $statement->fetch();
        if ($data){
            return $this->arrayToObj($data);
        }
        return null;
    }

    protected function hasMany($model,$foreignKey,$localKey)
    {

        if (!empty($this->{$this->primaryKey})){
            $modelObject = new $model();
            return $modelObject->getHasManyRelation($this->table,$foreignKey,$localKey,$this->$localKey);
        }

    }

    protected function getHasManyRelation($table,$foreignKey,$otherKey,$otherKeyValue)
    {
        // sql = "SELECT posts.* FROM categories JOIN  posts ON categories.id = posts.id"
        $this->setSql("SELECT `b`.* FROM `{$table}` AS `a` JOIN " . $this->getTableName() . " AS `b` ON `a`.`{$otherKey}` = `b`.`{$foreignKey}` ");
        $this->setWhere("AND","`a`.`$otherKey` = ? ");
        $this->table = "b";
        $this->addValue($otherKey,$otherKeyValue);
        return $this;
    }

    protected function blongsTo($model,$foreignKey,$localKey)
    {

        if (!empty($this->{$this->primaryKey})){
            $modelObject = new $model();
            return $modelObject->getBlongsToRelation($this->table,$foreignKey,$localKey,$this->$foreignKey);
        }

    }

    protected function getBlongsToRelation($table,$foreignKey,$otherKey,$foreignKeyValue)
    {
        // sql = "SELECT posts.* FROM categories JOIN  posts ON categories.id = posts.id"
        $this->setSql("SELECT `b`.* FROM `{$table}` AS `a` JOIN " . $this->getTableName() . " AS `b` ON `a`.`{$foreignKey}` = `b`.`{$otherKey}` ");
        $this->setWhere("AND", "`a`.`$foreignKey` = ? ");
        $this->addValue($otherKey, $foreignKeyValue);
        $this->table = "b";
        $statement  = $this->executeQuery();
        $data       = $statement->fetch();
        if ($data) {
            return $this->arrayToAttr($data);
        }
        return null;
    }

    protected function belongsToMany($model, $commonTable, $localKey, $middleForeignKey, $middleRelation, $foreignKey )
    {
        if($this->{$this->primaryKey})
        {
            $modelObject = new $model();
            return $modelObject->getBelongsToManyRelation($this->table, $commonTable , $localKey, $this->$localKey, $middleForeignKey, $middleRelation, $foreignKey);
        }
    }

    protected function getBelongsToManyRelation($table, $commonTable, $localKey, $localKeyValue, $middleForeignKey, $middleRelation, $foreignKey)
    {
//        $sql = "SELECT posts.* FROM ( SELECT category_post.* FROM categories JOIN category_post on categories.id = category_post.cat_id WHERE  categories.id = ? ) as relation JOIN posts on relation.post_id=posts.id ";
        $this->setSql("SELECT `c`.* FROM ( SELECT `b`.* FROM `{$table}` AS `a` JOIN `{$commonTable}` AS `b` on `a`.`{$localKey}` = `b`.`{$middleForeignKey}` WHERE  `a`.`{$localKey}` = ? ) AS `relation` JOIN ".$this->getTableName()." AS `c` ON `relation`.`{$middleRelation}` = `c`.`$foreignKey`");
        $this->addValue("{$table}_{$localKey}", $localKeyValue);
        $this->table = 'c';
        return $this;
    }
}