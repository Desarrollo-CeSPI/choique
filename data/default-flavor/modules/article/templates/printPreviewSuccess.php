<?php use_helper('Javascript') ?>

<div id="print-content">
  <?php echo image_tag(choiqueFlavors::getImagePath('logo', 'gif'), Array('alt' => __('Logotipo'), 'id' => 'top-image')) ?>
  <div class="article-content">
    <div class="path">
      <?php if_javascript(); ?>
        <?php echo link_to_function(image_tag(choiqueFlavors::getImagePath('print', 'png'), array('alt' => __('Imprimir'), 'title' => __('Imprimir'))),'print()', array('id' => 'top-print')) ?>
      <?php end_if_javascript(); ?>
     <!--<?php $sectionName = ($article->getSectionId()) ? $article->getSection()->getName() : Section::HOME ?>
      <?php echo Section::getPath($section) ?>-->
    </div>
    <noscript>
      <div id="contact_return_link">
        <?php echo link_to(__("Volver al artículo"), $article->getURLReference(), array('title' => __("Volver al artículo"), 'accesskey' => 'r')); ?>
      </div>
    </noscript>
    <?php echo $article->getFullHTMLRepresentation() ?>
  </div>

  <div class="footer">
    <div><?php echo url_for($article->getUrlReference(), true) ?></div>
    <div><?php echo CmsConfiguration::get('footer') ?></div>
  </div>
</div>

<script type="text/javascript">
//<![CDATA[
jQuery.noConflict();

if ($('text_right'))
{
  $('text_right').select('a').each(function (e, i)
  {
    $(e).removeAttribute('href');
    $(e).removeAttribute('onclick');
  });
};
//]]>
</script>