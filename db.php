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
      $verification_code = Infra::generate_random_code(20);

      $query = "INSERT INTO users (email, username, password, verification_code) VALUES(?, ?, ?, ?)";
      $query_stmt = mysqli_prepare($this->connection, $query);
      mysqli_stmt_bind_param($query_stmt, "ssss", $email_address, $username, $password_hash, $verification_code);

      mysqli_stmt_execute($query_stmt);

      if (mysqli_stmt_affected_rows($query_stmt) == 1) {
        $result = "success";

        $escaped_username = rawurlencode($username);
        Infra::mail(
          $email_address,
          "Account activation",
          template("templates/email_account_activation.php", array(
            "username" => "{$username}",
            "activation_link" => "http://" . Infra::get_base_url() . "/processes/activate_account.php?user={$escaped_username}&code={$verification_code}"
          ))
        );
      } else {
        return mysqli_error($this->connection);
      }

      mysqli_stmt_close($query_stmt);
      mysqli_close($this->connection);
    }

    return $result;
  }

  public function activate_account($username, $verification_code) {
    $success = false;

    // Values are stored escaped in the database
    // Must double-escape for query to return a match with the escaped value
    $username = mysqli_escape_string($this->connection, mysqli_escape_string($this->connection, $username));
    $query = "SELECT email FROM users WHERE username='{$username}' AND verification_code='{$verification_code}' LIMIT 1";

    $response = mysqli_query($this->connection, $query);
    if ($response) {
      if (mysqli_num_rows($response) == 1) {
        $row = mysqli_fetch_array($response);

        $query = "UPDATE users SET verification_code=NULL WHERE username='{$username}' AND verification_code='{$verification_code}' LIMIT 1";
        mysqli_query($this->connection, $query);

        $_SESSION["username"] = $username;
        $_SESSION["email_address"] = $row["email"];
        $success = true;
      }
    } else {
      echo mysqli_error($this->connection);
    }
    mysqli_close($this->connection);

    return $success;
  }

  public function login_user($email_address, $password) {
    $success = false;

    $email_address = trim($email_address);
    $password      = trim($password);

    if (!empty($email_address) && !empty($password)) {
      // Values are stored escaped in the database
      // Must double-escape for query to return a match with the escaped value
      $email_address = mysqli_escape_string($this->connection, mysqli_escape_string($this->connection, $email_address));
      $query = "SELECT username, password FROM users WHERE email='{$email_address}'";

      $response = mysqli_query($this->connection, $query);
      if ($response) {
        if (mysqli_num_rows($response) > 0) {
          $row = mysqli_fetch_array($response);

          require("../PBKDF2/password_hash.php");
          if (PasswordStorage::verify_password($password, $row["password"])) {
            $_SESSION["username"] = $row["username"];
            $_SESSION["email_address"] = $email_address;
            $success = true;
          }
        }
      } else {
        echo mysqli_error($this->connection);
      }
      mysqli_close($this->connection);
    }
    return $success;
  }
}

?>
