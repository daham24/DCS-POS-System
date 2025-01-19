<?php include("includes/header.php");?>

<div class="welcome-container" style="position: relative; overflow: hidden; width: 100%; height: 50vh; margin-top: 100px">
  <h1 class="welcome-title">Dimuthu Cellular Service</h1>
  <p class="welcome-description">
    Welcome to the DCS POS System. Manage, organize, and lead with efficiency. Together, let’s drive excellence.
  </p>
  <div class="button-container">
    <button class="login-button" tabindex="0">
      <a style="color: white;" href="login.php">Login</a>
    </button>
  </div>
</div>

<!-- Footer Image -->
<div class="footer-image-container" style="width: 100%; position: relative; height: 50vh;  ">
  <img
    loading="lazy"
    src="assets/img/welcomeBanner.jpg"
    class="footer-image welcome-image"
    style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 100%; height: 100%; object-fit: cover;"
    alt="Dimuthu Cellular Service Footer Image"
  />
</div>

<?php include("includes/footer.php");?>