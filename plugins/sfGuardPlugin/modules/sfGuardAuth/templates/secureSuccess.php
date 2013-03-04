<?php use_helper('I18N') ?>

<div
<p>
</p>

<?php use_helper('I18N', 'Javascript') ?>

<div align="center">
  <div class="error404">
    <h1><?php echo __("Acceso no autorizado") ?></h1>
    <?php echo __("Usted no dispone de los privilegios requeridos para acceder a esta página."); ?>
    <?php echo __("Si considera esto un error, por favor comuníqueselo al administrador del CMS.") ?>
    <div class="actions">
      <?php echo link_to_function("Volver a la página anterior", "history.go(-1);") ?>
      |
      <?php echo link_to("Volver al inicio", "@homepage") ?>
    </div>
  </div>
</div>
