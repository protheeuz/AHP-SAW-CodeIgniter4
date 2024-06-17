<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>SPK - PT Parkland World Indonesia</title>

    <!-- Custom fonts for this template-->
    <link href="<?= base_url('assets/vendor/fontawesome-free/css/all.min.css') ?>" rel="stylesheet" type="text/css" />
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet" />
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap" rel="stylesheet">
    
    <!-- Custom styles for this template-->
    <link rel="stylesheet" type="text/css" href="<?= base_url('assets/css/style.css'); ?>">
    <link rel="shortcut icon" href="<?= base_url('assets/img/PWI2.png') ?>" type="image/x-icon">
    <link rel="icon" href="<?= base_url('assets/img/PWI2.png') ?>" type="image/x-icon">
</head>
<body>
<section class="login d-flex">
    <div class="login-left w-50 h-100">
        <div class="row h-100 justify-content-center align-items-center h-100">
            <div class="col-6">
                <div class="header mt-100">
                    <img src="<?= base_url('assets/img/PWI3.png') ?>" alt="Logo">
                    <h1>Hallo, Selamat Datang!</h1>
                    <p>Silahkan masuk terlebih dahulu</p>
                </div>
                <?php if (session()->getFlashdata('message')): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?= session()->getFlashdata('message') ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>
                <form class="user" action="<?= site_url('login') ?>" method="post">
                    <div class="form-group">
                        <label for="username" class="form-label">Username</label>
                        <input required autocomplete="off" type="text" class="form-control form-control-user" id="username" placeholder="Username" name="username">
                        
                        <label for="password" class="form-label">Password</label>
                        <input required autocomplete="off" type="password" class="form-control form-control-user" id="password" name="password" placeholder="Password">
                        <button class="btn btn-primary btn-user btn-block" name="submit" type="submit">Masuk</button>
                        <span class="copy">Copyright &copy; 2022 PT Parkland World Indonesia</span>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="login-right w-50 h-100">
        <div id="carouselExampleFade" class="carousel slide carousel-fade" data-bs-ride="carousel">
            <div class="carousel-inner">
                <div class="carousel-item active">
                    <img src="<?= base_url('assets/img/Data1.jpg') ?>" class="d-block w-100" alt="...">
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Bootstrap core JavaScript-->
<script src="<?= base_url('assets/vendor/jquery/jquery.min.js') ?>"></script>
<script src="<?= base_url('assets/vendor/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>

<!-- Core plugin JavaScript-->
<script src="<?= base_url('assets/vendor/jquery-easing/jquery.easing.min.js') ?>"></script>

<!-- Custom scripts for all pages-->
<script src="<?= base_url('assets/js/sb-admin-2.min.js') ?>"></script>
</body>
</html>