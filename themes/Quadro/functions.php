<?php
add_action('after_setup_theme', 'et_setup_theme');
if (!function_exists('et_setup_theme')) {

	function et_setup_theme() {
		global $themename, $shortname;
		$themename = "Quadro";
		$shortname = "quadro";

		require_once(TEMPLATEPATH . '/epanel/custom_functions.php');

		require_once(TEMPLATEPATH . '/includes/functions/comments.php');

		require_once(TEMPLATEPATH . '/includes/functions/sidebars.php');

		load_theme_textdomain('Quadro', get_template_directory() . '/lang');

		require_once(TEMPLATEPATH . '/epanel/options_quadro.php');

		require_once(TEMPLATEPATH . '/epanel/core_functions.php');

		require_once(TEMPLATEPATH . '/epanel/post_thumbnails_quadro.php');

		include(TEMPLATEPATH . '/includes/widgets.php');
	}

}

add_action('wp_head', 'et_portfoliopt_additional_styles', 100);

function et_portfoliopt_additional_styles() {
	?>
	<style type="text/css">
		#et_pt_portfolio_gallery { margin-left: -15px; }
		.et_pt_portfolio_item { margin-left: 21px; }
		.et_portfolio_small { margin-left: -40px !important; }
		.et_portfolio_small .et_pt_portfolio_item { margin-left: 32px !important; }
		.et_portfolio_large { margin-left: -8px !important; }
		.et_portfolio_large .et_pt_portfolio_item { margin-left: 2px !important; }
	</style>
	<?php
}

function register_main_menus() {
	register_nav_menus(
			array(
				'primary-menu' => __('Primary Menu'),
				'secondary-menu' => __('Secondary Menu'),
                'footer-menu'   =>  __('Footer Menu')
			)
	);
}

;
if (function_exists('register_nav_menus'))
	add_action('init', 'register_main_menus');

if (!function_exists('et_list_pings')) {

	function et_list_pings($comment, $args, $depth) {
		$GLOBALS['comment'] = $comment;
		?>
		<li id="comment-<?php comment_ID(); ?>"><?php comment_author_link(); ?> - <?php comment_excerpt(); ?>
			<?php
		}

	}


	//added by uthpala sandirigama to get the wordcount of a post
	if (!function_exists('string_limit_words')) {

		function string_limit_words($string, $word_limit) {
			$words = explode(' ', $string, ($word_limit + 1));
			if (count($words) > $word_limit)
				array_pop($words);
			return implode(' ', $words);
		}

	}


	if (!function_exists('textClipper')) {

		function textClipper($mValue, $mAmount = null, $sLink = '', $bTrimToLastPunctuation = true) {
			$bAddReadMoreText = false;

			if (is_null($mAmount))
				return $mValue;

			$iCharacterAmount = strlen($mValue);

			if ($mAmount < $iCharacterAmount) {
				$mValue = substr($mValue, 0, $mAmount);

				if ($bTrimToLastPunctuation) {
					$iFinalLocation = 0;
					$iPunctuationToUse = -1;
					$aPunctuation = array(' ', '.', '!', '?');
					foreach ($aPunctuation as $iIndex => $sPunctuation) {
						$iLocation = strrpos($mValue, $sPunctuation);
						if ($iLocation !== false && $iLocation > $iFinalLocation) {
							$iFinalLocation = $iLocation;
							$iPunctuationToUse = $iIndex;
						}
					}
					if ($iFinalLocation != 0 && $iPunctuationToUse != -1) {
						//$sTrailingFragment = strrchr($mValue, $aPunctuation[$iPunctuationToUse]);
						//$mValue = str_replace($sTrailingFragment, $aPunctuation[$iPunctuationToUse], $mValue);
						$iTrailingFragmentPosition = strrpos($mValue, $aPunctuation[$iPunctuationToUse]);
						$mValue = substr($mValue, 0, $iTrailingFragmentPosition + 1);
					}
				}
				$bAddReadMoreText = true;
			}
			/*
			  if ($bAddReadMoreText) {
			  if ($sLink == '')
			  $mValue .= "... <a href='#' title='Coming Soon'>Read More</a>";
			  else
			  $mValue .= "... <a href='" . $sLink . "' title='Read More in the PDF Document' target='_blank'>Read More</a>";
			  }
			 */
			if ($bAddReadMoreText)
				$mValue .= "<span title='Read More'>...</span>";
			return $mValue;
		}

	}

	//added by uthpala sandirigama to get the breadcrum
	function the_breadcrumbs() {

		$showOnHome = 0; // 1 - show breadcrumbs on the homepage, 0 - don't show
		$delimiter = '&raquo;'; // delimiter between crumbs
		$home = 'Home'; // text for the 'Home' link
		$showCurrent = 1; // 1 - show current post/page title in breadcrumbs, 0 - don't show
		$before = '<span class="current">'; // tag before the current crumb
		$after = '</span>'; // tag after the current crumb

		global $post;
		$homeLink = get_bloginfo('url');

		if (is_home() || is_front_page()) {

			if ($showOnHome == 1)
				echo '<div id="crumbs"><a href="' . $homeLink . '">' . $home . '</a></div>';
		} else {

			echo '<div id="crumbs"><a href="' . $homeLink . '">' . $home . '</a> ' . $delimiter . ' ';

			if (is_category()) {
				$thisCat = get_category(get_query_var('cat'), false);
				if ($thisCat->parent != 0)
					echo get_category_parents($thisCat->parent, TRUE, ' ' . $delimiter . ' ');
				echo $before . 'Archive by category "' . single_cat_title('', false) . '"' . $after;
			} elseif (is_search()) {
				echo $before . 'Search results for "' . get_search_query() . '"' . $after;
			} elseif (is_day()) {
				echo '<a href="' . get_year_link(get_the_time('Y')) . '">' . get_the_time('Y') . '</a> ' . $delimiter . ' ';
				echo '<a href="' . get_month_link(get_the_time('Y'), get_the_time('m')) . '">' . get_the_time('F') . '</a> ' . $delimiter . ' ';
				echo $before . get_the_time('d') . $after;
			} elseif (is_month()) {
				echo '<a href="' . get_year_link(get_the_time('Y')) . '">' . get_the_time('Y') . '</a> ' . $delimiter . ' ';
				echo $before . get_the_time('F') . $after;
			} elseif (is_year()) {
				echo $before . get_the_time('Y') . $after;
			} elseif (is_single() && !is_attachment()) {
				if (get_post_type() != 'post') {
					$post_type = get_post_type_object(get_post_type());
					$slug = $post_type->rewrite;
					echo '<a href="' . $homeLink . '/' . $slug['slug'] . '/">' . $post_type->labels->singular_name . '</a>';
					if ($showCurrent == 1)
						echo ' ' . $delimiter . ' ' . $before . get_the_title() . $after;
				} else {
					$cat = get_the_category();
					$cat = $cat[0];
					$cats = get_category_parents($cat, TRUE, ' ' . $delimiter . ' ');
					if ($showCurrent == 0)
						$cats = preg_replace("#^(.+)\s$delimiter\s$#", "$1", $cats);
					echo $cats;
					if ($showCurrent == 1)
						echo $before . get_the_title() . $after;
				}
			} elseif (!is_single() && !is_page() && get_post_type() != 'post' && !is_404()) {
				$post_type = get_post_type_object(get_post_type());
				echo $before . $post_type->labels->singular_name . $after;
			} elseif (is_attachment()) {
				$parent = get_post($post->post_parent);
				$cat = get_the_category($parent->ID);
				$cat = $cat[0];
				//echo get_category_parents($cat, TRUE, ' ' . $delimiter . ' ');
				echo '<a href="' . get_permalink($parent) . '">' . $parent->post_title . '</a>';
				if ($showCurrent == 1)
					echo ' ' . $delimiter . ' ' . $before . get_the_title() . $after;
			} elseif (is_page() && !$post->post_parent) {
				if ($showCurrent == 1)
					echo $before . get_the_title() . $after;
			} elseif (is_page() && $post->post_parent) {
				$parent_id = $post->post_parent;
				$breadcrumbs = array();
				while ($parent_id) {
					$page = get_page($parent_id);
					$breadcrumbs[] = '<a href="' . get_permalink($page->ID) . '">' . get_the_title($page->ID) . '</a>';
					$parent_id = $page->post_parent;
				}
				$breadcrumbs = array_reverse($breadcrumbs);
				for ($i = 0; $i < count($breadcrumbs); $i++) {
					echo $breadcrumbs[$i];
					if ($i != count($breadcrumbs) - 1)
						echo ' ' . $delimiter . ' ';
				}
				if ($showCurrent == 1)
					echo ' ' . $delimiter . ' ' . $before . get_the_title() . $after;
			} elseif (is_tag()) {
				echo $before . 'Posts tagged "' . single_tag_title('', false) . '"' . $after;
			} elseif (is_author()) {
				global $author;
				$userdata = get_userdata($author);
				echo $before . 'Articles posted by ' . $userdata->display_name . $after;
			} elseif (is_404()) {
				echo $before . 'Error 404' . $after;
			}

			if (get_query_var('paged')) {
				if (is_category() || is_day() || is_month() || is_year() || is_search() || is_tag() || is_author())
					echo ' (';
				echo __('Page') . ' ' . get_query_var('paged');
				if (is_category() || is_day() || is_month() || is_year() || is_search() || is_tag() || is_author())
					echo ')';
			}

			echo '</div>';
		}
	}
    
function wash_the_content_filter($content) {
    $pre = chr(226).chr(128);    
    $search = array( '‚Äô','√®','√©','√†','√¥'
	                    );

    	$replace = array( "'","&egrave;","&eacute;","&agrave;","&ocirc;");

    	$string = str_replace($search,$replace,$content);
        //return $content;
        return $string;
}

add_filter( 'the_content', 'wash_the_content_filter' );
add_filter( 'the_title', 'wash_the_content_filter' );


// Add the Style Dropdown Menu to the second row of visual editor buttons
function my_mce_buttons_2($buttons)
{
	array_unshift($buttons, 'styleselect');
	return $buttons;
}
add_filter('mce_buttons_2', 'my_mce_buttons_2');
function my_mce_before_init($init_array)
	{
		// Now we add classes with title and separate them with;
		$style_formats = array(  
		// Each array child is a format with it's own settings
		array(  
			'title' => 'super header',  
			'block' => 'h1',  
			'classes' => 'superheader',
			'wrapper' => false,
			
		)
	);  
	// Insert the array, JSON ENCODED, into 'style_formats'
	$init_array['style_formats'] = json_encode( $style_formats ); 
	return $init_array;
}

add_filter('tiny_mce_before_init', 'my_mce_before_init');
add_editor_style('editor.css');

function order_combined_posts($a,$b){
    $stampA = strtotime($a->post_date);
    $stampB = strtotime($b->post_date);
    if($stampA==$stampB){
        return 0;
    }
    return ($stampA < $stampB) ? 1 : -1;
}
function the_title_trim($title)
{
  $pattern[0] = '/Protected:/';
  $pattern[1] = '/Private:/';
  $replacement[0] = ''; // Enter some text to put in place of Protected:
  $replacement[1] = ''; // Enter some text to put in place of Private:

  return preg_replace($pattern, $replacement, $title);
}
add_filter('the_title', 'the_title_trim');
function akvo_debug_dump($a){
    if(in_array($_SERVER['REMOTE_ADDR'],array('84.80.116.254','77.249.187.100'))){
        echo '<pre>';
        var_dump($a);
        echo '</pre>';
    }
}

add_action('wp','redirect_stuffs', 0);
function redirect_stuffs(){
global $wpdb; 
    if ($wpdb->last_result[0]->post_status == "private" && !is_user_logged_in() ):
        //wp_redirect( wp_login_url( get_permalink($wpdb->last_result[0]->ID) ), 301 );
        //echo get_template();
        include( get_stylesheet_directory() . '/customlogin.php' );
        exit();
        
    endif;
}


//function my_page_template_redirect()
//{
//    global $wpdb; 
//    if( $wpdb->last_result[0]->post_status == "private" && !is_user_logged_in() )
//    {
//        wp_redirect( home_url( '/signup/' ) );
//        exit();
//    }
//}
//add_action( 'template_redirect', 'my_page_template_redirect' );
// end the_breadcrumbs()
	?>