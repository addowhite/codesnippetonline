<?php

DEFINE("DB_USER", "1543834_login");
DEFINE("DB_PASSWORD", "LITTLEfriend9895");
DEFINE("DB_HOST", "pdb18.freehostingeu.com");
DEFINE("DB_NAME", "1543834_login");

$db = new DB();

class DB {
  protected $connection;

  public function __construct() {
    $this->connection = @mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME)
      OR die("Failed to connect to MySQL: " . mysqli_connect_error());
  }

  public function __destruct() {
    mysqli_close($this->connection);
  }

  public function create_account($email_address, $username, $password, $password_confirm) {
    $result = "An error occurred when creating the account. Account could not be created.";

    $email_address    = trim($email_address);
    $username         = trim($username);
    $password         = trim($password);
    $password_confirm = trim($password_confirm);

    if (!empty($email_address)
      && !empty($username)
      && !empty($password)
      && !empty($password_confirm)
      && $password == $password_confirm) {

      require("../PBKDF2/password_hash.php");
      $password_hash = PasswordStorage::create_hash($password);

      $email_address = mysqli_escape_string($this->connection, $email_address);
      $username = mysqli_escape_string($this->connection, $username);

      // Execute the SQL stored proc to check the username and email aren't already in use
      $check_info_result = $this->call("check_user_account_info_valid", array(
        "email_address" => "{$email_address}",
        "username"      => "{$username}"
      ));

      if (is_array($check_info_result)) {
        $errors = "";

        $row = $check_info_result[0];

        // If the username is already taken
        if ($row["username"] !== NULL) {
          $errors .= "Username \"{$username}\" is unavailable.<br>";
        }

        // If the email address is already taken
        if ($check_info_result["email"] !== NULL) {
          $errors .= "There is already a user registered with the email address \"{$email_address}\".<br>";
        }

        // If any errors occured, return them
        if ($errors !== "") return $errors;

        // Generate a random code for the account verification email
        $verification_code = Infra::generate_random_code(20);

        // Execute the stored proc to insert a new user
        $create_user_result = $this->call("create_user", array(
          "email_address"     => "{$email_address}",
          "username"          => "{$username}",
          "password"          => "{$password_hash}",
          "verification_code" => "{$verification_code}"
        ));

        if (is_array($create_user_result) && count($create_user_result) == 1 && $create_user_result[0]["row_count"] == "1") {
          $result = "success";
          Infra::send_account_verification_email($username, $email_address, $verification_code);
        } else {
          return mysqli_error($this->connection);
        }

      } else if (is_string($check_info_result) && substr($check_info_result, 0, 5) == "ERROR") {
        $result = $check_info_result;
      }
    }

    return $result;
  }

  public function activate_account($username, $verification_code) {
    $success = false;

    // Call the stored procedure to activate the account
    $result = $this->call("activate_account", array("username" => "{$username}", "verification_code" => "{$verification_code}"));

    // If the verification code was valid
    if (is_array($result) && !empty($result)) {
      $row = $result[0];
      // Sign the user in
      $_SESSION["user_id"] = $row["user_id"];
      $_SESSION["username"] = $username;
      $_SESSION["email_address"] = $row["email_address"];

      $success = true;
    }

    return $success;
  }

  public function login_user($email_address, $password) {
    $success = false;

    $email_address = trim($email_address);
    $password      = trim($password);

    if (!empty($email_address) && !empty($password)) {
      $email_address = mysqli_escape_string($this->connection, $email_address);

      // Call the stored procedure to get the info needed for login
      $results = $this->call("get_user_login_info", array("email_address" => "{$email_address}"));
      if (is_array($results) && !empty($results)) {

        $row = $results[0];

        require("../PBKDF2/password_hash.php");
        if (PasswordStorage::verify_password($password, $row["password"])) {
          if ($row["status"] == "unverified") {
            $_SESSION["login_attempt"] = true;
            Infra::redirect("pages/email_not_verified/email_not_verified.php");
          } else {
            // Sign the user in
            $_SESSION["user_id"] = $row["user_id"];
            $_SESSION["username"] = $row["username"];
            $_SESSION["email_address"] = $email_address;
            $success = true;
          }
        }

      }
    }
    return $success;
  }

  public function call($procedure_name, $params) {
    // Start the call to the procedure
    $query_string = "CALL `{$procedure_name}`(";

    // Add all the parameters to the procedure call
    foreach ($params as $key => $value) $query_string .= "'{$value}',";

    // Replace the last character (a comma) with a closing bracket
    $query_string = substr_replace($query_string, ")", strlen($query_string) - 1);

    // Run the query and return the result(s)
    $response = mysqli_multi_query($this->connection, $query_string);

    if ($response === false) {
      return "ERROR: An error occured running the query: {$query_string}<br>" . mysqli_error($this->connection);
    } else {
      $all_results = array();
      $errors = "";
      do {
        $results = mysqli_store_result($this->connection);

        if ($results === false) {
          if (mysqli_errno($this->connection) != 0) {
            $errors .= mysqli_error($this->connection) . "\n";
          }
        } else {
          $new_row = $results->fetch_assoc();
          while ($new_row != NULL) {
            array_push($all_results, $new_row);
            $new_row = $results->fetch_assoc();
          }
          $results->free();
        }
      } while (mysqli_more_results($this->connection) && mysqli_next_result($this->connection));

      if ($errors != "") return $errors;
      return $all_results;
    }
  }
}

?>
