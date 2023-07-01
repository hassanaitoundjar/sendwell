<nav class="navbar navbar-expand  navbar-dark sticky-top px-4 py-0">



    <div class="navbar-nav align-items-center ms-auto">

        <form class=" search d-flex ">
            <input class=" searchInput form-control me-2" type="search" placeholder="Search" aria-label="Search">
            <i class="searchBtn bi bi-search" id="searchBtn"></i>
        </form>

        <!-- ... -->

        <div class="nav-item dropdown">
            <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                <i class="fa fa-bell me-lg-2"></i>

            </a>
            <div class="dropdown-menu dropdown-menu-end bg-secondary border-0 rounded-0 rounded-bottom m-0">
                <a href="#" class="dropdown-item">
                    <h6 class="fw-normal mb-0">Profile updated</h6>
                    <small>15 minutes ago</small>
                </a>
                <hr class="dropdown-divider">
                <a href="#" class="dropdown-item">
                    <h6 class="fw-normal mb-0">New user added</h6>
                    <small>15 minutes ago</small>
                </a>
                <hr class="dropdown-divider">
                <a href="#" class="dropdown-item">
                    <h6 class="fw-normal mb-0">Password changed</h6>
                    <small>15 minutes ago</small>
                </a>
                <hr class="dropdown-divider">
                <a href="#" class="dropdown-item text-center">See all notifications</a>
            </div>
        </div>

        <!-- ... -->
        <div class="nav-item dropdown">
            <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                <!-- <img class="rounded-circle me-lg-2" src="img/user.jpg" alt=""
                                style="width: 40px; height: 40px;"> -->
                <span class="d-none d-lg-inline-flex"><?php echo htmlspecialchars($username); ?></span>
            </a>
            <div class="dropdown-menu dropdown-menu-end  border-0 rounded-0 rounded-bottom m-0">
                <a href="profile.php" class="dropdown-item">My Profile</a>
                <a href="#" class="dropdown-item">Settings</a>
                <a href="logout.php" class="dropdown-item">Log Out</a>
            </div>
        </div>
    </div>
</nav>