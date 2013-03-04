<?php
/**
 * @package sfLucenePlugin
 * @subpackage Module
 * @author Carl Vondrick <carlv@carlsoft.net>
 */
?>

<?php if ($pager->haveToPaginate()): ?>
  <div class="search-page-numbers">
    <div class="pager-title">
      <?php echo __('Páginas') ?>
    </div>
    <?php if ($pager->getPage() != $pager->getPreviousPage()): ?>
      <?php echo link_to(__('Anterior'), 'sfLucene/search?cms_search=1&query=' . $query . '&page=' . $pager->getPreviousPage() . (($category) ? '&category='.$category : ''), 'class=bookend') ?>
    <?php endif ?>

    <?php foreach ($links as $page): ?>
      <?php if ($page == $pager->getPage()): ?>
        <strong><?php echo $page ?></strong>
      <?php else: ?>
        <?php echo link_to($page, 'sfLucene/search?cms_search=1&query=' . $query . '&page=' . $page . (($category) ? '&category='.$category : '')) ?>
      <?php endif ?>
    <?php endforeach ?>

    <?php if ($pager->getPage() != $pager->getNextPage()): ?>
      <?php echo link_to(__('Siguiente'), 'sfLucene/search?cms_search=1&query=' . $query . '&page=' . $pager->getNextPage() . (($category) ? '&category='.$category : ''), 'class=bookend') ?>
    <?php endif ?>
  </div>

<?php endif ?>
