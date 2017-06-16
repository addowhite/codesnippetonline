<?php
require "../../infra.php";

// Check we were redirected here from the account creation page
if (isset($_SESSION["login_attempt"])) {
  echo page("content.php", array("head" => '<link rel="stylesheet" type="text/css" href="email_not_verified.css">'));
  unset($_SESSION["login_attempt"]);
} else {
  Infra::redirect();
}

?>
