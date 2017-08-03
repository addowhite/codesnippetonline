<?php
require "../infra.php";

if (isset($_GET["user"]) && isset($_GET["code"])) {

  $activation_result = $db->activate_account($_GET["user"], $_GET["code"]);

  if ($activation_result == 'success') {
    $_SESSION["email_verified"] = true;
    Infra::redirect("pages/email_verified/email_verified.php");
  } else {
    echo error_page($activation_result);
  }

} else {
  Infra::redirect();
}

?>
