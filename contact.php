<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - ToolMaster</title>
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
            max-width: 700px;
            margin: 0 auto;
            line-height: 1.6;
            font-weight: 400;
        }
        .contact-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 30px;
            margin-bottom: 50px;
        }
        .contact-card {
            background: var(--white);
            padding: 35px;
            border-radius: 12px;
            box-shadow: 0 4px 20px var(--shadow);
        }
        .contact-card h3 {
            font-size: 24px;
            font-weight: 700;
            color: var(--navy);
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 12px;
            letter-spacing: -0.01em;
        }
        }
        .contact-detail {
            display: flex;
            gap: 15px;
            margin-bottom: 22px;
            align-items: flex-start;
        }
        .contact-detail strong {
            min-width: 25px;
            font-size: 18px;
        }
        .contact-detail span {
            font-size: 17px;
            color: var(--gray-dark);
            line-height: 1.6;
            font-weight: 400;
        }
        .map-container {
            margin-top: 25px;
            height: 200px;
            background: var(--gray-light);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--gray);
            font-style: italic;
            border: 1px solid #e9ecef;
        }
        .info-section {
            background: var(--white);
            padding: 35px;
            border-radius: 12px;
            box-shadow: 0 4px 20px var(--shadow);
            margin-bottom: 50px;
        }
        .info-section h3 {
            font-size: 24px;
            font-weight: 700;
            color: var(--navy);
            margin-bottom: 22px;
            letter-spacing: -0.01em;
        }
        .info-section p {
            font-size: 17px;
            color: var(--gray-dark);
            line-height: 1.7;
            margin-bottom: 22px;
            font-weight: 400;
        }
        .cta-section {
            text-align: center;
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
            box-shadow: 0 6px 20px rgba(0, 51, 102, 0.3);
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
                font-size: 32px;
            }
            .page-subtitle {
                font-size: 18px;
            }
            .contact-grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }
            .contact-card {
                padding: 25px;
            }
            .info-section {
                padding: 25px;
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
                <a href="our-products.php">Our Products</a>
                <a href="about.php">About Us</a>
                <a href="contact.php" class="active">Contact</a>
                <a href="login.php" class="btn btn-primary" style="margin-left: 20px;">Login / Sign Up</a>
            </nav>
        </div>
    </header>

    <!-- Main Content -->
    <main class="main-content">
        <div class="page-header">
            <h1 class="page-title">Contact Us</h1>
            <p class="page-subtitle">
                Get in touch with The Tool Master & Engineering Company. We're here to help with all your industrial ventilation needs.
            </p>
        </div>

        <div class="contact-grid">
            <!-- Corporate Office -->
            <div class="contact-card">
                <h3>🏢 Corporate Office</h3>
                <div class="contact-detail">
                    <strong>📍</strong>
                    <span>House # 12, Road # 04, Sector # 01, Uttara, Dhaka</span>
                </div>
                <div class="contact-detail">
                    <strong>📞</strong>
                    <span>01911-576633</span>
                </div>
                <div class="map-container">
                   <iframe 
            src="https://maps.google.com/maps?q=uttara%20dhaka&t=&z=13&ie=UTF8&iwloc=&output=embed">
        </iframe>
                </div>
            </div>

            <!-- Dhaka Office -->
            <div class="contact-card">
                <h3>🏪 Dhaka Office</h3>
                <div class="contact-detail">
                    <strong>📍</strong>
                    <span>Al Karim Market, 162, Nawabpur First Floor, Dhaka-1100</span>
                </div>
                <div class="contact-detail">
                    <strong>📞</strong>
                    <span>01715-135786</span>
                </div>
                <div class="map-container">
                     <iframe 
            src="https://maps.google.com/maps?q=nawabpur%20dhaka&t=&z=13&ie=UTF8&iwloc=&output=embed">
        </iframe>
                </div>
            </div>
        </div>

        <div class="info-section">
            <h3>📧 General Information</h3>
            <p>
                For inquiries about our industrial ventilation products and services, please contact us using the information above.
                Our team is ready to assist you with customized air movement solutions for your specific needs.
            </p>
            <div class="contact-detail">
                <strong>🕒</strong>
                <span>Business Hours: Sunday - Thursday, 9:00 AM - 6:00 PM</span>
            </div>
            <div class="contact-detail">
                <strong>🌐</strong>
                <span>Website: www.toolmaster.com</span>
            </div>
        </div>

        <div class="cta-section">
            <a href="about.php" class="btn btn-primary">Learn More About Us</a>
        </div>
    </main>
</body>
</html>