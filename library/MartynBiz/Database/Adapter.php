<?php

namespace MartynBiz\Database;

class Adapter implements AdapterInterface {
	
	protected $pdo;
	
    /**
    * This construct takes an instance of PDO as a dependency so we can pass in test stubs for mocking
    * 
    * @param $pdo object PDO instance
    */
	public function __construct(\PDO $pdo) {
		
        $this->pdo = $pdo;
	}
	
	/**
    * Select rows from a table
    * 
    * @param $tableName string Name of the table to select from
    * @param $where Where query
    * @param $whereValues Where query values
    * @param $options Query options (e.g. limit, start)
    *
    * @return array Multi dimensional PHP array of rows
    */
    public function select($tableName, $where=null, $whereValues=null, $options=array())
    {

        // build the query
        $sql = 'SELECT * FROM ' . $tableName;
        $this->_setWhere($sql, $where, $whereValues);
        $this->_setOptions($sql, $options);

        // prepare the statement
        $stmt = $this->pdo->prepare($sql);
        
        // execute
        $stmt->execute($whereValues);

        return $stmt->fetchAll();
	}
	
	/**
    * Insert rows to a table
    * 
    * @param $tableName string Name of the table to insert to
    * @param $values Values to insert, can be multiple rows
    *
    * @return boolean Unless all rows are inserted, will return false
    */
    public function insert($tableName, array $values) {

        // if a single insert, let's prepare the array as though it were multiple inserts (array of arrays) to keep things consistent further on
        if(! isset($values[0])) {
            $values = array($values);
        }
        

        // build the query
        $sql = 'INSERT INTO '.$tableName.' (' . 
            implode(', ', array_keys($values[0])) . 
            ') VALUES (' . 
            implode(', ', array_fill(0, count($values[0]), '?')) . 
            ')';

        // prepare the statement
        $stmt = $this->pdo->prepare($sql);

        // loop through each item in $values and execute the prepared statement
        $result = true;
        foreach($values as $value) {
            if (! $stmt->execute(array_values($value))) {
                $result = false;
            }
        }

        return $result;
	}
	
	/**
    * Update rows on a table
    * 
    * @param $tableName string Name of the table to update to
    * @param $values Values to update
    * @param $where Where query
    * @param $whereValues Where query values
    * @param $options Query options (e.g. limit, start)
    *
    * @return boolean
    */
    public function update($tableName, array $values, $where=null, $whereValues=null, $options=array()) {

        // generate name values string for the UPDATE
        // will generate something like - ['name = ?', 'age = ?', ...]
        $nameValuePairs = array();
        foreach($values as $key => $value) {
        	array_push($nameValuePairs, $key . ' = ?');
        }

        // build the query
        
        // e.g. 'UPDATE users SET (name = ?, age = ?)'
        $sql = 'UPDATE ' . $tableName . ' SET ' . implode(', ', $nameValuePairs);
        
        // e.g. '... WHERE status = ?'
        $this->_setWhere($sql, $where, $whereValues);
        
        // e.g. '... LIMIT 0,10'
        $this->_setOptions($sql, $options);

        // prepare the statement
        $stmt = $this->pdo->prepare($sql);

        // execute
        // join the two arrays together to fill in the '?' markers for update values and where sections
        $executeValues = array_merge( array_values($values), $whereValues);
        return $stmt->execute($executeValues);
	}
	
	/**
    * Delete rows from a table
    * 
    * @param $tableName string Name of the table to delete from
    * @param $where Where query
    * @param $whereValues Where query values
    * @param $options Query options (e.g. limit, start)
    *
    * @return boolean
    */
    public function delete($tableName, $where=null, $whereValues=null, $options=array()) {

        // these are the values for each ? in our query template
        $templateValues = array();

        // build the query
        $sql = 'DELETE FROM ' . $tableName;
        $this->_setWhere($sql, $where, $whereValues);
        $this->_setOptions($sql, $options);

        // prepare the statement
        $stmt = $this->pdo->prepare($sql);

        // execute
        return $stmt->execute($whereValues);
	}
	
    /**
    * _setWhere
    *
    * Sets the WHERE part of a query.
    *
    * @param string $sql The SQL query to set.
    * @param $where Where query
    * @param $whereValues Where query values
    *
    * @return boolean It will return false if at least one row was not inserted. Otherwise it will return true.
    */
	protected function _setWhere(&$sql, $where, $whereValues) {
		
		if (!is_null($where)) $sql.= ' WHERE ' . $where;
	}
	
	/**
    * _setOptions
    *
    * Sets the options (ORDER BY, LIMIT etc).
    *
    * @param string $sql The SQL query to set.
    * @param array $options Additional options such as limit, start etc to set.
    *
    * @return boolean It will return false if at least one row was not inserted. Otherwise it will return true.
    */
	protected function _setOptions(&$sql, array $options) {
		
		// set order by
		
		if (isset($options['orderBy']) and is_array($options['orderBy'])) {
			
			// removing spaces to prevent SQL injections
			$options['orderBy'][0] = str_replace(' ', '', $options['orderBy'][0]);
			
			// set column
			$sql.= ' ORDER BY ' . $options['orderBy'][0];
			
			// set directions
			if (isset($options['orderBy'][1])) {
				$options['orderBy'][1] = str_replace(' ', '', $options['orderBy'][1]);
				$sql.= ' ' . $options['orderBy'][1];
			}
		}
		
		// set limit
		
		if (isset($options['limitMax']) and isset($options['limitStart'])) {
			
			// ensure that these are numeric
			$options['limitMax'] = (integer) $options['limitMax'];
			$options['limitStart'] = (integer) $options['limitStart'];
			
			$sql.= ' LIMIT ' . $options['limitStart'] . ', ' . $options['limitMax'];
			
		} elseif (isset($options['limitMax'])) {
			
			// ensure that these are numeric
			$options['limitMax'] = (integer) $options['limitMax'];
			
			$sql.= ' LIMIT ' . $options['limitMax'];
			
		}
	}
	
}
