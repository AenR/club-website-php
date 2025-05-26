<?php
include 'db.php';

$sql_info = "SELECT * FROM info LIMIT 1";
$result_info = $conn->query($sql_info);

if ($result_info && $result_info->num_rows > 0) {
    $row = $result_info->fetch_assoc();
    $title = $row['title'];
    $name = $row['name'];
    $logo = $row['logo'];
    $phone = $row['phone'];
    $email = $row['email'];
    $insta = $row['insta'];
    $face = $row['face'];
}

$events = [];
$sql_events = "SELECT * FROM events";
$result_events = $conn->query($sql_events);

if ($result_events && $result_events->num_rows > 0) {
    while ($event = $result_events->fetch_assoc()) {
        $events[] = $event;
    }
}

$cleanLogoPath = str_replace('../', '', $logo);

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($title); ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="icon" href="img/favicon.ico" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
</head>

<body class="bg-dark text-light">
    <div class="container text-center mt-5 col-6">
        <img src="<?php echo htmlspecialchars($cleanLogoPath); ?>" alt="<?php echo htmlspecialchars($name) ?> Logo" class="img-fluid rounded-circle mb-4 col-3">
        <h1 class="mb-3 text-weight-bold"><?php echo htmlspecialchars($name) ?></h1>
        <h3 class="my-2 text-weight-bold">Upcoming Events</h3>
        <div class="row mt-5">
            <?php foreach ($events as $event): ?>
                <div class="col-md-6">
                    <div class="card bg-secondary text-light mb-3">
                        <div class="card-body text-center">
                            <img src="<?php echo htmlspecialchars($event['image']); ?>" class="img-fluid mb-3" alt="Event Image">
                            <h5 class="card-title"><?php echo htmlspecialchars($event['name']); ?></h5>
                            <?php
                            ?>
                            <?php if ($event['is_active']): ?>
                                <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#registerModal"
                                    data-event-id="<?php echo $event['id']; ?>"
                                    data-event-name="<?php echo htmlspecialchars($event['name']); ?>">
                                    Reserve
                                </button>
                            <?php else: ?>
                                <button class="btn btn-danger" disabled>Not Available</button>
                            <?php endif; ?>

                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

    </div>

    <footer class="bg-dark text-light py-4 mt-5 border-top">
        <div class="container">
            <div class="row">
                <!-- Club Info -->
                <div class="col-md-6 mb-3">
                    <h5><?php echo htmlspecialchars($name) ?></h5>
                    <p><i class="bi bi-telephone-fill"></i> <?php echo htmlspecialchars($phone) ?></p>
                    <p><i class="bi bi-envelope-fill"></i> <?php echo htmlspecialchars($email) ?></p>
                </div>

                <!-- Links / Social -->
                <div class="col-md-6 text-md-end">
                    <h6>Follow Us</h6>
                    <a href="https://www.facebook.com/<?php echo htmlspecialchars($face) ?>" target="_blank" class="text-light me-2"><i class="bi bi-facebook"></i></a>
                    <a href="https://www.instagram.com/<?php echo htmlspecialchars($insta) ?>" target="_blank" class="text-light me-2"><i class="bi bi-instagram"></i></a>
                    <p class="mt-3 mb-0">&copy; 2025 <?php echo htmlspecialchars($name) ?>. All rights reserved.</p>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"
        integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy"
        crossorigin="anonymous"></script>

    <div class="modal fade text-dark" id="registerModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form class="modal-content" method="POST" action="register.php">
                <div class="modal-header">
                    <h5 class="modal-title">Event Registration</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="event_id" id="event_id">
                    <div class="mb-3">
                        <label>Full Name</label>
                        <input type="text" class="form-control" name="full_name" required>
                    </div>
                    <div class="mb-3">
                        <label>Email</label>
                        <input type="email" class="form-control" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label>Phone</label>
                        <input type="text" class="form-control" name="phone">
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary">Submit</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var registerModal = document.getElementById('registerModal');
            registerModal.addEventListener('show.bs.modal', function(event) {
                var button = event.relatedTarget;
                var eventId = button.getAttribute('data-event-id');
                var eventName = button.getAttribute('data-event-name');
                registerModal.querySelector('.modal-title').textContent = 'Register for: ' + eventName;
                registerModal.querySelector('#event_id').value = eventId;
            });
        });
    </script>

</body>

</html>