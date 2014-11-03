#Database

##Installation

Install with composer: 

```
"martynbiz/database": "dev-master"
```

##Todo

RowGateway class - when table gateway does a select, it will generate an array of RowGateway classes. It will also 

class Accounts extends TableGateway {
    
    
}

class RowGateway {
    public function __construct(TableGateway $table) {
        $this->table = $table;
    }
    
    public function __get($name) {
        // check in relation exists in TGw
        
        // e.g. get back array('type'=>'hasMany', 'table'=>'trans...', 'fkey'=>'account_id')
        //   get rows where Transaction.select(account_id=id)   
        
        // e.g. get back array('type'=>'belongsTo', 'table'=>'user...', 'fkey'=>'user_id')
        //   get rows where User->find(user_id)
    }
}