<?php
namespace Resources\Mvc;

use Resources\DB;

abstract class Model {
    public function getTbl() { $reflect = new \ReflectionClass($this); return $reflect->getShortName(); }
    public function getPK() {return 'Id';}

    public function isAI() {return false;}

    private function getPrimaryWhere() {
        $primaryKey = $this->getPK();
        if(!is_array($primaryKey)) return "$primaryKey = :$primaryKey";
        $temp = null;
        foreach ($primaryKey as $value) $temp = ($temp == null ? '' : $temp.' AND ').$value.' = :'.$value;
        return $temp;
    }
    private function getPrimaryParameters() {
        $primaryKey = $this->getPK();
        $params = array();
        if(!is_array($primaryKey)) $params[$primaryKey] = $this->$primaryKey;
        else foreach($primaryKey as $primaryKey) $params[$primaryKey] = $this->$primaryKey;
        return $params;
    }
    private function getFields() {
        return array_diff_key(
            get_object_vars($this),
            array('columns'=>null,'from'=>null,'where'=>null,'groupby'=>null,'having'=>null,'orderby'=>null,'skip'=>null,'take'=>null,'parameters'=>null)
        );
    }

    public function &columns($string) { $this->columns = $string; return $this; }
    public function &from($string) { $this->from = $string; return $this; }
    public function &fromAppend($string) { $this->from .= $string; return $this; }
    public function &where($string) { $this->where = $string; return $this; }
    public function &whereAppend($string) { $this->where .= $string; return $this; }
    public function &groupby($string) { $this->groupby = $string; return $this; }
    public function &having($string) { $this->having = $string; return $this; }
    public function &orderby($string) { $this->orderby = $string; return $this; }
    public function &skip($int) { $this->skip = $int; return $this; }
    public function &take($int) { $this->take = $int; return $this; }
    public function &addParam($name,$value) {if(!isset($this->parameters)) $this->parameters = array(); $this->parameters[$name] = $value; return $this; }

    public function __get($name) {return (isset($this->$name) ? $this->$name : null);}

    public function select() {
        return DB::fetchAllClass(
        'SELECT '.(isset($this->columns) ? $this->columns : '*').' FROM '.
        (isset($this->from) ? $this->from : '`'.$this->getTbl().'`').
        (isset($this->where) ? ' WHERE '.$this->where : '').
        (isset($this->groupby) ? ' GROUP BY '.$this->groupby : '').
        (isset($this->having) ? ' HAVING '.$this->having : '').
        (isset($this->orderby) ? ' ORDER BY '.$this->orderby : '').
        (isset($this->take) ? ' LIMIT '.(isset($this->skip) ? $this->skip.',' : '').$this->take : '')
        ,get_class($this),(isset($this->parameters) ? $this->parameters : array()));
    }
    public function first() {
        $take = $this->take;
        $this->take = 1;
        $result = $this->select();
        $this->take = $take;
        return (count($result) > 0 ? $result[0] : null);
    }
    public function last() {
        $take = $this->take;
        $orderby = $this->orderby;
        $this->take = 1;
        $this->orderby = $this->getPK().' DESC';
        $result = $this->select();
        $this->take = $take;
        return (count($result) > 0 ? $result[0] : null);
    }

    private function getInsertFields() {
        $params = $this->getFields();
        $primaryKey = $this->getPK();
        if(!is_array($primaryKey) && $this->isAI()) unset($params[$primaryKey]);
        return $params;
    }
    public function insertQuery() {
        $params = $this->getInsertFields();
        $fields = $sep = $values = '';
        foreach ($params as $key => $value) {$fields .= "$sep`$key`";$values .= "$sep:$key";$sep = ',';}
        return 'INSERT INTO `'.$this->getTbl().'` ('.$fields.') VALUES ('.$values.');';
    }
    public function insert() {
        $params = $this->getInsertFields();
        $fields = $sep = $values = '';
        foreach ($params as $key => $value) {$fields .= "$sep`$key`";$values .= "$sep:$key";$sep = ',';}
        return DB::execute('INSERT INTO `'.$this->getTbl().'` ('.$fields.') VALUES ('.$values.');',$params);
    }
    public function updateQuery() {
        $fields = $this->getFields();
        $pk = $this->getPK();
        $query = $sep = '';
        foreach ($fields as $key => $value) {
            if($key != $pk) {
                $query .= "$sep`$key` = :$key";
                $sep = ',';
            }
        }
        return 'UPDATE `'.$this->getTbl().'` SET '.$query.' WHERE '.(isset($this->where) ? $this->where : $this->getPrimaryWhere());
    }
    public function update($where = null) {
        $fields = $this->getFields();
        $pk = $this->getPK();
        $query = $sep = '';
        foreach ($fields as $key => $value) {
            if($key != $pk) {
                $query .= "$sep`$key` = :$key";
                $sep = ',';
            }
        }
        if($this->parameters != null) $fields = array_merge($fields,$this->parameters);
        return DB::execute('UPDATE `'.$this->getTbl().'` SET '.$query.' WHERE '.(isset($this->where) ? $this->where : $this->getPrimaryWhere()),$fields);
    }

    public function deleteQuery() {
        return 'DELETE FROM `'.$this->getTbl().'` WHERE '.(isset($this->where) ? $this->where : $this->getPrimaryWhere());
    }
    public function delete() {
        return DB::execute('DELETE FROM `'.$this->getTbl().'` WHERE '.(isset($this->where) ? $this->where : $this->getPrimaryWhere()),$this->getPrimaryParameters());
    }
}