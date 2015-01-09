<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>
<head profile="http://gmpg.org/xfn/11">
<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />
<title><?php elegant_titles(); ?></title>
<?php elegant_description(); ?>
<?php elegant_keywords(); ?>
<?php elegant_canonical(); ?>


<link rel="alternate" type="application/rss+xml" title="<?php bloginfo('name'); ?> RSS Feed" href="<?php bloginfo('rss2_url'); ?>" />
<link rel="alternate" type="application/atom+xml" title="<?php bloginfo('name'); ?> Atom Feed" href="<?php bloginfo('atom_url'); ?>" />
<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />

<?php if ( is_singular() ) wp_enqueue_script( 'comment-reply' ); ?>
<?php wp_head(); ?>
<link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>" type="text/css" media="screen" />
<!--[if IE 7]>
		<link rel="stylesheet" type="text/css" media="all" href="<?php bloginfo('template_directory'); ?>/iestyle.css" />
	<![endif]-->
    <!--[if IE 8]>
		<link rel="stylesheet" type="text/css" media="all" href="<?php bloginfo('template_directory'); ?>/ie8style.css" />
	<![endif]-->
<!--[if lt IE 7]>
	<link rel="stylesheet" type="text/css" media="all" href="<?php bloginfo('template_directory'); ?>/ie6style.css" />
	<![endif]-->
</head>
<body <?php body_class(); ?>>
<div id="iDivCustomWrapper">
<div id="bg">
<!--	<div id="pages">
		<?php $menuClass = 'nav superfish';
		$primaryNav = '';
		if (function_exists('wp_nav_menu')) {
            if ( is_user_logged_in() ) {
                echo 'LOGGED IN';
                $primaryNav = wp_nav_menu( array( 'theme_location' => 'primary-menu', 'container' => '', 'fallback_cb' => '', 'menu_class' => $menuClass, 'echo' => false ) );
            } else {
                echo 'NOT LOGGED IN';
                $primaryNav = wp_nav_menu( array( 'theme_location' => 'secondary-menu', 'container' => '', 'fallback_cb' => '', 'menu_class' => $menuClass, 'echo' => false ) );
            }
		};
		if ($primaryNav == '') { ?>
			<ul class="<?php echo $menuClass; ?>">
				<?php if (get_option('quadro_swap_navbar') == 'false') { ?>
					<?php if (get_option('quadro_home_link') == 'on') { ?>
						<li class="page_item"><a href="<?php bloginfo('url'); ?>"><?php esc_html_e('Home','Quadro') ?></a></li>
					<?php }; ?>

					<?php show_page_menu($menuClass,false,false); ?>
				<?php } else { ?>
					<?php show_categories_menu($menuClass,false); ?>
				<?php } ?>
			</ul>  end ul#nav
		<?php }
		else echo($primaryNav); ?>
	</div>  end #pages -->
	<div style="clear: both;"></div>

	<div id="header">
	    <div id="iDivLogo">
		<a class="cAlogo" href="<?php bloginfo('url'); ?>"><?php $logo = (get_option('quadro_logo') <> '') ? get_option('quadro_logo') : get_bloginfo('template_directory').'/images/logo.gif'; ?>
			<img src="<?php echo esc_url($logo); ?>" alt="Logo" class="logo"/> <br style="clear:both;" />
		</a>
	    </div>

	    <div id="iDivSocialNw">
            <div id="cnss_widget-2" class="header-box widget_cnss_widget">
                <h2> </h2>
                <table class="cnss-social-icon" style="width:216px" border="0" cellspacing="0" cellpadding="0">
                    <tbody>
                        <tr>
                            <td style="width:36px">
                                <a target="_blank" title="RSS" href="/feed">
                                    <img src="/wp-content/uploads/1378222050_1368465136_sm_icons_05.png" border="0" width="32" height="32">
                                </a>
                            </td>
                            <td style="width:36px">
                                <a target="_blank" title="Youtube" href="http://www.youtube.com/user/RAINfoundation">
                                    <img src="/wp-content/uploads/1378222061_1368465243_sm_icons_11.png" border="0" width="32" height="32" style="opacity: 1;">
                                </a>
                            </td>
                            <td style="width:36px">
                                <a target="_blank" title="Twitter" href="https://twitter.com/rainwater4food">
                                    <img src="/wp-content/uploads/1378222107_1376579549_IFAD-WebSetup_20130502_03.png" border="0" width="32" height="32" style="opacity: 1;">
                                </a>
                            </td>
                            <?php
                                $languages = icl_get_languages('skip_missing=0&orderby=code');
                                if(!empty($languages)){
                                    foreach($languages as $l){
                                        if($l['language_code']==='en'){
                                            ?>
                                            <td style="width:36px">
                                                <a title="en" href="<?php echo $l['url'];?>">
                                                    <img src="/wp-content/uploads/1402665631_en.png" border="0" width="32" height="32">
                                                </a>
                                            </td>
                                            <?php
                                        }elseif($l['language_code']==='fr'){
                                            ?>
                                            <td style="width:36px">
                                                <a title="fr" href="<?php echo $l['url'];?>">
                                                    <img src="/wp-content/uploads/1402665391_fr.png" border="0" width="32" height="32">
                                                </a>
                                            </td>
                                            <?php
                                        }elseif($l['language_code']==='es'){
                                            ?>
                                            <td style="width:36px">
                                                <a title="es" href="<?php echo $l['url'];?>">
                                                    <img src="<?php echo get_stylesheet_directory_uri();?>/images/icon_es.png" border="0" width="32" height="32">
                                                </a>
                                            </td>
                                            <?php
                                        }
                                    }
                                }
                            ?>
                            
                            
                        </tr>
                    </tbody>
                </table>
            </div>
	    </div>
	    <br clear="all"/>

	  <div class="search_bg">
                <div id="search">
                    <form method="get" action="<?php echo home_url(); ?>" style="padding:0px 0px 0px 0px; margin:0px 0px 0px 0px">
    <!--<p id="iPSearch">-->
                        <label id="iLblSearchInfield" for="s">Search...</label>
                        <input type="text" id="s" name="s" value="<?php echo esc_attr( get_search_query() ); ?>"/>
                        <input type="image" class="input" src="/wp-content/themes/f4w/images/search-btn.gif" value="submit"/>
    <!--</p>-->
                    </form>
                </div> <!-- end #search -->
            </div> <!-- end #search-bg -->

	</div> <!-- end #header -->
	<div style="clear: both;"></div>

	<div id="wrapper2">
		<div id="categories">
			<?php $menuClass = 'nav superfish';
			$menuID = 'nav2';
			$secondaryNav = '';
			if (function_exists('wp_nav_menu')) {
				$secondaryNav = wp_nav_menu( array( 'theme_location' => 'secondary-menu', 'container' => '', 'fallback_cb' => '', 'menu_class' => $menuClass, 'menu_id' => $menuID, 'echo' => false ) );
			};
			if ($secondaryNav == '') { ?>
				<ul id="<?php echo $menuID; ?>" class="<?php echo $menuClass; ?>">
					<?php if (get_option('quadro_swap_navbar') == 'false') { ?>
						<?php show_categories_menu($menuClass,false); ?>
					<?php } else { ?>
						<?php if (get_option('quadro_home_link') == 'on') { ?>
							<li class="page_item"><a href="<?php bloginfo('url'); ?>"><?php esc_html_e('Home','Quadro') ?></a></li>
						<?php }; ?>

						<?php show_page_menu($menuClass,false,false); ?>
					<?php } ?>
				</ul> <!-- end ul#nav -->
			<?php }
			else echo($secondaryNav); ?>
			<div style="clear: both;"></div>
		</div> <!-- end #categories -->

	<div id="pages">
		<?php $menuClass = 'nav superfish';
		$primaryNav = '';
		if (function_exists('wp_nav_menu')) {
			if ( is_user_logged_in() ) {
                $primaryNav = wp_nav_menu( array( 'theme_location' => 'primary-menu', 'container' => '', 'fallback_cb' => '', 'menu_class' => $menuClass, 'echo' => false ) );
            } else {
                $primaryNav = wp_nav_menu( array( 'theme_location' => 'secondary-menu', 'container' => '', 'fallback_cb' => '', 'menu_class' => $menuClass, 'echo' => false ) );
            }
		};
		if ($primaryNav == '') { ?>
			<ul class="<?php echo $menuClass; ?>">
				<?php if (get_option('quadro_swap_navbar') == 'false') { ?>
					<?php if (get_option('quadro_home_link') == 'on') { ?>
						<li class="page_item"><a href="<?php bloginfo('url'); ?>"><?php esc_html_e('Home','Quadro') ?></a></li>
					<?php }; ?>

					<?php show_page_menu($menuClass,false,false); ?>
				<?php } else { ?>
					<?php show_categories_menu($menuClass,false); ?>
				<?php } ?>
			</ul> <!-- end ul#nav-->
		<?php }
		else echo($primaryNav); ?>
	</div> <!-- end #pages -->


