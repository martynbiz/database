<?php

namespace MartynBiz\Database;

class Row
{
    /**
    * Table gateway class for this row
    */
    protected $table;
    
    /**
    * Values of this row
    */
    protected $values;
    
    public function __construct($values=array(), Table $table)
    {
        $this->values = $values;
        $this->table = $table;
    }
    
    /**
    * Getter
    */
    public function __get($name) {
        
        if (isset($this->values[$name]))
            return $this->values[$name];
        
        // check in relation exists in table class
        
        // e.g. get back array('type'=>'hasMany', 'table'=>'trans...', 'fkey'=>'account_id')
        //   get rows where Transaction.select(account_id=id)   
        
        // e.g. get back array('type'=>'belongsTo', 'table'=>'user...', 'fkey'=>'user_id')
        //   get rows where User->find(user_id)
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