<article>
  <?php if (!$page): ?>
    <h3><a href="<?php print $node_url; ?>"><?php print $title; ?></a></h3>
  <?php endif; ?>
  <?php if ($display_submitted): ?>
    <p class="date"><?php print $submitted; ?></p>
  <?php endif; ?>
  <?php
    hide($content['links']);
    print render($content);
  ?>
  <?php if (!$page): ?>
    <p><?php print l(t('Continue reading'), 'node/' . $nid, array('attributes' => array('class' => t('readmore')))); ?></p>
  <?php endif; ?>
</article>
