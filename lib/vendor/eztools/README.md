# eztools

Esta librería provee una serie de comandos útiles para el desarrollo de y
puesta en produccion de aplicaciones symfony.

## Herramientas

### ezsymlinks.sh

Crea los symlinks para el entorno indicado. Por ejemplo, dado los siguientes
archivos de configuración:
  * config/databases.yml-default
  * config/databases.yml-pm
  * config/propel.ini-default
  * config/propel.ini-pm

Si queremos que el entorno por defecto sea pm, ejecutando:

    $ ./lib/vendor/eztools/bin/ezsymlinks.sh pm

se crearán los siguientes symlinks:
  * config/databases.yml -> config/databases.yml-pm
  * config/propel.ini -> config/propel.ini-pm

### eztar.sh

Crea un tar.gz para la versión y el entorno indicado.
*No realiza ninguna modificación en el repositorio subversion.*
Ejemplo:

    $ ./lib/vendor/eztools/bin/eztar.sh 0-1-1 pm

generará el siguiente tar.gz: __RELEASE_0-1-1.tar.gz__

Este tag está listo para instalarse en testing y/o producción.

### eztag.sh

Crea un tar.gz para la versión y el entorno indicado, además crea un tag en el
respositorio subversion, con el mismo nombre que el tag (excepto por la
extensión).
Ejemplo:

    $ ./lib/vendor/eztools/bin/eztar.sh 0-1-1 pm

generará el siguiente tar.gz: __RELEASE_0-1-1.tar.gz__

y creará un tag en el respositorio subversion, llamado: __RELEASE_0-1-1__

