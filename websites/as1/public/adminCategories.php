<?php
//starting the session
session_start();
//database connection file
require_once 'db_connect.php';

if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    header("Location: login.php");
    exit;
}

//fetching categories
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
            <h1>Manage Categories</h1>
            <div style="margin-bottom: 2em;">
                <a href="addCategory.php" style="display: inline-block; padding: 0.5em 1em; background-color: #3665f3; color: white; text-decoration: none; border-radius: 5px;">Add New Category</a>
            </div>
            <table style="width: 100%; border-collapse: collapse;">
                <tr style="background-color: #f6f5f4;">
                    <th style="padding: 0.5em; border: 1px solid #ddd;">Name</th>
                    <th style="padding: 0.5em; border: 1px solid #ddd;">Actions</th>
                </tr>
                <?php foreach ($categories as $category): ?>
                    <tr>
                        <td style="padding: 0.5em; border: 1px solid #ddd;"><?= htmlspecialchars($category['name']) ?></td>
                        <td style="padding: 0.5em; border: 1px solid #ddd;">
                            <a href="editCategory.php?id=<?= $category['id'] ?>" style="display: inline-block; padding: 0.3em 0.7em; background-color: #4CAF50; color: white; text-decoration: none; border-radius: 5px; margin-right: 0.5em;">Edit</a>
                            <a href="deleteCategory.php?id=<?= $category['id'] ?>" onclick="return confirm('Are you sure?');" style="display: inline-block; padding: 0.3em 0.7em; background-color: #f44336; color: white; text-decoration: none; border-radius: 5px;">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
            <footer>
                © Carbuy 2024
            </footer>
        </main>
    </body>
</html>