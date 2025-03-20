<?php
$pageTitle = 'Home';
$pageHeader = 'Welcome to Glamora';
$content = <<<HTML
<div class="hero">
    <div class="hero-content">
        <h1>Luxury Jewelry Rentals</h1>
        <p>Access the finest jewelry pieces for your special occasions</p>
        <a href="/products" class="btn btn-primary">Browse Collection</a>
    </div>
</div>

<section class="featured-products">
    <h2>Featured Products</h2>
    <div class="product-grid">
        <?php
        $product = new Product($db);
        $featuredProducts = $product->getAllProducts(['featured' => true], 4);
        
        foreach ($featuredProducts as $item):
        ?>
        <div class="product-card">
            <div class="product-image">
                <img src="<?php echo $item['image_url']; ?>" alt="<?php echo $item['name']; ?>">
            </div>
            <div class="product-info">
                <h3><?php echo $item['name']; ?></h3>
                <p class="product-price">From $<?php echo number_format($item['base_price'], 2); ?></p>
                <a href="/product/<?php echo $item['id']; ?>" class="btn btn-outline">View Details</a>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</section>

<section class="categories">
    <h2>Shop by Category</h2>
    <div class="category-grid">
        <?php
        $categories = $product->getCategories();
        
        foreach ($categories as $category):
        ?>
        <div class="category-card">
            <img src="/images/categories/<?php echo strtolower($category['name']); ?>.jpg" alt="<?php echo $category['name']; ?>">
            <div class="category-info">
                <h3><?php echo $category['name']; ?></h3>
                <p><?php echo $category['description']; ?></p>
                <a href="/products?category=<?php echo $category['id']; ?>" class="btn btn-outline">View Collection</a>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</section>

<section class="how-it-works">
    <h2>How It Works</h2>
    <div class="steps">
        <div class="step">
            <div class="step-icon">
                <i class="fas fa-search"></i>
            </div>
            <h3>Browse Collection</h3>
            <p>Explore our curated selection of luxury jewelry pieces</p>
        </div>
        
        <div class="step">
            <div class="step-icon">
                <i class="fas fa-calendar-alt"></i>
            </div>
            <h3>Choose Rental Period</h3>
            <p>Select the duration that suits your needs</p>
        </div>
        
        <div class="step">
            <div class="step-icon">
                <i class="fas fa-shopping-cart"></i>
            </div>
            <h3>Add to Cart</h3>
            <p>Select your items and proceed to checkout</p>
        </div>
        
        <div class="step">
            <div class="step-icon">
                <i class="fas fa-truck"></i>
            </div>
            <h3>Receive & Return</h3>
            <p>Get your jewelry delivered and return it after use</p>
        </div>
    </div>
</section>

<section class="testimonials">
    <h2>What Our Customers Say</h2>
    <div class="testimonial-slider">
        <div class="testimonial">
            <div class="testimonial-content">
                <p>"The quality of the jewelry exceeded my expectations. I felt like a million bucks at my wedding!"</p>
                <div class="testimonial-author">
                    <img src="/images/testimonials/customer1.jpg" alt="Sarah Johnson">
                    <div>
                        <h4>Sarah Johnson</h4>
                        <p>Wedding Day</p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="testimonial">
            <div class="testimonial-content">
                <p>"Glamora made my special occasion even more memorable. The service was exceptional!"</p>
                <div class="testimonial-author">
                    <img src="/images/testimonials/customer2.jpg" alt="Michael Chen">
                    <div>
                        <h4>Michael Chen</h4>
                        <p>Anniversary Celebration</p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="testimonial">
            <div class="testimonial-content">
                <p>"I love how easy it is to rent luxury jewelry. The variety is amazing!"</p>
                <div class="testimonial-author">
                    <img src="/images/testimonials/customer3.jpg" alt="Emma Davis">
                    <div>
                        <h4>Emma Davis</h4>
                        <p>Red Carpet Event</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="cta">
    <div class="cta-content">
        <h2>Ready to Shine?</h2>
        <p>Join our community of satisfied customers and experience luxury jewelry rentals</p>
        <a href="/register" class="btn btn-primary">Get Started</a>
    </div>
</section>
HTML;

require_once __DIR__ . '/layouts/base.php'; 