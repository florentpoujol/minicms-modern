#!/usr/bin/env bash

# dump structure only
mysqldump minicms_mvc -d -uroot -proot > database_structure.sql

# clean test DB (drop if exists then create)
mysql -uroot -proot < create_test_db.sql;

# add structure to test db
mysql test_minicms_mvc -uroot -proot < database_structure.sql;
