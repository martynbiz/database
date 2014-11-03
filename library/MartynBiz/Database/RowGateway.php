<?php

namespace MartynBiz\Database;


abstract class RowGateway
{
    /**
    * Table gateway class for this row
    */
    protected $table;
    
    /**
    * Values of this row
    */
    protected $values;
    
    public function __construct($values=array(), TableGateway $table)
    {
        $this->adapter = $adapter;
        $this->table = $table;
    }
    
    /**
    * Getter
    */
    public function __get()
    {
        // check first if the property exists in the values, return that
        // next, check if the property exists in the relationships
    }
    
    /**
    * Setter
    */
    public function __set()
    {
        // check first if the property exists in the values, set that
    }
    
    /**
    * Save the row. If it is a new row (no id) then it will create, otherwise update
    */
    public function save()
    {
        
    }
    
    /**
    * Delete the row from the database, remove the id (so if we then save it will insert)
    */
    public function delete()
    {
        
    }
}