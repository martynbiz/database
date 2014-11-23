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
}