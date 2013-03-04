<?php
/**
 * @package sfLucenePlugin
 * @subpackage Module
 * @author Carl Vondrick <carlv@carlsoft.net>
 */
?>

<?php use_helper('I18N') ?>

<div class="search-results-title"><?php echo __('No se arrojaron coincidencias') ?></div>
<?php include_component($sf_context->getModuleName(), 'controls') ?>

<?php include_partial("search_query_error",array("searchErrors"=>$searchErrors,"rawquery"=>$rawquery,"query"=>$query)) ?>

<div class="search-heading">
  <?php if ($sf_params->has('referer_msg')): ?>
    <div style="font-weight: bold; margin: 10px;"><?php echo $sf_params->get('referer_msg') ?></div>
  <?php endif ?>
  <p><?php echo __('No se arrojaron coincidencias a partir de su(s) término(s) de búsqueda.') ?></p>
</div>
