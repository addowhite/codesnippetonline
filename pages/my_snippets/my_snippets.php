<?php
require "../../infra.php";

if (Infra::check_user_logged_in()) {

	$snippet_list = "";

	// Search by title
	$snippets_results = $db->search_snippets(null, Infra::get_user_id(), null, null, $_GET["q"]);

	if (is_array($snippets_results) && !empty($snippets_results)) {
		foreach ($snippets_results as $key => $value)
			$snippet_list .= snippet($value["snippet_id"], $value["title"], $value["content"], $value["language"], $value["privacy_status"], $value["privacy_status"], $value["like_count"], $value["dislike_count"]);
	}

	echo page("content.php", array(
		"snippet_list" => $snippet_list,
		"head" =>
			'<link rel="stylesheet" type="text/css" href="my_snippets.css">
			<script src="/ace/ace.js" type="text/javascript" charset="utf-8"></script>
			<script src="my_snippets.js" type="text/javascript"></script>'
	));
	
}

Infra::redirect();
?>
