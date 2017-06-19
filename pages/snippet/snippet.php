<?php
require "../../infra.php";

if (isset($_GET["id"]) && $_GET["id"] != -1) {
  $save_button_text = "Save";
  $_SESSION["snippet_id"] = $_GET["id"];
} else {
  $save_button_text = "Post";
  $_SESSION["snippet_id"] = -1;
}

echo page("content.php", array("head" => '<link rel="stylesheet" type="text/css" href="snippet.css">', "save_button_text" => "{$save_button_text}"));
?>
