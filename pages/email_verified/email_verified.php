<?php
require "../../infra.php";

// Check we were redirected here from the email verification process
if (isset($_SESSION["email_verified"])) {
  echo page("content.php", array("head" => '<link rel="stylesheet" type="text/css" href="email_verified.css">', "username" => "{$_SESSION["username"]}"));
  unset($_SESSION["email_verified"]);
} else {
  Infra::redirect();
}

?>
