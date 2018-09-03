<?php
	global $wpdb;

	$myrows = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}articles" );
  $amountOfRows = count($myrows);
?>

<style>
.container {
	margin: 1em 0;
	max-width: 1460px;
}
.list__item {
	background: white;
	padding: 1em;
	display: flex;
	align-items: center;
	border-bottom: 1px solid #eaeaea;
}

.list__item-logo {
	width: 20em;
}

.list__item-logo img {
	max-width: 100%;
}

.list__item-date {
	text-align: right;
	font-size: 1.3em;
}

.list__item-title {
	flex: 1;
	padding-left: 2em;
}

.list__item-title .custom-button {
	padding: .5em 1em;
	display: inline-block;
	background: #f4f4f4;
	text-decoration: none;
	text-transform: uppercase;
	border: 1px solid #dedede;
}

.list__item-date b {
	display: block;
}

.overlay {
    top: 0;
    left: 0;
    width: 100%;
    height: 100vh;
    background: #00000099;
    z-index: 100;
    position: fixed;
    display: none;
    align-items: center;
	justify-content: center;
}

.overlay.active {
	display: flex;
}

.modal {
    background: white;
}

.modal h3 {
    margin: 0;
    flex: 1;
}

.modal__title {
    padding: 1em;
	display: flex;
    border-bottom: 1px solid #eee;
    align-items: center;
}

.modal__body {
    padding: 1em;
    text-align: center;
}

.modal__footer {
    padding: 1em;
    text-align: right;
}

.modal__footer form {
    display: inline;
    margin-right: .5em;
}

#wpbody {
    position: unset;
}

.container__title {
	display: flex;
	align-items: center;	
	padding: 0 1em;
}

.container__title h1 {
	flex: 1;
}
</style>

<div class="container">  
	<div class="container__title">
		<h1>Articles</h1>
		<a class="button button-primary button-large" href="/wp-admin/admin.php?page=my-custom-submenu-page">Create Article</a>
	</div>
<?php
	for ($i = 1; $i <= $amountOfRows; $i++) {
    $row = $myrows[$i - 1];
?>
	<div class="list__item">
		<div class="list__item-logo">
			<a href="/wp-admin/admin.php?page=edit-article&article=<?php echo $row->id; ?>"><img src="<?php echo $row->logo; ?>"/></a>
		</div>
		<div class="list__item-title">
			<h3><a href="/wp-admin/admin.php?page=edit-article&article=<?php echo $row->id; ?>"><?php echo $row->title; ?></a></h3>
			<a class="custom-button" 
				href="/wp-admin/admin.php?page=edit-article&article=<?php echo $row->id; ?>">
				Edit Article
			</a>
			<a class="custom-button js-delete-button"
				data-article-id="<?php echo $row->id; ?>"
				data-article-title="<?php echo $row->title; ?>"
				href="#"
				style="color: red;">
				Delete Article
			</a>
		</div>
		<div class="list__item-date">
			<b>Created</b> <?php echo $row->time; ?>
		</div>
	</div>
	<?php } ?>

	<div class="overlay">
		<div class="modal">
			<div class="modal__title">
				<h3>Delete Article</h3>
				<button class="js-overlay-close">Close</button>
			</div>
			<div class="modal__body">
				<p>Are you sure you would like to delete article:</p>
				<b class="js-delete-article-title" style="font-size: 1.4em;">mcianuehf</b>
			</div>
			<div class="modal__footer">
				<form action="/wp-admin/admin-post.php" method="post">
					<input type="hidden" name="action" value="custom_action_hook"/>
					<input type="hidden" name="custom_nonce" value=""/>
					<input type="hidden" name="delete" value="1"/>
					<input class="js-delete-article-id" type="hidden" name="article" value="1"/>
					<button type="submit">Yes</button>
				</form>
				<a href="#" class="js-modal-no">No</a>
			</div>
		</div>
	</div>

</div>

<p>Plugin created by Michael Wilson @ Kuflink 2018</p>

<script>
jQuery(".js-delete-button").click(function(e) {
	e.preventDefault();

	var articleId = jQuery(this).data('article-id'),
		articleTitle = jQuery(this).data('article-title');

	jQuery(".overlay").addClass("active");
	jQuery(".js-delete-article-id").val(articleId);
	jQuery(".js-delete-article-title").text(articleTitle);
});

jQuery(".js-overlay-close, .js-modal-no").click(function(e) {
	e.preventDefault();

	jQuery(".overlay").removeClass("active");
});

</script>