<?php
include 'koneksi.php';

$query = mysqli_query($koneksi, "SELECT * FROM pariwisata");

if (!$query) {
    die("Query gagal: " . mysqli_error($koneksi));
}

$features = array();
$data_tabel = array();

while ($row = mysqli_fetch_assoc($query)) {
    $data_tabel[] = $row;
    
    $lat = (float)$row['latitude'];
    $lng = (float)$row['longitude'];
    
    if ($lat != 0 && $lng != 0) {
        $features[] = array(
            'type' => 'Feature',
            'geometry' => array(
                'type' => 'Point',
                'coordinates' => array($lng, $lat)
            ),
            'properties' => array(
                'id' => $row['id'],
                'remark' => $row['remark'],
                'namobj' => $row['namobj']
            )
        );
    }
}

$geojson = array(
    'type' => 'FeatureCollection',
    'features' => $features
);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Peta dan Data Destinasi Pariwisata Kabupaten Klaten</title>

    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background-color: #f8f9fa; }
        h2 { text-align: center; color: #333; margin-bottom: 20px; }
        
        #map {
            height: 450px;
            width: 100%;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
            margin-bottom: 30px;
        }

        table { width: 100%; border-collapse: collapse; background: #fff; box-shadow: 0 2px 5px rgba(0,0,0,0.1); border-radius: 6px; overflow: hidden; margin-bottom: 25px; }
        table, th, td { border: 1px solid #dee2e6; }
        th, td { padding: 12px; text-align: left; }
        th { background-color: #f1f3f5; color: #495057; font-weight: bold; }
        tr:nth-child(even) { background-color: #f8f9fa; }
        
        .btn-edit { color: #007bff; text-decoration: none; font-weight: bold; }
        .btn-delete { color: #dc3545; text-decoration: none; font-weight: bold; }
        .btn-edit:hover, .btn-delete:hover { text-decoration: underline; }
    </style>
</head>
<body>

    <h2>WebGIS & Data Destinasi Pariwisata Kabupaten Klaten</h2>

    <!-- Peta Interaktif -->
    <div id="map"></div>

    <!-- Tabel Data -->
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>ogc_fid</th>
                <th>Nama / Remark</th>
                <th>Longitude</th>
                <th>Latitude</th>
                <th>Namobj</th>
                <th>Simbol</th>
                <th colspan="2">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $no = 1;
            foreach ($data_tabel as $row) : 
            ?>
            <tr>
                <td><?= $no++; ?></td>
                <td><?= htmlspecialchars($row['ogc_fid']); ?></td>
                <td><?= htmlspecialchars($row['remark']); ?></td>
                <td><?= htmlspecialchars($row['longitude']); ?></td>
                <td><?= htmlspecialchars($row['latitude']); ?></td>
                <td><?= htmlspecialchars($row['namobj']); ?></td>
                <td><?= htmlspecialchars($row['simbol']); ?></td>
                <td>
                    <a class="btn-edit" href="edit/index.php?id=<?= $row['id']; ?>">Edit</a>
                </td>
                <td>
                    <a class="btn-delete" href="delete.php?id=<?= $row['id']; ?>" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')">Hapus</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <script>
        var map = L.map('map').setView([-7.745, 110.510], 12);

        var osm = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap'
        });

        var esriWorldImagery = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
            attribution: 'Tiles &copy; Esri'
        });

        var rupaBumiIndonesia = L.tileLayer('https://geoservices.big.go.id/rbi/rest/services/RBI/Peta_Cetak_50k/MapServer/tile/{z}/{y}/{x}', {
            attribution: '© BIG'
        });

        esriWorldImagery.addTo(map);

        var geojsonData = <?php echo json_encode($geojson); ?>;

        var pariwisataLayer = L.geoJSON(geojsonData, {
            onEachFeature: function (feature, layer) {
                if (feature.properties && feature.properties.remark) {
                    layer.bindPopup("<b>" + feature.properties.remark + "</b><br>" + feature.properties.namobj);
                }
            }
        }).addTo(map);

        var baseMaps = {
            "OpenStreetMap": osm,
            "Esri World Imagery": esriWorldImagery,
            "Rupa Bumi Indonesia": rupaBumiIndonesia
        };

        var overlayMaps = {
            "Titik Destinasi Pariwisata": pariwisataLayer
        };

        L.control.layers(baseMaps, overlayMaps, { position: 'topright' }).addTo(map);
    </script>

</body>
</html>