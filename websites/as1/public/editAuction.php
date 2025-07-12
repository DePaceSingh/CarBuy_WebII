<?php
session_start();
require_once 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$auctionId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$stmt = $pdo->prepare("SELECT * FROM auction WHERE id = ? AND userId = ?");
$stmt->execute([$auctionId, $_SESSION['user_id']]);
$auction = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$auction) {
    die("Auction not found or you don't have permission to edit it");
}

$categories = $pdo->query("SELECT * FROM category")->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (isset($_POST['delete'])) {
            $stmt = $pdo->prepare("DELETE FROM auction WHERE id = ?");
            $stmt->execute([$auctionId]);
            header("Location: index.php");
            exit;
        }
        $stmt = $pdo->prepare("UPDATE auction SET title = ?, description = ?, categoryId = ?, endDate = ? WHERE id = ?");
        $stmt->execute([$_POST['title'], $_POST['description'], $_POST['category'], $_POST['endDate'], $auctionId]);
        header("Location: index.php");
        exit;
    } catch (PDOException $e) {
        $error = "Failed to update auction: " . $e->getMessage();
    }
}
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
                <?php if ($_SESSION['is_admin']): ?>
                    <li><a class="categoryLink" href="adminCategories.php">Admin Categories</a></li>
                <?php endif; ?>
            </ul>
        </nav>
        <img src="banners/1.jpg" alt="Banner" />
        <main>
            <h1>Edit Auction</h1>
            <form action="editAuction.php?id=<?= $auctionId ?>" method="post">
                <label>Title</label> <input type="text" name="title" value="<?= htmlspecialchars($auction['title']) ?>" required />
                <label>Description</label> <textarea name="description" required><?= htmlspecialchars($auction['description']) ?></textarea>
                <label>Category</label> <select name="category" required>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?= $category['id'] ?>" <?= $category['id'] == $auction['categoryId'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($category['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <label>End Date</label> <input type="datetime-local" name="endDate" value="<?= str_replace(' ', 'T', $auction['endDate']) ?>" required />
                <input type="submit" value="Submit" />
                <input type="submit" name="delete" value="Delete Auction" onclick="return confirm('Are you sure?');" />
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