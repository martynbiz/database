<?php

namespace MartynBiz\Database;

interface AdapterInterface {
	
    //public function __construct(array $dbConfig);
	public function __construct($dsn, $user, $password, $pdo=null);
	
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
    public function select($tableName, $where=null, $whereValues=null, $options=array());
	
    /**
    * Insert rows to a table
    * 
    * @param $tableName string Name of the table to insert to
    * @param $values Values to insert, can be multiple rows
    *
    * @return boolean
    */
    public function insert($tableName, array $values);
	
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
    public function update($tableName, array $values, $where=null, $whereValues=null, $options=array());
	
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
    public function delete($tableName, $where=null, $whereValues=null, $options=array());
}
