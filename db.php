<?php

// Contains database login info
require "private_db_info.php";

// Create the default db connection
$db = new DB();

/**
 * A class which encapsulates all database interaction.
 */
class DB {
  // A connection to the mysql database
  protected $pdo;

  /**
   * The class constructor
   * Creates a new database connection
   */
  public function __construct() {
    try {
      // Connect to the mysql database
      $this->pdo = new PDO("mysql:dbname=" . DB_NAME . ";host=" . DB_HOST, DB_USER, DB_PASSWORD, array(
        PDO::ATTR_PERSISTENT => TRUE,
        PDO::ATTR_ERRMODE    => PDO::ERRMODE_EXCEPTION
      ));
    } catch (Exception $e) {
      die($e->getMessage());
    }
  }

  /**
   * The class destructor
   */
  public function __destruct() {
    // Close the database connection.
    // This is not strictly necessary as the object is being destructed anyway. It's just here for completeness/reminder.
    $pdo = null;
  }

  /**
   * Creates a new user account
   * @param string $email_address The email address of the new user
   * @param string $username The chosen display name of the new user
   * @param string $password The plaintext password of the new user
   * @param string $password_confirm The plaintext password of the new user...again
   * @return string 'success' if the user account was created successfully, otherwise returns what went wrong
   */
  public function create_account($email_address, $username, $password, $password_confirm) {
    // Trim all leading and trailing spaces from all user input
    $email_address    = trim($email_address);
    $username         = trim($username);
    $password         = trim($password);
    $password_confirm = trim($password_confirm);

    // Make sure each input is not blank and that the two entered passwords match
    if (!empty($email_address)
      && !empty($username)
      && !empty($password)
      && !empty($password_confirm)
      && $password == $password_confirm) {

      // Hash the password
      require("../PBKDF2/password_hash.php");
      $password_hash = PasswordStorage::create_hash($password);

      // Execute the SQL stored proc to check the username and email aren't already in use
      $check_info_result = $this->call("check_user_account_info_valid", array(
        "email_address" => "{$email_address}",
        "username"      => "{$username}"
      ));

      // If the query successfully returned some data
      if (is_array($check_info_result)) {
        $errors = "";

        // Get the top row of data (there should only be one anyway)
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

        // If the proc returned 1 row of data, where column `row_count` = '1', then 1 user has been successfully inserted into the database
        if (is_array($create_user_result) && count($create_user_result) == 1 && $create_user_result[0]["row_count"] == "1") {

          // Send an email to the new user asking them to verify their email address
          Infra::send_account_verification_email($username, $email_address, $verification_code);

          // Return the new user was created successfully
          return "success";

        } else {
          // If, for some reason, a new user could not be inserted into the database
          return 'Something went wrong.';
        }

      }
    } else {
      // If the user entered invalid data
      return "An error occurred when creating the account. Account could not be created.";
    }
  }

  /**
   * Activate a newly created user account
   * @param string $username The chosen display name of the new user
   * @param string $verification_code The one-time verification code for the user
   * @return bool true on success, false otherwise
   */
  public function activate_account($username, $verification_code) {
    // Call the stored procedure to activate the account
    $result = $this->call("activate_account", array("username" => "{$username}", "verification_code" => "{$verification_code}"));

    // If the verification code was valid, the query should return the username and email address
    if (is_array($result) && !empty($result)) {
      // Get the first row of data
      $row = $result[0];

      // Sign the user in
      $_SESSION["user_id"] = $row["user_id"];
      $_SESSION["username"] = $username;
      $_SESSION["email_address"] = $row["email_address"];

      // Return that the account was successfully activated
      return true;
    }

    // Return failure if the verification code was invalid
    return false;
  }

  /**
   * Sign a user into their account
   * @param string $email_address The email address entered by the user
   * @param string $password The plaintext password entered by the user
   * @return bool true on success, false otherwise
   */
  public function login_user($email_address, $password) {
    // Trim all leading and trailing spaces from the user input
    $email_address = trim($email_address);
    $password      = trim($password);

    // Make sure neither is blank
    if (!empty($email_address) && !empty($password)) {

      // Call the stored procedure to get the info needed for login
      $results = $this->call("get_user_login_info", array("email_address" => "{$email_address}"));

      // Make sure the query returned results
      if (is_array($results) && !empty($results)) {

        // Get the first row of results
        $row = $results[0];

        // Hash the password and check that is matches the hash in the database
        require("../PBKDF2/password_hash.php");
        if (PasswordStorage::verify_password($password, $row["password"])) {

          // If this user has not yet verified their email address
          if ($row["status"] == "unverified") {

            // Do not sign the user in, redirect the user to the page which explains why
            $_SESSION["login_attempt"] = true;
            Infra::redirect("pages/email_not_verified/email_not_verified.php");

          } else {
            // Sign the user in
            $_SESSION["user_id"] = $row["user_id"];
            $_SESSION["username"] = $row["username"];
            $_SESSION["email_address"] = $email_address;

            // Return that the user was signed in successfully
            return true;
          }
        }

      }

    }

    // Failure to sign the user in
    // Either the user provided invalid credentials or there was an issue querying the database
    return false;
  }

  /**
   * Call a MYSQL stored procedure
   * @param string $procedure_name The name of the stored procedure to invoke
   * @param array $params An array of parameters to be passed to the stored procedure
   * @param bool $return_results (optional) Whether to return a table of results or not
   * @return bool|array If $return_results is true, then returns a 2D array of results returned by executing the proc. If $return_results is false, then returns true for a successfully executed proc. If executing the proc failed, an error will be output and the script will be stopped.
   */
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
