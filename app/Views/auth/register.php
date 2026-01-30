<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
  <title>Register &mdash; Sistem Pemetaan</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
  
  <link rel="stylesheet" href="<?=base_url()?>/template/assets/css/style.css">
  <link rel="stylesheet" href="<?=base_url()?>/template/assets/css/components.css">
</head>

<body>
  <div id="app">
    <section class="section">
      <div class="container mt-5">
        <div class="row">
          <div class="col-12 col-sm-10 offset-sm-1 col-md-8 offset-md-2 col-lg-8 offset-lg-2 col-xl-8 offset-xl-2">
            <div class="login-brand">
              </div>

            <div class="card card-primary">
              <div class="card-header"><h4>Register UMKM</h4></div>

              <div class="card-body">
                
                <?php if(session()->getFlashdata('errors')) : ?>
                    <div class="alert alert-danger alert-dismissible show fade">
                        <div class="alert-body">
                            <button class="close" data-dismiss="alert">x</button>
                            <b>Gagal Register:</b>
                            <ul>
                            <?php foreach(session()->getFlashdata('errors') as $error): ?>
                                <li><?= $error ?></li>
                            <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                <?php endif ?>

                <form method="POST" action="<?= site_url('auth/registerProcess') ?>">
                    <?= csrf_field() ?>
                    
                    <div class="form-group">
                        <label for="nama">Nama UMKM / Pemilik</label>
                        <input id="nama" type="text" class="form-control" name="nama" value="<?= old('nama') ?>" required autofocus>
                    </div>

                    <div class="form-group">
                        <label for="username">Username</label>
                        <input id="username" type="text" class="form-control" name="username" value="<?= old('username') ?>" required>
                    </div>

                    <div class="row">
                        <div class="form-group col-6">
                            <label for="password" class="d-block">Password</label>
                            <input id="password" type="password" class="form-control" name="password" required>
                        </div>
                        <div class="form-group col-6">
                            <label for="password_conf" class="d-block">Konfirmasi Password</label>
                            <input id="password_conf" type="password" class="form-control" name="password_conf" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="custom-control custom-checkbox">
                          <input type="checkbox" name="agree" class="custom-control-input" id="agree" required>
                          <label class="custom-control-label" for="agree">Saya setuju dengan syarat dan ketentuan</label>
                        </div>
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-primary btn-lg btn-block">
                        Daftar Sekarang
                        </button>
                    </div>
                </form>
              </div>
            </div>
            <div class="mt-5 text-muted text-center">
              Sudah punya akun? <a href="<?= site_url('login') ?>">Login disini</a>
            </div>
            <div class="simple-footer">
              Copyright &copy; 2024
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>

  <script src="<?=base_url()?>/template/node_modules/jquery/dist/jquery.min.js"></script>
  <script src="<?=base_url()?>/template/node_modules/bootstrap/dist/js/bootstrap.min.js"></script>
  <script src="<?=base_url()?>/template/assets/js/scripts.js"></script>
  <script src="<?=base_url()?>/template/assets/js/custom.js"></script>
</body>
</html>