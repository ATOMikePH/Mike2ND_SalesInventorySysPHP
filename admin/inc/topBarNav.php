<style>
    .user-img {
        position: absolute;
        height: 27px;
        width: 27px;
        object-fit: cover;
        left: -7%;
        top: -12%;
    }

    .btn-rounded {
        border-radius: 50px;
    }

    .live-time-date.digital-clock {
        color: #000000;
        font-size: 15px; 
        margin-right: 15px;
        padding: 10px;
        border-radius: 5px;
        background-color: #b6d339;
        transition: background-color 0.3s ease, opacity 0.5s ease;
        animation: fadeInUp 0.5s ease;
        font-family: "orbitron", sans-serif;
        font-weight: bold;
}

    .message-container {
        text-align: center;
        margin-top: 10px;
    }

    .greeting-message {
        font-size: 24px;
        font-family: 'Pacifico', cursive; 
        color: #fff;
        opacity: 0;
        transition: opacity 0.5s ease, transform 0.5s ease;
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes fadeOut {
        from {
            opacity: 1;
        }
        to {
            opacity: 0;
        }
    }
    @-webkit-keyframes font-effect-fire-animation-keyframes {
  0% {
    text-shadow: 0 -0.05em 0.2em #FFF, 0.01em -0.02em 0.15em #FE0, 0.01em -0.05em 0.15em #FC0, 0.02em -0.15em 0.2em #F90, 0.04em -0.20em 0.3em #F70,0.05em -0.25em 0.4em #F70, 0.06em -0.2em 0.9em #F50, 0.1em -0.1em 1.0em #F40;
  }
  25% {
    text-shadow: 0 -0.05em 0.2em #FFF, 0 -0.05em 0.17em #FE0, 0.04em -0.12em 0.22em #FC0, 0.04em -0.13em 0.27em #F90, 0.05em -0.23em 0.33em #F70, 0.07em -0.28em 0.47em #F70, 0.1em -0.3em 0.8em #F50, 0.1em -0.3em 0.9em #F40;
  }
  50% {    text-shadow: 0 -0.05em 0.2em #FFF, 0.01em -0.02em 0.15em #FE0, 0.01em -0.05em 0.15em #FC0, 0.02em -0.15em 0.2em #F90, 0.04em -0.20em 0.3em #F70,0.05em -0.25em 0.4em #F70, 0.06em -0.2em 0.9em #F50, 0.1em -0.1em 1.0em #F40;
  }
  75% {
    text-shadow: 0 -0.05em 0.2em #FFF, 0 -0.06em 0.18em #FE0, 0.05em -0.15em 0.23em #FC0, 0.05em -0.15em 0.3em #F90, 0.07em -0.25em 0.4em #F70, 0.09em -0.3em 0.5em #F70, 0.1em -0.3em 0.9em #F50, 0.1em -0.3em 1.0em #F40;
  }
  100% {
    text-shadow: 0 -0.05em 0.2em #FFF, 0.01em -0.02em 0.15em #FE0, 0.01em -0.05em 0.15em #FC0, 0.02em -0.15em 0.2em #F90, 0.04em -0.20em 0.3em #F70,0.05em -0.25em 0.4em #F70, 0.06em -0.2em 0.9em #F50, 0.1em -0.1em 1.0em #F40;
  }
}
.font-effect-fire-animation {
  -webkit-animation-duration:0.8s;
  -webkit-animation-name:font-effect-fire-animation-keyframes;
  -webkit-animation-iteration-count:infinite;
  -webkit-animation-direction:alternate;
  color: #ffe;
}

@-webkit-keyframes font-effect-cold-animation-keyframes {
  0% {
    text-shadow: 0 -0.05em 0.2em #FFF, 0.01em -0.02em 0.15em #C0C0C0, 0.01em -0.05em 0.15em #A9A9A9, 0.02em -0.15em 0.2em #808080, 0.04em -0.20em 0.3em #778899, 0.05em -0.25em 0.4em #708090, 0.06em -0.2em 0.9em #4682B4, 0.1em -0.1em 1.0em #4169E1;
  }
  25% {
    text-shadow: 0 -0.05em 0.2em #FFF, 0 -0.05em 0.17em #C0C0C0, 0.04em -0.12em 0.22em #A9A9A9, 0.04em -0.13em 0.27em #808080, 0.05em -0.23em 0.33em #778899, 0.07em -0.28em 0.47em #708090, 0.1em -0.3em 0.8em #4682B4, 0.1em -0.3em 0.9em #4169E1;
  }
  50% {
    text-shadow: 0 -0.05em 0.2em #FFF, 0.01em -0.02em 0.15em #C0C0C0, 0.01em -0.05em 0.15em #A9A9A9, 0.02em -0.15em 0.2em #808080, 0.04em -0.20em 0.3em #778899, 0.05em -0.25em 0.4em #708090, 0.06em -0.2em 0.9em #4682B4, 0.1em -0.1em 1.0em #4169E1;
  }
  75% {
    text-shadow: 0 -0.05em 0.2em #FFF, 0 -0.06em 0.18em #C0C0C0, 0.05em -0.15em 0.23em #A9A9A9, 0.05em -0.15em 0.3em #808080, 0.07em -0.25em 0.4em #778899, 0.09em -0.3em 0.5em #708090, 0.1em -0.3em 0.9em #4682B4, 0.1em -0.3em 1.0em #4169E1;
  }
  100% {
    text-shadow: 0 -0.05em 0.2em #FFF, 0.01em -0.02em 0.15em #C0C0C0, 0.01em -0.05em 0.15em #A9A9A9, 0.02em -0.15em 0.2em #808080, 0.04em -0.20em 0.3em #778899, 0.05em -0.25em 0.4em #708090, 0.06em -0.2em 0.9em #4682B4, 0.1em -0.1em 1.0em #4169E1;
  }
}

.font-effect-cold-animation {
  -webkit-animation-duration: 0.8s;
  -webkit-animation-name: font-effect-cold-animation-keyframes;
  -webkit-animation-iteration-count: infinite;
  -webkit-animation-direction: alternate;
  color: #ffe;
}
@-webkit-keyframes font-effect-nighty-animation-keyframes {
  0% {
    text-shadow: 0 0.05em 0.2em #FFF, 0.01em 0.02em 0.15em #666, 0.01em 0.05em 0.15em #555, 0.02em 0.15em 0.2em #444, 0.04em 0.20em 0.3em #333, 0.05em 0.25em 0.4em #222, 0.06em 0.2em 0.9em #111, 0.1em 0.1em 1.0em #000;
  }
  25% {
    text-shadow: 0 0.05em 0.2em #FFF, 0 0.05em 0.17em #666, 0.04em 0.12em 0.22em #555, 0.04em 0.13em 0.27em #444, 0.05em 0.23em 0.33em #333, 0.07em 0.28em 0.47em #222, 0.1em 0.3em 0.8em #111, 0.1em 0.3em 0.9em #000;
  }
  50% {
    text-shadow: 0 0.05em 0.2em #FFF, 0.01em 0.02em 0.15em #666, 0.01em 0.05em 0.15em #555, 0.02em 0.15em 0.2em #444, 0.04em 0.20em 0.3em #333, 0.05em 0.25em 0.4em #222, 0.06em 0.2em 0.9em #111, 0.1em 0.1em 1.0em #000;
  }
  75% {
    text-shadow: 0 0.05em 0.2em #FFF, 0 0.06em 0.18em #666, 0.05em 0.15em 0.23em #555, 0.05em 0.15em 0.3em #444, 0.07em 0.25em 0.4em #333, 0.09em 0.3em 0.5em #222, 0.1em 0.3em 0.9em #111, 0.1em 0.3em 1.0em #000;
  }
  100% {
    text-shadow: 0 0.05em 0.2em #FFF, 0.01em 0.02em 0.15em #666, 0.01em 0.05em 0.15em #555, 0.02em 0.15em 0.2em #444, 0.04em 0.20em 0.3em #333, 0.05em 0.25em 0.4em #222, 0.06em 0.2em 0.9em #111, 0.1em 0.1em 1.0em #000;
  }
}

.font-effect-nighty-animation {
  -webkit-animation-duration: 0.8s;
  -webkit-animation-name: font-effect-nighty-animation-keyframes;
  -webkit-animation-iteration-count: infinite;
  -webkit-animation-direction: alternate;
  color: #ffe;
}
</style>

<style>
    body.dark-mode {
        background-color: #121212;
        color: #fff;
    }

    body.dark-mode .navbar-nav .nav-link,
    body.dark-mode .nav-item.message-container .greeting-message,
    body.dark-mode .nav-item .dropdown-menu a.dropdown-item {
        color: #000 !important;
    }

   
    body.dark-mode .card {
        background-color: #333;
    }

    body.dark-mode .card-body {
        color: #fff; 
    }

    body.dark-mode .card span {
        color: #fff !important;
    }

    body.dark-mode .info-box-number {
    color: #fff !important;
}


    .navbar-dark .navbar-toggler-icon {
        background-color: #fff;
    }
</style>


<!-- Navbar -->
<nav class="main-header navbar navbar-expand-lg navbar-dark border border-light border-top-0 border-left-0 border-right-0 navbar-light text-sm bg-lightblue">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
        </li>
        <!-- Live Time and Date -->
        <li class="nav-item">
            <div class="live-time-date digital-clock" id="liveTimeDate"></div>
        </li>
    </ul>
    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
        <li class="nav-item message-container">
            <div class="message-content">
                <div class="greeting-message" id="greetingMessage"></div>
            </div>
        </li>
        <li class="nav-item">
            <div class="btn-group nav-link">
                <button type="button" class="btn btn-rounded badge badge-light dropdown-toggle dropdown-icon" data-toggle="dropdown">
                    <span><img src="<?php echo validate_image($_settings->userdata('avatar')) ?>" class="img-circle elevation-2 user-img" alt="User Image" draggable="false"></span>
                    <span class="ml-3"><?php echo ucwords('My Account') ?></span>
                    <span class="sr-only">Toggle Dropdown</span>
                </button>
                <label class="switch">
                    <input type="checkbox" id="darkModeToggle">
                    <span class="slider round"></span>
                </label>
                <div class="dropdown-menu" role="menu">
                    <a class="dropdown-item" href="<?php echo base_url.'admin/?page=user' ?>"><span class="fa fa-user"></span> Manage Account</a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="javascript:void(0);" onclick="showLogoutConfirmation()"><span class="fa fa-power-off" aria-hidden="true"></span> Logout</a>
                </div>
            </div>
        </li>
    </ul>
</nav>

<!-- Logout Confirmation Modal -->
<div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="logoutModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content rounded-0">
            <div class="modal-header bg-danger text-white border-0">
                <h5 class="modal-title" id="logoutModalLabel">
                    <i class="fas fa-exclamation-triangle mr-2"></i> Logout Confirmation
                </h5>
            </div>
            <div class="modal-body">
                <p class="lead">Are you sure you want to logout?</p>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times mr-1"></i> Wait
                </button>
                <button type="button" class="btn btn-danger" id="confirmLogout">
                    <i class="fas fa-sign-out-alt mr-1"></i> Logout
                </button>
            </div>
        </div>
    </div>
</div>

<script>

const isDarkModeEnabled = localStorage.getItem('darkMode') === 'enabled';

darkModeToggle.checked = isDarkModeEnabled;

toggleDarkMode();


darkModeToggle.addEventListener('change', function() {
    if (darkModeToggle.checked) {
        enableDarkMode();
    } else {
        disableDarkMode();
    }
});


function enableDarkMode() {
    document.body.classList.add('dark-mode');
    localStorage.setItem('darkMode', 'enabled');
}


function disableDarkMode() {
    document.body.classList.remove('dark-mode');
    localStorage.setItem('darkMode', null);
}


function toggleDarkMode() {
    if (darkModeToggle.checked) {
        enableDarkMode();
    } else {
        disableDarkMode();
    }
}

// I-call ang function upang i-set ang initial dark mode base sa nakaraang estado
toggleDarkMode();


</script>

<script>
function showLogoutConfirmation() {
    Swal.fire({
        title: 'Are you sure you want to logout?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, logout me!'
    }).then((result) => {
        if (result.isConfirmed) {
            logoutUser();
        }
    });
}

function logoutUser() {
    // Add an AJAX request to update the last login time
    $.ajax({
        url: '<?php echo base_url.'/classes/Login.php?f=logout' ?>',
        method: 'POST',
        dataType: 'json',
        success: function(response) {
            if (response.status === 'success') {
                if (response.redirect) {
                    window.location.href = response.redirect;
                } else {
                    Swal.fire({
                        title: 'Logged out successfully!',
                        icon: 'success'
                    });
                }
            } else {
                Swal.fire({
                    title: 'Failed to logout',
                    text: response.message || '',
                    icon: 'error'
                });
            }
        },
        error: function(error) {
            console.log(error);
            Swal.fire({
                title: 'An error occurred',
                text: 'Please try again later',
                icon: 'error'
            });
        }
    });
}
</script>

<script>

    function updateLiveTimeDate() {
        var now = new Date();
        var options = {
            hour: 'numeric',
            minute: 'numeric',
            second: 'numeric',
            year: 'numeric',
            month: 'long',
            day: 'numeric',
        };
        var formattedDateTime = new Intl.DateTimeFormat('en-US', options).format(now);
        document.getElementById('liveTimeDate').textContent = formattedDateTime;
    }

    function updateGreetingMessage() {
        var now = new Date();
        var hour = now.getHours();

        var greetingMessageElement = document.getElementById('greetingMessage');
        var greetingMessage = "";

        var morningIcon = '<i class="fas fa-sun"></i>';
        var afternoonIcon = '<i class="fas fa-cloud-sun"></i>';
        var eveningIcon = '<i class="fas fa-moon"></i>';

        if (hour < 12) {
            greetingMessage = `<span class="font-effect-cold-animation">Good Morning! ${morningIcon}</span>`;
        } else if (hour < 18) {
            greetingMessage = `<span class="font-effect-fire-animation">Good Afternoon! ${afternoonIcon}</span>`;
        } else {
            greetingMessage = `<span class="font-effect-nighty-animation">Good Evening! ${eveningIcon}</span>`;
        }

        greetingMessageElement.innerHTML = greetingMessage;
    }

    function fadeInOutLoop() {
        var greetingMessageElement = document.getElementById('greetingMessage');

        fadeIn(greetingMessageElement);
        setTimeout(function () {
            fadeOut(greetingMessageElement);
 
            setTimeout(fadeInOutLoop, 1000);
        }, 3000); 
    }

    
    function fadeIn(element) {
        element.style.opacity = 1;
        element.style.transition = "opacity 0.5s ease, transform 0.5s ease";
    }

  
    function fadeOut(element) {
        element.style.opacity = 0;
        element.style.transition = "opacity 0.5s ease";
    }


    setInterval(function () {
        updateLiveTimeDate();
        updateGreetingMessage();
    }, 1000);


    document.addEventListener("DOMContentLoaded", function () {
        fadeInOutLoop();
    });
</script>

<style>

    .switch {
        position: relative;
        display: inline-block;
        width: 40px;
        height: 20px;
    }

    .switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #87CEEB;
        -webkit-transition: .4s;
        transition: .4s;
    }

    .slider:before {
        position: absolute;
        content: "";
        height: 14px;
        width: 14px;
        left: 4px;
        bottom: 3px;
        background-color: yellow; 
        border-radius: 50%; 
        -webkit-transition: .4s;
        transition: .4s;
    }

    input:checked + .slider {
        background-color: #121212; 
    }

    input:focus + .slider {
        box-shadow: 0 0 1px #2196F3;
    }

    input:checked + .slider:before {
        -webkit-transform: translateX(20px);
        -ms-transform: translateX(20px);
        transform: translateX(20px);
        background-color: #f8ce4a; 
    }


    .slider.round {
        border-radius: 34px;
    }

    .slider.round:before {
        border-radius: 50%;
    }
</style>