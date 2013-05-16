#!/bin/bash

#
# Choique CMS - A Content Management System.
# Copyright (C) 2012 CeSPI - UNLP <desarrollo@cespi.unlp.edu.ar>
#

#
# Easy installer for Choique CMS.
#
# Usage:
#   $ ./installer.sh
#

INSTALLER_URL='https://raw.github.com/Desarrollo-CeSPI/choique/master/installer'

PHP=`which php`
CURL=`which curl`
TMP_PATH='/tmp/choique-installer'

if [ ! -x $PHP ]; then
  echo PHP debe estar instalado y en el PATH.
  exit 1
fi

echo 'Descargando e iniciando instalador de Choique CMS...'
echo

if [ -x $CURL ]; then
  $CURL -sS $INSTALLER_URL > $TMP_PATH && $PHP $TMP_PATH
  rm -f $TMP_PATH
else
  $PHP -r "eval('?>'.file_get_contents('$INSTALLER_URL'));"
fi