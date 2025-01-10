<?php include("includes/header.php");?>


<div class="login-container">
    <div class="login-wrapper">
      <div class="form-column">
        <div class="login-content">
          <h1 class="login-title">Login</h1>
          <form class="login-form">
            <div class="input-group">
              <label for="email" class="input-label">Email</label>
              <div>
                <input type="email" id="email" class="input-value input-field" placeholder="Enter your email" required />
              </div>
            </div>
            <div class="input-group">
              <label for="password" class="input-label">Password</label>
              <div>
                <input type="password" id="password" class="input-value input-field" placeholder="Enter your password" required />
              </div>
            </div>
            <div class="submit-button">
              <button type="submit" class="login-button">Login</button>
            </div>
          </form>
          <a href="#" class="forgot-password">Forgot Password?</a>
          <div class="website-link">Don't have an account? <a href="#">Sign Up</a></div>
        </div>
      </div>
      <div class="image-column">
        <img src="https://cdn.builder.io/api/v1/image/assets/TEMP/d37b54890b4d30fc384399c3b521354cd6f40202dd03a51326663e4b303abd8b?placeholderIfAbsent=true&apiKey=2758efc56d724d1aacd00d329c35c80b" alt="" class="login-image" />
      </div>
    </div>
  </div>

<?php include("includes/footer.php");?>


   