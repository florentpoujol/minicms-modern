<?php
// needed to run when the

$testConfig = json_decode(file_get_contents(__dir__ . "/testsConfig.json"), true);

$options = [
    \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
    \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
    \PDO::ATTR_EMULATE_PREPARES   => false,
];

$dsn = "mysql:host=$testConfig[db_host];dbname=$testConfig[db_name];charset=utf8";
$pdo = new \PDO($dsn, $testConfig["db_user"], $testConfig["db_password"], $options);

$pdo->exec("drop database if exists $testConfig[db_name]");
$pdo->exec("create database $testConfig[db_name]");
$pdo->exec("use $testConfig[db_name]");

$sql = file_get_contents(__dir__ . "/../database_structure.sql");
$pdo->exec($sql);
echo "OK\n";
