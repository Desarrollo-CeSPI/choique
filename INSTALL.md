# Requerimientos

* PHP >= 5.2 y <= 5.3
* pecl Imagick
* pecl fileinfo (Ya está instalada en versiones actuales de PHP)

# Instalación

- Descargar un paquete de Choique [desde GitHub](https://github.com/Desarrollo-CeSPI/choique/tags)
  o clonar el repositorio utilizando `git`. **Se recomienda siempre descargar la última versión disponible**.
- Descomprimir el paquete y copiar en un directorio todo el proyecto.
- Renombrar:
  - `config/databases.yml-default` a `config/databases.yml`,
  - `config/propel.ini-default` a `config/propel.ini`,
  - `config/app.yml-default` a `config/app.yml`.
- Los únicos directorios visibles por el web server deberán ser:
  - `web-frontend`
  - `web-backend`

**Se recomienda que `web-frontend` sea público mientras que `web-backend` sea un VirtualHost
aparte con más restricciones de seguridad. Ver el apartado seguridad al final de esta guía.**

# Configuración de la Base de datos

## MySQL

* Configurar `config/databases.yml` con los datos de acceso a la base de datos
* Configurar `config/propel.ini` con los datos de acceso a la base de datos

**Nota:** Puede usar el asistente `lib/vendor/eztools/bin/ezconfigure.sh` para
luego seguir los pasos en pantalla, ejecutando:

```bash
$ ./lib/vendor/eztools/bin/ezconfigure.sh
```

## POSTGRES

Los archivos a configurar, así como el comando de configuración antes
mencionados solo funcionan con MySQL. Si se desea configurar **Choique** con el
motor de base de datos **Postgres** entonces deberá editar los archivos manualmente
como se indica a continuación:

```yaml
# config/databases.yml
all:
  propel:
    class:      sfPropelDatabase
    param:
      phptype:  pgsql
      hostspec: #HOST#
      database: #DB_NAME#
      username: #DB_USER#
      password: #DB_PASS#
      port:     #DB_PORT#
```

```ini
# config/propel.ini
propel.database           = pgsql
propel.database.createUrl = pgsql://#DB_USER#:#DB_PASS#@#DB_HOST#:#DB_PORT#/
propel.database.url       = pgsql://DB_USER#:#DB_PASS#@#DB_HOST#:#DB_PORT#/#DB_NAME#
```

# Inicialización de la aplicación

## Configuración de Choique

- Modificar el archivo `config/app.yml`. Para una explicación más detallada referirse
  al archivo [README.md](https://github.com/Desarrollo-CeSPI/choique/blob/master/README.md)
- Editar el nombre de la sesión del backend en `apps/backend/config/factories.yml`:

```yaml
all:
  storage:
    class: sfSessionStorage
    param:
      session_name: UN_NOMBRE   # <<< Cambiar este valor
```

## Correr los siguientes comandos

### Configuración de PHP

* Antes de iniciar la instalación asegurese que los valores de configuración de PHP
  en `/etc/php5/cli/php.ini` y `/etc/php5/apache2/php.ini` (o equivalentes según su
  entorno) sean adecuados. Por ejemplo: `memory_limit = 256M`.

### Primera inicialización

* Si es la primera vez que se instala correr antes que cualquier otro comando:

```bash
$ php symfony choique-flavors-initialize
```

### Importante

**Si desea inicializar la base de datos**, cree una base de datos vacía, configure
acorde a la base de datos el archivo `config/databases.yml` (como se explica
en un apartado anterior) y luego ejecute el siguiente comando:

```bash
$ php symfony propel-build-all-load backend
```

En caso de tratarse de una base de datos ya existente, tenga en cuenta que corriendo
el comando anterior la misma **será destruida**, por lo que es recomendable que haga
un dump (volcado de datos) **antes de ejecutar el comando** en caso de querer conservarlos.

**Si no desea inicializar la base de datos**, únicamente necesitará ejecutar, luego de
configurar el archivo `config/propel.ini`:

```bash
$ php symfony propel-build-model
```

### Estilos visuales

* Es requerido disponer de al menos un estilo visual instalado para que el sitio funcione.
* Para listar los estilos visuales utilizar `php symfony choique-flavors-list`.

### Permisos

* Para establecer correctamente los permisos sobre los directorios, ejecute el siguiente comando:

```bash
$ php symfony choique-fix-perms && php symfony clear-cache
```

### Indexación para la búsqueda

* Para inicializar los índice de búsqueda del sitio, ejecutar:

```bash
$ php symfony choique-reindex
```

### Problemas?

Si al correr alguno de los comandos anteriores ocurre el siguiente error:

```
[Zend_Search_Lucene_Exception]
  Index doesn't exists in the specified directory.
```

ejecute el siguiente comando, que creará los índices faltantes:

```bash
$ php symfony lucene-rebuild backend
```

# Configuración de Apache

**Choique** es un proyecto que consta de dos aplicaciones:
  * **Frontend**: Es el portal que ven los usuarios finales.
  * **Backend**: Es el administrador de contenidos. Esta aplicación **debe funcionar en un entorno seguro**.

Las aplicaciones symfony requieren que esté habilitado el modulo `rewrite` de Apache.

Para configurar las reescrituras del `mod-rewrite`, puede utilizarse un `.htaccess`,
ya provisto en este proyecto dentro de los directorios públicos (`web-frontend` y `web-backend`).
Sin embargo, se aconseja utilizar una configuración que no admita redefinir la
configuracion de apache con .htaccess, por ejemplo con el directorio web.

Un ejemplo de VirtualHost aconsejable sería el siguiente:

```htaccess
  <VirtualHost *:80>
    ServerName www.xxx.com

    DocumentRoot /xxx/choique/web

    <IfModule mod_php5.c>
      php_admin_value open_basedir /xxx/choique/:/tmp/
    </ifModule>

    <Directory /xxx/choique>
      AllowOverride None
    </Directory>

    <Directory /xxx/choique/web >
      Options FollowSymLinks
      AllowOverride None
      Order allow,deny
      allow from all

      RewriteEngine On
      RewriteRule ^$ index.html [QSA]
      RewriteRule ^([^.]+)$ $1.html [QSA]
      RewriteCond %{REQUEST_FILENAME} !-f
      RewriteRule ^(.*)$ index.php [QSA,L]
      <Files ~ "_dev\.php">
            deny from all
      </Files>
    </Directory>

    <LocationMatch /uploads/\.*>
             php_admin_flag engine off
    </LocationMatch>

    ErrorLog ${APACHE_LOG_DIR}/xxx-error.log
    LogLevel warn
    CustomLog ${APACHE_LOG_DIR}/xxx-access.log combined
</VirtualHost>
```

Y de manera similar con el directorio `web-backend`.

## Algunos consejos de seguridad

* Asegurar la configuracion provista por `/etc/apache2/con.d/security`.
* Utilizar SSL para el Backend.
* Utilizar un VirtualHost separado para `web-backend`.
* No permitir el uso de `.htaccess` ni en el Frontend ni en Backend.
* Restringir, de ser posible, el acceso al Backend a un conjunto de IPs/Redes.
* De ser posible cambiar el puerto en el que atiende el Backend por uno no estándar.
* Deshabilitar en todo momento la ejecución de scripts bajo el directorio `uploads`.

# Primer acceso

El acceso al backend luego de una instalación nueva será:

* **Usuario:** `admin`
* **Contraseña:** `admin`
