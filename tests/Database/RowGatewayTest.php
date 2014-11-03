<?php

class RowGatewayTest extends PHPUnit_Framework_TestCase
{
    protected $adapter;
    
    public function setUp()
    {
        $adapterMock = $this->getMockBuilder('MartynBiz\Database\Adapter')
            ->disableOriginalConstructor()
            ->getMock();
        
        $this->adapterMock = $adapterMock;
    }