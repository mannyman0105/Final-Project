<?php
session_start();
require '../database/database.php';

if (!isset($_SESSION['person_id'])) {
    header("Location: ../authentacation/login.php");
    exit;
}

$pdo = Database::connect();

// Handle sorting
$allowedSort = ['subject', 'description', 'fname'];
$sort = in_array($_GET['sort'] ?? '', $allowedSort) ? $_GET['sort'] : 'issues.id';
$order = ($_GET['order'] ?? 'asc') === 'desc' ? 'DESC' : 'ASC';

// Handle filtering
$filterName = $_GET['filter_name'] ?? '';

// Pagination
$limit = 5;
$page = max((int)($_GET['page'] ?? 1), 1);
$offset = ($page - 1) * $limit;

// Count total for pagination
$countSql = "SELECT COUNT(*) FROM issues 
             JOIN persons ON issues.person_id = persons.id 
             WHERE CONCAT(fname, ' ', lname) LIKE ?";
$countStmt = $pdo->prepare($countSql);
$countStmt->execute(['%' . $filterName . '%']);
$totalIssues = $countStmt->fetchColumn();
$totalPages = ceil($totalIssues / $limit);

// Fetch data with filter, sort, and pagination
$sql = "SELECT issues.id, issues.subject, issues.description, persons.fname, persons.lname, persons.title 
        FROM issues 
        JOIN persons ON issues.person_id = persons.id
        WHERE CONCAT(fname, ' ', lname) LIKE ?
        ORDER BY $sort $order
        LIMIT $limit OFFSET $offset";
$stmt = $pdo->prepare($sql);
$stmt->execute(['%' . $filterName . '%']);
$issues = $stmt->fetchAll(PDO::FETCH_ASSOC);

Database::disconnect();

// Function to build URL with updated query parameters
function buildUrl($params) {
    return '?' . http_build_query(array_merge($_GET, $params));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Issue List</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8d7da; }
        .table th a { color: white; text-decoration: none; }
    </style>
</head>
<body>
<style>
    body {
        background-color: #fff5f5;
    }
    h2 {
        color: #b02a37;
    }
    .table th a {
        color: white;
        text-decoration: none;
    }
    .table th {
        background-color: #dc3545;
    }
    .btn-danger {
        background-color: #b02a37;
        border-color: #b02a37;
    }
    .btn-danger:hover {
        background-color: #8a1d28;
        border-color: #8a1d28;
    }
    .page-item.active .page-link {
        background-color: #b02a37;
        border-color: #b02a37;
    }
    .page-link {
        color: #b02a37;
    }
</style>
<?php
if (!isset($_SESSION['person_id'])) {
    header("Location: ../authentacation/login.php");
    exit;
}
?>

<div class="d-flex justify-content-between align-items-center mb-3">
<h2 class="text-danger">Issues List</h2>

    <div>
        <span class="me-3">Welcome, <?= htmlspecialchars($_SESSION['name']) ?> (<?= $_SESSION['title'] ?>)</span>
        <a href="../authentacation/logout.php" class="btn btn-danger">Logout</a>
    </div>
</div>

<div class="container mt-4">
<h2 class="text-danger">Tell us your issue!</h2>
    <a href="create.php" class="btn btn-danger mb-3">Add New Issue</a>
    <a href="../persons/dashboard.php" class="btn btn-secondary mb-3">Back to Dashboard</a>

    <!-- Filter Form -->
    <form method="GET" class="mb-3">
        <div class="input-group">
            <input type="text" name="filter_name" value="<?= htmlspecialchars($filterName) ?>" class="form-control" placeholder="Filter by Person Name">
            <button type="submit" class="btn btn-dark">Filter</button>
        </div>
    </form>

    <table class="table table-bordered table-striped">
        <thead class="table-danger">
        <tr>
            <th>#</th>
            <th><a href="<?= buildUrl(['sort' => 'subject', 'order' => $order === 'ASC' ? 'desc' : 'asc']) ?>">Subject</a></th>
            <th><a href="<?= buildUrl(['sort' => 'description', 'order' => $order === 'ASC' ? 'desc' : 'asc']) ?>">Description</a></th>
            <th><a href="<?= buildUrl(['sort' => 'fname', 'order' => $order === 'ASC' ? 'desc' : 'asc']) ?>">Assigned To</a></th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($issues as $i => $issue): ?>
            <tr>
                <td><?= ($offset + $i + 1) ?></td>
                <td><?= htmlspecialchars($issue['subject']) ?></td>
                <td><?= htmlspecialchars($issue['description']) ?></td>
                <td><?= htmlspecialchars($issue['fname'] . ' ' . $issue['lname']) ?> (<?= $issue['title'] ?>)</td>
                <td>
                    <a href="detail.php?id=<?= $issue['id'] ?>" class="btn btn-success btn-sm">View</a>
                    <?php if ($_SESSION['title'] == 'Admin'): ?>
                        <a href="update.php?id=<?= $issue['id'] ?>" class="btn btn-warning btn-sm">Update</a>
                        <a href="delete.php?id=<?= $issue['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</a>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Pagination -->
    <nav>
        <ul class="pagination">
            <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                <li class="page-item <?= $p == $page ? 'active' : '' ?>">
                    <a class="page-link" href="<?= buildUrl(['page' => $p]) ?>"><?= $p ?></a>
                </li>
            <?php endfor; ?>
        </ul>
    </nav>
</div>
</body>
</html>
