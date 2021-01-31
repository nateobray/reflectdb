<?php

namespace obray\reflectdb;

class DB implements \JsonSerializable
{

    private array $joinProperties = [];

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
                $obj[str_replace('col_','',$key)] = $this->{$key}->getValue();    
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
}