<div class="content-wrapper">
  <form id="form_login" action="/processes/login_user.php" method="post">
    <input type="text"     name="email_address" placeholder="Email address" class="top">
    <input type="password" name="password"      placeholder="Password">
  </form>
  <div class="button-container">
    <?=button("button-create-account", "Create account", "/pages/create_account/create_account.php")?>
    <input form="form_login" class="button right" type="submit" name="submit" value="Sign in">
  </div>
</div>
