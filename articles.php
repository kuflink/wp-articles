<?php
   /*
   Plugin Name: Custom News Articles
   Plugin URI: http://my-awesomeness-emporium.com
   description: >-
  a plugin to create awesomeness and spread joy
   Version: 1.2
   Author: Mr. Awesome
   Author URI: http://mrtotallyawesome.com
   License: GPL2
   */

global $jal_db_version;
$jal_db_version = '1.0';

register_activation_hook( __FILE__, 'jal_install' );
//register_activation_hook( __FILE__, 'jal_install_data' );
add_action('admin_post_custom_action_hook', 'the_action_hook_callback');

register_deactivation_hook( __FILE__, 'my_plugin_remove_database' );

function my_plugin_remove_database() {
     global $wpdb;
     $table_name = $wpdb->prefix . 'articles';
     $sql = "DROP TABLE IF EXISTS $table_name";
     $wpdb->query($sql);
}   

function the_action_hook_callback() {
	$delete = $_POST["delete"];

	if(isset($delete) && (int)$delete === 1) {
		$articleId = $_POST["article"];

		if(isset($articleId)) {
			$articleId = (int)$articleId;

			global $wpdb;

			$table_name = $wpdb->prefix . 'articles';
		
			$wpdb->delete( 
				$table_name, 
				array( 'id' => $articleId )
			);

			wp_safe_redirect( "/wp-admin/admin.php?page=test-plugin" );
		}
	} else {
	$url = $_POST["url"];
	$title = $_POST["title"];
	$subtitle = $_POST["subtitle"];
	$description = $_POST["description"];
	$logo = $_POST["logo"];

	if(
		isset($url) &&
		isset($title) &&
		isset($description) &&
		isset($logo)
	) {
		global $wpdb;
		
		$table_name = $wpdb->prefix . 'articles';

		$edit = $_POST["edit"];

		$data = array( 
			'time' => current_time( 'mysql' ), 
			'logo' => $logo,
			'title' => $title,
			'subtitle' => $subtitle,
			'description' => $description,
			'url' => $url
		);

		if(isset($edit) && (int)$edit === 1) {
			$articleId = $_POST["article"];

			if(isset($articleId)) {
				$articleId = (int)$articleId;

				$wpdb->update( 
					$table_name, 
					$data,
					array( 'id' => $articleId )
				);
			}
		} else {
			$wpdb->insert( 
				$table_name, 
				$data
			);
		}
		


		wp_safe_redirect( "/wp-admin/admin.php?page=test-plugin" );
	} else {
		var_dump($url, $title, $subtitle, $description, $logo);
	}
	}
}

function jal_install() {
	global $wpdb;
	global $jal_db_version;

	$table_name = $wpdb->prefix . 'articles';
	
	$charset_collate = $wpdb->get_charset_collate();

	$sql = "CREATE TABLE $table_name (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
		logo varchar(500) NOT NULL,
		title varchar(255) NOT NULL,
		subtitle varchar(255) NOT NULL,
		description varchar(500) NOT NULL,
		url varchar(500) NOT NULL,
		PRIMARY KEY (id)
	) $charset_collate;";

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $sql );

	add_option( 'jal_db_version', $jal_db_version );
}

add_action('admin_menu', 'test_plugin_setup_menu');
 
function test_plugin_setup_menu(){
	add_menu_page( 'Articles', 'Articles', 'manage_options', 'test-plugin', 'list_articles');

	add_submenu_page('test-plugin', 'Create Article',
	'Create Article',
	'manage_options',
	'my-custom-submenu-page',
	'create_article');

	add_submenu_page(null, 'Edit Article',
	'Create Article',
	'manage_options',
	'edit-article',
	'edit_article');
}

function get_local_file_contents( $file_path ) {
	ob_start();
	include $file_path;
	$contents = ob_get_clean();

	return $contents;
}

function list_articles() {
	echo get_local_file_contents("articles-template.php");
} 

function edit_article() {
	echo get_local_file_contents("article-template.php");
} 

function create_article() {
	echo get_local_file_contents("article-template.php");
}

function get_article_field( $article, $field ) {
	if(isset($article)) {
	  return $article->{$field};
	}
	return "";
  }

function get_article_card_style() {
	wp_register_style( 'article-style', plugins_url( '/css/article-card.css', __FILE__ ), array(), '1.0.0', 'all' );
}

add_action('wp_enqueue_scripts', 'callback_for_setting_up_scripts');
function callback_for_setting_up_scripts() {
    wp_register_style( 'namespace', plugins_url( '/css/article-card.css', __FILE__ ));
    wp_enqueue_style( 'namespace' );
}

function articles_shortcode($atts, $content = null) 
{
	global $wpdb;

	$myrows = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}articles" );
	  $amountOfRows = count($myrows);
	  
	$containerClass = "";

	if(isset($atts['containerclass'])) {
		$containerClass = $atts['containerclass'];
	}

	$html = "<div class=\"${containerClass}\"><div class=\"article-grid\">";

	for ($i = 1; $i <= $amountOfRows; $i++) {
		$row = $myrows[$i - 1];
		$logo = get_article_field($row, 'logo');
		$title = wp_html_excerpt( get_article_field($row, 'title'), 81, "...");
		$subtitle = get_article_field($row, 'subtitle');
		$description = wp_html_excerpt(get_article_field($row, 'description'), 142, "...");
		$url = get_article_field($row, 'url');
		$active = "";

		if($i <= 6) {
			$active = "active";
		}

		$html .= "
		<div class=\"article-grid__item ${active}\">
			<a href=\"${url}\" target=\"_blank\" class=\"card--article\">
				<div class=\"card__title\">
					<h3>${title}</h3><p>date goes here</p>";
					//<p>${subtitle}</p>
		$html .= "</div>
				<div class=\"card__media\">
					<img src=\"${logo}\">
				</div>
				<div class=\"card__body\">
					<p>${description}</p>
				</div>
				<div class=\"card__actions\">
					<div class=\"card__actions-button\">Read Article</div>
					<div class=\"card__actions-icon\">
						<svg xmlns=\"http://www.w3.org/2000/svg\" width=\"24\" height=\"24\" viewBox=\"0 0 24 24\">
							<path d=\"M0 0h24v24H0z\" fill=\"none\"/>
							<path d=\"M18 16.08c-.76 0-1.44.3-1.96.77L8.91 12.7c.05-.23.09-.46.09-.7s-.04-.47-.09-.7l7.05-4.11c.54.5 1.25.81 2.04.81 1.66 0 3-1.34 3-3s-1.34-3-3-3-3 1.34-3 3c0 .24.04.47.09.7L8.04 9.81C7.5 9.31 6.79 9 6 9c-1.66 0-3 1.34-3 3s1.34 3 3 3c.79 0 1.5-.31 2.04-.81l7.12 4.16c-.05.21-.08.43-.08.65 0 1.61 1.31 2.92 2.92 2.92 1.61 0 2.92-1.31 2.92-2.92s-1.31-2.92-2.92-2.92z\"/>
						</svg>
					</div>
				</div>
			</a>
		</div>
		";
	}
	$html .= "</div>";

	if($amountOfRows > 6) {
		$amountOfPages = ceil($amountOfRows / 6);
		$html .= "<div class=\"article-paginator\">";

		for ($i = 1; $i <= $amountOfPages; $i++) {
			$isFirst = $i === 0;
			$active = "";
			if($isFirst) $active = "active";
			$html .= "<div class=\"article-paginator__item ${active}\">${i}</div>";
		}

		$html .= "</div>
		<script>
		var currentPage = 0;

		jQuery('.article-paginator__item').click(function(e) {
			e.preventDefault();
			var item = jQuery(this),
				page = item.text(),
				start = 6 * (jQuery(this).text() - 1),
				end   = start + 6,
				makeActive = jQuery(jQuery('.article-grid__item').splice(start, end));

			console.log(start, end, makeActive);

			jQuery('.article-grid__item').removeClass('active');
			makeActive.addClass('active');

			jQuery('.article-paginator__item').removeClass('active');
			item.addClass('active');
		});

		</script>
		";
	}

	$html .= "</div>";

	return $html;
}

add_shortcode('articles', 'articles_shortcode');

?>
