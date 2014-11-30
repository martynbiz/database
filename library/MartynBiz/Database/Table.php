<?php

namespace MartynBiz\Database;

use MartynBiz\Database\AdapterInterface;

abstract class Table implements TableInterface
{
    protected $tableName;
    
    protected $adapter;
    
    /**
    * Relationship arrays
    */
    protected $belongsTo = array();
    protected $hasMany = array();
    
    /**
    * These properties allow us to test by giving us access to the internal clas
    * However, during run time this is not required (and not really allowed)
    */
    protected $allowRuntimeSetting = false;
    
    public static function getInstance(AdapterInterface $adapter)
    {
        static $instance = null;
        if (null === $instance) {
            $className = get_called_class();
            $instance = new $className($adapter);
        }

        return $instance;
    }
    
    public function __construct(AdapterInterface $adapter)
    {
        $this->adapter = $adapter;
        
        //here we will also getInstances of any of our assoc 'table' values
        // foreach($this->belongsTo as $key => $arr) {
        //     $this->belongsTo[$key]['table'] = $arr['table']::getInstance($adapter);
        // }
        // foreach($this->hasMany as $key => $arr) {
        //     $this->hasMany[$key]['table'] = $arr['table']::getInstance($adapter);
        // }
    }
    
    public function find($id)
    {
        $where = 'id = ?';
        $whereValues = array(1);
        $options = array(
            'limitMax' => 1,
        );
        
        $rowset = $this->select($where, $whereValues, $options);
        
        if($rowset->count() == 0)
            throw new \InvalidArgumentException('Row of id ' . $id . ' could not be found');
        
        return $rowset->current();
    }
    
    public function select($where=null, $whereValues=null, $options=array())
    {
        $result = $this->adapter->select($this->tableName, $where, $whereValues, $options);
        
        $rowset = new Rowset();
        if(! empty($result)) {
            foreach ($result as $values) {
                $rowset->push( new Row($this, $values) );
            }
        }
        
        return $rowset;
    }
    
    public function prepareNew($value=array())
    {
        return new Row($this, $value);
    }
    
    public function create($values)
    {
        return $this->adapter->insert($this->tableName, $values);
    }
    
    public function update($values, $where, $whereValues, $options=array())
    {
        return $this->adapter->update($this->tableName, $values, $where, $whereValues, $options);
    }
    
    public function delete($where, $whereValues, $options=array())
    {
        return $this->adapter->delete($this->tableName, $where, $whereValues, $options);
    }
    
    /**
    * This will check the relationship arrays (belongsTo, hasMany etc) and return a match if exists
    * 
    * @param $name string name of related item
    */
    public function getAssoc($name) {
        
        if (isset($this->belongsTo[$name])) {
            $result = $this->belongsTo[$name];
            $result['type'] = 'belongsTo';
            $tableName = $this->belongsTo[$name]['table'];
            $result['table'] = $tableName::getInstance($this->adapter);
            return $result;
        }
        
        if (isset($this->hasMany[$name])) {
            $result = $this->hasMany[$name];
            $result['type'] = 'hasMany';
            $tableName = $this->hasMany[$name]['table'];
            $result['table'] = $tableName::getInstance($this->adapter);
            return $result;
        }
    }
    
    
    // public function allowRuntimeSetting()
    // {
    //     $this->allowRuntimeSetting = true;
    // }
    
    // public function setHasMany($name, $hasMany)
    // {
    //     if (! $this->allowRuntimeSetting)
    //         return false;
        
    //     $this->hasMany[$name] = $hasMany;
    // }
    
    // public function setBelongsTo($name, $belongsTo)
    // {
    //     if (! $this->allowRuntimeSetting)
    //         return false;
        
    //     $this->belongsTo[$name] = $belongsTo;
    // }
    
    // public function getAdapter()
    // {
    //     return $this->adapter;
    // }
    
    // public function setAdapter($adapter)
    // {
    //     if (! $this->allowRuntimeSetting)
    //         return false;
        
    //     $this->adapter = $adapter;
    // }
}