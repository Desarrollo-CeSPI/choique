<?php
/**
 * @package sfLucenePlugin
 * @subpackage Module
 * @author Carl Vondrick <carlv@carlsoft.net>
 */
?>

<?php use_helper('sfLucene') ?>

<?php echo form_tag('sfLucene/search', 'method=get class=search-controls') ?>

  <label for="query"><?php echo __('Búsqueda') ?></label>
  <?php echo input_tag('query', $query, 'accesskey=q') ?>
  <?php if (has_search_categories()): ?>
    <?php include_search_categories() ?>
  <?php endif ?>
  <?php echo submit_image_tag('search_arrow.png', array('alt' => '&gt;', 'id' => 'main-search-arrow', 'accesskey' => 's')) ?>
  <div class="where-to-search">
    <?php echo __('Buscar en este sitio').radiobutton_tag('cms_search', true, 1) ?>
    <?php echo __('Buscar en todo el sitio (Google)').radiobutton_tag('cms_search', false, 0) ?>
  </div>
  <?php if (sfConfig::get('app_lucene_advanced', true)): ?>
    <?php echo submit_tag(__('Advanced'), 'accesskey=a') ?>
  <?php endif ?>

</form>
