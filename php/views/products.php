<?php
$pageTitle = 'Products';
$pageHeader = 'Our Collection';

// Get filter parameters
$category = isset($_GET['category']) ? (int)$_GET['category'] : null;
$minPrice = isset($_GET['min_price']) ? (float)$_GET['min_price'] : null;
$maxPrice = isset($_GET['max_price']) ? (float)$_GET['max_price'] : null;
$search = isset($_GET['search']) ? trim($_GET['search']) : null;
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'name_asc';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = 12;

// Get products
$product = new Product($db);
$filters = [
    'category' => $category,
    'min_price' => $minPrice,
    'max_price' => $maxPrice,
    'search' => $search
];

$products = $product->getAllProducts($filters, $perPage, ($page - 1) * $perPage, $sort);
$totalProducts = $product->getTotalProducts($filters);
$totalPages = ceil($totalProducts / $perPage);

// Get categories for filter
$categories = $product->getCategories();

$content = <<<HTML
<div class="products-page">
    <div class="products-sidebar">
        <div class="filter-section">
            <h3>Categories</h3>
            <ul class="category-list">
                <li>
                    <a href="/products" class="<?php echo !$category ? 'active' : ''; ?>">
                        All Products
                    </a>
                </li>
                <?php foreach ($categories as $cat): ?>
                <li>
                    <a href="/products?category=<?php echo $cat['id']; ?>" 
                       class="<?php echo $category === $cat['id'] ? 'active' : ''; ?>">
                        <?php echo $cat['name']; ?>
                    </a>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>
        
        <div class="filter-section">
            <h3>Price Range</h3>
            <form action="/products" method="GET" class="price-filter">
                <?php if ($category): ?>
                <input type="hidden" name="category" value="<?php echo $category; ?>">
                <?php endif; ?>
                
                <div class="price-inputs">
                    <input type="number" name="min_price" placeholder="Min" value="<?php echo $minPrice; ?>">
                    <span>to</span>
                    <input type="number" name="max_price" placeholder="Max" value="<?php echo $maxPrice; ?>">
                </div>
                
                <button type="submit" class="btn btn-primary">Apply</button>
            </form>
        </div>
    </div>
    
    <div class="products-main">
        <div class="products-header">
            <div class="search-box">
                <form action="/products" method="GET">
                    <?php if ($category): ?>
                    <input type="hidden" name="category" value="<?php echo $category; ?>">
                    <?php endif; ?>
                    
                    <input type="text" name="search" placeholder="Search products..." value="<?php echo $search; ?>">
                    <button type="submit"><i class="fas fa-search"></i></button>
                </form>
            </div>
            
            <div class="sort-box">
                <select onchange="window.location.href=this.value">
                    <option value="/products?sort=name_asc<?php echo $category ? '&category=' . $category : ''; ?>" 
                            <?php echo $sort === 'name_asc' ? 'selected' : ''; ?>>
                        Name (A-Z)
                    </option>
                    <option value="/products?sort=name_desc<?php echo $category ? '&category=' . $category : ''; ?>"
                            <?php echo $sort === 'name_desc' ? 'selected' : ''; ?>>
                        Name (Z-A)
                    </option>
                    <option value="/products?sort=price_asc<?php echo $category ? '&category=' . $category : ''; ?>"
                            <?php echo $sort === 'price_asc' ? 'selected' : ''; ?>>
                        Price (Low to High)
                    </option>
                    <option value="/products?sort=price_desc<?php echo $category ? '&category=' . $category : ''; ?>"
                            <?php echo $sort === 'price_desc' ? 'selected' : ''; ?>>
                        Price (High to Low)
                    </option>
                </select>
            </div>
        </div>
        
        <div class="product-grid">
            <?php if (empty($products)): ?>
            <div class="no-results">
                <p>No products found matching your criteria.</p>
            </div>
            <?php else: ?>
                <?php foreach ($products as $item): ?>
                <div class="product-card">
                    <div class="product-image">
                        <img src="<?php echo $item['image_url']; ?>" alt="<?php echo $item['name']; ?>">
                        <?php if ($item['stock'] < 5): ?>
                        <div class="stock-badge low-stock">
                            Only <?php echo $item['stock']; ?> left
                        </div>
                        <?php endif; ?>
                    </div>
                    <div class="product-info">
                        <h3><?php echo $item['name']; ?></h3>
                        <p class="product-category"><?php echo $item['category_name']; ?></p>
                        <p class="product-price">From $<?php echo number_format($item['base_price'], 2); ?></p>
                        <a href="/product/<?php echo $item['id']; ?>" class="btn btn-outline">View Details</a>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        
        <?php if ($totalPages > 1): ?>
        <div class="pagination">
            <?php if ($page > 1): ?>
            <a href="?page=<?php echo $page - 1; ?><?php echo $category ? '&category=' . $category : ''; ?>" class="btn btn-outline">
                Previous
            </a>
            <?php endif; ?>
            
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <a href="?page=<?php echo $i; ?><?php echo $category ? '&category=' . $category : ''; ?>" 
               class="btn btn-outline <?php echo $i === $page ? 'active' : ''; ?>">
                <?php echo $i; ?>
            </a>
            <?php endfor; ?>
            
            <?php if ($page < $totalPages): ?>
            <a href="?page=<?php echo $page + 1; ?><?php echo $category ? '&category=' . $category : ''; ?>" class="btn btn-outline">
                Next
            </a>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
</div>
HTML;

require_once __DIR__ . '/layouts/base.php'; 