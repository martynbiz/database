<?php

class AbstractTableGatewayTest extends PHPUnit_Framework_TestCase
{
    protected $adapter;
    
    public function setUp()
    {
        $adapterMock = $this->getMockBuilder('MartynBiz\Database\Adapter')
            ->disableOriginalConstructor()
            ->getMock();
        
        $this->adapterMock = $adapterMock;
    }
    
    public function testGetInstanceReturnsInstanceOfClass()
    {
        $adapter = $this->adapterMock;
        $accountsTable = Account::getInstance($adapter);
        
        $this->assertTrue($accountsTable instanceof Account);
    }
    
    public function testGettingAndSettingAdapter()
    {
        $adapter = $this->adapterMock;
        $accountsTable = new Account($adapter); // getInstance doesn't work well in testing
        
        // test the dependency injection worked
        $this->assertEquals($accountsTable->getAdapter(), $adapter);
        
        // set adapter with clone
        $adapter2 = clone $adapter;
        
        // ensure that these two adapters are not the same instance
        $this->assertTrue($adapter != $adapter2);
        
        // set the adapter
        $accountsTable->setAdapter($adapter2);
        
        $this->assertEquals($accountsTable->getAdapter(), $adapter2);
    }
    
    public function testFindWithValidId()
    {
        // the id we are looking for
        $where = 'id = ?';
        $whereValues = array(1); // id=1
        $options = array(
            'limitMax' => 1,
        );
        
        // our fake result
        $mockResult = array(
            array(
                'id' => $whereValues[0],
                'name' => 'Joe',
                'amount' => 100,
            ),
        );
        
        $adapter = $this->adapterMock;
        
        // prepare the mock to expect the variables passed, and return a row
        $adapter->expects( $this->once() )
            ->method('select')
            ->with('accounts', $where, $whereValues, $options)
            ->will( $this->returnValue($mockResult) );
        
        $accountsTable = new Account($adapter); // getInstance doesn't work well in testing
        
        $result = $accountsTable->find(1);
        
        $this->assertEquals($result['id'], $whereValues[0]); // check the ids match
    }
    
    /**
     * @expectedException     InvalidArgumentException
     */
    public function testFindThrowsAnRuntimeExceptionWithInvalidId()
    {
        // mock result from Adapter#select, empty array (not found)
        $mockResult = array();
        
        // our mock adapter
        $adapter = $this->adapterMock;
        
        // prepare out mock to return an empty array on call
        $adapter->expects( $this->once() )
            ->method('select')
            ->will( $this->returnValue($mockResult) );
        
        // perform the test
        
        $accountsTable = new Account($adapter); // getInstance doesn't work well in testing
        
        $accountsTable->find(1);
    }
    
    public function testSelect()
    {
        // the rows we are looking for
        $where = 'amount > 99';
        $whereValues = array(1); // id=1
        $options = array(
            'limitMax' => 1,
        );
        
        // our fake result
        $mockResult = array(
            array(
                'id' => 1,
                'name' => 'Joe',
                'amount' => 100,
            ),
            array(
                'id' => 2,
                'name' => 'Jim',
                'amount' => 100,
            ),
        );
        
        // our mock adapter
        $adapter = $this->adapterMock;
        
        // prepare the mock to expect the variables passed, and return a row
        $adapter->expects( $this->once() )
            ->method('select')
            ->with('accounts', $where, $whereValues, $options)
            ->will( $this->returnValue($mockResult) );
        
        $accountsTable = new Account($adapter); // getInstance doesn't work well in testing
        
        $result = $accountsTable->select($where, $whereValues, $options);
        
        $this->assertEquals(count($result), count($mockResult)); // check the ids match
    }
    
    public function testSelectWithNoParameters()
    {
        // our mock adapter
        $adapter = $this->adapterMock;
        
        // prepare the mock to expect the variables passed, and return a row
        $adapter->expects( $this->once() )
            ->method('select')
            ->with('accounts', null, null, array());
        
        $accountsTable = new Account($adapter); // getInstance doesn't work well in testing
        
        $result = $accountsTable->select();
    }
    
    public function testCreate()
    {
        // row to create
        $values = array(
            'id' => 1,
            'name' => 'Joe',
            'amount' => 100,
        );
        
        // our mock adapter
        $adapter = $this->adapterMock;
        
        // prepare the mock to expect the variables passed, and return a row
        $adapter->expects( $this->once() )
            ->method('insert')
            ->with('accounts', $values)
            ->will( $this->returnValue(true) );
        
        $accountsTable = new Account($adapter); // getInstance doesn't work well in testing
        
        $result = $accountsTable->create($values);
        
        $this->assertTrue($result); 
    }
    
    public function testUpdate()
    {
        // the rows we are looking to update
        $where = 'amount > ?';
        $whereValues = array(1); // id=1
        $options = array(
            'limitMax' => 1,
        );
        $values = array(
            'id' => 1,
            'name' => 'Joe',
            'amount' => 100,
        );
        
        // our mock adapter
        $adapter = $this->adapterMock;
        
        // prepare the mock to expect the variables passed, and return a row
        $adapter->expects( $this->once() )
            ->method('update')
            ->with('accounts', $values, $where, $whereValues, $options)
            ->will( $this->returnValue(true) );
        
        $accountsTable = new Account($adapter); // getInstance doesn't work well in testing
        
        $result = $accountsTable->update($values, $where, $whereValues, $options);
        
        $this->assertTrue($result); 
    }
    
    public function testDelete()
    {
        // the rows we are looking to update
        $where = 'amount > 99';
        $whereValues = array(1); // id=1
        $options = array(
            'limitMax' => 1,
        );
        
        // our mock adapter
        $adapter = $this->adapterMock;
        
        // prepare the mock to expect the variables passed, and return a row
        $adapter->expects( $this->once() )
            ->method('delete')
            ->with('accounts', $where, $whereValues, $options)
            ->will( $this->returnValue(true) );
        
        $accountsTable = new Account($adapter); // getInstance doesn't work well in testing
        
        $result = $accountsTable->delete($where, $whereValues, $options);
        
        $this->assertTrue($result); 
    }
    
    public function testNewReturnsInstanceOfRow()
    {
        // our mock adapter
        $adapter = $this->adapterMock;
        
        $accountsTable = new Account($adapter); // getInstance doesn't work well in testing // just need adapter to instantiate
        
        $account = $accountsTable->prepareNew();
        
        $this->assertTrue($account instanceof \MartynBiz\Database\Row);
    }
    
    public function testGetAssoc()
    {
        // our mock adapter - only needed for construction of Accounts
        $adapter = $this->adapterMock;
        
        $accountsTable = new Account($adapter); // getInstance doesn't work well in testing
        
        // test returns the correct array
        $userAssoc = $accountsTable->getAssoc('user'); // returns and array with User
        $transactionsAssoc = $accountsTable->getAssoc('transactions'); // returns and array with Transaction
        $nullAssoc = $accountsTable->getAssoc('idontexist'); // returns null
        
        $this->assertEquals($userAssoc['type'], 'belongsTo');
        $this->assertEquals($transactionsAssoc['type'], 'hasMany');
        $this->assertTrue(is_null($nullAssoc));
    }
    
    public function testSwappingAssocAllowedWhenAllowIsSetToTrue()
    {
        // our mock adapter - only needed for construction of Accounts
        $adapter = $this->adapterMock;
        
        $accountsTable = new Account($adapter); // getInstance doesn't work well in testing
        
        $accountsTable->setBelongsTo('user', array(
            'foreign_key' => 'new_belongsTo',
        ));
        
        // set our protected properties to something else
        $accountsTable->setHasMany('transactions', array(
            'foreign_key' => 'new_hasMany',
        ));
        
        $userAssoc = $accountsTable->getAssoc('user'); // returns and array with User
        $transactionsAssoc = $accountsTable->getAssoc('transactions');
        
        $this->assertEquals($userAssoc['foreign_key'], 'new_belongsTo');
        $this->assertEquals($transactionsAssoc['foreign_key'], 'new_hasMany');
    }
    
    public function testSwappingAssocNotAllowedWhenAllowIsSetToFalse()
    {
        // our mock adapter - only needed for construction of Accounts
        $adapter = $this->adapterMock;
        
        $userTable = new Account($adapter); // getInstance doesn't work well in testing
        // $transactionTable = new Transaction($adapter);
        
        // set our protected properties to something else
        
        $userTable->setHasMany('transactions', array(
            'foreign_key' => 'new_hasMany',
        ));
        
        // $transactionTable->setBelongsTo('user', array(
        //     'foreign_key' => 'new_belongsTo',
        // ));
        
        $userAssoc = $userTable->getAssoc('user'); // returns and array with User
        // $transactionsAssoc = $userTable->getAssoc('transactions');
        
        $this->assertEquals($userAssoc['foreign_key'], 'user_id');
        // $this->assertEquals($transactionsAssoc['foreign_key'], 'user_id');
    }
}