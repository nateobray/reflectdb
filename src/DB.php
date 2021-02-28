<?php

namespace obray\reflectdb;

class DB implements \JsonSerializable
{

    private array $joinProperties = [];

    public function hasProperty(string $key): bool
    {
        $calledClass = get_called_class();
        if(property_exists($calledClass, 'col_' . $key)){ 
            return true;
        }
        return false;
    }

    public function isValid()
    {
        $calledClass = get_called_class();
        $properties = get_class_vars($calledClass);

        forEach($properties as $key => $property){
            if(strpos($key, 'col') !== 0) continue;
            if(empty($this->{$key})) return false;
        }
        return true;
    }

    public function __set(string $key, $value)
    {
        $calledClass = get_called_class();
        if(property_exists($calledClass, 'col_' . $key)){
            $ref = new \ReflectionClass($calledClass);
            $property = $ref->getProperty('col_' . $key);
            $type = $property->getType();
            $class = $type->getName();
            $this->{'col_'.$key} = new $class($value);
        } else {
            $this->$key = $value;
        }
    }

    public function __get(string $key)
    {
        $calledClass = get_called_class();
        if(property_exists($calledClass, 'col_' . $key)){
            return $this->{'col_' . $key}->getValue();
        }
    }
    
    public function dropTable(): void
    {
        return;
    }

    public function addJoinProperty($property)
    {
        $this->joinProperties[] = $property;
    }

    public function jsonSerialize() 
    {    
        $calledClass = get_called_class();
        $properties = get_class_vars($calledClass);
        $obj = [];
        // encode our columns
        forEach($properties as $key => $value){
            if(strpos($key, 'col_', 0) === 0){
                print_r($key . "\n");
                $obj[str_replace('col_','',$key)] = $this->{$key};    
            }
        }
        // encode our joins
        if(!empty($this->joinProperties)){
            forEach($this->joinProperties as $property){
                $obj[$property] = $this->{$property};
            }
        }
        return $obj;
    }

    public function getValues()
    {
        $calledClass = get_called_class();
        $properties = get_class_vars($calledClass);
        
        // encode our columns
        $values = [];
        forEach($properties as $key => $value){
            if(strpos($key, 'col_', 0) === 0 && !empty($this->{$key})){
                $values[str_replace('col_','',$key)] = $this->{$key};    
            }
        }
        return $values;
    }

    public static function create(array $params)
    {
        
        $calledClass = get_called_class();
        $obj = new $calledClass();
        
        forEach($params as $key => $value){
            print_r($key);
            if($obj->hasProperty($key)){
                print_r("Hello world\n");
                $obj->$key = $value;        
            } else {
                print_r("Hello world\n");
                throw new \Exception("Invalid Property");
            }
        }
        if(!$obj->isValid()) throw new \Exception("Incomplete object");
        return $obj;
    }
}