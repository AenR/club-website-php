<?php
session_start();
include '../db.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

// Bilgileri güncelle
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $face = $_POST['face'];
    $insta = $_POST['insta'];

    $uploadDir = '../uploads/';
    $logoPath = isset($row['logo']) ? $row['logo'] : '';

    if (isset($_FILES['logo_file']) && $_FILES['logo_file']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['logo_file']['tmp_name'];
        $fileName = basename($_FILES['logo_file']['name']);
        $dest_path = $uploadDir . time() . '_' . $fileName;

        if (move_uploaded_file($fileTmpPath, $dest_path)) {
            $logoPath = $dest_path;
        }
    }

    $stmt = $conn->prepare("UPDATE info SET title=?, name=?, phone=?, email=?, face=?, insta=?, logo=? LIMIT 1");
    $stmt->bind_param("sssssss", $title, $name, $phone, $email, $face, $insta, $logoPath);
    $stmt->execute();
}

// Bilgileri çek
$result = $conn->query("SELECT * FROM info LIMIT 1");
$row = $result->fetch_assoc();

// Rezervasyonları sil
if (isset($_POST['delete_reservation_id'])) {
    $deleteId = $_POST['delete_reservation_id'];
    $stmt = $conn->prepare("DELETE FROM events WHERE id = ?");
    $stmt->bind_param("i", $deleteId);
    $stmt->execute();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
<nav class="navbar navbar-dark bg-dark">
    <div class="container-fluid">
        <span class="navbar-brand mb-0 h1">Admin Panel</span>
        <a href="logout.php" class="btn btn-outline-light">Logout</a>
    </div>
</nav>

<div class="container mt-5 col-8">
    <h3 class="mb-4">Update Site Information</h3>
    <form method="post" enctype="multipart/form-data">
        <div class="mb-3">
            <label class="form-label">Site Title</label>
            <input type="text" name="title" class="form-control" value="<?php echo htmlspecialchars($row['title']); ?>">
        </div>
        <div class="mb-3">
            <label class="form-label">Name</label>
            <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($row['name']); ?>">
        </div>
        <div class="mb-3">
            <label class="form-label">Phone</label>
            <input type="text" name="phone" class="form-control" value="<?php echo htmlspecialchars($row['phone']); ?>">
        </div>
        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($row['email']); ?>">
        </div>
        <div class="mb-3">
            <label class="form-label">Facebook</label>
            <input type="text" name="face" class="form-control" value="<?php echo htmlspecialchars($row['face']); ?>">
        </div>
        <div class="mb-3">
            <label class="form-label">Instagram</label>
            <input type="text" name="insta" class="form-control" value="<?php echo htmlspecialchars($row['insta']); ?>">
        </div>
        <div class="mb-3">
            <label class="form-label">Logo Upload</label>
            <input type="file" name="logo_file" class="form-control">
        </div>
        <button type="submit" class="btn btn-success float-end my-3 col-5">Update</button>
    </form>
</div>

<div class="container mt-5 col-10">
    <h3 class="mb-4">Edit Events</h3>

    <?php
    $events_sql = "SELECT id, name, image, is_active FROM events ORDER BY id";
    $events_result = $conn->query($events_sql);

    if ($events_result && $events_result->num_rows > 0): ?>
        <div class="table-responsive">
            <table class="table table-bordered table-striped bg-white">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Image</th>
                    <th>Upload New Image</th>
                    <th>Active</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                <?php while ($event = $events_result->fetch_assoc()): ?>
                    <tr data-id="<?= $event['id'] ?>">
                        <td><?= $event['id'] ?></td>
                        <td contenteditable="true" class="editable" data-field="name"><?= htmlspecialchars($event['name']) ?></td>
                        <td><?= htmlspecialchars($event['image']) ?></td>
                        <td>
                            <form action="upload_event_image.php" method="POST" enctype="multipart/form-data">
                                <input type="hidden" name="event_id" value="<?= $event['id'] ?>">
                                <input type="file" name="event_image" accept="image/*" class="form-control mb-1">
                                <button type="submit" class="btn btn-sm btn-secondary">Upload</button>
                            </form>
                        </td>
                        <td>
                            <input type="checkbox" class="editable-checkbox" data-field="is_active" <?= $event['is_active'] ? 'checked' : '' ?>>
                        </td>
                        <td>
                            <button class="btn btn-sm btn-primary save-event-btn">Save</button>
                        </td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="alert alert-info">No events found.</div>
    <?php endif; ?>
</div>

<div class="container mt-5 col-10">
    <h3 class="mb-4">Event Registrations</h3>

    <?php
    $sql = "SELECT event_registrations.id, event_registrations.full_name, event_registrations.email, event_registrations.phone, event_registrations.registered_at, events.name AS event_name
            FROM event_registrations
            JOIN events ON event_registrations.event_id = events.id
            ORDER BY event_registrations.registered_at DESC";

    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0): ?>
        <div class="table-responsive">
            <table class="table table-bordered table-striped bg-white">
                <thead>
                <tr>
                    <th>#</th>
                    <th>Full Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Event</th>
                    <th>Registered At</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr data-id="<?= $row['id'] ?>">
                        <td><?= htmlspecialchars($row['id']) ?></td>
                        <td contenteditable="true" class="editable" data-field="full_name"><?= htmlspecialchars($row['full_name']) ?></td>
                        <td contenteditable="true" class="editable" data-field="email"><?= htmlspecialchars($row['email']) ?></td>
                        <td contenteditable="true" class="editable" data-field="phone"><?= htmlspecialchars($row['phone']) ?></td>
                        <td><?= htmlspecialchars($row['event_name']) ?></td>
                        <td><?= htmlspecialchars($row['registered_at']) ?></td>
                        <td>
                            <button class="btn btn-sm btn-primary save-btn">Save</button>
                            <a href="delete_registration.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?');">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="alert alert-info">No registrations found.</div>
    <?php endif; ?>
</div>

<script>
document.querySelectorAll('.save-btn').forEach(button => {
    button.addEventListener('click', function () {
        const tr = this.closest('tr');
        const id = tr.getAttribute('data-id');
        const full_name = tr.querySelector('[data-field="full_name"]').textContent.trim();
        const email = tr.querySelector('[data-field="email"]').textContent.trim();
        const phone = tr.querySelector('[data-field="phone"]').textContent.trim();

        fetch('edit_registration.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `id=${encodeURIComponent(id)}&full_name=${encodeURIComponent(full_name)}&email=${encodeURIComponent(email)}&phone=${encodeURIComponent(phone)}`
        })
            .then(response => response.text())
            .then(data => {
                alert('Registration updated successfully!');
            })
            .catch(error => {
                alert('Error updating registration.');
                console.error(error);
            });
    });
});

document.querySelectorAll('.save-event-btn').forEach(button => {
    button.addEventListener('click', function () {
        const tr = this.closest('tr');
        const id = tr.getAttribute('data-id');
        const name = tr.querySelector('[data-field="name"]').textContent.trim();
        const image = tr.querySelector('td:nth-child(3)').textContent.trim();
        const is_active = tr.querySelector('[data-field="is_active"]').checked ? 1 : 0;

        fetch('edit_event.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `id=${encodeURIComponent(id)}&name=${encodeURIComponent(name)}&image=${encodeURIComponent(image)}&is_active=${encodeURIComponent(is_active)}`
        })
            .then(response => response.text())
            .then(data => {
                alert('Event updated successfully!');
            })
            .catch(error => {
                alert('Error updating event.');
                console.error(error);
            });
    });
});
</script>

</body>
</html>