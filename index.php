<?php
require "infra.php";

$snippet_list = "";

// Search by title
$snippets_results = $db->search_snippets(null, null, null, "public", $_GET["q"]);

if (is_array($snippets_results) && !empty($snippets_results)) {
	foreach ($snippets_results as $key => $value)
		$snippet_list .= snippet($value["snippet_id"], $value["title"], $value["content"], $value["language"], $value["username"]);
}

echo page("pages/index/content.php", array(
	"head"         => '<link rel="stylesheet" type="text/css" href="pages/index/index.css">',
	"snippet_list" => $snippet_list
));
?>
