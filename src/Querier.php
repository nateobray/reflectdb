<?php

namespace obray\reflectdb;

use obray\reflectdb\Join;

class Querier
{
    private string $queryObject;
    private string $operation = 'select';
    private array $joins = [];
    private array $conditions = [];
    private array $bindings = [];
    private array $indexes = [];
    private array $orderBy = [];
    private array $columns = [];

    public function __construct(string $obj, string $operation='select')
    {
        $this->queryObject = $obj;
        $this->operation = $operation;
    }

    public function setColumns(array $columns){
        $this->columns = $columns;
    }

    public function setBindings(array $bindings){
        $this->bindings = array_merge($this->bindings, $bindings);
    }
    
    public static function select(string $obj)
    {
        $q = new Querier($obj);
        return $q;
    }

    public function join(string $join, string $on=null)
    {
        if($on === null) $on = $this->queryObject;
        if(defined("$on::FOREIGN_KEYS") && !empty($on::FOREIGN_KEYS[$join])){
            $localKey = $on::FOREIGN_KEYS[$join];
            $foreignKey = $join::PRIMARY_KEY;
            $relation = "one-to-one";
        } else if(defined("$join::FOREIGN_KEYS") && !empty($join::FOREIGN_KEYS[$on])) {
            $localKey = $on::PRIMARY_KEY;
            $foreignKey = $join::FOREIGN_KEYS[$on];
            $relation = "one-to-many";
        } else {
            throw new \Exception("Join is not defined.  Make sure foreign keys have been defined.");
        }

        if($on === $this->queryObject){
            $this->joins[] = new Join($localKey, $this->queryObject, $foreignKey, $join, $relation);
        } else {
            forEach($this->joins as $j){
                $j->add($join, $on);
            }
        }
        
        return $this;
    }

    public function where($object, $column, $operator, $value)
    {
        $this->conditions[] = [$object, $column, $operator, $value, ''];
        return $this;
    }

    public function and($object, $column, $operator, $value)
    {
        $this->conditions[] = [$object, $column, $operator, $value, ' AND '];
        return $this;
    }

    public function or($object, $column, $operator, $value)
    {
        $this->conditions[] = [$object, $column, $operator, $value, ' OR '];
        return $this;
    }

    public function orderBy(string $object, string $column)
    {
        if($object === $this->queryObject){
            $this->orderBy[] = $column;
        } else {
            forEach($this->joins as $join){
                $join->orderBy($object, $column);
            }
        }
        return $this;
    }

    public function first($dbh)
    {
        $results = $this->execute($dbh);
        if(empty($results)) throw new \Exception("No results found.");
        return $results[0];
    }

    public function executeAndGet($dbh)
    {
        $this->execute($dbh);
        if($this->operation == 'insert'){
            return Querier::select($this->queryObject)->where($this->queryObject, $this->queryObject::PRIMARY_KEY, '=', $dbh->lastInsertId())->first($dbh);
        } else {
            return Querier::select($this->queryObject)->where($this->queryObject, $this->queryObject::PRIMARY_KEY, '=', $this->bindings[$this->queryObject::PRIMARY_KEY]->getValue())->first($dbh);
        }
        
    }

    public function execute($dbh)
    {
        // generate SQL
        $sql = $this->getSQL();  
        
        // prepate our query
        $stmt = $dbh->prepare($sql);

        // bind our parameters
        
        forEach($this->bindings as $key => $binding){
            if(strpos($key, ':') !== 0) $key = ':' . $key;
            $stmt->bindValue($key, $binding->getValue(), $binding->__getSQLDataType());
        }
        
        // execute query
        $stmt->execute();
        
        // handle errors
        $error = $stmt->errorInfo();
        
        // return results
        $results = $stmt->fetchAll(\PDO::FETCH_CLASS, $this->queryObject);
        
        // build foreign key indexes
        if(!empty($this->joins)) $this->buildIndex($results);

        // get join results
        forEach($this->joins as $join){
            $joinResults = $join->on(array_keys($this->indexes[$join->getLocalColumn()]))->execute($dbh);
            if(!empty($joinResults)){
                $this->applyJoinResults($results, $joinResults, $join);
            }
        }
        return $results;
    }

    public function getSQL()
    {
        switch($this->operation){
            // Create a SQL SELECT statement
            case 'select':
                $sql = "SELECT * FROM " . ($this->queryObject)::TABLE;
            break;
            // Create a SQL INSERT statement
            case 'insert':
                $sql = "INSERT INTO " . $this->queryObject::TABLE . "(" . implode(',', $this->columns) . ") VALUES (:" . implode(', :', $this->columns) . ")";
            break;
            // Create a SQL UPDATE statement
            case 'update':
                $sql = "UPDATE " . $this->queryObject::TABLE . " SET "; $setCount = 0;
                forEach($this->columns as $column) {
                    if($column == $this->queryObject::PRIMARY_KEY) continue;
                    if($setCount != 0) $sql .= ', ';
                    $sql .= $column . ' = :' . $column;
                    ++$setCount;
                }
        }
        
        // create our where statement if applicable
        $objectProperties = []; $inOr = false;
        forEach($this->conditions as $index => $condition){
            if($condition[2] == 'IN' && is_array($condition[3])){
                if($index == 0) $sql .= " WHERE ";
                $sql .= $condition[4] . $condition[1] . ' ' . $condition[2] . ' (' . implode(',',$condition[3]) . ')';
                continue;
            }
            // reflect our object
            $ref = new \ReflectionClass($condition[0]);
            // grab our column
            $property = $ref->getProperty('col_' . $condition[1]);
            // get the columns type
            $class = '\\' . $property->getType()->getName();
            // store the parameter we need to bind
            $this->bindings[':'. $condition[1]] = new $class($condition[3]);
            // build our WHERE clause
            if($index == 0) $sql .= " WHERE ";
            $sql .= $condition[4] . $condition[1] . ' ' . $condition[2] . ' :' . $condition[1];
        }

        // build ORDER BY
        forEach($this->orderBy as $index => $orderBy){
            if($index == 0) $sql .= " ORDER BY ";
            if($index != 0) $sql .= ", ";
            $sql .= $orderBy;
        }
        print_r($sql . "\n");
        return $sql . ";";
    }

    private function buildIndex(&$results)
    {
        forEach($this->joins as $join){
            if(!empty($this->indexes[$join->getLocalColumn()])){
                $this->indexes[$join->getLocalColumn()] = [];
            }
            forEach($results as $index => $result){
                if(empty($this->indexes[$join->getLocalColumn()][$result->{$join->getLocalColumn()}])) $this->indexes[$join->getLocalColumn()][$result->{$join->getLocalColumn()}] = [];
                $this->indexes[$join->getLocalColumn()][$result->{$join->getLocalColumn()}][] = $index;
            }
        }
    }

    public function applyJoinResults(array &$results, array &$joinResults, Join $join)
    {
        $joinObj = $join->getForeignObject();
        $column = $join->getForeignColumn();
        forEach($joinResults as $joinResult){
            forEach($this->indexes[$column][$joinResult->{$column}] as $resultIndex){
                $reflect = new \ReflectionClass($joinObj);    
                if(empty($results[$resultIndex]->{lcfirst($reflect->getShortName())})){
                    $results[$resultIndex]->{lcfirst($reflect->getShortName())} = [];
                    $results[$resultIndex]->addJoinProperty(lcfirst($reflect->getShortName()));
                }
                if($join->isOneToOne()){
                    $results[$resultIndex]->{lcfirst($reflect->getShortName())} = $joinResult;
                } else {
                    $results[$resultIndex]->{lcfirst($reflect->getShortName())}[] = $joinResult;
                }
                
            }
        }
    }

    public static function insert(\obray\reflectdb\DB $object)
    {
        $q = new Querier(get_class($object), 'insert');
        $values = $object->getValues();
        $q->setColumns(array_keys($values));
        $q->setBindings($values);
        return $q;
    }

    public static function update(\obray\reflectdb\DB $object)
    {
        print_r("Attempting to update\n");
        $q = new Querier(get_class($object), 'update');
        $values = $object->getValues();
        $q->setColumns(array_keys($values));
        $q->setBindings($values);
        $q->where(get_class($object), $object::PRIMARY_KEY, '=', $object->{$object::PRIMARY_KEY});
        return $q;
    }

}