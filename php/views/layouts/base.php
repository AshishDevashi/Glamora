<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle . ' - ' . APP_NAME : APP_NAME; ?></title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="/images/favicon.ico">
    
    <!-- CSS -->
    <link rel="stylesheet" href="/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <?php if (isset($extraCss)): ?>
        <?php foreach ($extraCss as $css): ?>
            <link rel="stylesheet" href="<?php echo $css; ?>">
        <?php endforeach; ?>
    <?php endif; ?>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="container">
            <nav class="nav">
                <a href="/" class="logo">
                    <img src="/images/logo.png" alt="<?php echo APP_NAME; ?>">
                </a>
                
                <ul class="nav-links">
                    <li><a href="/products">Products</a></li>
                    <li><a href="/about">About</a></li>
                    <li><a href="/contact">Contact</a></li>
                </ul>
                
                <div class="nav-actions">
                    <a href="/cart" class="cart-link">
                        <i class="fas fa-shopping-cart"></i>
                        <span class="cart-count">0</span>
                    </a>
                    
                    <?php if ($auth->isLoggedIn()): ?>
                        <div class="user-menu">
                            <button class="user-menu-toggle">
                                <i class="fas fa-user"></i>
                            </button>
                            <ul class="user-menu-dropdown">
                                <li><a href="/orders">My Orders</a></li>
                                <li><a href="/profile">Profile</a></li>
                                <li><a href="/logout">Logout</a></li>
                            </ul>
                        </div>
                    <?php else: ?>
                        <div class="auth-links">
                            <a href="/login" class="btn btn-outline">Login</a>
                            <a href="/register" class="btn btn-primary">Register</a>
                        </div>
                    <?php endif; ?>
                </div>
            </nav>
        </div>
    </header>
    
    <!-- Main Content -->
    <main class="main">
        <?php if (isset($pageHeader)): ?>
            <div class="page-header">
                <div class="container">
                    <h1><?php echo $pageHeader; ?></h1>
                </div>
            </div>
        <?php endif; ?>
        
        <div class="container">
            <?php if (isset($flashMessage)): ?>
                <div class="alert alert-<?php echo $flashMessage['type']; ?>">
                    <?php echo $flashMessage['message']; ?>
                </div>
            <?php endif; ?>
            
            <?php echo $content; ?>
        </div>
    </main>
    
    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3>About Us</h3>
                    <p>Glamora is your premier destination for luxury jewelry rentals. We offer a curated collection of stunning pieces for any occasion.</p>
                </div>
                
                <div class="footer-section">
                    <h3>Quick Links</h3>
                    <ul>
                        <li><a href="/products">Products</a></li>
                        <li><a href="/about">About</a></li>
                        <li><a href="/contact">Contact</a></li>
                        <li><a href="/terms">Terms & Conditions</a></li>
                        <li><a href="/privacy">Privacy Policy</a></li>
                    </ul>
                </div>
                
                <div class="footer-section">
                    <h3>Contact Us</h3>
                    <ul>
                        <li><i class="fas fa-phone"></i> +1 (555) 123-4567</li>
                        <li><i class="fas fa-envelope"></i> info@glamora.com</li>
                        <li><i class="fas fa-map-marker-alt"></i> 123 Luxury Lane, Beverly Hills, CA 90210</li>
                    </ul>
                </div>
                
                <div class="footer-section">
                    <h3>Follow Us</h3>
                    <div class="social-links">
                        <a href="#"><i class="fab fa-facebook"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-pinterest"></i></a>
                    </div>
                </div>
            </div>
            
            <div class="footer-bottom">
                <p>&copy; <?php echo date('Y'); ?> <?php echo APP_NAME; ?>. All rights reserved.</p>
            </div>
        </div>
    </footer>
    
    <!-- JavaScript -->
    <script src="/js/app.js"></script>
    
    <?php if (isset($extraJs)): ?>
        <?php foreach ($extraJs as $js): ?>
            <script src="<?php echo $js; ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
</body>
</html> 