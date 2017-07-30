<div class="card">
  <form id="form_login" action="/processes/login_user.php" method="post">

    <p class="label">Email address</p>
    <input type="text" id="email_address" name="email_address" placeholder="YourEmailAddress@ProbablyGmail.com" class="top">

    <p class="label">Password</p>
    <input type="password" id="password" name="password" placeholder="not1234">

    <div class="button-container">
      <input form="form_login" class="button" type="submit" name="submit" value="Sign in">
      <?=button("button-create-account", "Create account", "/pages/create_account/create_account.php")?>
    </div>

  </form>
</div>
