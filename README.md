# Qué es Choique

**Choique** es un administrador de contenidos desarrollado a medida para sitios de 
la [Universidad Nacional de La Plata UNLP](http://www.unlp.edu.ar/). 

# Configuracion

**Choique** fue desarrollado en symfony 1.0 y por tanto respeta la jerarquía de carpetas que 
provee el framework. Sin embargo, se ha modificado el comportamiento original del framework
eliminando la carpta web, y creando dos carpetas diferentes para cada aplicacion del CMS:
  * `web-frontend`: carpeta web para la aplicacion del frontend.
  * `web-backend`: carpeta web para la aplicacion del backend.

Leer el documento `INSTALL.md` para realizar una instalación con los valores por defecto


## Valores a editar en config/app.yml

* **Frontend URL**: indicar la URL del frontend de la instancia de choique asi puede
  usar el PREVIEW desde el backend. Editar `config/app.yml` con los siguientes valores:

```yaml
all:
  choique:
    url:
      fontend: http://choique.mi.dominio.com.ar/
```

* **Backend URL**: indicar la URL del backend de la instancia de choique asi funciona
  el editor de artículos en forma correcta. Editar `config/app.yml` con los 
  siguientes valores:

```yaml
all:
  choique:
    url:
      backend: http://dominio.backend.choique.com.ar/
```

* **Mime types**: los mime soportados para la gestion del backend: sean multimedias, 
  documentos y archivos de texto se editan desde `config/app.yml`. La 
  configuracion es a partir de:

```yaml
all:
  valid_mime_types:
    multimedia:
    text:
    document:
    link:
    editor:
      images
      css:
```

**Notar que el resultado de las funciones de mime type dependen de si el sistema 
tiene instalado `fileinfo`. En este caso corroborar como interpreta el comando 
file los mime de los archivos verificando que coinciden con los indicados en 
`app.yml`.**

* **Indexación de contenidos de documentos:** **Choique** indexa documentos cuyo 
  contenido pueda extraerse segun los binarios indicados en `config/app.yml`. 
  La configuracion es a partir de:

```yaml
all:
  extractors:
```

  En cada caso está disponible un parametro: `%s`, que se corresponde con el archivo a 
  extraer.

* **Aviso que se está trabajando en versión de pruebas**: **Choique** puede mostrar
  un cartel que indique que la versión sobre la cual se está trabajando es de pruebas.
  Esto es particularmente útil para evitar confusiones si se utilizan dos versiones en
  paralelo, como una de producción y una de testing.
  Para ello es necesario cambiar las siguientes claves en el archivo `config/app.yml`.

```yaml
all:
  choique:
    testing: true
    testing_text: Sitio de prueba
```

## Sobre los estilos visuales

**Choique** permite editar los estilos visuales editando estilos CSS, y cambiando 
valores de algunos archivos de configuración.
Los cambios grandes en estilos pueden hacerse descargando el *flavor* por defecto
y luego subiendo una versión modificada de los estilos.

El editor de estilos permite cambiar en línea los estilos visuales e ir 
aplicando estos estilos en forma directa sobre el portal. 

Es importante conocer qué parámetros externos a los CSS pueden cambiarse desde
el editor de estilos:

* **Tags meta, JavaScripts a incluir**: Esta configuracion se regula por el archivo
  `config/view.yml`. El archivo se escribe en formato YAML, que requiere que la
  anidación sea con dos espacios, siendo este hecho de importantisimo valor.

* **El título de la página**: Esto se realiza desde el archivo `templates/layout.php`.
  Aquí se provee el tag `<title>`, que se define por defecto de la siguiente forma:

```php
<title>
  <?php echo $sf_context->getResponse()->getTitle() ?>
  <?php isset($section) and print ' - '.$section->getTitle() ?>
  <?php isset($article) and print ' - '.$article->getTitle() ?>
</title>
```

  **Notar que en este caso, se utiliza el nombre definido en `config/view.yml` 
  y luego si se seteo una sección, entonces se continúa con el nombre de la
  misma, y si se seteo un artículo, entonces se continúa con el nombre del
  artículo.**

* **Estilos visuales por sección**: Cada una de las secciones que se dibujan cargan
  en `layout.php` (ver `template/layout.php` del flavor por defecto) una hoja de 
  estilos que es redibujada para cada una de las secciones. En ella se puede 
  personalizar muchos de los estilos para esa seccion específicamente. Es decir
  si por ejemplo el titulo es de color azul en todo choique, para una seccion 
  determinada podríamos hacer que cambia a rojo. La configuracion para que esto
  sea posible está en el archivo que puede editarse desde el editor de estilos 
  visuales, editando el archivo `template/layout.php`
  Asegurarse que exista una linea que diga:

```php
<link rel="stylesheet" 
      type="text/css" 
      media="all" 
      href="<?php echo url_for('@section_css?name='.$sf_request->getParameter('section_name', '')) ?>" />
```
