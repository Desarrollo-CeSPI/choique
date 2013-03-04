#!/bin/bash

source `dirname $0`/../lib/ez.sh

USAGE="Usage: $0 product-name version [ENV]"

if [ $# -lt 2 -o $# -gt 3 ]
then
  echo $USAGE
  exit 1
fi

WORKING_DIR=`pwd`

VERSION=$2
PRODUCT_NAME=$1
TAG_NAME="$PRODUCT_NAME""_RELEASE_$VERSION"
ENV=( $3 "default" )

# check "config/databases.yml-$ENV" and "config/propel.ini-$ENV" existance
check_file_existance config/databases.yml-$ENV
check_file_existance config/propel.ini-$ENV
check_file_existance README.md README
check_file_existance INSTALL.md INSTALL
check_file_existance UPGRADE.md UPGRADE

# export the tag
echo "Exporting $TAG_NAME"

if [ -d /tmp/$TAG_NAME ]
then
  rm -rf /tmp/$TAG_NAME
fi
svn export . /tmp/$TAG_NAME

cd /tmp/$TAG_NAME

# delete configuration files (except for this environment)
cd config
if [ -f databases.yml ]
then
  rm databases.yml
fi
if [ -f propel.ini ]
then
  rm propel.ini
fi

mv databases.yml-$ENV databases.yml
mv propel.ini-$ENV propel.ini
rm databases.yml-* propel.ini-*

cd ..

find *web* -name '*_dev.php' -exec rm -f {} \;


# change the version in the tar.gz (not in the release)
echo ${VERSION//-/.} > VERSION

run_hooks `pwd`/data/ezhooks/eztar


# create .tar.gz
echo "Creating $TAG_NAME.tar.gz"

cd /tmp/
tar czf $TAG_NAME.tar.gz $TAG_NAME

cd $WORKING_DIR
mv /tmp/$TAG_NAME.tar.gz .
SHASUM=`shasum $TAG_NAME.tar.gz`

echo "$TAG_NAME.tar.gz created. SHASUM = $SHASUM"

message_success "Tar done!"
