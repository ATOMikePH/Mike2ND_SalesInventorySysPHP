<?php require_once('../config.php') ?>
<!DOCTYPE html>
<html lang="en" class="" style="height: auto;">
 <?php require_once('inc/header.php') ?>
<body class="hold-transition login-page dark-mode">

  <style>
    body {
      background-image: url("<?php echo validate_image($_settings->info('cover')) ?>");
      background-size: cover;
      background-repeat: no-repeat;
      animation: gradientAnimation 10s ease infinite;
    }

    @keyframes gradientAnimation {
      0% {
        background-position: 0% 50%;
      }
      50% {
        background-position: 100% 50%;
      }
      100% {
        background-position: 0% 50%;
      }
    }

    .login-title {
      text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
      color: #fff;
    }

    .login-box {
  position: absolute;
  top: 50%;
  left: 50%;
  width: 400px;
  padding: 40px;
  transform: translate(-50%, -50%);
  background: rgba(0,0,0,0.8);
  box-sizing: border-box;
  box-shadow: 0 15px 25px rgba(0,0,0,.6);
  border-radius: 10px;
}

    .login-box .card {
      border: none;
      box-shadow: 0 0 20px rgba(0, 0, 0, 0.2);
    }

    .login-box .card-header {
      background-color: rgba(0, 0, 0, 0.5);
      color: #fff;
    }

    .login-box .btn-primary {
      background-color: #007bff;
      border-color: #007bff;
    }

    .login-box .btn-primary:hover {
      background-color: #0056b3;
      border-color: #0056b3;
    }

    
    @keyframes fadeInOut {
  0% {
    opacity: 0;
  }
  50% {
    opacity: 1;
  }
  100% {
    opacity: 0;
  }
}

.login-title {
  animation: fadeInOut 5s infinite; 
}

.center-content {
            text-align: center;
        }

      
        .center-content .btn-primary {
            display: inline-block;
            margin-top: 10px; 
        }

        .login-box .user-box {
  position: relative;
}

.login-box .user-box input {
  width: 100%;
  padding: 10px 0;
  font-size: 16px;
  color: #fff;
  margin-bottom: 30px;
  border: none;
  border-bottom: 1px solid #fff;
  outline: none;
  background: transparent;
}
.login-box .user-box label {
  position: absolute;
  top:0;
  left: 0;
  padding: 10px 0;
  font-size: 16px;
  color: #fff;
  pointer-events: none;
  transition: .5s;
}

.login-box .user-box input:focus ~ label,
.login-box .user-box input:valid ~ label {
  top: -20px;
  left: 0;
  color: #03e9f4;
  font-size: 12px;
}

.login-box form button {
  
  text-align: center;
border-radius: 50px;
background: linear-gradient(145deg, #2c2c2c, #252525);
box-shadow:  20px 20px 60px #101010,
             -20px -20px 60px #424242;
  position: block;
  display: inline-block;
  padding: 10px 20px;
  color: #529D8C;
  font-size: 16px;
  text-decoration: none;
  text-transform: uppercase;
  overflow: hidden;
  transition: .7s;
  margin-top: 40px;
  letter-spacing: 4px
}

.login-box button:hover {

border-radius: 50px;
background: linear-gradient(315deg, #2c2c2c, #252525);
box-shadow:  -20px -20px 60px #101010,
             20px 20px 60px #424242;
}

.login-box button span {
  position: absolute;
  display: block;
}

</style> 
  

  <div class="login-box">
    <div class="card-primary">
    <div class="text-center">
    <div class="logo-container">
  </div>
  <hr>
        <h2 class="h2"><b class="fas fa-lock"> Secured Login</b></h2>
      </div>
      <div class="card-body">
        <p class="login-box-msg">Sign in to start your session ⓘ</p>
        <form id="login-frm" action="" method="post">
        <div class="user-box">
      <input type="text" autofocus name="username" required="" autocomplete="off">
      <label><i class="fas fa-user" ></i> Username</label>
    </div>
    <div class="user-box">
      <input type="password" name="password" id="password" required="" autocomplete="new-password">
      <label><i class="fas fa-lock"></i> Password</label>
    </div>
    <hr>
<div class="row">
<div class="col-12 center-content">
                    <button type="submit" class="btn btn-primary">Sign In</button>
                </div>
            </div>
        </div>
    </div>
        </form>
      </div>


  <!-- jQuery -->
  <script src="plugins/jquery/jquery.min.js"></script>
  <!-- Bootstrap 4 -->
  <script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
  <!-- AdminLTE App -->
  <script src="dist/js/adminlte.min.js"></script>



</body>
<footer style="background-color: rgba(0, 0, 0, 0.8); color: #fff; text-align: center; padding: 10px; position: fixed; bottom: 0; width: 100%;">
  <div style="font-size: 18px; animation: fadeInOut 5s infinite;">
  Copyright and Licensed to 
  <!-- <b><?php echo $_settings->info('short_name') ?> &copy; 2024  </b>. All rights reserved.  -->
    <!-- JavaScript to handle click event -->
   <small><b>Developed By: <a href="mailto:michael.cabalona.28@gmail.com" target="gmail.com">ATOMUS</a></b></small> 
        <i class="fas fa-info-circle ml-2" data-toggle="tooltip" data-placement="top" onclick="alert('Contact me / Avail me for other system: \n\nMichael B. Cabalona\n0939 240 6870\nSubic, Zambales PH 2209')"></i>
  </div>
</footer>
</html>
