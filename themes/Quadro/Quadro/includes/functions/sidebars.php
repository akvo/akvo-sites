<?php
if ( function_exists('register_sidebar') ){
    register_sidebar(array(
		'name' => 'Sidebar',
        'before_widget' => '<div id="%1$s" class="sidebar-box %2$s">',
		'after_widget' => '</div> <!-- end .sidebar-box -->',
		'before_title' => '<h2>',
        'after_title' => '</h2>',
    ));
    register_sidebar(array(
		'name' => 'Sidebar home',
        'id' => 'sidebar-home',
        'description' => 'sidebar on home',
        'before_widget' => '<div id="%1$s" class="sidebar-box %2$s">',
		'after_widget' => '</div> <!-- end .sidebar-box -->',
		'before_title' => '<h2>',
        'after_title' => '</h2>',
    ));

    register_sidebar(array(
		'name' => 'News box',
        'id' => 'news-box',
        'description' => 'news box on home',
        'before_widget' => '<div id="%1$s" class="sidebar-box %2$s">',
		'after_widget' => '</div> <!-- end .sidebar-box -->',
		'before_title' => '<h2>',
        'after_title' => '</h2>',
    ));
    register_sidebar(array(
		'name' => 'About box',
        'id' => 'about-box',
        'description' => 'about box on home',
        'before_widget' => '',
		'after_widget' => '',
		'before_title' => '<h2>',
        'after_title' => '</h2>',
    ));
    register_sidebar(array(
		'name' => 'Header',
        'id' => 'header',
        'description' => 'header',
        'before_widget' => '<div id="%1$s" class="header-box %2$s">',
		'after_widget' => '</div> <!-- end .sidebar-box -->',
		'before_title' => '<h2>',
        'after_title' => '</h2>',
    ));

$args = array(
	'name'          => 'Footer',
	'id'            => 'sidebar-footer',
	'description'   => 'in the footer',
        'class'         => '',
	'before_widget' => '<div id="%1$s" class="widget %2$s">',
	'after_widget'  => '</div>',
	'before_title'  => '<h2 class="widgettitle">',
	'after_title'   => '</h2>' );
register_sidebar($args);
}
?>