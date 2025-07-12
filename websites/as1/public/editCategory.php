<?php
session_start();
require_once 'db_connect.php';

if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    header("Location: login.php");
    exit;
}

$categoryId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$stmt = $pdo->prepare("SELECT * FROM category WHERE id = ?");
$stmt->execute([$categoryId]);
$category = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$category) {
    die("Category not found");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $stmt = $pdo->prepare("UPDATE category SET name = ? WHERE id = ?");
        $stmt->execute([$_POST['name'], $categoryId]);
        header("Location: adminCategories.php");
        exit;
    } catch (PDOException $e) {
        $error = "Failed to update category: " . $e->getMessage();
    }
}

$categories = $pdo->query("SELECT * FROM category")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Carbuy Auctions</title>
        <link rel="stylesheet" href="carbuy.css" />
    </head>
    <body>
        <header>
            <h1><span class="C">C</span>
                <span class="a">a</span>
                <span class="r">r</span>
                <span class="b">b</span>
                <span class="u">u</span>
                <span class="y">y</span></h1>
            <form action="#">
                <input type="text" name="search" placeholder="Search for a car" />
                <input type="submit" name="submit" value="Search" />
            </form>
        </header>
        <nav>
            <ul>
                <?php foreach ($categories as $cat): ?>
                    <li><a class="categoryLink" href="category.php?id=<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></a></li>
                <?php endforeach; ?>
                <li><a class="categoryLink" href="addAuction.php">More</a></li>
                <li><a class="categoryLink" href="logout.php">Logout</a></li>
                <li><a class="categoryLink" href="adminCategories.php">Admin Categories</a></li>
            </ul>
        </nav>
        <img src="banners/1.jpg" alt="Banner" />
        <main>
            <h1>Edit Category</h1>
            <form action="editCategory.php?id=<?= $categoryId ?>" method="post">
                <label>Name</label> <input type="text" name="name" value="<?= htmlspecialchars($category['name']) ?>" required />
                <input type="submit" value="Submit" />
            </form>
            <?php if (isset($error)): ?>
                <p style="color: red;"><?= htmlspecialchars($error) ?></p>
            <?php endif; ?>
            <footer>
                © Carbuy 2024
            </footer>
        </main>
    </body>
</html>