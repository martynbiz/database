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

// TableGateway needs to be extended, so we'll create AccountsTable for the sake of testing
class Account extends Table
{
    protected $belongsTo = array(
        'user' => array(
            'class' => 'User',
            'fkey' => 'user_id'
        ),
    );
    
    protected $hasMany = array(
        'transactions' => array(
            'class' => 'Transaction',
            'fkey' => 'account_id'
        ),
    );
    
    protected $tableName = 'accounts';
}

// two more classes to simulate related tables (belongsTo, hasMany)

class User extends Table
{
    protected $tableName = 'users';
    
    protected $hasMany = array(
        'transactions' => array(
            'table' => 'transactions',
            'foreign_key' => 'user_id',
        )
    );
}

class Transaction extends Table
{
    protected $tableName = 'transactions';
    
    protected $belongsTo = array(
        'user' => array(
            'table' => 'users',
            'foreign_key' => 'user_id',
        )
    );
}