<?php
/**
 * This makes our life easier when dealing with paths. Everything is relative
 * to the application root now.
 */
chdir(dirname('../'));

// require composer autoloader for loading classes
require 'vendor/autoload.php';

// seems PDO cannot be mocked, this is the solution
class PDOMock extends \PDO
{
    public function __construct() {}
}

// seems PDOStatement cannot be mocked, this is the solution
class PDOStatementMock extends \PDOStatement
{
    public function __construct() {}
}

use MartynBiz\Database\Table;

// issues trying to unit test these assocs

// I want to unit test assocs but having difficulty swapping out those classes

// define 'class' as string - objects gets instantiated inside the method, cannot mock
//                from container - container becomes tightly coupled
//                from a singleton - static methods can't be mocked
//                new instance - non singular
// define instead 'table' - no reference to the Table to generate Row objects

// best solution I an think of is - allow setting of belongsTo and hasMany during run-time


// TableGateway needs to be extended, so we'll create AccountsTable for the sake of testing
class Account extends Table
{
    protected $tableName = 'accounts';
    
    protected $belongsTo = array(
        'user' => array(
            'table' => 'User',
            'foreign_key' => 'user_id'
        ),
    );
    
    protected $hasMany = array(
        'transactions' => array(
            'table' => 'Transaction',
            'foreign_key' => 'account_id'
        ),
    );
}

// two more classes to simulate related tables (belongsTo, hasMany)

class User extends Table
{
    protected $tableName = 'users';
    
    protected $hasMany = array(
        'accounts' => array(
            'table' => 'Account',
            'foreign_key' => 'account_id',
        )
    );
}

class Transaction extends Table
{
    protected $tableName = 'transactions';
    
    protected $belongsTo = array(
        'account' => array(
            'table' => 'Account',
            'foreign_key' => 'account_id',
        )
    );
}


