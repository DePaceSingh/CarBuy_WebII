<?php
session_start();   //session start
require_once 'db_connect.php'; //database connection

//getting categoy id from URL
$categoryId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$stmt = $pdo->prepare("SELECT a.*, c.name AS categoryName, u.name AS userName FROM auction a 
    JOIN category c ON a.categoryId = c.id 
    JOIN users u ON a.userId = u.id 
    WHERE a.categoryId = ?");

//execute query for fetching auctions    
$stmt->execute([$categoryId]);
$auctions = $stmt->fetchAll(PDO::FETCH_ASSOC);

//getting category name
$category = $pdo->prepare("SELECT name FROM category WHERE id = ?");
$category->execute([$categoryId]);
$categoryName = $category->fetchColumn() ?: "Unknown";

//fetching all categories
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
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li><a class="categoryLink" href="addAuction.php">Add Auction</a></li>
                    <li><a class="categoryLink" href="logout.php">Logout</a></li>
                    <?php if ($_SESSION['is_admin']): ?>
                        <li><a class="categoryLink" href="adminCategories.php">Admin Categories</a></li>
                    <?php endif; ?>
                <?php else: ?>
                    <li><a class="categoryLink" href="login.php">Login</a></li>
                    <li><a class="categoryLink" href="register.php">Register</a></li>
                <?php endif; ?>
            </ul>
        </nav>
        <img src="banners/1.jpg" alt="Banner" />
        <main>
            <h1>Latest <?= htmlspecialchars($categoryName) ?> Listing</h1>
            <ul class="carList">
                <?php foreach ($auctions as $auction): ?>
                    <li>
                        <img src="car.png" alt="car name">
                        <article>
                            <h2><?= htmlspecialchars($auction['title']) ?></h2>
                            <h3><?= htmlspecialchars($auction['categoryName']) ?></h3>
                            <p><?= htmlspecialchars($auction['description']) ?></p>
                            <?php
                            $stmt = $pdo->prepare("SELECT MAX(amount) as maxBid FROM bid WHERE auctionId = ?");
                            $stmt->execute([$auction['id']]);
                            $bid = $stmt->fetch();
                            ?>
                            <p class="price">Current bid: £<?= $bid['maxBid'] ? number_format($bid['maxBid'], 2) : '0.00' ?></p>
                            <a href="auction.php?id=<?= $auction['id'] ?>" class="more auctionLink">More >></a>
                        </article>
                    </li>
                <?php endforeach; ?>
            </ul>
            <footer>
                © Carbuy 2024
            </footer>
        </main>
    </body>
</html>