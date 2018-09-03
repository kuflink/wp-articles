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
.container > div {
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
.box input[type=text], .box input[type=url], .box textarea {
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
				<label>* Article description</label>
				<textarea class="js-article-description" name="description" required><?php echo get_article_field($article, 'description'); ?></textarea>
			</div>
			<div>
				<label>* Logo URL</label>
				<input class="js-article-logo" name="logo" type="text" value="<?php echo get_article_field($article, 'logo'); ?>"placeholder="https://cdn.example.com/images/image.jpg" required/>
			</div>
			<input class="button button-primary button-large" type="submit" name="submit" value="<?php echo (isset($article)) ? "Update" : "Create"; ?> Article"/>
		</form>
	</div>
	<div>
		<h2 style="margin-top: 0;">Article Preview</h2>
		<div class="box--article">
			<div class="box__logo">
				<img class="js-changeable-logo" src="<?php echo get_article_field($article, 'logo'); ?>"/>
			</div>
			<div class="box__body">
				<h3 class="js-changeable-title"><?php echo get_article_field($article, 'title'); ?></h3>
				<p class="js-changeable-subtitle box__body-subtitle"><?php echo get_article_field($article, 'subtitle'); ?></p>
                <p class="js-changeable-description box__body-description"><?php echo get_article_field($article, 'description'); ?></p>  
			</div>
			<div class="box__footer">
				<div>
					<a href="#">Read Article</a>
				</div>
				<div>
					<span>28/08/2018</span>
				</div>
			</div>
		</div>
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
</script>