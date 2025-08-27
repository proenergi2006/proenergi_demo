<header class="main-header" style="background-color:#56386a">
    <div class="logo">
        <a href="<?php echo BASE_URL_CLIENT."/home.php"; ?>" style="display:block; margin:0px;">
            <p style="font-size:18px; font-weight:bold; color:#fff; text-shadow:0px 2px #777; line-height:55px;">SYOP PRO ENERGI</p>
        </a>
    </div>

    <nav class="navbar navbar-static-top" role="navigation">
        <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
            <i class="fa fa-bars"></i>
            <span class="sr-only">Toggle navigation</span>
        </a>
        <div class="navbar-custom-menu">
            <ul class="nav navbar-nav">
				<?php /*
				<?php if(getenv('APP_ENV') == 'development'){ ?>        
                <li>
                    <div style="padding:15px;"><small class="badge badge-success">DEMO</small></div>
                </li>
                <?php } ?>
                <li>
                    <a href="<?php echo BASE_URL_CLIENT."/chat.php"; ?>">
                        <i class="fa fa-envelope jarak-kanan"></i> Chat <span class="badge" id="chat_notif">0</span>
                    </a>
                </li>
				*/ ?>
                <li>
                    <a href="<?php echo BASE_URL_CLIENT."/acl-change-profil.php"; ?>">
                        <i class="fa fa-user jarak-kanan"></i> <span>Account</span>
                    </a>
                </li>
                <li>
                    <a href="<?php echo ACTION_CLIENT."/acl-logout.php"; ?>">
                        <i class="fas fa-sign-out-alt jarak-kanan"></i> <span>Logout</span>
                    </a>
                </li>
            </ul>
        </div>
    </nav>
</header>