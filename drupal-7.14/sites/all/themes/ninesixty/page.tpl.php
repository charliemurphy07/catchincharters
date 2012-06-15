<?php
// $Id: page.tpl.php,v 1.1.2.2.4.2 2011/01/11 01:08:49 dvessel Exp $
?>
<div id="page" class=" clearfix">
 <div id="container" class="container-12">
  <div id="left-sidebar" class="grid-3 clearfix">
    <div id="branding" class="grid-3 clearfix">
    <?php if ($linked_logo_img): ?>
      <div id="logo" class="alpha"><?php print $linked_logo_img; ?></div>
    <?php endif; ?>
    <?php if ($linked_site_name): ?>
      <h1 id="site-name" class="grid-3 omega"><?php print $linked_site_name; ?></h1>
    <?php endif; ?>
    <?php if ($site_slogan): ?>
      <div id="site-slogan" class="omega"><?php print $site_slogan; ?></div>
    <?php endif; ?>
    </div>

  <?php if ($main_menu_links || $secondary_menu_links): ?>
    <div id="site-menu" class="grid-3">
      <?php print $main_menu_links; ?>
      <?php print $secondary_menu_links; ?>
    </div>
  <?php endif; ?>

  <?php if ($page['search_box']): ?>
    <div id="search-box" class="grid-3 prefix-10"><?php print render($page['search_box']); ?></div>
  <?php endif; ?>
  <div id="site-subheader" class="grid-3 clearfix">
  <?php if ($page['highlighted']): ?>
    <div id="highlighted" class="<?php print ns('grid-14', $page['header'], 7); ?>">
      <?php print render($page['highlighted']); ?>
    </div>
  <?php endif; ?>

  <?php if ($page['header']): ?>
    <div id="header-region" class="region <?php print ns('grid-3', $page['highlighted'], 7); ?> clearfix">
      <?php print render($page['header']); ?>
    </div>
  <?php endif; ?>
  <?php if ($page['sidebar_first']): ?>
  <div id="sidebar-left" class="column sidebar region grid-4 <?php print ns('pull-12', $page['sidebar_second'], 3); ?>">
    <?php print render($page['sidebar_first']); ?>
  </div>
  <?php endif; ?>
  </div>
 </div>

  <div id="main" class="column grid-8 shadow">
    <?php print render($title_prefix); ?>
    <?php if ($title): ?>
      <h1 class="title" id="page-title"><?php print $title; ?></h1>
    <?php endif; ?>
    <?php print render($title_suffix); ?>      
    <?php if ($tabs): ?>
      <div class="tabs"><?php print render($tabs); ?></div>
    <?php endif; ?>
    <?php print $messages; ?>
    <?php print render($page['help']); ?>

    <div id="main-content" class="region clearfix">
      <?php print render($page['content']); ?>
    </div>

    <?php print $feed_icons; ?>
  </div>

<?php if ($page['sidebar_second']): ?>
  <div id="sidebar-right" class="column sidebar region grid-3">
    <?php print render($page['sidebar_second']); ?>
  </div>
<?php endif; ?>
 </div>
</div>
  <div id="footer" class="suffix-1">
    <?php if ($page['footer']): ?>
      <div id="footer-region" class="region clearfix">
        <?php print render($page['footer']); ?>
      </div>
    <?php endif; ?>
  </div>
