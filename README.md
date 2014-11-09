#Database

Light weight ORM classes.

##Installation

Install with composer: 

```
"martynbiz/database": "dev-master"
```

##Usage

User.php

```
class User extends MartynBiz\Database\Table
{
    protected $table = 'users';
}
```

###Adapter class

```
$adapter = new MartynBiz\Database\Adapter(array(
    'dsn' => '...',
    'user' => '...',
    'password' => '...',
));

// select many rows by query (with options)
$where = 'age > 34';
$options = array(
    'limitStart' => 10
    'limitMax' => 25,
    'includeDeleted' => true, // fetch deleted rows too (under development)
);
$users = $adapter->select('users', $where, $options);

// insert new row
$values = array(
    'name' => 'Hugo',
    'age' => 54,
);
$adapter->create('users', $values);

// update rows
$adapter->update('users', $values, $where, $options);

// delete rows
$options = array(
    'cascade' => true, // delete hasMany rows (under development)
    'softDelete' => true, // set deteled_at column (under development)
);
$adapter->delete('users', $where, $options);

```

###Table class

```
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

###Relationships (in development)

```
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

##Todo

Build relationships in Row
Fillable