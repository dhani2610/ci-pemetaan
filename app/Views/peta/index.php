<?= $this->extend('layout/default') ?>

<?= $this->section('title') ?>
<title>Peta Sebaran — Apps Pemetaan</title>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine@3.2.12/dist/leaflet-routing-machine.css" />

<style>
    /* Styling Peta */
    #map-wrapper {
        position: relative;
        height: 85vh; /* Sedikit ditinggikan */
        width: 100%;
        border: 1px solid #ddd;
        border-radius: 5px;
        overflow: hidden;
    }
    #map {
        height: 100%;
        width: 100%;
        z-index: 1;
    }

    /* Floating Search Bar */
    .search-container {
        position: absolute;
        top: 20px;
        left: 60px;
        z-index: 999;
        background: white;
        padding: 10px;
        border-radius: 5px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        width: 300px;
    }

    /* Floating Route Panel */
    .route-panel {
        position: absolute;
        top: 20px;
        right: 20px;
        z-index: 999;
        background: white;
        padding: 15px;
        border-radius: 5px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        width: 320px;
        display: none;
        max-height: 85vh; /* Agar bisa discroll jika rute panjang */
        overflow-y: auto;
    }

    /* Styling Instruksi Rute */
    .step-item {
        font-size: 12px;
        border-bottom: 1px solid #eee;
        padding: 5px 0;
        display: flex;
        justify-content: space-between;
    }
    .step-icon { margin-right: 5px; }
    
    .custom-icon { width: 40px; height: 40px; }
</style>

<section class="section">
    <div class="section-header">
        <h1>Peta Realtime & Navigasi</h1>
    </div>

    <div class="section-body">
        <div class="card">
            <div class="card-body p-0">
                <div id="map-wrapper">
                    
                    <div class="search-container">
                        <div class="input-group">
                            <input type="text" id="search-input" class="form-control" placeholder="Cari Outlet...">
                            <div class="input-group-append">
                                <button class="btn btn-primary" id="btn-search"><i class="fas fa-search"></i></button>
                            </div>
                        </div>
                    </div>

                    <div class="route-panel" id="route-panel">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="m-0"><i class="fas fa-directions"></i> Petunjuk Arah</h6>
                            <button class="btn btn-sm btn-danger" onclick="clearRoute()">&times;</button>
                        </div>
                        <div id="route-summary" class="alert alert-info py-2 px-3 mb-2" style="font-size: 13px;"></div>
                        <div id="route-instructions">Loading...</div>
                    </div>

                    <div id="map"></div>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="modal fade" id="modalDetail" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Lokasi</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-5">
                        <img id="d-foto" src="" class="img-fluid rounded mb-3" style="width: 100%; height: 200px; object-fit: cover;">
                        <button class="btn btn-primary btn-block" id="btn-rute-modal"><i class="fas fa-location-arrow"></i> Rute Ke Sini</button>
                    </div>
                    <div class="col-md-7">
                        <table class="table table-sm table-borderless">
                            <tr><th width="30%">Nama</th><td>: <span id="d-nama"></span></td></tr>
                            <tr><th>Kategori</th><td>: <span id="d-kategori" class="badge badge-info"></span></td></tr>
                            <tr><th>Alamat</th><td>: <span id="d-alamat"></span></td></tr>
                            <tr><th>Deskripsi</th><td>: <span id="d-deskripsi"></span></td></tr>
                        </table>
                        <hr>
                        <h6>Daftar Produk</h6>
                        <div class="table-responsive" style="max-height: 150px; overflow-y: auto;">
                            <table class="table table-bordered table-sm">
                                <thead><tr><th>Nama Produk</th><th>Harga</th></tr></thead>
                                <tbody id="d-produk"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet-routing-machine@3.2.12/dist/leaflet-routing-machine.js"></script>

<script>
    var map;
    var userMarker;
    var userLat, userLng;
    
    // UBAH: Gunakan Object {} bukan Array [] agar bisa update berdasarkan ID dengan cepat
    var outletMarkers = {}; 
    
    var routingControl;

    // Icons
    var iconUser = L.icon({ iconUrl: '<?= base_url('icon/user.png') ?>', iconSize: [40, 40], popupAnchor: [0, -20] });
    var iconKuliner = L.icon({ iconUrl: '<?= base_url('icon/kuliner.png') ?>', iconSize: [35, 45], popupAnchor: [0, -20] });
    var iconOleh = L.icon({ iconUrl: '<?= base_url('icon/oleh-oleh.png') ?>', iconSize: [35, 45], popupAnchor: [0, -20] });

    $(document).ready(function() {
        initMap();
        getUserLocation();
        loadGeoJson();
        
        // Load data pertama kali
        fetchOutlets(); 
        
        // UBAH: Set interval untuk refresh data setiap 3 detik (3000 ms)
        setInterval(fetchOutlets, 3000);
    });

    function initMap() {
        map = L.map('map').setView([-7.79558, 110.36949], 11);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);
    }

    function getUserLocation() {
        if (navigator.geolocation) {
            navigator.geolocation.watchPosition(function(position) { // Pakai watchPosition agar marker user juga realtime
                userLat = position.coords.latitude;
                userLng = position.coords.longitude;
                
                if(userMarker) {
                    userMarker.setLatLng([userLat, userLng]);
                } else {
                    userMarker = L.marker([userLat, userLng], {icon: iconUser}).addTo(map)
                        .bindPopup("<b>Lokasi Anda</b>").openPopup();
                }
            }, function(err) { console.log(err); }, { enableHighAccuracy: true });
        }
    }

    function loadGeoJson() {
        fetch("<?= base_url('geojson/yogya.geojson') ?>")
        .then(res => res.json())
        .then(data => {
            L.geoJSON(data, { style: { color: "#666", weight: 1, fillOpacity: 0.1 } }).addTo(map);
        });
    }

    // UBAH: Fungsi Fetch Realtime
    function fetchOutlets() {
        $.getJSON("<?= site_url('peta/api/outlets') ?>", function(data) {
            
            // Loop data baru dari server
            data.forEach(function(outlet) {
                var id = outlet.id_outlet;
                
                // Cek apakah marker sudah ada di Peta?
                if (outletMarkers[id]) {
                    // JIKA ADA: Update posisinya saja (Animasi pindah)
                    outletMarkers[id].setLatLng([outlet.latitude, outlet.longitude]);
                    
                    // Update data yang tersimpan di marker (untuk search & popup)
                    outletMarkers[id].outletData = outlet; 
                } else {
                    // JIKA BELUM ADA: Buat marker baru
                    var myIcon = (outlet.kategori == 'Kuliner') ? iconKuliner : iconOleh;
                    
                    var marker = L.marker([outlet.latitude, outlet.longitude], {icon: myIcon}).addTo(map);
                    
                    // Isi konten Popup
                    var popupContent = `
                        <div class="text-center">
                            <b>${outlet.nama}</b><br>
                            <span class="badge badge-light">${outlet.kategori}</span><br>
                            <button class="btn btn-sm btn-info mt-2" onclick="showDetail(${outlet.id_outlet})">Detail</button>
                            <button class="btn btn-sm btn-success mt-2" onclick="getRoute(${outlet.latitude}, ${outlet.longitude})">Ke Sini</button>
                        </div>
                    `;
                    marker.bindPopup(popupContent);
                    
                    // Simpan data di properti marker
                    marker.outletData = outlet;

                    // Masukkan ke Object penyimpanan
                    outletMarkers[id] = marker;
                }
            });
        });
    }

    // Fitur Search (Diperbarui karena outletMarkers sekarang Object)
    $('#search-input').on('keyup', function() {
        var keyword = $(this).val().toLowerCase();
        
        // Ubah Object ke Array dulu untuk searching
        var markersArray = Object.values(outletMarkers);
        
        var found = markersArray.find(m => m.outletData.nama.toLowerCase().includes(keyword));
        
        if(found && keyword.length > 2) {
            map.setView(found.getLatLng(), 15);
            found.openPopup();
        }
    });

    // Detail Function (Sama seperti sebelumnya)
    window.showDetail = function(id) {
        $.getJSON("<?= site_url('peta/api/detail/') ?>" + id, function(res) {
            var o = res.outlet;
            $('#d-nama').text(o.nama);
            $('#d-kategori').text(o.kategori);
            $('#d-alamat').text(o.alamat);
            $('#d-deskripsi').text(o.deskripsi);
            var imgUrl = o.foto ? '<?= base_url('uploads/outlet/') ?>/' + o.foto : 'https://via.placeholder.com/300';
            $('#d-foto').attr('src', imgUrl);
            $('#btn-rute-modal').attr('onclick', `getRoute(${o.latitude}, ${o.longitude}); $('#modalDetail').modal('hide');`);
            
            var prodHtml = '';
            if(res.produk && res.produk.length > 0){
                res.produk.forEach(p => {
                    prodHtml += `<tr><td>${p.nama}</td><td>Rp ${new Intl.NumberFormat('id-ID').format(p.harga)}</td></tr>`;
                });
            } else { prodHtml = '<tr><td colspan="2">Kosong</td></tr>'; }
            $('#d-produk').html(prodHtml);
            $('#modalDetail').modal('show');
        });
    };

    // UBAH: Routing Logic dengan Instruksi Lengkap
    window.getRoute = function(destLat, destLng) {
        if(!userLat) { alert("Menunggu lokasi GPS Anda..."); return; }

        $('#route-panel').show();
        $('#route-instructions').html('<i>Menghitung rute...</i>');

        if(routingControl) { map.removeControl(routingControl); }

        routingControl = L.Routing.control({
            waypoints: [
                L.latLng(userLat, userLng),
                L.latLng(destLat, destLng)
            ],
            routeWhileDragging: false,
            // Sembunyikan default container leaflet
            createMarker: function() { return null; },
            show: false // Jangan show default panel di map
        }).addTo(map);

        routingControl.on('routesfound', function(e) {
            var routes = e.routes;
            var summary = routes[0].summary;
            var instructions = routes[0].instructions; // UBAH: Ambil data instruksi

            // 1. Tampilkan Summary (Jarak & Waktu)
            var dist = (summary.totalDistance / 1000).toFixed(1) + ' km';
            var time = Math.round(summary.totalTime / 60) + ' menit';
            $('#route-summary').html(`<b>Jarak:</b> ${dist} &bull; <b>Estimasi:</b> ${time}`);

            // 2. Loop Instruksi Jalan (Turn-by-turn)
            var stepsHtml = '<div class="list-group list-group-flush">';
            
            instructions.forEach(function(step, i) {
                // translate ikon arah sederhana (opsional)
                var icon = '<i class="fas fa-arrow-up"></i>'; // Default lurus
                if(step.type == 'TurnRight') icon = '<i class="fas fa-arrow-right"></i>';
                if(step.type == 'TurnLeft') icon = '<i class="fas fa-arrow-left"></i>';
                if(step.type == 'Roundabout') icon = '<i class="fas fa-sync"></i>';
                if(step.type == 'DestinationReached') icon = '<i class="fas fa-map-marker-alt text-danger"></i>';

                stepsHtml += `
                    <div class="step-item">
                        <span><span class="step-icon text-primary">${icon}</span> ${step.text}</span>
                        <span class="text-muted font-weight-bold" style="min-width:50px; text-align:right;">${Math.round(step.distance)}m</span>
                    </div>
                `;
            });
            stepsHtml += '</div>';

            $('#route-instructions').html(stepsHtml);
            
            // Sembunyikan container bawaan Leaflet Routing Machine agar tidak double
            $('.leaflet-routing-container').hide();
        });
    };

    window.clearRoute = function() {
        if(routingControl) {
            map.removeControl(routingControl);
            routingControl = null;
        }
        $('#route-panel').hide();
    }
</script>

<?= $this->endSection() ?>