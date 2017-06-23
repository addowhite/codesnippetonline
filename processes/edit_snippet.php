<?php
include "../infra.php";

if (isset($_POST["submit"])) {
  $db->call("edit_snippet", array(
    "snippet_id"     => $_SESSION["snippet_id"],
    "user_id"        => $_SESSION["user_id"],
    "language"       => $_POST["language"],
    "privacy_status" => $_POST["privacy"],
    "title"          => $_POST["title"],
    "content"        => $_POST["content"]
  ));
}

Infra::redirect();
?>
