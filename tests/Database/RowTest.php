<?php

use MartynBiz\Database\Row;

class RowGatewayTest extends PHPUnit_Framework_TestCase
{
    protected $userTableMock;
    
    public function setUp()
    {
        $userTableMock = $this->getMockBuilder('User')
            ->disableOriginalConstructor()
            ->getMock();
        
        $this->userTableMock = $userTableMock;
    }
    
    public function testRowIsInitiatedWithValues()
    {
        // values
        $values = array(
            'id' => 1,
            'name' => 'Martyn',
            'email' => 'joe@yahoo.com',
        );
        
        // we don't need to do anything with this mock, it just has to an instance of this class
        $userTableMock = $this->userTableMock;
        
        $user = new Row($values, $userTableMock);
        
        $this->assertEquals($user->id, $values['id']);
        $this->assertEquals($user->name, $values['name']);
    }
    
    public function testSaveCallsTableCreateMethodWithoutId()
    {
        
    }
    
    public function testSaveCallsTableUpdateMethodWithId()
    {
        
    }
    
    public function testDeleteCallsTableDeleteMethodWithId()
    {
        
    }
    
    public function testDeleteThrowsExceptionWithoutId()
    {
        
    }

}