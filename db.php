<?php

require "private_db_info.php";

$db = new DB();

class DB {
  protected $pdo;

  public function __construct() {
    try {
      $this->pdo = new PDO("mysql:dbname=" . DB_NAME . ";host=" . DB_HOST, DB_USER, DB_PASSWORD, array(
        PDO::ATTR_PERSISTENT => TRUE,
        PDO::ATTR_ERRMODE    => PDO::ERRMODE_EXCEPTION
      ));
    } catch (Exception $e) {
      die($e->getMessage());
    }
  }

  public function __destruct() {
    $pdo = null;
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
        if ($row["email"] !== NULL) {
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
          "user_password"     => "{$password_hash}",
          "verification_code" => "{$verification_code}"
        ));

        if (is_array($create_user_result) && count($create_user_result) == 1 && $create_user_result[0]["row_count"] == "1") {
          $result = "success";
          Infra::send_account_verification_email($username, $email_address, $verification_code);
        } else {
          return 'Something went wrong.';
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

  public function call($procedure_name, $params, $return_results = true) {
    // Start the call to the procedure
    $query_string = "CALL `{$procedure_name}`(";

    // Add all the parameters to the procedure call
    foreach ($params as $key => $value) $query_string .= ":{$key},";

    // Replace the last character (a comma) with a closing bracket
    $query_string = substr_replace($query_string, ")", strlen($query_string) - 1);

    try {
      // Prepare the sql statement
      $statement = $this->pdo->prepare($query_string);
    } catch (PDOException $e) {
      echo "Exception preparing database query statement: " . $e->getMessage();
      exit();
    }

    // Bind all the parameter values to the identifiers in the sql query (this is sql injection safe)
    foreach ($params as $key => &$value) {
      $statement->bindParam(":{$key}", $value);
    }

    try {
      // Execute the prepared database query statement
      $statement->execute();
    } catch (PDOException $e) {
      echo "Exception executing prepared statement: " . $e->getMessage();
      exit();
    }

    // If this call to the stored proc should return a table of results
    // and there are some results to return
    if ($return_results && $statement->rowCount() > 0) {
      try {
        // Return all the results from the query as a 2D array
        return $statement->fetchAll();
      } catch (PDOException $e) {
        echo "Exception fetching results from database: " . $e->getMessage();
        exit();
      }
    }

    // Return that the query was completed successfully
    return true;
  }

}

?>
