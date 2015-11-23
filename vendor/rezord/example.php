<?php

require __DIR__  . '\DB.php';

require __DIR__ . '\DBQuery.php';


$db = DB::connect('mysql:dbname=z_short_link;host=localhost','root','');


$db->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$query = new DBQuery($db);


print_r($query->queryAll('SELECT * FROM users'));

/**
 *  Array
    (
        [0] => Array
        (
            [id] => 1
            [email] => zotov_mv@groupbwt.com
            [password] => $2y$10$0MTsYsnMVJ7iq6bTvIpUGOK8WVN1uErud
        )

        [1] => Array
        (
            [id] => 2
            [email] => admin@groupbwt.com
            [password] => $2y$10$0MTsYsnMVJ7iq6bTvIpUGOK8WVN1uErud
        )
    )
 */


print_r($query->queryRow('SELECT * FROM users limit 1'));

/**
 * Array
    (
        [id] => 1
        [email] => zotov_mv@groupbwt.com
        [password] => $2y$10$0MTsYsnMVJ7iq6bTvIpUGOK8WVN1uErud
    )
 */



print_r($query->queryColumn('SELECT email FROM users'));
/**
    Array
    (
        [0] => zotov_mv+24787@groupbwt.com
        [1] => zotov_mv+47748@groupbwt.com
        [2] => zotov_mv@groupbwt.com
    )
 */


echo $query->queryScalar('SELECT email FROM users');

/**
 * admin@groupbwt.com
 */

$db->reconnect();


$data = [
    'email' => 'zotov_mv+' . rand(1,99999) . '@groupbwt.com',
    'password' => password_hash('qwerty' . time() ,PASSWORD_DEFAULT)
];

$rowCount = $query->execute("INSERT INTO `users` (`email`, `password`) VALUES (:email, :password)", $data);

echo "\ncount inserts row -> " . $rowCount . "\n";


$lastId = $db->getLastInsertID();

print_r($query->queryRow('SELECT * FROM users where id = :id', ['id' => $lastId]));

/**
  Array
    (
        [id] => 20
        [email] => zotov_mv+70773@groupbwt.com
        [password] => $2y$10$m7ai3oLBxbF4akWMLXEDteF.0zbv6deN0
    )
 */


$updateData = [
    'password' => password_hash('qwerty' . time() ,PASSWORD_DEFAULT),
    'id' => $lastId
];

$rowCountUpdate = $query->execute("Update `users` SET password = :password where id = :id", $updateData);

echo "\ncount update row -> " . $rowCountUpdate . "\n";


$rowCountDelete = $query->execute("DELETE FROM `users` where id = :id", ['id' => $lastId]);

echo "\ncount delete row -> " . $rowCountDelete . "\n";


echo "\nlast query execution time -> ".$query->getLastQueryTime() . "\n";