<?= $this->extend('layout/default') ?>

<?= $this->section('title') ?>
<title>Data Outlet &mdash; Apps Pemetaan</title>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<style>
    /* Agar map muncul di dalam modal */
    #mapContainer { height: 400px; width: 100%; border-radius: 5px; margin-top: 28px;}
    .ops-row { margin-bottom: 10px; border-bottom: 1px dashed #eee; padding-bottom: 5px; }
    .badge-warning{
        background: orange;
    }
</style>

<section class="section">
    <div class="section-header">
        <h1>Data Outlet</h1>
        <div class="section-header-button">
            <button class="btn btn-primary" onclick="tambahData()">Tambah Data</button>
        </div>
    </div>

    <?php if(session()->getFlashdata('success')) : ?>
        <div class="alert alert-success alert-dismissible show fade">
            <div class="alert-body"><button class="close" data-dismiss="alert">x</button>
                <?= session()->getFlashdata('success') ?>
            </div>
        </div>
    <?php endif ?>

    <div class="section-body">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-md">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Outlet</th>
                                <th>Kategori</th>
                                <th>Kabupaten</th>
                                <th>Lokasi</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($outlets as $key => $row): ?>
                            <tr>
                                <td><?= $key + 1 ?></td>
                                <td><?= $row['nama'] ?></td>
                                <td>
                                    <?= $row['kategori'] ?>
                                </td>
                                <td><?= $row['kabupaten'] ?></td>
                                <td><a href="https://maps.google.com/?q=<?= $row['latitude'] ?>,<?= $row['longitude'] ?>" target="_blank" class="btn btn-sm btn-info"><i class="fas fa-map-marker-alt"></i> Maps</a></td>
                                <td>
                                    <?php 
                                    $badgeClass = 'badge-secondary';
                                    if($row['status'] == 'Approved') $badgeClass = 'badge-success';
                                    elseif($row['status'] == 'Rejected') $badgeClass = 'badge-danger';
                                    elseif($row['status'] == 'Waiting Approval') $badgeClass = 'badge-warning';
                                    ?>
                                    <span class="badge <?= $badgeClass ?>"><?= $row['status'] ?></span>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-warning btn-edit" data-id="<?= $row['id_outlet'] ?>"><i class="fas fa-edit"></i></button>
                                        <form action="<?= base_url('outlet/' . $row['id_outlet']) ?>" method="post" class="d-inline" onsubmit="return confirm('Hapus data?')">
                                            <input type="hidden" name="_method" value="DELETE">
                                            <button class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                                        </form>

                                        <?php if (session()->get('role') == 'admin') : ?>
                                            <div class="dropdown d-inline">
                                                <button class="btn btn-sm btn-info dropdown-toggle" type="button" data-toggle="dropdown">
                                                    Approval
                                                </button>
                                                <div class="dropdown-menu">
                                                    <form action="<?= base_url('outlet/approve') ?>" method="post">
                                                        <input type="hidden" name="id_outlet" value="<?= $row['id_outlet'] ?>">
                                                        <button type="submit" name="status" value="Approved" class="dropdown-item text-success">
                                                            <i class="fas fa-check"></i> Approve
                                                        </button>
                                                    </form>
                                                    <form action="<?= base_url('outlet/approve') ?>" method="post">
                                                        <input type="hidden" name="id_outlet" value="<?= $row['id_outlet'] ?>">
                                                        <button type="submit" name="status" value="Rejected" class="dropdown-item text-danger">
                                                            <i class="fas fa-times"></i> Reject
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="modal fade" id="modalOutlet" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog modal-xl" role="document"> <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Tambah Data Outlet</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formOutlet" action="<?= site_url('outlet/store') ?>" method="post" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <input type="hidden" name="id_outlet" id="id_outlet"> <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Nama Outlet</label>
                                <input type="text" name="nama" id="nama" class="form-control" required>
                            </div>
                            
                            <div class="form-group">
                                <label>Pemilik UMKM</label>
                                <select name="id_umkm" id="id_umkm" class="form-control">
                                    <?php foreach($umkm_list as $u): ?>
                                        <option value="<?= $u->id_umkm ?>"><?= $u->nama ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Alamat</label>
                                <textarea name="alamat" id="alamat" class="form-control" style="height: 60px;"></textarea>
                            </div>

                            <div class="form-group">
                                <label>Kabupaten</label>
                                <select name="kabupaten" id="kabupaten" class="form-control">
                                    <option value="Yogyakarta">Yogyakarta</option>
                                    <option value="Sleman">Sleman</option>
                                    <option value="Bantul">Bantul</option>
                                    <option value="Gunung Kidul">Gunung Kidul</option>
                                    <option value="Kulon Progo">Kulon Progo</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Kategori Outlet</label>
                                <select name="kategori" id="kategori" class="form-control">
                                    <option value="Pusat Oleh Oleh">Pusat Oleh Oleh</option>
                                    <option value="Kuliner">Kuliner</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Jam Operasional <button type="button" class="btn btn-xs btn-primary ml-2" onclick="addOperasionalRow()"><i class="fas fa-plus"></i></button></label>
                                <div id="operasional-container">
                                    </div>
                            </div>

                            <div class="form-group">
                                <label>Kontak (WA/Telp)</label>
                                <input type="text" name="kontak" id="kontak" class="form-control">
                            </div>

                            <div class="form-group">
                                <label>Deskripsi</label>
                                <textarea name="deskripsi" id="deskripsi" class="form-control"></textarea>
                            </div>

                            <div class="row">
                                <div class="col-6">
                                    <div class="form-group">
                                        <label>Latitude</label>
                                        <input type="text" name="latitude" id="latitude" class="form-control" readonly required>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group">
                                        <label>Longitude</label>
                                        <input type="text" name="longitude" id="longitude" class="form-control" readonly required>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Foto Outlet</label>
                                <input type="file" name="foto" required class="form-control">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label>Pilih Titik Koordinat</label>
                            <button type="button" class="btn btn-sm btn-info mb-2 float-right" onclick="getLocation()"><i class="fas fa-location-arrow"></i> Lokasi Saya</button>
                            <div id="mapContainer"></div>
                            <small class="text-muted">Geser pin untuk menyesuaikan lokasi.</small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-whitesmoke br">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    var map, marker;
    var defaultLat = -7.79558; // Yogyakarta Center
    var defaultLng = 110.36949;

    $(document).ready(function() {
        // Saat modal dibuka, inisialisasi Map agar tidak error ukuran
        $('#modalOutlet').on('shown.bs.modal', function(){
            setTimeout(function() {
                initMap();
            }, 100);
        });
    });

    // --- LOGIKA MAPS & GEOJSON ---
    function initMap() {
        if (map != undefined) { map.remove(); } // Reset map jika sudah ada

        var curLat = $('#latitude').val() || defaultLat;
        var curLng = $('#longitude').val() || defaultLng;

        map = L.map('mapContainer').setView([curLat, curLng], 11);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        // Load GeoJSON Yogyakarta
        fetch("<?= base_url('geojson/yogya.geojson') ?>")
            .then(response => response.json())
            .then(data => {
                L.geoJSON(data, {
                    style: function (feature) {
                        return {color: "#3388ff", weight: 2, fillOpacity: 0.1};
                    }
                }).addTo(map);
            })
            .catch(error => console.error('Error loading GeoJSON:', error));

        // Tambah Marker (Draggable)
        marker = L.marker([curLat, curLng], {draggable: true}).addTo(map);

        // Event saat marker digeser
        marker.on('dragend', function (e) {
            var position = marker.getLatLng();
            updateInputLatLng(position.lat, position.lng);
        });

        // Event saat map diklik (pindahkan marker)
        map.on('click', function(e) {
            marker.setLatLng(e.latlng);
            updateInputLatLng(e.latlng.lat, e.latlng.lng);
        });
    }

    function updateInputLatLng(lat, lng) {
        $('#latitude').val(lat.toFixed(6));
        $('#longitude').val(lng.toFixed(6));
    }

    // Fitur Auto Detect Lokasi User
    function getLocation() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(showPosition);
        } else {
            alert("Geolocation is not supported by this browser.");
        }
    }

    function showPosition(position) {
        var lat = position.coords.latitude;
        var lng = position.coords.longitude;
        marker.setLatLng([lat, lng]);
        map.setView([lat, lng], 15);
        updateInputLatLng(lat, lng);
    }

    // --- LOGIKA CRUD ---
    
    // 1. Tambah Data
    function tambahData() {
        $('#formOutlet')[0].reset();
        $('#id_outlet').val('');
        $('#formOutlet').attr('action', '<?= site_url('outlet/store') ?>');
        $('#modalTitle').text('Tambah Data Outlet');
        
        // Bersihkan dynamic row
        $('#operasional-container').html('');
        addOperasionalRow(); // Tambah 1 baris default

        // Reset LatLong ke default Yogya
        $('#latitude').val(defaultLat);
        $('#longitude').val(defaultLng);

        $('#modalOutlet').modal('show');
    }

    // 2. Edit Data (Fetch via AJAX)
    function editData(id) {
        $('#formOutlet')[0].reset();
        $('#operasional-container').html(''); // Bersihkan dulu

        // Ambil data via API kecil di Controller
        $.ajax({
            url: '<?= site_url('outlet/getData/') ?>' + id,
            type: 'GET',
            dataType: 'JSON',
            success: function(response) {
                var d = response.outlet;
                var ops = response.ops;

                $('#id_outlet').val(d.id_outlet);
                $('#id_umkm').val(d.id_umkm);
                $('#nama').val(d.nama);
                $('#alamat').val(d.alamat);
                $('#kabupaten').val(d.kabupaten);
                $('#kategori').val(d.kategori);
                $('#kontak').val(d.kontak);
                $('#deskripsi').val(d.deskripsi);
                $('#latitude').val(d.latitude);
                $('#longitude').val(d.longitude);

                // Populate Operasional
                if(ops.length > 0) {
                    ops.forEach(function(item){
                        addOperasionalRow(item.nama_hari, item.jam_buka, item.jam_tutup);
                    });
                } else {
                    addOperasionalRow();
                }

                // Setup Modal
                $('#formOutlet').attr('action', '<?= site_url('outlet/update') ?>');
                $('#modalTitle').text('Edit Data Outlet');
                $('#modalOutlet').modal('show');
            }
        });
    }

    // 3. Delete SweetAlert
    function confirmDelete(id) {
        Swal.fire({
            title: 'Yakin hapus outlet ini?',
            text: "Data tidak bisa dikembalikan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Ya, Hapus!'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-form-' + id).submit();
            }
        })
    }

    // --- DYNAMIC INPUT OPERASIONAL ---
    function addOperasionalRow(hariVal = '', bukaVal = '', tutupVal = '') {
        var hariOption = `
            <select name="hari[]" class="form-control form-control-sm">
                <option value="">- Pilih Hari -</option>
                <option value="Senin">Senin</option>
                <option value="Selasa">Selasa</option>
                <option value="Rabu">Rabu</option>
                <option value="Kamis">Kamis</option>
                <option value="Jumat">Jumat</option>
                <option value="Sabtu">Sabtu</option>
                <option value="Minggu">Minggu</option>
            </select>
        `;

        var html = `
            <div class="row ops-row">
                <div class="col-4">${hariOption}</div>
                <div class="col-3"><input type="time" name="jam_buka[]" class="form-control form-control-sm" value="${bukaVal}"></div>
                <div class="col-3"><input type="time" name="jam_tutup[]" class="form-control form-control-sm" value="${tutupVal}"></div>
                <div class="col-2"><button type="button" class="btn btn-danger btn-sm" onclick="$(this).closest('.row').remove()"><i class="fas fa-times"></i></button></div>
            </div>
        `;

        var $row = $(html);
        $('#operasional-container').append($row);
        
        // Set nilai select hari jika ada (saat edit)
        if(hariVal) {
            $row.find('select[name="hari[]"]').val(hariVal);
        }
    }
</script>

<?= $this->endSection() ?>