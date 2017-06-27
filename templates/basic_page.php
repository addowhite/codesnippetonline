<!DOCTYPE html>
<html>

<head>
	<title>Code Snippets Online</title>
	<link rel="stylesheet" type="text/css" href="../../common.css">
	<?=$head?>
</head>

<body>
	<div class="page page-header">
		<div class="header-container">
			<div class="title-container">CodeSnippetOnline</div>
			<div class="button-container">
				<?=Infra::check_user_logged_in()
					? button("button-create-account", "New snippet", "/pages/snippet/snippet.php") . button("button-logout", "Sign out", "/processes/logout_user.php")
					: button("button-login", "Sign in", "/pages/login/login.php") . button("button-create-account", "Sign up", "/pages/create_account/create_account.php")?>
			</div>
		</div>
	</div>
	<div class="scrollable-content-pane">
		<div class="page page-content">
			<?=$content?>
		</div>
		<div class="page page-footer">
			<div class="footer-container">
				Code Snippet Online © 2017 Adorjan White; Released under MIT License.
			</div>
		</div>
	</div>
</body>

</html>
