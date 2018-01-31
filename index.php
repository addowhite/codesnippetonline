<?php
require "infra.php";

$snippet_list = "";

// Search by title
$snippets_results = $db->search_snippets(null, null, null, "public", $_GET["q"]);

if (is_array($snippets_results) && !empty($snippets_results)) {
	foreach ($snippets_results as $key => $value)
		$snippet_list .= snippet($value["snippet_id"], $value["title"], $value["content"], $value["language"], "by " . $value["username"], $value["privacy_status"], $value["like_count"], $value["dislike_count"]);
}

echo page("pages/index/content.php", array(
	"snippet_list" => $snippet_list,
	"head"         =>
		'<link rel="stylesheet" type="text/css" href="pages/index/index.css">
		<script src="/ace/ace.js" type="text/javascript" charset="utf-8"></script>
		<script src="pages/index/index.js" type="text/javascript"></script>'
	
));
?>
