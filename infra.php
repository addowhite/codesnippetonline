<?php
session_start();
require "db.php";
require "template.php";

class Infra {

  public static function check_user_logged_in() {
    return isset($_SESSION["user_id"]) && isset($_SESSION["username"]) && isset($_SESSION["email_address"]);
  }

  public static function get_base_url() {
    return $_SERVER['HTTP_HOST'];
  }

  public static function redirect($destination_path = "") {
    header("Location: http://" . Infra::get_base_url() . "/${destination_path}");
  }

  public static function generate_random_code($length) {
    // Digits, uppercase and lowercase letters but not vowels (this is to prevent accidental offensive language in random codes)
    $valid_characters = "0123456789bcdfghjklmnpqrstvwxyzBCDFGHJKLMNPQRSTVWXYZ";
    $end_index = strlen($valid_characters) - 1;

    $code = "";
    for ($i = 0; $i < $length; $i++) $code .= $valid_characters[mt_rand(0, $end_index)];

    return $code;
  }

  public static function mail($address, $subject, $content) {
    $headers = "From: no-reply@codesnippetonline.com\r\n" .
               "Reply-To: no-reply@codesnippetonline.com\r\n" .
               "Return-Path: no-reply@codesnippetonline.com\r\n" .
               "X-Mailer: PHP/" . phpversion() . "\r\n" .
               "MIME-Version: 1.0\r\n" .
               "Content-Type: text/html; charset=ISO-8859-1\r\n";

    mail($address, $subject, $content, $headers);
  }

  public static function send_account_verification_email($username, $email_address, $verification_code) {
    // URL escape the username so it can be used as a GET parameter
    $escaped_username = rawurlencode($username);

    Infra::mail(
      $email_address,
      "Account activation",
      template("templates/email_account_activation.php", array(
        "username" => "{$username}",
        "activation_link" => "http://" . Infra::get_base_url() . "/processes/activate_account.php?user={$escaped_username}&code={$verification_code}"
      ))
    );
  }

}

?>
