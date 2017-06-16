<?php
require "../infra.php";

if (isset($_GET["user"])
  && isset($_GET["code"])
  && $db->activate_account($_GET["user"], $_GET["code"])) {

  $_SESSION["email_verified"] = true;
  Infra::redirect("pages/email_verified/email_verified.php");

} else {

  Infra::redirect();

}

?>
