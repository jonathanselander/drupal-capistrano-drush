<header>
  <a id="logo" href="<?php print $front_page; ?>">M</a>
  <?php print render($page['header']); ?>
  <?php if ($main_menu): ?>
    <nav><?php print theme('links__system_main_menu', array('links' => $main_menu)); ?></nav>
  <?php endif; ?>
</header>
<section>
  <?php if ($title): ?>
    <h1><?php print $title; ?></h1>
  <?php endif; ?>
  <?php print render($page['content']); ?>
</section>
<footer>
  <?php print $messages; ?>
  <?php if ($tabs && !empty($tabs['#primary'])): print render($tabs); endif; ?>
  <?php print render($page['footer']); ?>
  <nav>
    <ul>
      <li><?php print $site_name; ?></li>
      <li><a href="tel:+46-730-400082" title="Call me"><i class="icon-phone"></i>Call 46-730-400082</a>
      <li><a href="#top">Back to top<i class="icon-arrow-up"></i></a>
    </ul>
  </nav>
</footer>
