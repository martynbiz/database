#Database

Light weight ORM classes.

##Installation

Install with composer: 

```
"martynbiz/database": "dev-master"
```

##Usage

Models/User.php

```php
class User extends MartynBiz\Database\Table
{
    protected $table = 'users';
}
```

Script

```php
$adapter = new MartynBiz\Database\Adapter(array(
    'dsn' => '...',
    'user' => '...',
    'password' => '...',
));

$usersTable = new Users($adapter);

// return Row object
$user = $usersTable->find($id);

// select many rows by query (with options)
$where = 'age > 34';
$options = array(
    'limitStart' => 10,
    'limitMax' => 25,
);
$users = $usersTable->select($where, $options);

// insert new row
$values = array(
    'name' => 'Hugo',
    'age' => 54,
);
$users->create($values);

// update rows
$usersTable->update($values, $where, $options);

// delete rows
$usersTable->delete($where, $options);

```
###Unit testing

```php
$adapterMock = $this->getMockBuilder('MartynBiz\Database\Adapter')
    ->disableOriginalConstructor()
    ->getMock();

$usersTable = new Users($adapterMock);
```

###Relationships (in development)

```php
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
```