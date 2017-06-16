<?php
require "../infra.php";
if (isset($_POST["submit"])) session_destroy();
Infra::redirect();
?>
