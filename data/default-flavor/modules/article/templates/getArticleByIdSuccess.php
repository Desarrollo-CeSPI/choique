<?php echo $article->getActionToolbar($with_navigation) ?>

<!-- This part should look like the output of Article::getFullHTMLRepresentation(), from here... -->
<div id="full-html">
  <div class="title">
    <?php echo $article->getTitle() ?>
  </div>
  <div class="heading">
    <?php echo $article->getHeading() ?>
  </div>
  <div class="body">
  <?php if( $article->hasMultimedia()) : ?>
    <div id="article-image">
      <?php echo $article->getRepresentedMultimedia() ?>
    </div>
  <?php endif; ?>
    <?php echo $article->getHTMLText() ?>
  </div>
</div>
<?php if ($article->shouldIncludeLastUpdateDate()): ?>
<div class="updated-at"><?php echo $article->getPrintUpdatedAt() ?></div>
<?php endif; ?>

<!-- ...to here -->
<?php echo $article->getFooter() ?>
