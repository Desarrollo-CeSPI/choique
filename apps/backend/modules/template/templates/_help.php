<?php 
/*
 * Choique CMS - A Content Management System.
 * Copyright (C) 2012 CeSPI - UNLP <desarrollo@cespi.unlp.edu.ar>
 * 
 * This file is part of Choique CMS.
 * 
 * Choique CMS is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License v2.0 as published by
 * the Free Software Foundation.
 * 
 * Choique CMS is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with Choique CMS.  If not, see <http://www.gnu.org/licenses/gpl-2.0.html>.
 */ ?>
<div class="help-container">
  <span class="help-toggle">
    <?php echo link_to_function(
      image_tag('backend/help.png', array('alt' => '?', 'title' => __('Desplegar/ocultar ayuda'))).' '.__('Ayuda'),
      "$('template_editor_help').toggle(); $(this).up().toggleClassName('active');"
    ) ?>
  </span>
  <div id="template_editor_help" style="display: none;">
    <h2><?php echo __('Ayuda para el editor de diseño de portadas') ?></h2>
    <p>
      <?php echo __('Este editor está destinado al diseño de la visual de las portadas a utilizar en las secciones del ChoiqueCMS') ?>.
      <?php echo __('En el lado izquierdo de la página se encuentra la parte de diseño del editor, mientras que del lado derecho se encuentra el listado de artículos disponibles para la portada') ?>.
      <?php echo __('Es una buena práctica previsualizar las portadas antes de guardarlas, mediante el ícono de previsualización') ?>
      (<?php echo image_tag('backend/magnifier.png') ?>).
    </p>

    <h2><?php echo __('Diseño') ?></h2>
    <p>
      <?php echo __('El diseño indica la cantidad de filas que contendrá la portada, el orden de las mismas y el orden de los artículos en cada una de esas filas') ?>.
      <?php echo __('A su vez, para cada uno de los artículos se puede seleccionar la forma en que se representará') ?>.
    </p>
    <p>
      <?php echo __('Cada una de las filas permite que se suelten artículos del listado de la sección "Artículos disponibles" sobre ellas, con el fin de agregarlos a la misma.') ?>.
    </p>
    <p>
      <?php echo __('Una vez que una fila disponga de dos o más artículos asignados, puede cambiar el orden de los mismos arrastrándolos por su ícono de movimiento') ?>
      (<?php echo image_tag('backend/move_handle.png') ?>).
    </p>
    <p>
      <?php echo __('Para cambiar la representación de alguno de los artículos asignados a una fila, puede hacer clic sobre el ícono de edición de representación') ?>
      (<?php echo image_tag('/sf/sf_admin/images/edit.png') ?>)
      <?php echo __('y a continuación seleccionar la representación deseada de entre las opciones que se mostrarán') ?>.
    </p>
    <p>
      <?php echo __('Para especificar el ancho de alguna celda, utilice el campo seleccionable que se encuentra al final de cada elemento de las filas.') ?>
      <?php echo __('El valor "Automático" (valor por defecto del campo) manejará automáticamente el ancho en función de la cantidad de elementos que introduzca en la fila.') ?>
    </p>

    <h2><?php echo __('Artículos disponibles') ?></h2>
    <p>
      <?php echo __('Esta sección contiene inicialmente los artículos que se están utilizando en la portada, listados mostrando su título y dando la opción de visualizar sus detalles (haciendo clic sobre "detalles").') ?>.
      <?php echo __('Los artículos listados en esta sección pueden ser agregados a alguna de las filas de la sección "Diseño" arrastrándolos hacia la fila deseada y soltándolos en la misma') ?>.
    </p>
    <p>
      <?php echo __('Para agregar un nuevo artículo, comience a tipear su nombre en el campo "Buscar otro artículo" de esta sección y cuando aparezca en la lista emergente, selecciónelo haciendo clic una vez sobre su título en dicha lista') ?>.
      <?php echo __('Luego de esto, si el artículo no se encontraba en el listado, se lo agregará al final del mismo, listo para ser arrastrado hacia alguna fila la sección "Diseño"') ?>.
    </p>

    <h2><?php echo __('Interacciones') ?></h2>
    <p>
      <?php echo __('Aquí se presentan los diferentes puntos de interacción, relacionados por su ícono identificador.') ?>
    </p>
    <p>
      <span class="image"><?php echo image_tag('backend/delete_item.png') ?></span> <?php echo __('Eliminar un elemento') ?>.
    </p>
    <p>
      <span class="image"><?php echo image_tag('/sf/sf_admin/images/edit.png') ?></span> <?php echo __('Editar la representación de un artículo') ?>.
    </p>
    <p>
      <span class="image"><?php echo image_tag('backend/move_handle.png') ?></span> <?php echo __('Desplazar un elemento') ?>. <?php echo __('Puede indicar cambiar el orden de un artículo (en la sección "Diseño") o indicar la posibilidad de arrastrar un artículo desde la sección "Artículos disponibles" hasta la sección "Diseño", con el fin de agregarlo a alguna fila') ?>.
    </p>
    <p>
      <span class="image"><?php echo image_tag('backend/prepend_row.png') ?></span> <?php echo __('Agregar una nueva fila vacía antes que el resto de las filas ya presentes en el diseño') ?>.
    </p>
    <p>
      <span class="image"><?php echo image_tag('backend/append_row.png') ?></span> <?php echo __('Agregar una nueva fila vacía después del resto de las filas ya presentes en el diseño') ?>.
    </p>
    <p>
      <span class="image"><?php echo image_tag('backend/magnifier.png') ?></span> <?php echo __('Generar una previsualización del diseño actual de la portada') ?>. <?php echo __('Esta interacción abrirá una ventana nueva simulando cómo se verá la portada en la parte pública ("frontend") del sitio') ?>. <?php echo __('Es importante notar que la previsualización no implica el guardado del diseño de su portada') ?>.
    </p>
    <p>
      <span class="image"><?php echo image_tag('common/indicator.gif') ?></span> <?php echo __('Indicador de procesamiento') ?>. <?php echo __('Este ícono indica que se está realizando el procesamiento necesario para completar la acción solicitada') ?>. <?php echo __('Mientras este ícono esté visible, deberá esperar para poder realizar una nueva acción en el editor') ?>.
    </p>
  </div>
</div>