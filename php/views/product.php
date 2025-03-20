<?php
$pageTitle = 'Product Details';

// Get product ID from URL
$productId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Get product details
$product = new Product($db);
$item = $product->getProductById($productId);

if (!$item) {
    header('Location: /products');
    exit();
}

// Get rental periods
$periods = $product->getRentalPeriods();

$content = <<<HTML
<div class="product-details">
    <div class="product-gallery">
        <div class="main-image">
            <img src="<?php echo $item['image_url']; ?>" alt="<?php echo $item['name']; ?>">
        </div>
        <?php if (!empty($item['gallery_images'])): ?>
        <div class="thumbnail-list">
            <?php foreach ($item['gallery_images'] as $image): ?>
            <div class="thumbnail">
                <img src="<?php echo $image; ?>" alt="<?php echo $item['name']; ?>">
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
    
    <div class="product-info">
        <h1><?php echo $item['name']; ?></h1>
        <p class="product-category"><?php echo $item['category_name']; ?></p>
        
        <div class="product-description">
            <?php echo nl2br($item['description']); ?>
        </div>
        
        <div class="product-meta">
            <div class="meta-item">
                <i class="fas fa-box"></i>
                <span>Stock: <?php echo $item['stock']; ?> available</span>
            </div>
            <div class="meta-item">
                <i class="fas fa-tag"></i>
                <span>Base Price: $<?php echo number_format($item['base_price'], 2); ?></span>
            </div>
        </div>
        
        <div class="rental-options">
            <h3>Rental Periods</h3>
            <div class="period-grid">
                <?php foreach ($periods as $period): ?>
                <div class="period-card">
                    <h4><?php echo $period['name']; ?></h4>
                    <p class="period-price">
                        $<?php echo number_format($product->calculateRentalPrice($item['base_price'], $period['id']), 2); ?>
                    </p>
                    <?php if ($period['discount'] > 0): ?>
                    <p class="period-discount"><?php echo $period['discount']; ?>% off</p>
                    <?php endif; ?>
                    <button class="btn btn-primary add-to-cart" 
                            data-product-id="<?php echo $item['id']; ?>"
                            data-period-id="<?php echo $period['id']; ?>">
                        Add to Cart
                    </button>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        
        <div class="product-details-tabs">
            <div class="tabs-header">
                <button class="tab-btn active" data-tab="details">Details</button>
                <button class="tab-btn" data-tab="care">Care Instructions</button>
                <button class="tab-btn" data-tab="shipping">Shipping</button>
            </div>
            
            <div class="tab-content active" id="details">
                <h3>Product Details</h3>
                <ul class="details-list">
                    <li><strong>Material:</strong> <?php echo $item['material']; ?></li>
                    <li><strong>Weight:</strong> <?php echo $item['weight']; ?>g</li>
                    <li><strong>Length:</strong> <?php echo $item['length']; ?>cm</li>
                    <?php if (!empty($item['stone_type'])): ?>
                    <li><strong>Stone Type:</strong> <?php echo $item['stone_type']; ?></li>
                    <?php endif; ?>
                </ul>
            </div>
            
            <div class="tab-content" id="care">
                <h3>Care Instructions</h3>
                <div class="care-instructions">
                    <?php echo nl2br($item['care_instructions']); ?>
                </div>
            </div>
            
            <div class="tab-content" id="shipping">
                <h3>Shipping Information</h3>
                <div class="shipping-info">
                    <?php echo nl2br($item['shipping_info']); ?>
                </div>
            </div>
        </div>
        
        <?php if (!empty($item['related_products'])): ?>
        <div class="related-products">
            <h3>You May Also Like</h3>
            <div class="product-grid">
                <?php foreach ($item['related_products'] as $related): ?>
                <div class="product-card">
                    <div class="product-image">
                        <img src="<?php echo $related['image_url']; ?>" alt="<?php echo $related['name']; ?>">
                    </div>
                    <div class="product-info">
                        <h4><?php echo $related['name']; ?></h4>
                        <p class="product-price">From $<?php echo number_format($related['base_price'], 2); ?></p>
                        <a href="/product/<?php echo $related['id']; ?>" class="btn btn-outline">View Details</a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle tab switching
    const tabBtns = document.querySelectorAll('.tab-btn');
    const tabContents = document.querySelectorAll('.tab-content');
    
    tabBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            const tabId = btn.dataset.tab;
            
            tabBtns.forEach(b => b.classList.remove('active'));
            tabContents.forEach(c => c.classList.remove('active'));
            
            btn.classList.add('active');
            document.getElementById(tabId).classList.add('active');
        });
    });
    
    // Handle add to cart
    const addToCartBtns = document.querySelectorAll('.add-to-cart');
    
    addToCartBtns.forEach(btn => {
        btn.addEventListener('click', async () => {
            const productId = btn.dataset.productId;
            const periodId = btn.dataset.periodId;
            
            try {
                const response = await fetch('/api/cart', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        action: 'add',
                        product_id: productId,
                        period_id: periodId
                    })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    // Update cart count
                    const cartCount = document.querySelector('.cart-count');
                    if (cartCount) {
                        cartCount.textContent = data.cart_count;
                    }
                    
                    // Show success message
                    showMessage('Item added to cart successfully', 'success');
                } else {
                    showMessage(data.message || 'Failed to add item to cart', 'error');
                }
            } catch (error) {
                showMessage('An error occurred. Please try again.', 'error');
            }
        });
    });
    
    // Handle gallery thumbnails
    const thumbnails = document.querySelectorAll('.thumbnail');
    const mainImage = document.querySelector('.main-image img');
    
    thumbnails.forEach(thumb => {
        thumb.addEventListener('click', () => {
            mainImage.src = thumb.querySelector('img').src;
        });
    });
});

function showMessage(message, type) {
    const alert = document.createElement('div');
    alert.className = `alert alert-${type}`;
    alert.textContent = message;
    
    document.querySelector('.container').insertBefore(alert, document.querySelector('.product-details'));
    
    setTimeout(() => {
        alert.remove();
    }, 3000);
}
</script>
HTML;

require_once __DIR__ . '/layouts/base.php'; 