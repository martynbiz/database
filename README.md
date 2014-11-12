#Database

Light weight ORM classes. It's designed to be well suited for unit testing where the database adapter can be mocked easily. Also provides just enough to do common tasks such as find/select/create/update/delete, and associations between tables (e.g. hasMany/ belongsTo). Can be run stand along or within a framework. 

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

$usersTable = Users::getInstance($adapter);

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

###Associations

```php
class User extends Table
{
    protected $tableName = 'users';
    
    protected $hasMany = array(
        'transactions' => array(
            'table' => 'App\Model\Transaction', // class name for the hasMany rows
            'foreign_key' => 'user_id',
        )
    );
}

class Transaction extends Table
{
    protected $tableName = 'transactions';
    
    protected $belongsTo = array(
        'user' => array(
            'table' => 'App\Model\User', // class name for the belongsTo row
            'foreign_key' => 'user_id',
        )
    );
}
```

##Unit testing

###Mocking table adapter

```php
$adapterMock = $this->getMockBuilder('MartynBiz\Database\Adapter')
    ->disableOriginalConstructor()
    ->getMock();

$usersTable = new Users($adapterMock);
```

###Mocking PDO adapter

If it was neccessary to extend the adapter class, the PDO object which is usually generated internally can be injected instead (upon which the database credentials will be ingored). This allows the extended class to be unit testing with a mock PDO object.

```php
class MyCustomAdapter extands Adapter {
    
}

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
$adapter = new MyCustomAdapter(null, $PDOMock);
```
