<?php
session_start();
require_once 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $email = $_POST['username'];
        $password = $_POST['password'];
        $name = $_POST['name'];
        $stmt = $pdo->prepare("INSERT INTO users (email, password, name) VALUES (?, ?, ?)");
        if ($stmt->execute([$email, $password, $name])) {
            header("Location: login.php");
            exit;
        }
    } catch (PDOException $e) {
        $error = "Registration failed: " . $e->getMessage();
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
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li><a class="categoryLink" href="addAuction.php">More</a></li>
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
            <h1>Register</h1>
            <form action="register.php" method="post">
                <label>Username</label> <input type="text" name="username" required />
                <label>Password</label> <input type="password" name="password" required />
                <label>Name</label> <input type="text" name="name" required />
                <input type="submit" value="Register" />
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