<article class="profile">
  <?php hide($user_profile['summary']); ?>
  <?php print render($user_profile); ?>
  <h6>You look great today, <?php print $user_profile['name']; ?>!</h6>
  <p>Now, get some stuff done will you.</p>
</article>
