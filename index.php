<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ToolMaster - Industrial Ventilation Solutions</title>
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
            font-family: -apple-system, BlinkMacSystemFont, 'SF Pro Display', 'SF Pro Text', 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: var(--gray-light);
            color: var(--gray-dark);
            line-height: 1.7;
            font-weight: 400;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
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
            padding: 22px 18px;
            font-weight: 600;
            transition: all 0.3s;
            border-bottom: 3px solid transparent;
            letter-spacing: 0.01em;
            font-size: 15px;
        }
        .nav-menu a:hover, .nav-menu a.active {
            background: var(--navy-light);
            border-bottom-color: var(--accent);
        }
        .main-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 60px 20px;
            text-align: center;
        }
        .hero-section {
            background: var(--white);
            padding: 70px 40px;
            border-radius: 8px;
            box-shadow: 0 4px 20px var(--shadow);
            margin-bottom: 40px;
        }
        .hero-title {
            font-size: 42px;
            font-weight: 700;
            color: var(--navy);
            margin-bottom: 24px;
            line-height: 1.15;
            letter-spacing: -0.02em;
        }
        .hero-subtitle {
            font-size: 22px;
            color: var(--gray);
            margin-bottom: 40px;
            max-width: 700px;
            margin-left: auto;
            margin-right: auto;
            line-height: 1.6;
            font-weight: 400;
        }
        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            margin-bottom: 50px;
        }
        .feature-card {
            background: var(--white);
            padding: 35px 30px;
            border-radius: 8px;
            box-shadow: 0 4px 20px var(--shadow);
            transition: transform 0.3s;
        }
        .feature-card:hover {
            transform: translateY(-5px);
        }
        .feature-icon {
            font-size: 56px;
            margin-bottom: 24px;
        }
        .feature-title {
            font-size: 24px;
            font-weight: 700;
            color: var(--navy);
            margin-bottom: 18px;
            letter-spacing: -0.01em;
        }
        .feature-text {
            font-size: 17px;
            color: var(--gray);
            line-height: 1.7;
            font-weight: 400;
        }
        .cta-buttons {
            display: flex;
            gap: 20px;
            justify-content: center;
            flex-wrap: wrap;
        }
        .btn {
            padding: 16px 32px;
            border-radius: 10px;
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
            box-shadow: 0 6px 20px rgba(0, 51, 102, 0.3);
        }
        .btn-secondary {
            background: var(--white);
            color: var(--navy);
            border: 2px solid var(--navy);
        }
        .btn-secondary:hover {
            background: var(--navy);
            color: var(--white);
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
            .hero-section {
                padding: 50px 25px;
            }
            .hero-title {
                font-size: 36px;
            }
            .hero-subtitle {
                font-size: 19px;
            }
            .features-grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }
            .cta-buttons {
                flex-direction: column;
                align-items: center;
            }
            .btn {
                width: 100%;
                max-width: 300px;
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
                <a href="index.php" class="active">Home</a>
                <a href="our-products.php">Our Products</a>
                <a href="about.php">About Us</a>
                <a href="contact.php">Contact</a>
                <a href="login.php" class="btn btn-primary">Login / Sign Up</a>
            </nav>
        </div>
    </header>

    <!-- Main Content -->
    <main class="main-content">
        <section class="hero-section">
            <h1 class="hero-title">Industrial Ventilation Solutions</h1>
            <p class="hero-subtitle">
                Welcome to The Tool Master & Engineering Company. For over 40 years, we've been Bangladesh's trusted leader
                in industrial ventilation and air management solutions, delivering innovative products that improve indoor air quality
                in factories, warehouses, workshops, and commercial spaces nationwide.
            </p>
        </section>

        <section class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">⏱️</div>
                <h3 class="feature-title">40+ Years Experience</h3>
                <p class="feature-text">Established in 1983, leading the industry with proven expertise and innovation.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">🏭</div>
                <h3 class="feature-title">State-of-the-Art Facility</h3>
                <p class="feature-text">15,000 sq ft manufacturing facility in BSCIC Industrial Area, Tongi, Gazipur.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">🌍</div>
                <h3 class="feature-title">Global Standards</h3>
                <p class="feature-text">Equipment sourced from Germany, Italy, Malaysia, Thailand, Taiwan & China.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">🔧</div>
                <h3 class="feature-title">Custom Solutions</h3>
                <p class="feature-text">Tailored air movement systems for exhaust fans, coolers, dehumidifiers & more.</p>
            </div>
        </section>

        <div class="cta-buttons">
            <a href="login.php" class="btn btn-primary">Shop Our Products</a>
            <a href="contact.php" class="btn btn-secondary">Get in Touch</a>
        </div>
    </main>
</body>
</html>