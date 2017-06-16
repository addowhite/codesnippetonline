<?php
require "../infra.php";
if (isset($_POST["submit"])) {
  $result = $db->create_account($_POST["email_address"], $_POST["username"], $_POST["password"], $_POST["password-confirm"]);

  if ($result == "success") {
    $_SESSION["email_address"] = $_POST["email_address"];
    $_SESSION["account_created"] = true;
    Infra::redirect("pages/account_created/account_created.php");
  } else {
    echo $result;
  }

}

?>
