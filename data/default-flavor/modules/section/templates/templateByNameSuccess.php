<?php echo $template ?>

<?php if (CmsConfiguration::get('check_show_show_all', 1)): ?>
  <?php if (strtolower(sfContext::getInstance()->getRequest()->getParameter('section_name', Section::HOME)) == strtolower(Section::HOME)): ?>
    <div class="show-more">
      <?php echo link_to(__('todas las noticias'), '@show_all') ?>
    </div>
  <?php endif ?>
<?php endif ?>
