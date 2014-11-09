<?php

use MartynBiz\Database\Adapter;

class AdapterTest extends PHPUnit_Framework_TestCase
{
    protected $PDOMock;
    protected $PDOStatementMock;
    
    public function setUp()
    {
        $PDOMock = $this->getMockBuilder('PDOMock')
            ->getMock();
        $PDOStatementMock = $this->getMockBuilder('PDOStatementMock')
            ->getMock();
        
        // set PDOMock's method prepare to return PDOStatementMock
        $PDOMock->method('prepare')
            ->will( $this->returnValue($PDOStatementMock) );
        
        $this->PDOMock = $PDOMock;
        $this->PDOStatementMock = $PDOStatementMock;
    }
    
    public function testSelect()
    {
        // test criteria
        $tableName = 'accounts';
        $where = 'name = ? AND age = ?';
        $whereValues = array('Joe', 29);
        $options = array('limitStart' => 10, 'limitMax' => 20);
        
        $mockExecuteResult = array('id' => 99);
        
        $sql = 'SELECT * FROM ' . $tableName . ' WHERE ' . $where . ' LIMIT ' . $options['limitStart'] . ', ' . $options['limitMax'];
        
        // create mocks
        $PDOMock = $this->PDOMock;
        $PDOStatementMock = $this->PDOStatementMock;
        
        $PDOMock->expects( $this->once() )
            ->method('prepare')
            ->with($sql);
        $PDOStatementMock->expects( $this->once() )
            ->method('execute')
            ->with($whereValues);
        $PDOStatementMock->expects( $this->once() )
            ->method('fetchAll')
            ->will( $this->returnValue($mockExecuteResult) );
        
        // create instance with mock
        $adapter = new Adapter(null, $PDOMock);
        
        // perform test
        $result = $adapter->select($tableName, $where, $whereValues, $options);
        
        $this->assertEquals($result, $mockExecuteResult);
    }
    
    public function testInsert()
    {
        // test criteria
        $tableName = 'accounts';
        $values = array(
            array(
                'name' => 'Joe',
                'age' => 29
            ),
            array(
                'name' => 'Jim',
                'age' => 43
            )
        );
        
        $sql = 'INSERT INTO ' . $tableName . ' (' . implode(', ', array_keys($values[0])) . ') VALUES (?, ?)';
        
        // create mocks
        $PDOMock = $this->PDOMock;
        $PDOStatementMock = $this->PDOStatementMock;
        
        $PDOMock->expects( $this->once() )
            ->method('prepare')
            ->with($sql);
        $PDOStatementMock->expects( $this->exactly(count($values)) )
            ->method('execute')
            ->will( $this->returnValue(true) );
        
        // create instance with mock
        $adapter = new Adapter(null, $PDOMock);
        
        $result = $adapter->insert($tableName, $values);
        
        $this->assertEquals($result, true);
    }
    
    public function testUpdate()
    {
        // test criteria
        $tableName = 'accounts';
        $values = array(
            'name' => 'Jim',
            'age' => 43
        );
        $where = 'name = ? AND age = ?';
        $whereValues = array('Joe', 29);
        $options = array('limitStart' => 10, 'limitMax' => 20);
        
        $sql = 'UPDATE ' . $tableName . ' SET name = ?, age = ? WHERE ' . $where . ' LIMIT ' . $options['limitStart'] . ', ' . $options['limitMax'];
        
        // create mocks
        $PDOMock = $this->PDOMock;
        $PDOStatementMock = $this->PDOStatementMock;
        
        $PDOMock->expects( $this->once() )
            ->method('prepare')
            ->with($sql);
        $PDOStatementMock->expects( $this->once() )
            ->method('execute')
            ->will( $this->returnValue(true) );
        
        // create instance with mock
        $adapter = new Adapter(null, $PDOMock);
        
        $result = $adapter->update($tableName, $values, $where, $whereValues, $options);
        
        $this->assertEquals($result, true);
    }
    
    public function testDelete()
    {
        // test criteria
        $tableName = 'accounts';
        $where = 'name = ? AND age = ?';
        $whereValues = array('Joe', 29);
        $options = array('limitStart' => 10, 'limitMax' => 20);
        
        $sql = 'DELETE FROM ' . $tableName . ' WHERE ' . $where . ' LIMIT ' . $options['limitStart'] . ', ' . $options['limitMax'];
        
        // create mocks
        $PDOMock = $this->PDOMock;
        $PDOStatementMock = $this->PDOStatementMock;
        
        $PDOMock->expects( $this->once() )
            ->method('prepare')
            ->with($sql);
        $PDOStatementMock->expects( $this->once() )
            ->method('execute')
            ->will( $this->returnValue(true) );
        
        // create instance with mock
        $adapter = new Adapter(null, $PDOMock);
        
        $result = $adapter->delete($tableName, $where, $whereValues, $options);
        
        $this->assertEquals($result, true);
    }
    
    protected function getMockPDO()
    {
        return $this->getMockBuilder('PDOMock')
            ->getMock();
    }
    
    protected function getMockPDOStatement()
    {
        return $this->getMockBuilder('PDOStatementMock')
            ->getMock();
    }
}