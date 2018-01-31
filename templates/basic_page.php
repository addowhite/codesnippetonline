<!DOCTYPE html>
<html>

<head>
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
	<title>Code Snippets Online</title>
	<link rel="stylesheet" type="text/css" href="../../font-awesome/css/font-awesome.css">
	<link rel="stylesheet" type="text/css" href="../../common.css">
	<?=$head?>
</head>

<body>
	<div class="page-wrapper">
		<div class="header-wrapper">
			<div class="header">
				<table>
					<tr class="mobile-logo-container">
						<td><a href="<?='http://' . Infra::get_base_url()?>"><div class="logo">CodeSnippetOnline</div></a></td>
					</tr>
					<tr>
						<td class="logo-container"><a class="logo" href="<?='http://' . Infra::get_base_url()?>">CodeSnippetOnline</a></td>
						<td class="button-container">
							<?=Infra::check_user_logged_in()
								? button("button_logout", "Sign out", "/processes/logout_user.php")
									. button("button_new_snippet", "Public Snippets", "http://" . Infra::get_base_url())
									. button("button_my_snippets", "My Snippets", "/pages/my_snippets/my_snippets.php")
									. button("button_new_snippet", "Create snippet", "/pages/snippet/snippet.php")
								: button("button_login", "Sign in", "/pages/login/login.php")
							?>
						</td>
					</tr>
				</table>
			</div>
		</div>
		<div class="content">
			<?=$search?>
			<?=$content?>
		</div>
		<div class="footer">
			<p>Code Snippet Online © 2017 Adorjan White; Released under MIT License.</p>
		</div>
	</div>
</body>

</html>
