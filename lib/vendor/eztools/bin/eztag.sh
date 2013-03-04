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
check_file_existance "config/databases.yml-$ENV"
check_file_existance "config/propel.ini-$ENV"
check_file_existance "README.md README"
check_file_existance "INSTALL.md INSTALL"
check_file_existance "UPGRADE.md UPGRADE"

# guess repository url
REPOSITORY_ROOT=`svn info 2>/dev/null | sed -ne 's#^Repository Root: ##p'`
if [ -z $REPOSITORY_ROOT ]
then
  REPOSITORY_ROOT=`svn info 2>/dev/null | sed -ne 's#^Raíz del repositorio: ##p'`
fi

TRUNK_REPOSITORY="$REPOSITORY_ROOT/trunk"
TAG_REPOSITORY="$REPOSITORY_ROOT/tags/$TAG_NAME"

echo "Tagging $TAG_NAME for $ENV"

# change the version
echo ${VERSION//-/.} > VERSION
svn ci -m "changed the version (eztag)"

svn cp . $TAG_REPOSITORY -m "Tagging $TAG_NAME. (eztag)"

if [ $? -ne 0 ]
then
  echo "Error while copying from $TRUNK_REPOSITORY to $TAG_REPOSITORY"
  exit 3
fi

# switch to tag repository
svn switch $TAG_REPOSITORY

if [ $? -ne 0 ]
then
  echo "Error while switching to $TAG_REPOSITORY"
  exit 4
fi

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

svn mv databases.yml-$ENV databases.yml
svn mv propel.ini-$ENV propel.ini
svn del databases.yml-* propel.ini-*

# delete svn:ignore property from config directory
svn propdel svn:ignore .

cd $WORKING_DIR

run_hooks `pwd`/data/ezhooks/eztag

# update cofiguration files
svn ci -m "Updated configuration files for $TAG_NAME. (eztag)"

# export the tag
echo "Exporting $TAG_NAME"

if [ -d /tmp/$TAG_NAME ]
then
  rm -rf /tmp/$TAG_NAME
fi
svn export . /tmp/$TAG_NAME
cp VERSION /tmp/$TAG_NAME

cd /tmp/$TAG_NAME
find *web* -name '*_dev.php' -exec rm -f {} \;

# create .tar.gz
echo "Creating $TAG_NAME.tar.gz"

cd /tmp/
tar czf $TAG_NAME.tar.gz $TAG_NAME 

cd $WORKING_DIR
mv /tmp/$TAG_NAME.tar.gz .
SHASUM=`shasum $TAG_NAME.tar.gz`

echo "$TAG_NAME.tar.gz created. SHASUM = $SHASUM"

# switch back to trunk repository
svn switch $TRUNK_REPOSITORY

if [ $? -ne 0 ]
then
  echo "Error while switching to $TRUNK_REPOSITORY"
  exit 5
fi

message_success "Tag done!"
