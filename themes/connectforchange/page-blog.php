<?php
/*
  Template Name: Blog Page
 */
?>
<?php
$et_ptemplate_settings = array();
$et_ptemplate_settings = maybe_unserialize(get_post_meta($post->ID, 'et_ptemplate_settings', true));

$fullwidth = isset($et_ptemplate_settings['et_fullwidthpage']) ? (bool) $et_ptemplate_settings['et_fullwidthpage'] : false;

$et_ptemplate_blogstyle = isset($et_ptemplate_settings['et_ptemplate_blogstyle']) ? (bool) $et_ptemplate_settings['et_ptemplate_blogstyle'] : false;

$et_ptemplate_showthumb = isset($et_ptemplate_settings['et_ptemplate_showthumb']) ? (bool) $et_ptemplate_settings['et_ptemplate_showthumb'] : false;

$blog_cats = isset($et_ptemplate_settings['et_ptemplate_blogcats']) ? (array) $et_ptemplate_settings['et_ptemplate_blogcats'] : array();
$et_ptemplate_blog_perpage = isset($et_ptemplate_settings['et_ptemplate_blog_perpage']) ? (int) $et_ptemplate_settings['et_ptemplate_blog_perpage'] : 10;
?>

<?php get_header(); ?>

<div id="container">

	<div id="iDivBreadcrumb">
		<?php the_breadcrumbs(); ?>
		<div id="iDivCurrentTabName">&nbsp;&raquo;&nbsp;All Blog Posts</div>
	</div>


	<div id="container2">

        <div  class="cDivBlogPageContainer">
					<?php $tabOptions['showTabs']=true;
                $tabOptions['showUpdates']=false;
                $tabOptions['categories']='all';
                $tabOptions['showcategories'] = true;
                get_template_part('includes/tabs'); ?>

		</div> <!-- end cDivBlogPageContainer -->
		<?php //if (!$fullwidth) get_sidebar();   ?>
	</div> <!-- end #container2 -->


</div> <!-- end #container -->
<?php get_footer(); ?>
</body>
</html>




