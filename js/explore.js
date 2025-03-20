/**
 * Explore Page Module
 * Handles jewelry filtering, sorting, and display
 */

// DOM Elements
const jewelryGrid = document.getElementById('jewelry-grid');
const searchInput = document.getElementById('search-jewelry');
const priceRange = document.getElementById('price-range');
const clearFiltersBtn = document.getElementById('clear-filters');
const itemsCount = document.getElementById('items-count');
const categoryFilters = document.querySelectorAll('input[type="checkbox"][id^="filter-"]');

// State
let currentFilters = {
    search: '',
    categories: [],
    materials: [],
    maxPrice: 1000
};

let currentSort = 'name';

/**
 * Initialize the explore page
 */
function init() {
    loadJewelry();
    setupEventListeners();
}

/**
 * Set up event listeners
 */
function setupEventListeners() {
    // Search input
    searchInput.addEventListener('input', debounce(() => {
        currentFilters.search = searchInput.value.toLowerCase();
        applyFilters();
    }, 300));

    // Price range
    priceRange.addEventListener('input', () => {
        currentFilters.maxPrice = parseInt(priceRange.value);
        applyFilters();
    });

    // Category and material filters
    categoryFilters.forEach(filter => {
        filter.addEventListener('change', () => {
            updateFilters();
            applyFilters();
        });
    });

    // Clear filters
    clearFiltersBtn.addEventListener('click', clearFilters);

    // Sort dropdown
    document.querySelectorAll('.dropdown-item[data-sort]').forEach(item => {
        item.addEventListener('click', (e) => {
            e.preventDefault();
            currentSort = e.target.dataset.sort;
            applyFilters();
        });
    });
}

/**
 * Load and display jewelry items
 */
function loadJewelry() {
    const jewelry = getAllJewelry();
    displayJewelry(jewelry);
}

/**
 * Update filters based on checkbox states
 */
function updateFilters() {
    const categories = [];
    const materials = [];

    categoryFilters.forEach(filter => {
        if (filter.checked) {
            if (filter.id.includes('filter-')) {
                const type = filter.id.replace('filter-', '');
                if (['necklaces', 'earrings', 'bracelets', 'rings'].includes(type)) {
                    categories.push(type);
                } else if (['gold', 'silver', 'platinum'].includes(type)) {
                    materials.push(type);
                }
            }
        }
    });

    currentFilters.categories = categories;
    currentFilters.materials = materials;
}

/**
 * Apply filters and sort to jewelry items
 */
function applyFilters() {
    let jewelry = getAllJewelry();

    // Apply search filter
    if (currentFilters.search) {
        jewelry = jewelry.filter(item =>
            item.name.toLowerCase().includes(currentFilters.search) ||
            item.description.toLowerCase().includes(currentFilters.search)
        );
    }

    // Apply category filter
    if (currentFilters.categories.length > 0) {
        jewelry = jewelry.filter(item =>
            currentFilters.categories.includes(item.category.toLowerCase())
        );
    }

    // Apply material filter
    if (currentFilters.materials.length > 0) {
        jewelry = jewelry.filter(item =>
            currentFilters.materials.includes(item.material.toLowerCase())
        );
    }

    // Apply price filter
    jewelry = jewelry.filter(item => item.price <= currentFilters.maxPrice);

    // Apply sorting
    jewelry.sort((a, b) => {
        switch (currentSort) {
            case 'price-low':
                return a.price - b.price;
            case 'price-high':
                return b.price - a.price;
            case 'name':
            default:
                return a.name.localeCompare(b.name);
        }
    });

    displayJewelry(jewelry);
}

/**
 * Display jewelry items in the grid
 */
function displayJewelry(jewelry) {
    jewelryGrid.innerHTML = '';

    if (jewelry.length === 0) {
        jewelryGrid.innerHTML = `
            <div class="col-12 text-center">
                <p class="text-muted">No jewelry items found matching your criteria.</p>
            </div>
        `;
    } else {
        jewelry.forEach(item => {
            const card = createJewelryCard(item);
            jewelryGrid.appendChild(card);
        });
    }

    // Update items count
    itemsCount.textContent = `Showing ${jewelry.length} items`;
}

/**
 * Create a jewelry card element
 */
function createJewelryCard(item) {
    const col = document.createElement('div');
    col.className = 'col-md-6 col-lg-4';

    col.innerHTML = `
        <div class="card jewelry-card">
            <img src="${item.image}" class="card-img-top" alt="${item.name}" 
                onerror="this.src='https://via.placeholder.com/300x300?text=Jewelry+Image'">
            <div class="card-body">
                <h5 class="card-title">${item.name}</h5>
                <p class="card-text">${item.description}</p>
                <p class="jewelry-price">${formatPrice(item.price)}</p>
                <button class="btn btn-primary w-100 add-to-cart-btn" data-id="${item.id}">
                    Add to Cart
                </button>
            </div>
        </div>
    `;

    // Add event listener to add to cart button
    const addToCartBtn = col.querySelector('.add-to-cart-btn');
    addToCartBtn.addEventListener('click', () => {
        mainModule.addToCart(item, '3 days'); // Default rental period
    });

    return col;
}

/**
 * Clear all filters
 */
function clearFilters() {
    // Reset search
    searchInput.value = '';
    currentFilters.search = '';

    // Reset price range
    priceRange.value = 1000;
    currentFilters.maxPrice = 1000;

    // Reset checkboxes
    categoryFilters.forEach(filter => {
        filter.checked = false;
    });

    // Reset filter arrays
    currentFilters.categories = [];
    currentFilters.materials = [];

    // Reset sort
    currentSort = 'name';

    // Apply reset
    applyFilters();
}

/**
 * Debounce function for search input
 */
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', init); 