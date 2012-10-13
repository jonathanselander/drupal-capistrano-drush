<section class="<?php print $block_html_id; ?>">
  <?php print render($title_prefix); ?>
  <?php if ($block->subject): ?>
    <h3<?php print $title_attributes; ?>><?php print $block->subject ?></h3>
  <?php endif;?>
  <?php print render($title_suffix); ?>
  <?php print $content ?>
</section>
