<?php

namespace obray\reflectdb;

class DB
{
    private ?string $table = null;
    private ?string $primaryKey = null;

    public function createTable(): void
    {
        $sql = "CREATE TABLE " . $this->table;
        $ref = new \ReflectionClass(get_called_class());
        $properties = $ref->getProperties();

        forEach($properties as $property){
            $type = $property->getType();
            if($type !== null){
                print_r($type->getName() . "\n");
            }
            
            print_r($property->name . "\n");
        }

        print_r($properties);
        
        return;
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
}