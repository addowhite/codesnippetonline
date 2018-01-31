<?php

// Start or resume the session
session_start();

// Include all other infrastructure files
require "db.php";
require "template.php";

/**
 * A class to provide commonly needed functions
 */
class Infra {

  // An array of CodeSnippetOnline programming language names
  public static $cso_languages = NULL;

  // An array of AceEditor programming language names
  public static $ace_languages = NULL;

  /**
   * Initialise all static data
   */
  public static function init() {

    // Create the array of CodeSnippetOnline programming language names
    self::$cso_languages = array(
      "c++",
      "c#",
      "c",
      "java",
      "javascript",
      "python",
      "lua",
      "php",
      "html"
    );

    // Create the array of AceEditor programming language names
    self::$ace_languages = array(
      "c_cpp",
      "csharp",
      "c_cpp",
      "java",
      "javascript",
      "python",
      "lua",
      "php",
      "html"
    );

  }
  
  /**
   * Get's the ID of the currently signed-in user.
   * @return String The ID of the signed-in user.
   */
  public static function get_user_id() {
    return $_SESSION["user_id"];
  }

  /**
   * Check if a user is logged in.
   * @return bool True if the user is currently logged in. False otherwise.
   */
  public static function check_user_logged_in() {
    return isset($_SESSION["user_id"]) && isset($_SESSION["username"]) && isset($_SESSION["email_address"]);
  }

  /**
   * Get the base url for the current server
   * @return string Does not include the initial 'http://www.' e.g. 'codesnippetonline.co.uk'
   */
  public static function get_base_url() {
    return $_SERVER['HTTP_HOST'];
  }

  /**
   * Send the user to another page
   * @param string $destination_path (optional) The path to the destination page, starting from (and not including) the base url.
   */
  public static function redirect($destination_path = "") {
    header("Location: http://" . Infra::get_base_url() . "/${destination_path}");
  }

  /**
   * Generates a random string of characters of the specified length
   * @param int $length The number of characters that should be in the generated code
   * @return string The generated string of characters
   */
  public static function generate_random_code($length) {
    // Digits, uppercase and lowercase letters but not vowels (this is to prevent accidental offensive language in random codes)
    $valid_characters = "0123456789bcdfghjklmnpqrstvwxyzBCDFGHJKLMNPQRSTVWXYZ";
    $end_index = strlen($valid_characters) - 1;

    $code = "";
    for ($i = 0; $i < $length; $i++) $code .= $valid_characters[mt_rand(0, $end_index)];

    return $code;
  }

  /**
   * Send an email.
   * @param string $address The destination email address
   * @param string $subject The subject of the email
   * @param string $content The body of the email, may contain markup
   */
  public static function mail($address, $subject, $content) {
    $headers = "From: no-reply@codesnippetonline.com\r\n" .
               "Reply-To: no-reply@codesnippetonline.com\r\n" .
               "Return-Path: no-reply@codesnippetonline.com\r\n" .
               "X-Mailer: PHP/" . phpversion() . "\r\n" .
               "MIME-Version: 1.0\r\n" .
               "Content-Type: text/html; charset=ISO-8859-1\r\n";

    mail($address, $subject, $content, $headers);
  }

  /**
   * Send an email with a link to confirm a user's email address
   * @param string username The user's chosen display name
   * @param string $email_address The email address of the user
   * @param string $verification_code The randomly generated code for verifying the identity of the user
   */
  public static function send_account_verification_email($username, $email_address, $verification_code) {
    // URL escape the username so it can be used as a GET parameter
    $escaped_username = rawurlencode($username);

    Infra::mail(
      // Recipient email address
      $email_address,

      // Email subject
      "Account activation",

      // Use template system create a html email body
      template("templates/email_account_activation.php", array(
        "username" => "{$username}",
        "activation_link" => "http://" . Infra::get_base_url() . "/processes/activate_account.php?user={$escaped_username}&code={$verification_code}"
      ))
    );
  }

  /**
   * From a given CodeSnippetOnline programming language name, return the corresponding AceEditor programming language name
   * @param string $cso_language_name The name of the programming language as used by Ace Editor
   * @return string the name of the programming language as used by CodeSnippetOnline
   */
  public static function get_ace_language_name($cso_language_name) {
    return self::$ace_languages[array_search($cso_language_name, self::$cso_languages)];
  }

  /**
   * Escape a string such that it can be embedded as text in a webpage
   * @param string $unescaped_string The raw string
   * @param string The escaped string
   */
  public static function html_escape($unescaped_string) {
    return str_replace(">", "&gt;", str_replace("<", "&lt;", $unescaped_string));
  }

}

// Initialise all static members of the class
Infra::init();

?>
