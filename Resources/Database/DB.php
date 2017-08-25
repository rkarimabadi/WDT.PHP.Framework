<?php
namespace Resources\Database;

abstract class DB {
    public static $conn;
    
    private static $errorInfo = array();
    private static $throwException = true;
    private static $statement;

    public static function init($host = DB_Host, $dbname = DB_Name, $username = DB_User, $password = DB_Pass)
    {
        try {
            DB::$conn = new \PDO("mysql:host=$host;dbname=$dbname",$username,$password);
            DB::$conn->exec('SET NAMES UTF8; SET sql_mode = "ALLOW_INVALID_DATES";');
            DB::$conn->setAttribute(\PDO::ATTR_ERRMODE, DB_Exception);
        } catch (\PDOException $ex) { die($ex->getMessage()); }
    }
    private static function parameterInitializer(\PDOStatement &$statement,array &$parameters) {
        foreach ($parameters as $key => $value) {
            $type = \PDO::PARAM_STR;
            switch (gettype($value)) {
                case 'NULL': $type = \PDO::PARAM_NULL; break;
                case 'boolean': $type = \PDO::PARAM_BOOL; break;
                case 'integer': $type = \PDO::PARAM_INT; break;
            }
            $statement->bindValue($key,$value,$type);
        }
    }

    public static function beginTransaction() {DB::$conn->beginTransaction();}
    public static function rollback() {DB::$conn->rollBack();}
    public static function commit() {DB::$conn->commit();}

    public static function errorInfo() { return DB::$errorInfo; }
    public static function execute($query,$parrameters = array()) {
        try {
            DB::$statement = DB::$conn->prepare($query);
            self::parameterInitializer(DB::$statement, $parrameters);
            DB::$statement->execute();
        } catch (\PDOException $ex) {
            if(DB::$throwException) { $ex->query = $query; $ex->parameters = $parrameters; throw $ex; }
            return false;
        }
        return true;
    }
    private static function fetch($query,$type,$parrameters = array(),$class = null) {
        try {
            DB::$statement = DB::$conn->prepare($query);
            self::parameterInitializer(DB::$statement, $parrameters);
            DB::$statement->execute();
            if($class == null) return DB::$statement->fetch($type);
            return DB::$statement->fetch($type,$class);
        } catch (\PDOException $ex) {
            if(DB::$throwException) { $ex->query = $query; $ex->parameters = $parrameters; throw $ex; }
            return null;
        }
    }
    private static function next($type,$class = null) {
        try {
            if($class == null) return DB::$statement->fetch($type);
            return DB::$statement->fetch($type,$class);
        } catch (\PDOException $ex) {
            if(DB::$throwException) throw $ex;
            return null;
        }
    }
    private static function fetchAll($query,$type,$parrameters = array(),$class = null) {
        try {
            $statement = DB::$conn->prepare($query);
            self::parameterInitializer($statement, $parrameters);
            $statement->execute();
            if($class == null) return $statement->fetchAll($type);
            return $statement->fetchAll($type,$class);
        } catch (\PDOException $ex) {
            if(DB::$throwException) { $ex->query = $query; $ex->parameters = $parrameters; throw $ex; }
            return array();
        }
    }
    public static function nextAssoc() {return self::next(\PDO::FETCH_ASSOC);}
    public static function fetchAssoc($query,$parrameters = array()) {return self::fetch($query,\PDO::FETCH_ASSOC,$parrameters);}
    public static function fetchAllAssoc($query,$parrameters = array()) {return self::fetchAll($query,\PDO::FETCH_ASSOC,$parrameters);}

    public static function nextObj() {return self::next(\PDO::FETCH_OBJ);}
    public static function fetchObj($query,$parrameters = array()) {return self::fetch($query,\PDO::FETCH_OBJ,$parrameters);}
    public static function fetchAllObj($query,$parrameters = array()) {return self::fetchAll($query,\PDO::FETCH_OBJ,$parrameters);}

    public static function nextClass($class) {return self::next(\PDO::FETCH_CLASS,$class);}
    public static function fetchClass($query,$class,$parrameters = array()) {return self::fetch($query,\PDO::FETCH_CLASS,$parrameters,$class);}
    public static function fetchAllClass($query,$class,$parrameters = array()) {return self::fetchAll($query,\PDO::FETCH_CLASS,$parrameters,$class);}
}
DB::init();
?>