<?php

require_once 'vendor/autoload.php';

$dsn = 'mysql:host=ec2-54-243-124-240.compute-1.amazonaws.com;dbname=dccpro45ju5lof;charset=utf8';
$usr = 'mfpyjwdlehhhvo';
$pwd = '008e494e873a65a1b1c70d46ec85074032c08ae7eaa7a53e78ae4c2bb195caa8';

$pdo = new \Slim\PDO\Database($dsn, $usr, $pwd);

// SELECT * FROM users WHERE id = ?
$selectStatement = $pdo->select()
                       ->from('users');

$stmt = $selectStatement->execute();
$data = $stmt->fetch();

// INSERT INTO users ( id , usr , pwd ) VALUES ( ? , ? , ? )
$insertStatement = $pdo->insert(array('id', 'usr', 'pwd'))
                       ->into('users')
                       ->values(array(1234, 'your_username', 'your_password'));

$insertId = $insertStatement->execute(false);

// UPDATE users SET pwd = ? WHERE id = ?
$updateStatement = $pdo->update(array('pwd' => 'your_new_password'))
                       ->table('users')
                       ->where('id', '=', 1234);

$affectedRows = $updateStatement->execute();

// DELETE FROM users WHERE id = ?
$deleteStatement = $pdo->delete()
                       ->from('users')
                       ->where('id', '=', 1234);

$affectedRows = $deleteStatement->execute();
