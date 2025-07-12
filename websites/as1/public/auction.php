<?php
session_start(); //Starting the session
require_once 'db_connect.php'; //Including the database connection file

$auctionId = isset($_GET['id']) ? (int)$_GET['id'] : 0; //Get auction ID from URL

$stmt = $pdo->prepare("SELECT a.*, c.name AS categoryName, u.name AS userName FROM auction a 
    JOIN category c ON a.categoryId = c.id 
    JOIN users u ON a.userId = u.id 
    WHERE a.id = ?");
$stmt->execute([$auctionId]); //Execute query
$auction = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$auction) {
    die("Auction not found"); 
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reviewText']) && isset($_SESSION['user_id'])) {
    //Insert a new review for the auction creator
    $stmt = $pdo->prepare("INSERT INTO review (reviewText, userId, reviewedUserId, createdAt) VALUES (?, ?, ?, NOW())");
    $stmt->execute([$_POST['reviewText'], $_SESSION['user_id'], $auction['userId']]);
    header("Location: auction.php?id=" . $auctionId);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bid']) && isset($_SESSION['user_id'])) {
    $bidAmount = floatval($_POST['bid']); 
    if ($bidAmount > 0) {
        $stmt = $pdo->prepare("INSERT INTO bid (auctionId, userId, amount, createdAt) VALUES (?, ?, ?, NOW())");
        $stmt->execute([$auctionId, $_SESSION['user_id'], $bidAmount]);
        header("Location: auction.php?id=" . $auctionId);
        exit;
    }
}

$stmt = $pdo->prepare("SELECT MAX(amount) as maxBid FROM bid WHERE auctionId = ?");
$stmt->execute([$auctionId]); //Fetching highest bid
$maxBid = $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT r.*, u.name AS reviewerName FROM review r JOIN users u ON r.userId = u.id WHERE r.reviewedUserId = ?");
$stmt->execute([$auction['userId']]); // Fetch all reviews for the auction creator
$reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);

$categories = $pdo->query("SELECT * FROM category")->fetchAll(PDO::FETCH_ASSOC); // Fetch all categories

$endDate = new DateTime($auction['endDate']); 
$now = new DateTime(); 
$interval = $now->diff($endDate); // Calculate time difference
$timeLeft = $interval->format('%h hours %i minutes'); // Format time left
if ($interval->invert) {
    $timeLeft = "Ended"; 
}
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Carbuy Auctions</title>
        <link rel="stylesheet" href="carbuy.css" /> <!-- Link to the CSS stylesheet -->
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
            <h1>Latest <?= htmlspecialchars($auction['categoryName']) ?> Listing</h1>
            <article class="car">
                <img src="car.png" alt="car name">
                <section class="details">
                    <h2><?= htmlspecialchars($auction['title']) ?></h2>
                    <h3><?= htmlspecialchars($auction['categoryName']) ?></h3>
                    <p>Auction created by <a href="#"><?= htmlspecialchars($auction['userName']) ?></a></p>
                    <p class="price">Current bid: £<?= $maxBid ? number_format($maxBid, 2) : '0.00' ?></p>
                    <time>Time left: <?= $timeLeft ?></time>
                    <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $auction['userId']): ?>
                        <p><a href="editAuction.php?id=<?= $auction['id'] ?>">Edit Auction</a></p>
                    <?php endif; ?>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <form action="auction.php?id=<?= $auctionId ?>" method="post" class="bid">
                            <input type="number" name="bid" placeholder="Enter bid amount" step="0.01" min="0.01" required />
                            <input type="submit" value="Place bid" />
                        </form>
                    <?php endif; ?>
                </section>
                <section class="description">
                    <p><?= htmlspecialchars($auction['description']) ?></p>
                </section>
                <section class="reviews">
                    <h2>Reviews of <?= htmlspecialchars($auction['userName']) ?></h2>
                    <ul>
                        <?php foreach ($reviews as $review): ?>
                            <li><strong><?= htmlspecialchars($review['reviewerName']) ?> said </strong><?= htmlspecialchars($review['reviewText']) ?> <em><?= date('d/m/Y', strtotime($review['createdAt'])) ?></em></li>
                        <?php endforeach; ?>
                    </ul>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <form action="auction.php?id=<?= $auctionId ?>" method="post">
                            <label>Add your review</label> <textarea name="reviewText" required></textarea>
                            <input type="submit" value="Add Review" />
                        </form>
                    <?php endif; ?>
                </section>
            </article>
            <footer>
                © Carbuy 2024
            </footer>
        </main>
    </body>
</html>