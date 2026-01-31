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
        height: 85vh;
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
        max-height: 85vh;
        overflow-y: auto;
    }

    /* Floating LOG Panel (DEBUG) */
    #debug-log {
        position: absolute;
        bottom: 20px;
        left: 20px;
        z-index: 9999;
        background: rgba(0, 0, 0, 0.8);
        color: #00ff00; /* Warna hijau ala hacker */
        padding: 10px;
        border-radius: 5px;
        width: 300px;
        font-family: monospace;
        font-size: 11px;
        max-height: 200px;
        overflow-y: auto;
        pointer-events: none; /* Supaya klik tembus ke peta */
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

                    <div id="debug-log">
                        <strong>SYSTEM LOG:</strong><br>
                        <span id="log-content">Menunggu GPS...</span>
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
    
    // Variabel Global Navigasi
    var activeDestLat = null; 
    var activeDestLng = null; 
    var isNavigating = false; 

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
        fetchOutlets(); 
        
        setInterval(fetchOutlets, 3000);
    });

    function addLog(message) {
        var time = new Date().toLocaleTimeString();
        var html = `[${time}] ${message}<br>`;
        $('#log-content').prepend(html); // Pesan baru di atas
    }

    function initMap() {
        map = L.map('map').setView([-7.79558, 110.36949], 11);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap'
        }).addTo(map);
    }

    function getUserLocation() {
        if (navigator.geolocation) {
            addLog("Mengaktifkan GPS Watcher...");
            
            navigator.geolocation.watchPosition(function(position) {
                var lat = position.coords.latitude;
                var lng = position.coords.longitude;
                var acc = position.coords.accuracy;

                userLat = lat;
                userLng = lng;
                var newUserLatLng = new L.LatLng(userLat, userLng);
                
                addLog(`GPS Update: ${lat.toFixed(5)}, ${lng.toFixed(5)}`);
                
                if(userMarker) {
                    userMarker.setLatLng(newUserLatLng);
                    userMarker.setPopupContent(`<b>Lokasi Anda</b><br>Akurasi: ${Math.round(acc)}m`);
                } else {
                    userMarker = L.marker(newUserLatLng, {icon: iconUser}).addTo(map)
                        .bindPopup(`<b>Lokasi Anda</b><br>Akurasi: ${Math.round(acc)}m`).openPopup();
                    map.setView(newUserLatLng, 15); // Zoom awal ke user
                }

                // ===============================================
                // UPDATE RUTE JIKA SEDANG NAVIGASI
                // ===============================================
                if (isNavigating && routingControl && activeDestLat && activeDestLng) {
                    addLog("-> Menghitung ulang rute...");
                    
                    routingControl.setWaypoints([
                        newUserLatLng,
                        L.latLng(activeDestLat, activeDestLng)
                    ]);
                }

            }, function(err) { 
                addLog("ERROR GPS: " + err.message);
                console.log(err); 
            }, { 
                enableHighAccuracy: true, 
                maximumAge: 0,            
                timeout: 5000             
            });
        } else {
            alert("Browser Anda tidak mendukung Geolocation.");
        }
    }

    function loadGeoJson() {
        fetch("<?= base_url('geojson/yogya.geojson') ?>")
        .then(res => res.json())
        .then(data => {
            L.geoJSON(data, { style: { color: "#666", weight: 1, fillOpacity: 0.1 } }).addTo(map);
        });
    }

    function fetchOutlets() {
        
        $.getJSON("<?= site_url('peta/api/outlets') ?>", function(data) {
            data.forEach(function(outlet) {
                var id = outlet.id_outlet;
                
                if (outletMarkers[id]) {
                    var oldLatLng = outletMarkers[id].getLatLng();
                    if(oldLatLng.lat != outlet.latitude || oldLatLng.lng != outlet.longitude) {
                        outletMarkers[id].setLatLng([outlet.latitude, outlet.longitude]);
                        addLog(`Outlet ${outlet.nama} berpindah lokasi.`);
                    }
                    outletMarkers[id].outletData = outlet; 
                } else {
                    var myIcon = (outlet.kategori == 'Kuliner') ? iconKuliner : iconOleh;
                    var marker = L.marker([outlet.latitude, outlet.longitude], {icon: myIcon}).addTo(map);
                    
                    var popupContent = `
                        <div class="text-center">
                            <b>${outlet.nama}</b><br>
                            <span class="badge badge-light">${outlet.kategori}</span><br>
                            <button class="btn btn-sm btn-info mt-2" onclick="showDetail(${outlet.id_outlet})">Detail</button>
                            <button class="btn btn-sm btn-success mt-2" onclick="getRoute(${outlet.latitude}, ${outlet.longitude})">Ke Sini</button>
                        </div>
                    `;
                    marker.bindPopup(popupContent);
                    marker.outletData = outlet;
                    outletMarkers[id] = marker;
                }
            });
        });
    }

    $('#search-input').on('keyup', function() {
        var keyword = $(this).val().toLowerCase();
        var markersArray = Object.values(outletMarkers);
        var found = markersArray.find(m => m.outletData.nama.toLowerCase().includes(keyword));
        if(found && keyword.length > 2) {
            map.setView(found.getLatLng(), 15);
            found.openPopup();
        }
    });

    window.showDetail = function(id) {
        addLog("Membuka detail outlet ID: " + id);
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

    window.getRoute = function(destLat, destLng) {
        if(!userLat) { alert("Menunggu lokasi GPS Anda..."); return; }

        activeDestLat = destLat;
        activeDestLng = destLng;
        isNavigating = true; 

        addLog(`Memulai navigasi ke: ${destLat}, ${destLng}`);
        $('#route-panel').show();
        $('#route-instructions').html('<i>Menghitung rute...</i>');

        if(routingControl) { map.removeControl(routingControl); }

        routingControl = L.Routing.control({
            waypoints: [
                L.latLng(userLat, userLng), 
                L.latLng(destLat, destLng)
            ],
            routeWhileDragging: false,
            addWaypoints: false, 
            createMarker: function() { return null; },
            show: false 
        }).addTo(map);

        // Event listener saat rute ditemukan
        routingControl.on('routesfound', function(e) {
            var routes = e.routes;
            var summary = routes[0].summary;
            var instructions = routes[0].instructions;

            var dist = (summary.totalDistance / 1000).toFixed(1) + ' km';
            var time = Math.round(summary.totalTime / 60) + ' menit';
            $('#route-summary').html(`<b>Jarak:</b> ${dist} &bull; <b>Estimasi:</b> ${time}`);
            
            addLog("Rute diperbarui. Jarak: " + dist);

            var stepsHtml = '<div class="list-group list-group-flush">';
            instructions.forEach(function(step, i) {
                var icon = '<i class="fas fa-arrow-up"></i>';
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
            $('.leaflet-routing-container').hide();
        });
        
        routingControl.on('routingerror', function(e) {
            addLog("Error Routing: " + e.error.message);
        });
    };

    window.clearRoute = function() {
        if(routingControl) {
            map.removeControl(routingControl);
            routingControl = null;
        }
        isNavigating = false;
        activeDestLat = null;
        activeDestLng = null;
        addLog("Navigasi dihentikan.");
        $('#route-panel').hide();
    }
</script>

<?= $this->endSection() ?>