<?php
require "../../infra.php";

$params = array("head" => '<link rel="stylesheet" type="text/css" href="edit_post.css">');
$save_button_text = "Post";

if (isset($_GET["post_id"]) && $_GET["post_id"] != -1) {
  $params .= array("post_title" => "Title here...");
  $save_button_text = "Save";
}

echo page("content.php", $params + array("save_button_text" => "{$save_button_text}"));
?>
