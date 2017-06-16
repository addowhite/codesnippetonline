<?php
require "infra.php";

if (Infra::check_user_logged_in()) {
  echo page("pages/index/content_private.php", array("username" => "{$_SESSION["username"]}"));
} else {
  echo page("pages/index/content_public.php", array("message" => "You must login to create posts."));
}
?>
