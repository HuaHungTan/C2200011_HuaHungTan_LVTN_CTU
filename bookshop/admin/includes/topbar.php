<!-- Topbar -->
 <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

                    <!-- Sidebar Toggle (Topbar) -->

                    <!-- Topbar Search -->
                    <!-- Topbar Navbar -->
                    <ul class="navbar-nav ml-auto">

                        <!-- Nav Item - Search Dropdown (Visible Only XS) -->
                        <!-- Nav Item - Alerts -->
                        <!-- Nav Item - Messages -->
                        <div class="topbar-divider d-none d-sm-block"></div>
                        <!-- Nav Item - User Information -->
                        <li class="nav-item dropdown no-arrow">
                            <div class="text-end">
                                <?php if (isset($_SESSION['user_id'])) { ?>
                                <a href="userprofile.php?id=<?=$_SESSION['user_id']?>">
                                    <img src="../<?= $_SESSION['avt'] ?: 'data_image/avatar/default.jpg' ?>"
                                    alt="Ảnh đại diện"
                                    style="width:40px; height:40px; object-fit:cover; border-radius:50%; border:1px solid #ccc;">
                                <span class="ms-2 fw-bold"><?= $_SESSION['name'] ?></span>
                                </a>
                                
                                <a href="../user/logout.php" class="btn btn-sm btn-outline-danger ms-2">Đăng xuất</a>
                                <?php }  ?>
                            </div>
                            <!-- Dropdown - User Information -->

                        </li>

                    </ul>

                </nav>
                
<!-- End of Topbar -->