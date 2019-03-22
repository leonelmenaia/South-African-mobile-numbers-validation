<?php

$db = require __DIR__ . '/db.php';
// test database! Important not to run tests on production or development databases
//$db['dsn'] = 'mysql:host=' . TEST_MYSQL_HOST . ';dbname=' . TEST_MYSQL_DATABASE;

return $db;
