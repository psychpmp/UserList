<?php
include 'db_connection.php';

// Fetch users
$sql = "SELECT id, fullname, address, birthday, emergency FROM users";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User List</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        #map { width: 100%; height: 400px; }
    </style>
</head>
<body class="bg-light">
    <div class="container mt-5">
        <h1 class="text-center mb-4">User List</h1>
        <div class="table-responsive">
            <table class="table table-striped table-bordered text-center">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Full Name</th>
                        <th>Address</th>
                        <th>Birthday</th>
                        <th>Emergency</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= $row['id'] ?></td>
                            <td><?= htmlspecialchars($row['fullname']) ?></td>
                            <td><?= htmlspecialchars($row['address']) ?></td>
                            <td><?= htmlspecialchars($row['birthday']) ?></td>
                            <td><?= $row['emergency'] === 'ON' ? '<span class="badge bg-success">ON</span>' : '<span class="badge bg-danger">OFF</span>' ?></td>
                            <td>
                                <button class="btn btn-primary btn-sm view-location-btn" 
                                        data-id="<?= $row['id'] ?>" 
                                        data-fullname="<?= htmlspecialchars($row['fullname']) ?>" 
                                        data-address="<?= htmlspecialchars($row['address']) ?>">
                                    View Location
                                </button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="locationModal" tabindex="-1" aria-labelledby="locationModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="locationModalLabel">User Location</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p id="user-info"></p>
                    <div id="map"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS and Leaflet JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        const modal = new bootstrap.Modal(document.getElementById('locationModal'));
        let map;

        document.querySelectorAll('.view-location-btn').forEach(button => {
            button.addEventListener('click', function () {
                const userId = this.dataset.id;
                const fullName = this.dataset.fullname;
                const address = this.dataset.address;

                document.getElementById('locationModalLabel').textContent = `${fullName}'s Last Location`;
                document.getElementById('user-info').textContent = `Address: ${address}`;

                fetch(`get_location.php?id=${userId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            const mapContainer = document.getElementById('map');

                            if (map) {
                                map.remove();
                            }

                            map = L.map(mapContainer).setView([data.latitude, data.longitude], 14);

                            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
                            }).addTo(map);

                            L.marker([data.latitude, data.longitude])
                                .addTo(map)
                                .bindPopup(`${fullName}'s Last Location`)
                                .openPopup();
                        }
                    });

                modal.show();

                setTimeout(() => {
                    if (map) {
                        map.invalidateSize();
                    }
                }, 300);
            });
        });
    </script>
</body>
</html>