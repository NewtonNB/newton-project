<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Form</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="modern-footer.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@3.3.7/dist/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

<!-- Optional theme -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@3.3.7/dist/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">

<!-- Latest compiled and minified JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@3.3.7/dist/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
<style>
  .login-wrapper {
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 100vh;
  background: url('./nyabzgallery/current.jpg') no-repeat center center/cover;
  padding: 20px;
}

.login-box {
  background-color: #1e1e1e;
  padding: 40px 30px;
  width: 100%;
  max-width: 400px;
  border-radius: 10px;
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.2);
}

.login-title {
  background-color: #00bfff;
  color: white;
  font-weight: bold;
  font-size: 22px;
  text-align: center;
  padding: 15px 10px;
  margin-bottom: 25px;
  border-radius: 6px;
}

.login-title h4 {
  font-size: 14px;
  font-weight: normal;
  margin-top: 8px;
  color: #f5f5f5;
}

.login-form .form-group {
  display: flex;
  flex-direction: column;
  margin-bottom: 20px;
}

.login-form label {
  color: skyblue;
  margin-bottom: 6px;
  font-weight: 500;
}

.login-form input {
  padding: 10px 14px;
  border: none;
  border-radius: 6px;
  font-size: 15px;
  background-color: #2c3e50;
  color: white;
  outline: none;
  transition: 0.3s ease;
}

.login-form input:focus {
  background-color: #34495e;
}

.submit-btn {
  background-color: #00bfff;
  color: white;
  border: none;
  padding: 12px;
  border-radius: 6px;
  font-size: 16px;
  font-weight: bold;
  cursor: pointer;
  transition: background 0.3s ease;
}

.submit-btn:hover {
  background-color: #009ace;
}

/* Mobile tweaks */
@media (max-width: 480px) {
  .login-box {
    padding: 30px 20px;
  }

  .login-title {
    font-size: 18px;
  }
}

</style>
</head>
<?php
session_start();
?>
<div class="login-wrapper">
  <div class="login-box">
    <div class="login-title">
      Login Form
      <h4>
        <?php
          if (isset($_SESSION['loginMessage'])) {
            echo $_SESSION['loginMessage'];
            unset($_SESSION['loginMessage']); // Clear the message after displaying
          }
        ?>
      </h4>
    </div>

    <form action="login_check.php" method="POST" class="login-form">
      <div class="form-group">
        <label>Username</label>
        <input type="text" name="username" required>
      </div>

      <div class="form-group">
        <label>Password</label>
        <input type="password" name="password" required>
      </div>

      <div class="form-group">
        <button type="submit" name="submit" class="submit-btn">Login</button>
      </div>
    </form>
  </div>
</div>
</body>
</html>