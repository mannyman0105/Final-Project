<?php
session_start();
if (!isset($_SESSION['person_id'])) {
    header("Location: ../authentacation/login.php");
    exit;
}

require '../database/database.php';

$pdo = Database::connect();

// Sorting
$allowedSorts = ['id', 'fname', 'lname', 'email', 'title'];
$sort = isset($_GET['sort']) && in_array($_GET['sort'], $allowedSorts) ? $_GET['sort'] : 'id';
$order = (isset($_GET['order']) && $_GET['order'] === 'asc') ? 'asc' : 'desc';

// Filtering
$search = $_GET['search'] ?? '';

// Pagination
$limit = 5;
$page = isset($_GET['page']) ? max((int)$_GET['page'], 1) : 1;
$offset = ($page - 1) * $limit;

// Count total results
if (!empty($search)) {
    $countStmt = $pdo->prepare("SELECT COUNT(*) FROM persons WHERE fname LIKE :search OR lname LIKE :search");
    $countStmt->execute(['search' => "%$search%"]);
    $total = $countStmt->fetchColumn();

    $stmt = $pdo->prepare("SELECT * FROM persons 
                           WHERE fname LIKE :search OR lname LIKE :search 
                           ORDER BY $sort $order LIMIT $limit OFFSET $offset");
    $stmt->execute(['search' => "%$search%"]);
} else {
    $countStmt = $pdo->query("SELECT COUNT(*) FROM persons");
    $total = $countStmt->fetchColumn();

    $query = "SELECT * FROM persons ORDER BY $sort $order LIMIT $limit OFFSET $offset";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
}

$persons = $stmt->fetchAll(PDO::FETCH_ASSOC);
$totalPages = ceil($total / $limit);

Database::disconnect();

function sortLink($col, $label, $currentSort, $currentOrder, $search) {
    $newOrder = ($currentSort === $col && $currentOrder === 'asc') ? 'desc' : 'asc';
    $url = "dashboard.php?sort=$col&order=$newOrder";
    if (!empty($search)) $url .= "&search=" . urlencode($search);
    return "<a href=\"$url\" class=\"text-white text-decoration-none\">$label</a>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8d7da;
        }
        .table thead th a {
            color: white;
        }
    </style>
</head>
<body style="background-color: #f8d7da;">
<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center">
        <h2 class="text-danger">Welcome, <?= htmlspecialchars($_SESSION['name']) ?>!</h2>
        <a href="../authentacation/logout.php" class="btn btn-outline-danger">Logout</a>
    </div>
    <p class="text-dark">Role: <strong><?= htmlspecialchars($_SESSION['title']) ?></strong></p>

    <!-- Filter -->
    <form class="mb-3" method="GET" action="dashboard.php">
        <div class="input-group">
            <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" class="form-control border-danger" placeholder="Filter by name...">
            <input type="hidden" name="sort" value="<?= $sort ?>">
            <input type="hidden" name="order" value="<?= $order ?>">
            <button class="btn btn-danger">Search</button>
        </div>
    </form>

    <!-- Admin Actions -->
    <?php if ($_SESSION['title'] === 'Admin'): ?>
        <a href="create.php" class="btn btn-danger mb-3">+ Add New Person</a>
    <?php endif; ?>

    <!-- Persons Table -->
    <div class="table-responsive">
        <table class="table table-bordered">
            <thead class="table-danger text-white">
            <tr>
                <th>#</th>
                <th><?= sortLink('fname', 'First Name', $sort, $order, $search) ?></th>
                <th><?= sortLink('lname', 'Last Name', $sort, $order, $search) ?></th>
                <th><?= sortLink('email', 'Email', $sort, $order, $search) ?></th>
                <th><?= sortLink('title', 'Role', $sort, $order, $search) ?></th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody class="bg-light">
            <?php foreach ($persons as $index => $row): ?>
                <tr>
                    <td><?= (($page - 1) * $limit) + $index + 1 ?></td>
                    <td><?= htmlspecialchars($row['fname']) ?></td>
                    <td><?= htmlspecialchars($row['lname']) ?></td>
                    <td><?= htmlspecialchars($row['email']) ?></td>
                    <td><?= htmlspecialchars($row['title']) ?></td>
                    <td>
                        <a href="read.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-outline-danger">View</a>
                        <?php if ($_SESSION['title'] === 'Admin'): ?>
                            <a href="update.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-danger">Edit</a>
                            <a href="delete.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-dark" onclick="return confirm('Delete this person?')">Delete</a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>

            <?php if (count($persons) === 0): ?>
                <tr>
                    <td colspan="6" class="text-center text-muted">No persons found.</td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <?php if ($totalPages > 1): ?>
        <nav>
            <ul class="pagination justify-content-center">
                <?php for ($i = 1; $i <= $totalPages; $i++): 
                    $url = "dashboard.php?page=$i&sort=$sort&order=$order";
                    if (!empty($search)) $url .= "&search=" . urlencode($search);
                ?>
                    <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                        <a class="page-link bg-danger text-white border-danger" href="<?= $url ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>
            </ul>
        </nav>
    <?php endif; ?>

    <div class="mt-4">
        <a href="../issues/index.php" class="btn btn-outline-danger">Go to Issues Page</a>
    </div>
</div>
</body>

</html>
