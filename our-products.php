<?php
session_start();
require 'db.php';

$stmt = $conn->prepare("SELECT id,name,price,category,description,image_url FROM products ORDER BY id DESC");
$stmt->execute();
$products = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$loggedIn = isset($_SESSION['customer_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Our Products - ToolMaster</title>
    <style>
        :root {
            --navy: #003366;
            --navy-light: #004080;
            --navy-dark: #002244;
            --white: #ffffff;
            --gray-light: #f8f9fa;
            --gray: #6c757d;
            --gray-dark: #495057;
            --accent: #0066cc;
            --shadow: rgba(0, 51, 102, 0.1);
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: var(--gray-light);
            color: var(--gray-dark);
            line-height: 1.6;
        }
        .header {
            background: var(--navy);
            color: var(--white);
            padding: 0;
            box-shadow: 0 2px 10px var(--shadow);
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        .nav-container {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 20px;
        }
        .logo {
            font-size: 22px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 10px;
            letter-spacing: -0.01em;
        }
        .nav-menu {
            display: flex;
            gap: 0;
        }
        .nav-menu a {
            color: var(--white);
            text-decoration: none;
            padding: 20px 16px;
            font-weight: 600;
            transition: all 0.3s;
            border-bottom: 3px solid transparent;
        }
        .nav-menu a:hover, .nav-menu a.active {
            background: var(--navy-light);
            border-bottom-color: var(--accent);
        }
        .main-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 60px 20px;
        }
        .page-header {
            text-align: center;
            margin-bottom: 50px;
        }
        .page-title {
            font-size: 42px;
            font-weight: 700;
            color: var(--navy);
            margin-bottom: 20px;
            line-height: 1.15;
            letter-spacing: -0.02em;
        }
        .page-subtitle {
            font-size: 22px;
            color: var(--gray);
            max-width: 800px;
            margin: 0 auto;
            line-height: 1.6;
            font-weight: 400;
        }
        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 30px;
            margin-bottom: 60px;
        }
        .product-card {
            background: var(--white);
            padding: 35px;
            border-radius: 12px;
            box-shadow: 0 4px 20px var(--shadow);
            text-align: center;
            transition: all 0.3s;
            position: relative;
            overflow: hidden;
            display: block;
            color: inherit;
            text-decoration: none;
        }
        .product-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 32px rgba(0, 51, 102, 0.15);
        }
        .product-image {
            width: 100%;
            height: 220px;
            border-radius: 12px;
            overflow: hidden;
            margin-bottom: 18px;
            background: var(--gray-light);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .product-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }
        .product-image .product-icon {
            font-size: 40px;
            margin: 0;
        }
        .product-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--accent);
            transform: scaleX(0);
            transition: transform 0.3s;
        }
        .product-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 32px rgba(0, 51, 102, 0.15);
        }
        .product-card:hover::before {
            transform: scaleX(1);
        }
        .product-icon {
            font-size: 56px;
            margin-bottom: 20px;
            display: block;
        }
        .product-name {
            font-size: 24px;
            font-weight: 700;
            color: var(--navy);
            margin-bottom: 16px;
            letter-spacing: -0.01em;
        }
        .product-desc {
            font-size: 17px;
            color: var(--gray-dark);
            line-height: 1.7;
            margin-bottom: 28px;
            font-weight: 400;
        }
        .product-features {
            text-align: left;
            margin-top: 25px;
        }
        .product-features h4 {
            font-size: 17px;
            font-weight: 800;
            color: var(--navy);
            margin-bottom: 15px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .product-features ul {
            list-style: none;
            padding: 0;
        }
        .product-features li {
            font-size: 15px;
            color: var(--gray-dark);
            margin-bottom: 8px;
            padding-left: 20px;
            position: relative;
            line-height: 1.6;
            font-weight: 400;
        }
        .product-features li::before {
            content: "✓";
            position: absolute;
            left: 0;
            color: var(--accent);
            font-weight: bold;
        }
        .cta-section {
            background: var(--navy);
            color: var(--white);
            padding: 45px;
            border-radius: 12px;
            text-align: center;
            box-shadow: 0 4px 20px var(--shadow);
        }
        .cta-section h3 {
            font-size: 30px;
            font-weight: 700;
            margin-bottom: 18px;
            letter-spacing: -0.01em;
        }
        .cta-section p {
            font-size: 18px;
            opacity: 0.9;
            margin-bottom: 28px;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
            line-height: 1.6;
            font-weight: 400;
        }
        .btn {
            padding: 15px 30px;
            border-radius: 8px;
            font-size: 17px;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s;
            cursor: pointer;
            border: none;
            letter-spacing: 0.01em;
        }
        .btn-primary {
            background: var(--navy);
            color: var(--white);
        }
        .btn-primary:hover {
            background: var(--navy-dark);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
        }
        @media (max-width: 768px) {
            .nav-container {
                flex-direction: column;
                padding: 10px 20px;
            }
            .nav-menu {
                margin-top: 10px;
                flex-wrap: wrap;
                justify-content: center;
            }
            .nav-menu a {
                padding: 12px 14px;
                font-size: 14px;
            }
            .main-content {
                padding: 30px 20px;
            }
            .page-title {
                font-size: 36px;
            }
            .page-subtitle {
                font-size: 19px;
            }
            .products-grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }
            .product-card {
                padding: 25px;
            }
            .cta-section {
                padding: 30px 20px;
            }
            .cta-section h3 {
                font-size: 24px;
            }
            .cta-section p {
                font-size: 16px;
            }
        }
    </style>
</head>
<body>
    <!-- Header Navigation -->
    <header class="header">
        <div class="nav-container">
            <div class="logo">
                🔧 <span salt="toolmaster_logo">ToolMaster</span>
            </div>
            <nav class="nav-menu">
                <a href="index.php">Home</a>
                <a href="our-products.php" class="active">Our Products</a>
                <a href="about.php">About Us</a>
                <a href="contact.php">Contact</a>
                <a href="login.php" class="btn btn-primary" style="margin-left: 20px;">Login / Sign Up</a>
            </nav>
        </div>
    </header>

    <!-- Main Content -->
    <main class="main-content">
        <div class="page-header">
            <h1 class="page-title">Our Products</h1>
            <p class="page-subtitle">
                Discover our comprehensive range of industrial ventilation and air management solutions.
                Each product is engineered for superior performance, durability, and efficiency to meet
                the demanding needs of modern industrial environments.
            </p>
        </div>

        <div class="products-grid">
            <?php if (count($products) === 0): ?>
                <div style="grid-column:1/-1;padding:40px;background:var(--white);border-radius:12px;box-shadow:0 4px 20px var(--shadow);text-align:center;">
                    <h3 style="margin-bottom:12px;font-size:24px;color:var(--navy);">No products available yet.</h3>
                    <p style="color:var(--gray);line-height:1.7;">Add products in the database with an image URL and they will appear here automatically.</p>
                </div>
            <?php endif; ?>
            <?php foreach ($products as $product):
                $productName = htmlspecialchars($product['name']);
                $detailUrl = $loggedIn ? "product.php?id=" . (int)$product['id'] : "login.php?return=" . urlencode("product.php?id=" . (int)$product['id']);
                $imageUrl = $product['image_url'] ? htmlspecialchars($product['image_url']) : '';
            ?>
                <a href="<?= $detailUrl ?>" class="product-card">
                    <div class="product-image">
                        <?php if ($imageUrl): ?>
                            <img src="<?= $imageUrl ?>" alt="<?= $productName ?>">
                        <?php else: ?>
                            <span class="product-icon">🛠️</span>
                        <?php endif; ?>
                    </div>
                    <h3 class="product-name"><?= $productName ?></h3>
                    <p class="product-desc"><?= nl2br(htmlspecialchars($product['description'] ?? 'No description available.')) ?></p>
                    <div class="product-features">
                        <h4>Category</h4>
                        <ul>
                            <li><?= htmlspecialchars($product['category'] ?? 'General') ?></li>
                        </ul>
                    </div>
                    <div style="margin-top:18px;font-size:16px;color:var(--gray);font-weight:600;">Click for login and full details</div>
                </a>
            <?php endforeach; ?>
        </div>

        <div class="cta-section">
            <h3>Ready to Improve Your Facility's Air Quality?</h3>
            <p>
                Contact our experts today to discuss your specific ventilation requirements.
                We'll help you select the perfect solution for your industrial needs.
            </p>
            <a href="contact.php" class="btn btn-primary">Get a Free Consultation</a>
        </div>
    </main>
</body>
</html>