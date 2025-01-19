<?php include("includes/header.php");

if(isset($_SESSION['loggedIn'])){
    ?>
    <script>window.location.href = 'index.php';</script>
    <?php
}

?>

<div class="login-container">
    <div class="login-wrapper">
      <div class="form-column">
        <div class="login-content">

          <?php alertMessage()?>

          <h1 class="login-title">Login</h1>

          <form class="login-form" action="login-code.php" method="POST">

              <div class="input-group">
                <label for="email" class="input-label">Email</label>
                <div>
                  <input type="email" name="email" class="input-value input-field" placeholder="Enter your email" required />
                </div>
              </div>
              <div class="input-group">
                <label for="password" class="input-label">Password</label>
                <div>
                  <input type="password" name="password" class="input-value input-field" placeholder="Enter your password" required />
                </div>
              </div>
              <div class="submit-button">
                <button type="submit" name="loginBtn" class="login-button">Login</button>
              </div>
          </form>

          <!-- <a href="#" class="forgot-password">Forgot Password?</a>
          <div class="website-link">Don't have an account? <a href="#">Sign Up</a></div> -->
        </div>
      </div>
      <div class="image-column">
        <img src="assets/img/loginBanner.jpg" alt="" class="login-image" />
      </div>
    </div>
  </div>

<?php include("includes/footer.php");?>


   