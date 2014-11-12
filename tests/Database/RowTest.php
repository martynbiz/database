<?php

use MartynBiz\Database\Row;

class RowTest extends PHPUnit_Framework_TestCase
{
    protected $userTableMock;
    
    public function setUp()
    {
        $userTableMock = $this->getMockBuilder('User')
            ->disableOriginalConstructor()
            ->getMock();
        $accountTableMock = $this->getMockBuilder('Account')
            ->disableOriginalConstructor()
            ->getMock();
        $transactionTableMock = $this->getMockBuilder('Transaction')
            ->disableOriginalConstructor()
            ->getMock();
        
        $this->userTableMock = $userTableMock;
        $this->accountTableMock = $accountTableMock;
        $this->transactionTableMock = $transactionTableMock;
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
        
        $user = new Row($userTableMock, $values);
        
        $this->assertEquals($user->id, $values['id']);
        $this->assertEquals($user->name, $values['name']);
    }
    
    public function testRowIsInitiatedWithoutValues()
    {
        // we don't need to do anything with this mock, it just has to an instance of this class
        $userTableMock = $this->userTableMock;
        
        $user = new Row($userTableMock);
        
        $this->assertTrue($user instanceof Row);
    }
    
    public function testPropertyValueGetterWorks()
    {
        // create a row with values and a mock table
        $values = array(
            'id' => 1,
            'name' => 'Martyn',
            'email' => 'joe@yahoo.com',
        );
        
        // we don't need to do anything with this mock, it just has to an instance of this class
        $userTableMock = $this->userTableMock;
        
        $user = new Row($userTableMock, $values);
        
        // check value of properties matches
        $this->assertEquals($values['id'], $user->id);
        $this->assertEquals($values['name'], $user->name);
        $this->assertEquals($values['email'], $user->email);
    }
    
    public function testPropertyValueSetterWorks()
    {
        // we don't need to do anything with this mock, it just has to an instance of this class
        $userTableMock = $this->userTableMock;
        
        $user = new Row($userTableMock);
        
        $user->id = 1;
        $user->name = 'Joe';
        $user->email = 'joe@mw.co.uk';
        
        // check value of properties matches
        $this->assertEquals(1, $user->id);
        $this->assertEquals('Joe', $user->name);
        $this->assertEquals('joe@mw.co.uk', $user->email);
    }
    
    public function testHasManyGetterReturnsArrayOfRowsWhenAssociationsAreFound()
    {
        // gonna create a user row which 'hasMany' accounts
        
        $userValues = array(
            'id' => 99, // this is the user_id value of accounts
        );
        
        // this is a really complex set of mock object nesting. Basically the user Row will call it's
        // tables getAssoc function to see if any associations exist on that key (e.g. accounts). If so,
        // an instance of the accounts table will be returned. So our mock user table will return a mock
        // account table (still with me? :) which we will use to query for rows by the foreign key (user_id)
        // the Row object will convert the array returned into an array of objects 
        
        
        // account - mock query/ return values. this will be found in the array returned from user->getAssoc
        // the account object will query it's table by the foreign for users (user_id) with the id of this row
        // it will be set to return an array of array row values
        $where = 'user_id = ?'; // a has many for users will compare the user_id of the table with this row id
        $whereValues = array( $userValues['id'] ); // row.id
        $returnAccounts = array( // will return two accounts
            array(
                'id' => 10,
                'name' => 'Cool bank',
                'user_id' => $userValues['id'],
            ),
            array(
                'id' => 100,
                'name' => 'Bad bank',
                'user_id' => $userValues['id'],
            ),
        );
        $accountTableMock = $this->accountTableMock;
        $accountTableMock->expects( $this->once() )
            ->method('select')
            ->with( $where, $whereValues )
            ->will( $this->returnValue( $returnAccounts ) );
        
        // now we have our account mock we can set it in the return array from user->getAssoc
        $returnAssoc = array(
            'type' => 'hasMany',
            'table' => $accountTableMock,
            'foreign_key' => 'user_id'
        );
        
        // mock user table. This table will expect a call to getAssoc which will return everything we 
        // need to get association rows
        $userTableMock = $this->userTableMock;
        $userTableMock->expects( $this->once() )
            ->method('getAssoc')
            ->with('accounts')
            ->will( $this->returnValue( $returnAssoc ) );
        
        // create a row with values (user_id=1) and a mock table
        $user = new Row($userTableMock, $userValues);
        
        // now, try to access the assoc via the Row object. We should pass the mock object assertions
        // such as $this->once, method('select') etc and have a return value
        $accounts = $user->accounts;
        
        //
        
        // check the count
        $this->assertEquals( count($returnAccounts), count($accounts) );
        
        // check that each is an instance of Account
        foreach($accounts as $account) {
            $this->assertTrue( $account instanceof Row );
        }
    }
    
    public function testHasManyGetterReturnsEmptyArrayWhenAssociationsAreNotFound()
    {
        // gonna create a user row which 'hasMany' accounts
        
        $userValues = array(
            'id' => 99, // this is the user_id value of accounts
        );
        
        
        // account - mock query/ return values. this will be found in the array returned from user->getAssoc
        // the account object will query it's table by the foreign for users (user_id) with the id of this row
        // it will be set to return an array of array row values
        $where = 'user_id = ?'; // a has many for users will compare the user_id of the table with this row id
        $whereValues = array( $userValues['id'] ); // row.id
        $returnAccounts = array();
        $accountTableMock = $this->accountTableMock;
        $accountTableMock->expects( $this->once() )
            ->method('select')
            ->with( $where, $whereValues )
            ->will( $this->returnValue( $returnAccounts ) );
        
        // now we have our account mock we can set it in the return array from user->getAssoc
        $returnAssoc = array(
            'type' => 'hasMany',
            'table' => $accountTableMock,
            'foreign_key' => 'user_id'
        );
        
        // mock user table. This table will expect a call to getAssoc which will return everything we 
        // need to get association rows
        $userTableMock = $this->userTableMock;
        $userTableMock->expects( $this->once() )
            ->method('getAssoc')
            ->with('accounts')
            ->will( $this->returnValue( $returnAssoc ) );
        
        // create a row with values (user_id=1) and a mock table
        $user = new Row($userTableMock, $userValues);
        
        // now, try to access the assoc via the Row object. We should pass the mock object assertions
        // such as $this->once, method('select') etc and have a return value
        $accounts = $user->accounts;
        
        // check the count
        $this->assertEquals( count($returnAccounts), count($accounts) );
    }
    
    public function testBelongsToGetterReturnsArrayOfRowsWhenAssociationsAreFound()
    {
        // gonna create a user row which 'hasMany' accounts
        
        $accountValues = array(
            'id' => 99,
            'user_id' => 10,
        );
        
        // same process, this time for belongsTo
        
        
        // account - mock query/ return values. this will be found in the array returned from user->getAssoc
        // the account object will query it's table by the foreign for users (user_id) with the id of this row
        // it will be set to return an array of array row values
        $where = 'user_id = ?'; // a has many for users will compare the user_id of the table with this row id
        $whereValues = array( $accountValues['user_id'] ); // row.id
        $options = array(
            'limitMax' => 1, // we only want one
        );
        $returnUsers = array(
            array( // will return two accounts
                'id' => 10,
                'username' => 'banksie',
            )
        );
        $userTableMock = $this->userTableMock;
        $userTableMock->expects( $this->once() )
            ->method('select')
            ->with( $where, $whereValues, $options )
            ->will( $this->returnValue( $returnUsers ) );
        
        // now we have our account mock we can set it in the return array from user->getAssoc
        $returnAssoc = array(
            'type' => 'belongsTo',
            'table' => $userTableMock,
            'foreign_key' => 'user_id',
        );
        
        // mock user table. This table will expect a call to getAssoc which will return everything we 
        // need to get association rows
        $accountTableMock = $this->accountTableMock;
        $accountTableMock->expects( $this->once() )
            ->method('getAssoc')
            ->with('user')
            ->will( $this->returnValue( $returnAssoc ) );
        
        // create a row with values (user_id=1) and a mock table
        $account = new Row($accountTableMock, $accountValues);
        
        // now, try to access the assoc via the Row object. We should pass the mock object assertions
        // such as $this->once, method('select') etc and have a return value
        $user = $account->user;
        
        // check the count
        $this->assertTrue( $user instanceof Row );
    }
    
    public function testBelongsToGetterReturnsNullWhenAssociationIsNotFound()
    {
        // gonna create a user row which 'hasMany' accounts
        
        $accountValues = array(
            'id' => 99,
            'user_id' => 10,
        );
        
        // same process, this time for belongsTo
        
        
        // account - mock query/ return values. this will be found in the array returned from user->getAssoc
        // the account object will query it's table by the foreign for users (user_id) with the id of this row
        // it will be set to return an array of array row values
        $where = 'user_id = ?'; // a has many for users will compare the user_id of the table with this row id
        $whereValues = array( $accountValues['user_id'] ); // row.id
        $options = array(
            'limitMax' => 1, // we only want one
        );
        $returnUsers = array(); // empty array, nothing found!!!
        $userTableMock = $this->userTableMock;
        $userTableMock->expects( $this->once() )
            ->method('select')
            ->with( $where, $whereValues, $options )
            ->will( $this->returnValue( $returnUsers ) );
        
        // now we have our account mock we can set it in the return array from user->getAssoc
        $returnAssoc = array(
            'type' => 'belongsTo',
            'table' => $userTableMock,
            'foreign_key' => 'user_id',
        );
        
        // mock user table. This table will expect a call to getAssoc which will return everything we 
        // need to get association rows
        $accountTableMock = $this->accountTableMock;
        $accountTableMock->expects( $this->once() )
            ->method('getAssoc')
            ->with('user')
            ->will( $this->returnValue( $returnAssoc ) );
        
        // create a row with values (user_id=1) and a mock table
        $account = new Row($accountTableMock, $accountValues);
        
        // now, try to access the assoc via the Row object. We should pass the mock object assertions
        // such as $this->once, method('select') etc and have a return value
        $user = $account->user;
        
        // check the count
        $this->assertTrue( is_null($user) );
    }
    
    public function testGetterReturnsNullWhenAssociationNorPropertySet() 
    {
        // gonna create a user row which 'hasMany' accounts
        
        $accountValues = array(
            'id' => 99,
            'user_id' => 10,
        );
        
        // this is what getAssoc will return, nothing
        $returnAssoc = null;
        
        // mock user table. This table will expect a call to getAssoc which will return everything we 
        // need to get association rows
        $accountTableMock = $this->accountTableMock;
        $accountTableMock->expects( $this->once() )
            ->method('getAssoc')
            ->with('idontexist')
            ->will( $this->returnValue( $returnAssoc ) );
        
        // create a row with values (user_id=1) and a mock table
        $account = new Row($accountTableMock, $accountValues);
        
        // now, try to access the assoc via the Row object. We should pass the mock object assertions
        // such as $this->once, method('select') etc and have a return value
        $user = $account->idontexist;
        
        // check the count
        $this->assertTrue( is_null($user) );
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