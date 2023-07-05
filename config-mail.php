<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>DarkPan - Bootstrap 5 Admin Template</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="" name="keywords">
    <meta content="" name="description">

    <?php


include('./admin/include/header.php')

     
     ?>
</head>

<body>
    <div class="page-content   ">
        <!-- Spinner Start -->
        <div id="spinner"
            class="show bg-dark position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
            <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
                <span class="sr-only">Loading...</span>
            </div>
        </div>
        <!-- Spinner End -->


        <!-- Sidebar Start -->
        <?php


include('../admin/include/sidebar.php')

     
     ?>
        <!-- Sidebar End -->


        <!-- Content Start -->
        <div class="content w-100 align-items-center ">
            <!-- Navbar Start -->
            <nav class="navbar navbar-expand  navbar-dark sticky-top px-4 py-0">



                <div class="navbar-nav align-items-center ms-auto">

                    <form class=" search d-flex ">
                        <input class=" searchInput form-control me-2" type="search" placeholder="Search"
                            aria-label="Search">
                        <i class="searchBtn bi bi-search" id="searchBtn"></i>
                    </form>

                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link " data-bs-toggle="dropdown">
                            <i class="fa fa-bell me-lg-2"></i>
                            <!-- <span class="d-none d-lg-inline-flex">Notificatin</span> -->
                        </a>
                        <div class="dropdown-menu dropdown-menu-end  border-0 rounded-0 rounded-bottom m-0">
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
                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                            <!-- <img class="rounded-circle me-lg-2" src="img/user.jpg" alt=""
                                style="width: 40px; height: 40px;"> -->
                            <span class="d-none d-lg-inline-flex">John Doe</span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end  border-0 rounded-0 rounded-bottom m-0">
                            <a href="profile.html" class="dropdown-item">My Profile</a>
                            <a href="#" class="dropdown-item">Settings</a>
                            <a href="#" class="dropdown-item">Log Out</a>
                        </div>
                    </div>
                </div>
            </nav>
            <!-- Navbar End -->


            <!-- Sale & Revenue Start -->
            <div class=" pt-4 px-4">
                <div class="row g-4">
                    <div class="col-sm-12 col-xl-12">
                        <div class="bg-white rounded d-flex align-items-center justify-content-between p-4">
                            <i class="fa fa-chart-line fa-3x text-primary"></i>
                            <div class="ms-3">
                                <p class="mb-2">Today Sale</p>
                                <h6 class="mb-0">$1234</h6>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <!-- Sale & Revenue End -->


            <!-- Sales Chart Start -->
            <div class=" pt-4 px-4">

                <div class="row g-4">
                    <div class="profile ">
                        <div class="col-sm-12 col-xl-12">

                            <h2 class="text-muted">My profile</h2>
                        </div>

                    </div>
                </div>
            </div>


            <div class="card-with pt-4 px-4">
                <div class="row g-4">
                    <div class="col-sm-12 col-xl-12">
                        <div class="chartone text-center rounded p-4">
                            <div class="d-flex align-items-center justify-content-between mb-4">
                                <h6 class="mb-0  ">Your details</h6>

                            </div>
                            <form class="update">
                                <div class="mb-3   form-row">
                                    <label for="exampleInputEmail1" class="form-label">Username</label>
                                    <input type="text" class="form-control" id="exampleInputEmail1"
                                        aria-describedby="emailHelp">

                                </div>
                                <hr>
                                <div class="mb-3  form-row">
                                    <label for="exampleInputEmail1" class="form-label">Email address</label>
                                    <input type="email" class="form-control" id="exampleInputEmail1"
                                        aria-describedby="emailHelp">

                                </div>
                                <hr>
                                <div class="mb-3 form-row">
                                    <label for="disabledTextInput" class="form-label">Password</label>
                                    <input type="password" class="form-control " id="disabledTextInput"
                                        placeholder="****************" disabled>
                                    <!-- Button trigger modal -->



                                </div>

                                <div class="change-password">
                                    <button type="button"
                                        class="d-flex justify-content-start align-items-center  btn btn-primary"
                                        data-bs-toggle="modal" data-bs-target="#exampleModal">
                                        Change Password
                                    </button>
                                    <!-- Modal -->
                                    <div class="modal fade" id="exampleModal" tabindex="-1"
                                        aria-labelledby="exampleModalLabel" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title text-muted" id="exampleModalLabel">Change
                                                        Password</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <label for="exampleInputPassword1"
                                                        class="form-label">Old-Password</label>
                                                    <input type="password" class="form-control"
                                                        id="exampleInputPassword1">
                                                    <label for="exampleInputPassword1"
                                                        class="form-label">New-Password</label>
                                                    <input type="password" class="form-control"
                                                        id="exampleInputPassword1">
                                                    <label for="exampleInputPassword1"
                                                        class="form-label">Confirm-Password</label>
                                                    <input type="password" class="form-control"
                                                        id="exampleInputPassword1">
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-bs-dismiss="modal">Close</button>
                                                    <button type="button" class="btn btn-primary">Save changes</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <hr>

                                <div class="account">

                                    <button type="button" class="btn btn-primary">Update </button>
                                    <button type="button" class="btn btn-primary">Delete Account</button>
                                </div>



                            </form>
                        </div>
                    </div>

                </div>
            </div>
            <!-- Sales Chart End -->







            <!-- Footer Start -->
            <div class=" pt-4 px-4">
                <div class=" rounded-top p-4">
                    <div class="row">
                        <div class="col-12 col-sm-6 text-center text-sm-start">
                            &copy; <a href="#">Your Site Name</a>, All Right Reserved.
                        </div>
                        <div class="col-12 col-sm-6 text-center text-sm-end">
                            <!--/*** This template is free as long as you keep the footer author’s credit link/attribution link/backlink. If you'd like to use the template without the footer author’s credit link/attribution link/backlink, you can purchase the Credit Removal License from "https://htmlcodex.com/credit-removal". Thank you for your support. ***/-->
                            Designed By <a href="https://htmlcodex.com">HTML Codex</a>
                            <br>Distributed By: <a href="https://themewagon.com" target="_blank">ThemeWagon</a>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Footer End -->
        </div>
        <!-- Content End -->


        <!-- Back to Top -->
        <a href="#" class="btn btn-lg btn-primary btn-lg-square back-to-top"><i class="bi bi-arrow-up"></i></a>
    </div>

    <?php


include('../admin/include/javascript.php')

     
     ?>
</body>

</html>