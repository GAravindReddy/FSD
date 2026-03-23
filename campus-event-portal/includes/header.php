<?php session_start(); ?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Campus Portal</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="/campus-event-portal/assets/css/style.css">
</head>
<body>
<nav class="navbar navbar-dark bg-dark">
<div class="container">
<a class="navbar-brand" href="/campus-event-portal/">🎓 Campus Portal</a>
<div>
<?php if(isset($_SESSION['user'])): ?>
<a class="btn btn-outline-light" href="/campus-event-portal/dashboard.php">Dashboard</a>
<a class="btn btn-danger" href="/campus-event-portal/logout.php">Logout</a>
<?php else: ?>
<a class="btn btn-success" href="/campus-event-portal/login.php">Login</a>
<?php endif; ?>
</div>
</div>
</nav>
<div class="container mt-4">