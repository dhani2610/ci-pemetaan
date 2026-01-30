<?= $this->extend('layout/default') ?>

<?= $this->section('title') ?>
<title>Data Produk — Apps Pemetaan</title>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<section class="section">
    <div class="section-header">
        <h1>Data Produk</h1>
    </div>

    <?php if (session()->getFlashdata('success')) : ?>
        <div class="alert alert-success alert-dismissible show fade">
            <div class="alert-body">
                <button class="close" data-dismiss="alert">x</button>
                <?= session()->getFlashdata('success') ?>
            </div>
        </div>
    <?php endif ?>

    <div class="section-body">
        <div class="card">
            <div class="card-header">
                <h4>List Produk</h4>
                <div class="card-header-action">
                    <button class="btn btn-primary" onclick="tambahData()">
                        <i class="fas fa-plus"></i> Tambah Produk
                    </button>
                </div>
            </div>
            <div class="card-body">
                
                <div class="row mb-3">
                    <div class="col-md-4">
                        <form action="" method="get">
                            <label>Filter berdasarkan Outlet:</label>
                            <div class="input-group">
                                <select name="id_outlet" class="form-control" onchange="this.form.submit()">
                                    <option value="">-- Tampilkan Semua --</option>
                                    <?php foreach($outlets as $out): ?>
                                        <option value="<?= $out['id_outlet'] ?>" <?= ($selected_outlet == $out['id_outlet']) ? 'selected' : '' ?>>
                                            <?= $out['nama'] ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-md">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Produk</th>
                                <th>Jenis</th>
                                <th>Harga</th>
                                <th>Asal Outlet</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(empty($produk)): ?>
                                <tr><td colspan="6" class="text-center">Tidak ada data produk</td></tr>
                            <?php else: ?>
                                <?php foreach ($produk as $key => $value) : ?>
                                    <tr>
                                        <td><?= $key + 1 ?></td>
                                        <td><?= $value->nama ?></td>
                                        <td><?= $value->jenis ?></td>
                                        <td>Rp <?= number_format($value->harga, 0, ',', '.') ?></td>
                                        <td><?= $value->nama_outlet ?></td>
                                        <td>
                                            <button class="btn btn-success btn-sm btn-edit" 
                                                data-id="<?= $value->id_produk ?>"
                                                data-nama="<?= $value->nama ?>"
                                                data-jenis="<?= $value->jenis ?>"
                                                data-harga="<?= $value->harga ?>"
                                                data-outlet="<?= $value->id_outlet ?>">
                                                <i class="fas fa-pencil-alt"></i>
                                            </button>
                                            
                                            <form id="delete-form-<?= $value->id_produk ?>" action="<?= site_url('produk/' . $value->id_produk) ?>" method="post" class="d-inline">
                                                <?= csrf_field() ?>
                                                <input type="hidden" name="_method" value="DELETE">
                                                <button type="button" class="btn btn-danger btn-sm" onclick="confirmDelete(<?= $value->id_produk ?>)">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="modal fade" id="modalProduk" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Tambah Produk</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formProduk" action="<?= site_url('produk/store') ?>" method="post">
                <?= csrf_field() ?>
                <div id="methodInput"></div> 
                <input type="hidden" name="id_produk" id="id_produk">

                <div class="modal-body">
                    <div class="form-group">
                        <label>Nama Produk</label>
                        <input type="text" name="nama" id="nama" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label>Pilih Outlet</label>
                        <select name="id_outlet" id="id_outlet" class="form-control" required>
                            <option value="">-- Pilih Outlet --</option>
                            <?php foreach($outlets as $out): ?>
                                <option value="<?= $out['id_outlet'] ?>"><?= $out['nama'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Jenis Produk</label>
                        <select name="jenis" id="jenis" class="form-control">
                            <option value="Makanan">Makanan</option>
                            <option value="Minuman">Minuman</option>
                            <option value="Kerajinan">Kerajinan</option>
                            <option value="Jasa">Jasa</option>
                            <option value="Lainnya">Lainnya</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Harga (Rp)</label>
                        <input type="number" name="harga" id="harga" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer bg-whitesmoke br">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // 1. Fungsi Buka Modal Tambah
    function tambahData() {
        // Reset Form
        $('#formProduk')[0].reset();
        $('#id_produk').val('');
        $('#methodInput').html(''); // Hapus spoofing PUT
        
        // Setup Modal untuk Mode Tambah
        $('#formProduk').attr('action', '<?= site_url('produk/store') ?>');
        $('#modalTitle').text('Tambah Produk');
        $('#modalProduk').modal('show');
    }

    // 2. Fungsi Buka Modal Edit (Menggunakan jQuery Event)
    $(document).ready(function() {
        $('.btn-edit').on('click', function() {
            // Ambil data dari tombol
            const id = $(this).data('id');
            const nama = $(this).data('nama');
            const jenis = $(this).data('jenis');
            const harga = $(this).data('harga');
            const outlet = $(this).data('outlet');

            // Isi Form
            $('#id_produk').val(id);
            $('#nama').val(nama);
            $('#jenis').val(jenis);
            $('#harga').val(harga);
            $('#id_outlet').val(outlet);

            // Tambahkan Method Spoofing PUT untuk Update
            $('#methodInput').html('<input type="hidden" name="_method" value="PUT">');

            // Setup Modal untuk Mode Edit
            $('#formProduk').attr('action', '<?= site_url('produk/update') ?>');
            $('#modalTitle').text('Edit Produk');
            $('#modalProduk').modal('show');
        });
    });

    // 3. Fungsi Hapus dengan SweetAlert
    function confirmDelete(id) {
        Swal.fire({
            title: 'Yakin hapus produk ini?',
            text: "Data yang dihapus tidak dapat dikembalikan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus!'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-form-' + id).submit();
            }
        })
    }
</script>
<?= $this->endSection() ?>