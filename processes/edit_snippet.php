<?php
include "../infra.php";

if (isset($_POST["submit"])) {
  $db->call("edit_snippet", array(
    "snippet_id"     => $_SESSION["snippet_id"],
    "user_id"        => $_SESSION["user_id"],
    "language"       => $_POST["language"],
    "privacy_status" => $_POST["privacy"],
    "title"          => Infra::html_escape($_POST["title"]),
    "content"        => Infra::html_escape($_POST["content"])
  ));
}

Infra::redirect();
?>
