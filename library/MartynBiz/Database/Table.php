<?php

namespace MartynBiz\Database;

use MartynBiz\Database\AdapterInterface;

abstract class Table
{
    protected $tableName;
    
    protected $adapter;
    
    /**
    * Relationship arrays
    */
    protected $belongsTo = array();
    protected $hasMany = array();
    
    public function __construct(AdapterInterface $adapter)
    {
        $this->adapter = $adapter;
    }
    
    public function find($id)
    {
        $where = 'id = ?';
        $whereValues = array(1);
        $options = array(
            'limitMax' => 1,
        );
        
        $result = $this->select($where, $whereValues, $options);
        
        if(empty($result))
            throw new \InvalidArgumentException('Row of id ' . $id . ' could not be found');
        
        return $result[0];
    }
    
    public function select($where=null, $whereValues=null, $options=array())
    {
        return $this->adapter->select($this->tableName, $where, $whereValues, $options);
    }
    
    public function prepareNew($value=array())
    {
        return new Row($value, $this);
    }
    
    public function create($values)
    {
        return $this->adapter->insert($this->tableName, $values);
    }
    
    public function update($values, $where, $whereValues, $options)
    {
        return $this->adapter->update($this->tableName, $values, $where, $whereValues, $options);
    }
    
    public function delete($where, $whereValues, $options)
    {
        return $this->adapter->delete($this->tableName, $where, $whereValues, $options);
    }
    
    /**
    * This will check the relationship arrays (belongsTo, hasMany etc) and return a match if exists
    * 
    * @param $name string name of related item
    */
    public function getRelationship($name) {
        // check each of the arrays if they contain the give name
        $relationshipArrays = array('belongsTo', 'hasMany');
        
        foreach($relationshipArrays as $type) {
            if (isset($this->$type[$name])) {
                $result = $this->$type[$name];
                $result['type'] = $type; // add the type too, it will be useful
                return $result;
            } 
        }
    }
}