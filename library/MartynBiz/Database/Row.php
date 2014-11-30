<?php

/**
* TODO:
* - have property called, updatedValues so we are only updating values that have changed
*/

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
        if(! is_array($values))
            throw new \Exception('Values passed should be an array');
        
        $this->values = $values;
        $this->table = $table;
    }
    
    public function set($first=array(), $value=null)
    {
        if (is_array($first)) {
            foreach($first as $key => $value) {
                $this->values[$key] = $value;
            }
        } elseif(! is_null($value)) {
            $this->values[$first] = $value;
        }
    }
    
    public function uset($name)
    {
        if (is_array($name)) {
            foreach($name as $single) {
                unset($this->values[$single]);
            }
        } else {
            unset($this->values[$name]);
        }
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
                    
                    $rowset = $table->select($where, $whereValues, $options);
                    
                    if($rowset->count() > 0) {
                        return new Row($table, $rowset->current()->toArray());
                    }
                    
                    break;
                case 'hasMany':
                    
                    $where = $foreignKey . ' = ?';
                    $whereValues = array($this->values['id']); //***what happens if id not set
                    
                    $rowset = $table->select($where, $whereValues);
                    
                    // $rowset = new Rowset(); // what we return
                    // foreach($rows as $values) {
                    //     $rowset->push( new Row($table, $values) );
                    // }
                    
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
        if (isset($this->values['id']) && ! is_null(isset($this->values['id']))) {
            return $this->table->update($this->values, 'id = ?', array((int) $this->values['id']));
        } else {
            return $this->table->create($this->values);
        }
    }
    
    /**
    * Delete the row from the database, remove the id (so if we then save it will insert)
    */
    public function delete()
    {
        if (isset($this->values['id']) && ! is_null(isset($this->values['id']))) {
            return $this->table->delete('id = ?', array((int) $this->values['id']));
        } else {
            throw new \InvalidArgumentException('ID not set');
        }
    }
    
    /**
    * Return values as array
    */
    public function toArray()
    {
        return $this->values;
    }
}