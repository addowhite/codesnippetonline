<?php
require "../../infra.php";

if (isset($_GET["id"]) && $_GET["id"] != -1) {
  $save_button_text = "Save";
  $_SESSION["snippet_id"] = $_GET["id"];

  // Get the details of the snippet
  $snippet_info = $db->get_snippet_info($_SESSION["snippet_id"]);

  // Only allow editing if the user is signed in as the original author of the snippet
  $allow_edit = Infra::check_user_logged_in() && $snippet_info['user_id'] == $_SESSION['user_id'];
  
  // If this is a private snippet and the user is either not signed in or is not the original author
  if ($snippet_info['privacy_status'] != 'public' && !$allow_edit) {
    // Redirect the user to the homepage
    Infra::redirect();
    exit;
  }
} else {
  if (!Infra::check_user_logged_in()) {
    Infra::redirect();
    exit;
  }

  $save_button_text = "Post";
  $_SESSION["snippet_id"] = -1;
}

echo page("content.php", array(
  "head"                   => '<link rel="stylesheet" type="text/css" href="snippet.css">',
  "save_button_text"       => "{$save_button_text}",
  "snippet_title"          => "{$snippet_info['title']}",
  "snippet_content"        => "{$snippet_info['content']}",
  "snippet_language"       => "{$snippet_info['language']}",
  "snippet_privacy_status" => "{$snippet_info['privacy_status']}",
  "allow_edit"             => "{$allow_edit}"
));
?>
