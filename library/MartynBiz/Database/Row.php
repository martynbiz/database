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
    
    public function __construct(Table $table, $values=array())
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
        
        // name doesn't exist within this rows values array, check if an assoc has been set on
        // the table class
        
        $assoc = $this->table->getAssoc($name);
        if (is_array($assoc)) {
            // set the table
            $table = $assoc['table'];
            $foreignKey = $assoc['foreign_key'];
            
            switch ($assoc['type']) {
                case 'belongsTo':
                    
                    $where = $foreignKey . ' = ?';
                    $whereValues = array($this->values[$foreignKey]); //***what happens if id not set
                    $options = array(
                        'limitMax' => 1,
                    );
                    
                    $rows = $table->select($where, $whereValues, $options);
                    
                    if(count($rows) > 0) {
                        return new Row($table, $rows[0]);
                    }
                    
                    break;
                case 'hasMany':
                    
                    $where = $foreignKey . ' = ?';
                    $whereValues = array($this->values['id']); //***what happens if id not set
                    
                    $rows = $table->select($where, $whereValues);
                    
                    $rowset = array(); // what we return
                    foreach($rows as $values) {
                        array_push($rowset, new Row($table, $values));
                    }
                    
                    return $rowset;
            }
        }
        
        return null;
        
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