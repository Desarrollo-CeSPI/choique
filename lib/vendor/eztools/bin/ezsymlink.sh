#!/bin/bash

source `dirname $0`/../lib/ez.sh

USAGE="Usage: $0 [ENV]"

if [ $# -ne 1 ]
then
  echo $USAGE
  exit 1
fi

ENV=( $1 "default" )

echo "Checking for $ENV"

check_file_existance "config/databases.yml-$ENV"
check_file_existance "config/propel.ini-$ENV"

cd config
ln -nfs databases.yml-$ENV databases.yml
ln -nfs propel.ini-$ENV propel.ini
cd ..

run_hooks `pwd`/data/ezhooks/ezsymlinks

message_success "Symlinks done!"
