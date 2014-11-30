<?php

namespace MartynBiz\Database;

class Rowset implements \Iterator
{
    private $position = 0;
    private $rowset = array();  

    public function __construct()
    {
        $this->position = 0;
    }

    function rewind()
    {
        $this->position = 0;
    }

    function current()
    {
        return $this->rowset[$this->position];
    }

    function first()
    {
        return $this->rowset[0];
        //return (isset($this->rowset[0])) ? $this->rowset[0] : null;
    }

    function key()
    {
        return $this->position;
    }

    function next()
    {
        ++$this->position;
    }

    function valid()
    {
        return isset($this->rowset[$this->position]);
    }
    
    function count()
    {
        return count($this->rowset);
    }
    
    function push(Row $row)
    {
        return array_push($this->rowset, $row);
    }
    
    /**
    * Return values as array
    */
    public function toArray()
    {
        $return = array();
        foreach($this->rowset as $row) {
            array_push($return, $row->toArray() );
        }
        
        return $return;
    }
    
    /**
    * Return values as array
    */
    public function filter($criteria)
    {
        // loop through each row in the rowset
        $filtered = new $this; // we'll populate this new Rowset with those which meet $criteria
        foreach($this->rowset as $row) {
            $pass = true; // true by default, until proved wrong
            
            // loop through each criteria and compare the rows porperties
            foreach($criteria as $name => $value) {
                if($row->$name != $value) $pass = false;
            }
            
            // we have finished checking, did all criteria pass?
            if($pass) {
                $filtered->push($row);
            }
        }
        
        return $filtered;
    }
}