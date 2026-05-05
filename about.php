<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - ToolMaster</title>
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
        .content-section {
            background: var(--white);
            padding: 45px;
            border-radius: 8px;
            box-shadow: 0 4px 20px var(--shadow);
            margin-bottom: 30px;
        }
        .content-section h2 {
            font-size: 30px;
            font-weight: 700;
            color: var(--navy);
            margin-bottom: 24px;
            letter-spacing: -0.01em;
        }
        .content-section p {
            font-size: 18px;
            line-height: 1.7;
            margin-bottom: 22px;
            color: var(--gray-dark);
            font-weight: 400;
        }
        .highlight-box {
            background: var(--gray-light);
            padding: 35px;
            border-radius: 12px;
            border-left: 4px solid var(--accent);
            margin: 30px 0;
        }
        .highlight-box h3 {
            font-size: 26px;
            font-weight: 800;
            color: var(--navy);
            margin-bottom: 20px;
            letter-spacing: -0.01em;
        }
        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin: 20px 0;
        }
        .product-item {
            background: var(--white);
            padding: 28px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 2px 10px var(--shadow);
            border: 1px solid #e9ecef;
        }
        .product-item strong {
            display: block;
            font-size: 20px;
            margin-bottom: 8px;
            color: var(--navy);
            font-weight: 700;
        }
        .features-list {
            list-style: none;
            padding: 0;
        }
        .features-list li {
            padding: 10px 0;
            font-size: 17px;
            color: var(--gray-dark);
        }
        .features-list li:before {
            content: "✓";
            color: var(--accent);
            font-weight: bold;
            margin-right: 10px;
        }
        .cta-section {
            text-align: center;
            margin-top: 50px;
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
        .clients-carousel {
            margin: 50px 0;
        }
        .carousel-container {
            position: relative;
            background: var(--white);
            padding: 40px 20px;
            border-radius: 12px;
            box-shadow: 0 4px 20px var(--shadow);
        }
        .clients-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }
        .client-card {
            background: var(--gray-light);
            padding: 30px;
            border-radius: 8px;
            text-align: center;
            transition: all 0.3s;
            border: 2px solid transparent;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 200px;
        }
        .client-card:hover {
            border-color: var(--accent);
            transform: translateY(-5px);
            box-shadow: 0 6px 20px var(--shadow);
        }
        .client-logo {
            width: 100%;
            height: 120px;
            object-fit: contain;
            margin-bottom: 15px;
        }
        .client-name {
            font-size: 16px;
            font-weight: 700;
            color: var(--navy);
            word-wrap: break-word;
        }
        .carousel-nav {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 20px;
            margin-top: 30px;
        }
        .carousel-btn {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: var(--navy);
            color: var(--white);
            border: none;
            font-size: 24px;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .carousel-btn:hover:not(:disabled) {
            background: var(--navy-dark);
            transform: scale(1.1);
        }
        .carousel-btn:disabled {
            background: var(--gray);
            cursor: not-allowed;
            opacity: 0.5;
        }
        .carousel-counter {
            font-size: 16px;
            font-weight: 600;
            color: var(--gray-dark);
            min-width: 100px;
            text-align: center;
        }
        @media (max-width: 1024px) {
            .clients-grid {
                grid-template-columns: repeat(3, 1fr);
            }
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
            .content-section {
                padding: 30px 20px;
            }
            .content-section h2 {
                font-size: 24px;
            }
            .products-grid {
                grid-template-columns: 1fr;
                gap: 15px;
            }
            .highlight-box {
                padding: 20px;
            }
            .clients-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 15px;
            }
            .carousel-btn {
                width: 40px;
                height: 40px;
                font-size: 18px;
            }
        }
        @media (max-width: 480px) {
            .clients-grid {
                grid-template-columns: 1fr;
            }
            .carousel-nav {
                gap: 10px;
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
                <a href="about.php" class="active">About Us</a>
                <a href="contact.php">Contact</a>
                <a href="login.php" class="btn btn-primary" style="margin-left: 20px;">Login / Sign Up</a>
            </nav>
        </div>
    </header>

    <!-- Main Content -->
    <main class="main-content">
        <div class="page-header">
            <h1 class="page-title">About The Tool Master & Engineering Company</h1>
            <p class="page-subtitle">
                Bangladesh's trusted name in industrial ventilation and air management solutions since 1983.
            </p>
        </div>

        <section class="content-section">
            <p>
                Welcome to The Tool Master & Engineering Company – Bangladesh's trusted name in industrial ventilation
                and air management solutions. Since our founding in 1983 by our honorable Managing Director Late Haji Md. Abdul Alim,
                we have been committed to engineering excellence in the air movement industry. For over four decades, we have
                consistently delivered innovative "Ventilation Solutions" that improve indoor air quality in both workspaces
                and living environments across the country.
            </p>
        </section>

        <section class="content-section">
            <h2>Industry Leaders in Ventilation & Air Movement</h2>
            <p>Today, The Tool Master & Engineering Company proudly stands as a market leader in Bangladesh for:</p>

            <div class="products-grid">
                <div class="product-item">
                    <strong>Industrial Exhaust Fans</strong>
                </div>
                <div class="product-item">
                    <strong>Air Coolers</strong>
                </div>
                <div class="product-item">
                    <strong>Dehumidifiers</strong>
                </div>
                <div class="product-item">
                    <strong>Axial Blowers</strong>
                </div>
                <div class="product-item">
                    <strong>Cooling Pads</strong>
                </div>
            </div>

            <p>
                We are one of the leading importers and suppliers of these products, providing cost-effective,
                high-performance solutions for factories, warehouses, workshops, greenhouses, and commercial spaces.
            </p>
        </section>

        <section class="content-section">
            <h2>Our Infrastructure</h2>
            <p>
                Our state-of-the-art manufacturing facility spans 15,000 square feet, strategically located in the
                prestigious BSCIC Industrial Area, Tongi, Gazipur — a hub for high-quality engineering production.
            </p>
            <p>
                We utilize cutting-edge manufacturing equipment sourced from Germany, Italy, Malaysia, Thailand, Taiwan,
                and China. This, combined with our highly skilled and experienced engineering team, ensures our products
                meet global standards in performance and durability.
            </p>
        </section>

        <section class="content-section">
            <h2>Why Choose Us?</h2>
            <div class="highlight-box">
                <ul class="features-list">
                    <li>Over 40 years of industrial expertise</li>
                    <li>In-house research, design, and product development</li>
                    <li>Best-in-class imported machinery</li>
                    <li>Customized air movement solutions</li>
                    <li>Strong distribution network across Bangladesh</li>
                    <li>Dedicated after-sales service</li>
                </ul>
            </div>
        </section>

        <section class="content-section clients-carousel">
            <h2 style="text-align: center; margin-bottom: 40px;">🤝 Our Valuable Clients</h2>
            <div class="carousel-container">
                <div class="clients-grid" id="clientsGrid">
                    <!-- Client cards will be populated by JavaScript -->
                </div>
                <div class="carousel-nav">
                    <button class="carousel-btn" id="prevBtn" onclick="previousClients()">←</button>
                    <span class="carousel-counter" id="counter"></span>
                    <button class="carousel-btn" id="nextBtn" onclick="nextClients()">→</button>
                </div>
            </div>
        </section>

        <div class="cta-section">
            <a href="contact.php" class="btn btn-primary">Get in Touch</a>
        </div>
    </main>

    <script>
        const clients = [
            { name: 'Bosch Bangladesh', logo: '/inventory_project/uploads/client-logo-1.png' },
            { name: 'Black & Decker', logo: '/inventory_project/uploads/client-logo-2.png' },
            { name: 'Makita Corporation', logo: '/inventory_project/uploads/client-logo-3.png' },
            { name: 'Stanley Tools', logo: '/inventory_project/uploads/client-logo-4.png' },
            { name: 'DeWalt Industries', logo: '/inventory_project/uploads/client-logo-5.png' },
            { name: 'Hilti Bangladesh', logo: '/inventory_project/uploads/client-logo-6.png' },
            { name: 'Metabo Ltd', logo: '/inventory_project/uploads/client-logo-7.png' },
            { name: 'Festool Systems', logo: '/inventory_project/uploads/client-logo-8.png' },
            { name: 'Ryobi Power Tools', logo: '/inventory_project/uploads/client-logo-9.png' },
            { name: 'Milwaukee Tools', logo: '/inventory_project/uploads/client-logo-10.png' },
            { name: 'Hitachi Koki', logo: '/inventory_project/uploads/client-logo-11.png' },
            { name: 'Panasonic Industrial', logo: '/inventory_project/uploads/client-logo-12.png' },
            { name: 'AEG Power Tools', logo: '/inventory_project/uploads/client-logo-13.png' },
            { name: 'Craftsman Tools', logo: '/inventory_project/uploads/client-logo-14.png' },
            { name: 'Snap-on Solutions', logo: '/inventory_project/uploads/client-logo-15.png' }
        ];

        let currentPage = 0;
        const itemsPerPage = 5;
        const totalPages = Math.ceil(clients.length / itemsPerPage);

        function displayClients() {
            const grid = document.getElementById('clientsGrid');
            grid.innerHTML = '';
            
            const start = currentPage * itemsPerPage;
            const end = start + itemsPerPage;
            const pageClients = clients.slice(start, end);
            
            pageClients.forEach(client => {
                const card = document.createElement('div');
                card.className = 'client-card';
                card.innerHTML = `
                    <img src="${client.logo}" alt="${client.name}" class="client-logo" onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22120%22 height=%22120%22%3E%3Crect fill=%22%23f0f0f0%22 width=%22120%22 height=%22120%22/%3E%3Ctext x=%2250%25%22 y=%2250%25%22 font-family=%22Arial%22 font-size=%2214%22 fill=%22%23999%22 text-anchor=%22middle%22 dy=%22.3em%22%3ENo Logo%3C/text%3E%3C/svg%3E'">
                `;
                grid.appendChild(card);
            });
            
            updateDots();
        }

        function updateDots() {
            const counter = document.getElementById('counter');
            counter.innerHTML = '';
            
            for (let i = 0; i < totalPages; i++) {
                const dot = document.createElement('span');
                dot.style.display = 'inline-block';
                dot.style.width = '12px';
                dot.style.height = '12px';
                dot.style.borderRadius = '50%';
                dot.style.margin = '0 6px';
                dot.style.cursor = 'pointer';
                dot.style.transition = 'all 0.3s';
                dot.style.backgroundColor = i === currentPage ? 'var(--accent)' : 'var(--gray)';
                dot.onclick = () => {
                    currentPage = i;
                    displayClients();
                };
                counter.appendChild(dot);
            }
        }

        function nextClients() {
            currentPage = (currentPage + 1) % totalPages;
            displayClients();
        }

        function previousClients() {
            currentPage = (currentPage - 1 + totalPages) % totalPages;
            displayClients();
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', displayClients);
    </script>
</html>