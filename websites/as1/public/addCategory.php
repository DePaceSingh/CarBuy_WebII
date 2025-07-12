<?php
session_start();
require_once 'db_connect.php';

if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $stmt = $pdo->prepare("INSERT INTO category (name) VALUES (?)");
        $stmt->execute([$_POST['name']]);
        header("Location: adminCategories.php");
        exit;
    } catch (PDOException $e) {
        $error = "Failed to add category: " . $e->getMessage();
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
                <?php foreach ($categories as $category): ?>
                    <li><a class="categoryLink" href="category.php?id=<?= $category['id'] ?>"><?= htmlspecialchars($category['name']) ?></a></li>
                <?php endforeach; ?>
                <li><a class="categoryLink" href="addAuction.php">More</a></li>
                <li><a class="categoryLink" href="logout.php">Logout</a></li>
                <li><a class="categoryLink" href="adminCategories.php">Admin Categories</a></li>
            </ul>
        </nav>
        <img src="banners/1.jpg" alt="Banner" />
        <main>
            <h1>Add Category</h1>
            <form action="addCategory.php" method="post">
                <label>Name</label> <input type="text" name="name" required />
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