<?php
require "infra.php";

$get_snippets_results = $db->call("get_snippet_list", array(
	"user_id"          => "-1",
	"snippet_language" => "-1",
	"privacy_status"   => "public",
	"title"            => "-1"
));

$snippet_list = "";

if (is_array($get_snippets_results) && !empty($get_snippets_results)) {
	foreach ($get_snippets_results as $key => $value) {
		$snippet_list .= snippet($value["snippet_id"], $value["title"], $value["content"], $value["language"], $value["username"]);
	}
}

echo page("pages/index/content.php", array(
	"head"         => '<link rel="stylesheet" type="text/css" href="pages/index/index.css">',
	"snippet_list" => $snippet_list
));
?>
