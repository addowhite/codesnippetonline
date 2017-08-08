<?php
include "../infra.php";

if (isset($_GET["id"]) && Infra::check_user_logged_in()) {
  $row = $db->call("like_snippet", array(
    "snippet_id" => $_GET["id"],
    "user_id"    => $_SESSION["user_id"]
  ))[0];

  echo $row[0] . '|' . $row[1];
}
?>
