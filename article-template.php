<?php

global $wpdb;


if(isset($_GET['article'])) {
    $article_id = $_GET['article'];

    $query = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}articles WHERE id = {$article_id} LIMIT 1" );

    if(count($query) > 0 && isset($query[0])) {
        $article = $query[0];
    } else {
        $article = null;
    }
} else {
    $article = null;
}

?>

<style>
.container {
	margin: 1em 0;
	max-width: 1460px;
	display: flex;
	align-items: flex-start;
}
.container > div:first-child {
    flex: 1;
}
.container > .box:first-child {
    margin-right: 1em;
}
.box {
	background: white;
	padding: 1em;
	box-shadow: 4px 3px 11px #e4e4e4;
}
.box label {
	display: block;
	font-weight: bold;
	margin-bottom: 0.5em;
}
.box input[type=text], .box input[type=url], .box input[type=date], .box textarea {
	width: 100%;
	padding: 0.7em;
	font-size: 1.2em;
	border-radius: 4px;
	margin-bottom: 0.5em;
}
.input-article-title {
	font-size: 1.6em;
}
.box textarea {
	min-height: 8em;
}
.box .button {
	margin-top: 1em;
}
.box h2 {
	margin-top: 0;
}

.box--article {
	background: white;
	box-shadow: 4px 3px 11px #e4e4e4;
	cursor: pointer;
}
.box--article .box__logo {
	padding: 2em 1em;
}
.box--article .box__logo img {
	max-width: 40%;
}
.box--article .box__body {
	padding: 1em;
	padding-top: 0;
}
.box--article .box__body h3 {
	margin-top: 0;
	font-size: 1.7em;
	font-weight: normal;
}

.box--article .box__body-description {
	font-size: 1em;
}

.box--article .box__body-subtitle {
	font-size: 1.25em;
}
.box__footer {
    padding: 1em;
    background: #f4f4f4;
    display: flex;
    align-items: center;
}

.box__footer > div {
    flex: 1;
}

.box__footer > div:last-child {
	text-align: right;
	font-size: 1.4em;
}

.box__footer div a {
	padding: 1em;
	display: block;
	text-transform: uppercase;
	text-decoration: none;
	font-weight: bold;
}
</style>
<h1><?php echo (isset($article)) ? "Update" : "Create"; ?> Article</h1>
<div class="container">
	<div class="box">
		<h2>Article Details</h2>
		<form action="/wp-admin/admin-post.php" method="post">
			<input type="hidden" name="action" value="custom_action_hook"/>
			<input type="hidden" name="custom_nonce" value=""/>
			<input type="hidden" name="edit" value="<?php echo (isset($article)) ? 1 : 0; ?>"/>
			<input type="hidden" name="article" value="<?php echo get_article_field($article, 'id'); ?>"/>

			<div>
				<label>* Article URL</label>
				<input class="js-article-url" name="url" type="url" value="<?php echo get_article_field($article, 'url'); ?>" placeholder="https://google.com" required/>
			</div>
			<div>
				<label>* Article title</label>
                <input class="js-article-title" name="title" type="text" value="<?php echo get_article_field($article, 'title'); ?>" class="input-article-title" placeholder="The news article title" required/>
			</div>
			<div>
				<label>Article sub title</label>
				<input class="js-article-subtitle" name="subtitle" type="text" value="<?php echo get_article_field($article, 'subtitle'); ?>" placeholder="The news article sub title"/>
			</div>
			<div>
				<label>Custom Date</label>
				<input class="js-article-custom-date" name="custom_date" type="date" value="<?php echo get_article_field($article, 'custom_date'); ?>"/>
			</div>
			<div>
				<label>* Article description</label>
				<textarea class="js-article-description" name="description" required><?php echo get_article_field($article, 'description'); ?></textarea>
			</div>
			<div>
				<label>* Image Background Color</label>
				<input type="color" name="background" value="<?php echo get_article_field($article, 'background'); ?>" />
			</div>
			<div>
				<label>* Image URL</label>
				<input class="js-article-logo" name="logo" type="text" value="<?php echo get_article_field($article, 'logo'); ?>" placeholder="https://cdn.example.com/images/image.jpg" required/>
			</div>
			<input class="button button-primary button-large" type="submit" name="submit" value="<?php echo (isset($article)) ? "Update" : "Create"; ?> Article"/>
		</form>
	</div>
	<div style="width: 26em;">
		<h2 style="margin-top: 0;">Article Preview</h2>

<?php
	$html = "";
	$logo = get_article_field($article, 'logo');
	$title = wp_html_excerpt( get_article_field($article, 'title'), 76, "...");
	$subtitle = get_article_field($article, 'subtitle');
	$description = wp_html_excerpt(get_article_field($article, 'description'), 142, "...");
	$url = get_article_field($article, 'url');
	$backgroundColor = get_article_field($article, 'background');
	$time = date('F Y', strtotime(get_article_field($article, 'time')));
	$custom_date = get_article_field($article, 'custom_date');
	$sub = $time;

	$html .= "
		<div href=\"${url}\" target=\"_blank\" class=\"card--article\">
			<div class=\"card__title\">
				<h3 class=\"js-changeable-title\">${title}</h3>";
		
		if($subtitle !== '') $sub = $subtitle;

		$html .= "<p class=\"js-changeable-subtitle\">${sub}</p>";

		$html .= "</div>
				<div class=\"card__media\" style=\"background: ${backgroundColor}\">
					<img class=\"js-changeable-logo\" src=\"${logo}\">
				</div>
				<div class=\"card__body\">
					<p class=\"js-changeable-description\">${description}</p>
				</div>
				<div class=\"card__actions\">
					<div class=\"card__actions-button\">Read Article</div>
					<!-- <div class=\"card__actions-icon\">
						<svg xmlns=\"http://www.w3.org/2000/svg\" width=\"24\" height=\"24\" viewBox=\"0 0 24 24\">
							<path d=\"M0 0h24v24H0z\" fill=\"none\"/>
							<path d=\"M18 16.08c-.76 0-1.44.3-1.96.77L8.91 12.7c.05-.23.09-.46.09-.7s-.04-.47-.09-.7l7.05-4.11c.54.5 1.25.81 2.04.81 1.66 0 3-1.34 3-3s-1.34-3-3-3-3 1.34-3 3c0 .24.04.47.09.7L8.04 9.81C7.5 9.31 6.79 9 6 9c-1.66 0-3 1.34-3 3s1.34 3 3 3c.79 0 1.5-.31 2.04-.81l7.12 4.16c-.05.21-.08.43-.08.65 0 1.61 1.31 2.92 2.92 2.92 1.61 0 2.92-1.31 2.92-2.92s-1.31-2.92-2.92-2.92z\"/>
						</svg>
					</div>-->
				</div>
			</div>
		";

		echo $html;
?>
		<p>NOTE: font will be different based on your theme</p>
	</div>

</div>
<p>Plugin created by Michael Wilson @ Kuflink 2018</p>
<script>
jQuery(".js-article-title").on('input', function() {
	jQuery(".js-changeable-title").text(jQuery(this).val());
});

jQuery(".js-article-subtitle").on('input', function() {
	jQuery(".js-changeable-subtitle").text(jQuery(this).val());
});

jQuery(".js-article-description").on('input', function() {
	jQuery(".js-changeable-description").text(jQuery(this).val());
});

jQuery(".js-article-logo").on('input', function() {
	jQuery(".js-changeable-logo").attr('src', jQuery(this).val());
});

jQuery(".js-article-custom-date").on('input', function() {
	console.log(jQuery(this).val());
});
</script>