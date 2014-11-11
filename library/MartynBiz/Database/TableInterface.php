<?php

namespace MartynBiz\Database;

/**
* 
*/
interface TableInterface
{
    
    public static function getInstance(AdapterInterface $adapter);
}