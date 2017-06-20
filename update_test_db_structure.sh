
mysql -u root -proot -h localhost < "
create database if not exists test_minicms_mvc;
drop database test_minicms_mvc;
create database test_minicms_mvc;";

mysql test_minicms_mvc -u root -proot -h localhost < database_structure.sql;
