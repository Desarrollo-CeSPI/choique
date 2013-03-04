<?php
/**
 * @package sfLucenePlugin
 * @subpackage Module
 * @author Carl Vondrick <carlv@carlsoft.net>
 */
?>

<?php use_helper('sfLucene', 'I18N') ?>

<div class="search-results-title"><?php echo __('Resultados de la búsqueda') ?></div>
<?php include_search_controls($query) ?>

<div class="search-heading">
  <?php if ($sf_params->has('referer_msg')): ?>
    <div style="font-weight: bold; margin: 10px;"><?php echo $sf_params->get('referer_msg') ?></div>
  <?php endif ?>
  <?php echo __('%%number%% resultados encontrados para: "%%query%%"',
                array('%%number%%' => $num,
                      '%%query%%'  => htmlspecialchars($rawquery))) ?>
</div>
<?php include_partial("search_query_error",array("searchErrors"=>$searchErrors,"rawquery"=>$rawquery,"query"=>$query)) ?>

<ul start="<?php echo $pager->getFirstIndice() ?>" class="search-results">
  <?php foreach ($pager->getResults() as $result): ?>
    <li><?php include_search_result($result, $query) ?></li>
  <?php endforeach ?>
</ul>

<?php include_search_pager($pager, sfConfig::get('app_lucene_pager_radius'), $category = false) ?>
