<?php

use MartynBiz\Database\Rowset;
use MartynBiz\Database\Row;

class RowsetTest extends PHPUnit_Framework_TestCase
{
    protected $rowMock;
    protected $accountTableMock;
    
    public function setUp()
    {
        $rowMock = $this->getMockBuilder('MartynBiz\Database\Row')
            ->disableOriginalConstructor()
            ->getMock();
        $this->rowMock = $rowMock;
        
        $accountTableMock = $this->getMockBuilder('Account')
            ->disableOriginalConstructor()
            ->getMock();
        $this->accountTableMock = $accountTableMock;
    }
    
    public function testCountReturnsZero()
    {
        $rowset = new Rowset();
        
        $this->assertEquals( 0, $rowset->count() );
    }
    
    public function testPush()
    {
        $rowset = new Rowset();
        
        $rowMock = $this->rowMock;
        
        $rowset->push($rowMock);
        
        $this->assertEquals( 1, $rowset->count() );
    }
    
    public function testFirst()
    {
        $arraySet = array(
            array(
                'id' => 1,
                'name' => 'Joe',
            ),
            array(
                'id' => 2,
                'name' => 'Jim',
            ),
        );
        
        // build up the rowset
        $rowset = new Rowset();
        foreach($arraySet as $values) {
            $rowset->push( new Row( $this->accountTableMock, $values ) );
        }
        
        $this->assertEquals( $arraySet[0]['id'], $rowset->first()->id );
    }
    
    public function testToArray()
    {
        $arraySet = array(
            array(
                'id' => 1,
                'name' => 'Joe',
            ),
            array(
                'id' => 2,
                'name' => 'Jim',
            ),
        );
        
        // build up the rowset
        $rowset = new Rowset();
        foreach($arraySet as $values) {
            $rowset->push( new Row( $this->accountTableMock, $values ) );
        }
        
        $this->assertEquals( $arraySet, $rowset->toArray() );
    }
    
    /**
     * @dataProvider filterCriteria
     */
    public function testFilter($arraySet, $criteria, $expected)
    {
        // build up the rowset
        $rowset = new Rowset();
        foreach($arraySet as $values) {
            $rowset->push( new Row( $this->accountTableMock, $values ) );
        }
        
        $filtered = $rowset->filter($criteria);
        
        $this->assertEquals( $expected, $filtered->count() );
    }
    
    public function filterCriteria()
    {
        $a = array(
            array(
                'id' => 1,
                'name' => 'Joe',
            ),
            array(
                'id' => 2,
                'name' => 'Jim',
            ),
            array(
                'id' => 3,
                'name' => 'Jim',
            ),
        );
        
        return array(
            array( $a, array('id' => $a[0]['id']), 1 ),
            array( $a, array('name' => $a[1]['name']), 2 ),
            array( $a, array('id' => $a[1]['id'], 'name' => $a[1]['name']), 1 ),
            array( $a, array('id' => 999999), 0 ),
        );
    }

}