#!/bin/bash

source `dirname $0`/../lib/ez.sh

echo -n "Database host [localhost]: "
read DB_HOST
DB_HOST=${DB_HOST:-"localhost"}

echo -n "Database name: "
read DB_NAME

echo -n "Database username [root]: "
read DB_USER
DB_USER=${DB_USER:-"root"}

echo -n "Database password [null]: "
read DB_PASS
DB_PASS=${DB_PASS:-""}

if [ -z $DB_NAME ]
then
  message_warning "You must provide the database name"
  exit 1
fi

message_warning "**************************************************************************************"
message_warning "* WARNING: This step cannot be reverted. Press enter to continue or Ctrl-C to cancel *"
message_warning "**************************************************************************************"
read

# check "config/databases.yml-$ENV" and "config/propel.ini-$ENV" existance
check_file_existance "config/databases.yml"
check_file_existance "config/propel.ini"

REPLACE_HOST="sed s/\#db_host\#/$DB_HOST/g"
REPLACE_NAME="sed s/\#db_name\#/$DB_NAME/g"
REPLACE_USER="sed s/\#db_user\#/$DB_USER/g"
REPLACE_PASS="sed s/\#db_pass\#/$DB_PASS/g"

cat config/databases.yml | $REPLACE_HOST | $REPLACE_NAME | $REPLACE_USER | $REPLACE_PASS > /tmp/databases.yml
mv /tmp/databases.yml config/databases.yml
cat config/propel.ini | $REPLACE_HOST | $REPLACE_NAME | $REPLACE_USER | $REPLACE_PASS > /tmp/propel.ini
mv /tmp/propel.ini config/propel.ini

run_hooks `pwd`/data/ezhooks/ezconfigure

message_success "Configuration done!"
