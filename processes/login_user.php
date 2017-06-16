<?php
require "../infra.php";
if (isset($_POST["submit"])) {

  if ($db->login_user($_POST["email_address"], $_POST["password"])) {
    Infra::redirect();
  } else {
    echo "Username or password incorrect.";
  }

}

?>
