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

use MartynBiz\Database\AbstractTableGateway;

// TableGateway needs to be extended, so we'll create AccountsTable for the sake of testing
class Account extends AbstractTableGateway
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

class User extends AbstractTableGateway
{
    protected $tableName = 'users';
}

class Transaction extends AbstractTableGateway
{
    protected $tableName = 'transactions';
}