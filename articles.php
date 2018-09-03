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
		isset($subtitle) &&
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
		logo varchar(255) NOT NULL,
		title varchar(255) NOT NULL,
		subtitle varchar(255) NOT NULL,
		description varchar(255) NOT NULL,
		url varchar(55) NOT NULL,
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

	for ($i = 1; $i < $amountOfRows; $i++) {
		$logo = get_article_field($myrows[$i], 'logo');
		$title = get_article_field($myrows[$i], 'title');
		$subtitle = get_article_field($myrows[$i], 'subtitle');
		$description = get_article_field($myrows[$i], 'description');
		$url = get_article_field($myrows[$i], 'url');

		$html .= "
		<div class=\"article-grid__item\">
			<a href=\"${url}\" target=\"_blank\" class=\"box--article\">
				<div class=\"box__logo\">
					<img src=\"${logo}\"/>
				</div>
				<div class=\"box__body\">
					<h3>${title}</h3>
					<p class=\"box__body-subtitle\">${subtitle}</p>
					<p class=\"box__body-description\">${description}</p>  
				</div>
				<div class=\"box__footer\">
					<div>
						<button>Read Article</button>
					</div>
					<div>
						<span>28/08/2018</span>
					</div>
				</div>
			</a>
		</div>
		";
	}

	if ($amountOfRows % 2 == 0) {
		$html .= "<div class=\"article-grid__item\"></div>";
	}

	$html .= "</div></div>";

	echo $html;
}

add_shortcode('articles', 'articles_shortcode');

?>
