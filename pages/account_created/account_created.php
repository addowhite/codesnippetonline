<?php
require "../../infra.php";

// Check we were redirected here from the account creation page
if (isset($_SESSION["account_created"])) {
  echo page("content.php", array("head" => '<link rel="stylesheet" type="text/css" href="account_created.css">', "email_address" => "{$_SESSION["email_address"]}"));
  unset($_SESSION["account_created"]);
} else {
  Infra::redirect();
}

?>
