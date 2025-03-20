/**
 * Jewelry Rental Website - Main Module
 * Handles homepage functionality and jewelry listings
 */

// DOM Elements
const jewelryContainer = document.getElementById('jewelry-container');

/**
 * Main module for homepage functionality
 */
const mainModule = {
    /**
     * Initialize the main module
     */
    init() {
        // Load and display jewelry items
        this.loadJewelry();

        // Set up event listeners
        this.setupEventListeners();
    },

    /**
     * Load and display jewelry items
     */
    loadJewelry() {
        // Clear loading spinner
        jewelryContainer.innerHTML = '';

        // Get all jewelry items
        const jewelry = getAllJewelry();

        // Display each jewelry item
        jewelry.forEach(item => {
            const card = this.createJewelryCard(item);
            jewelryContainer.appendChild(card);
        });
    },

    /**
     * Create a jewelry card element
     * @param {Object} item - Jewelry item data
     * @returns {HTMLElement} Jewelry card element
     */
    createJewelryCard(item) {
        // Create card column
        const col = document.createElement('div');
        col.className = 'col-md-6 col-lg-4 col-xl-3';

        // Create card
        const card = document.createElement('div');
        card.className = 'card jewelry-card h-100';
        card.dataset.id = item.id;

        // Create card image
        const img = document.createElement('img');
        img.className = 'card-img-top';
        img.src = item.image;
        img.alt = item.name;
        img.onerror = function () {
            this.src = 'https://via.placeholder.com/300x200?text=Jewelry+Image';
        };

        // Create card body
        const cardBody = document.createElement('div');
        cardBody.className = 'card-body d-flex flex-column';

        // Create card title
        const title = document.createElement('h5');
        title.className = 'card-title';
        title.textContent = item.name;

        // Create card description
        const description = document.createElement('p');
        description.className = 'card-text mb-2';
        description.textContent = item.description;

        // Create rental period selection
        const rentalPeriodGroup = document.createElement('div');
        rentalPeriodGroup.className = 'form-group mb-3';

        const rentalPeriodLabel = document.createElement('label');
        rentalPeriodLabel.className = 'form-label';
        rentalPeriodLabel.textContent = 'Rental Period:';

        const rentalPeriodSelect = document.createElement('select');
        rentalPeriodSelect.className = 'form-select rental-period';

        // Add rental period options
        item.rentalPeriods.forEach(period => {
            const option = document.createElement('option');
            option.value = period;
            option.textContent = period;
            rentalPeriodSelect.appendChild(option);
        });

        rentalPeriodGroup.appendChild(rentalPeriodLabel);
        rentalPeriodGroup.appendChild(rentalPeriodSelect);

        // Create price display
        const priceContainer = document.createElement('div');
        priceContainer.className = 'mt-auto';

        const price = document.createElement('p');
        price.className = 'jewelry-price mb-2';
        price.textContent = formatPrice(item.price);

        // Create add to cart button
        const addToCartBtn = document.createElement('button');
        addToCartBtn.className = 'btn btn-primary w-100 add-to-cart-btn';
        addToCartBtn.innerHTML = '<i class="fas fa-shopping-cart me-2"></i>Add to Cart';
        addToCartBtn.dataset.id = item.id;

        // Add event listener to rental period select to update price
        rentalPeriodSelect.addEventListener('change', () => {
            const selectedPeriod = rentalPeriodSelect.value;
            const calculatedPrice = calculateRentalPrice(item.price, selectedPeriod);
            price.textContent = formatPrice(calculatedPrice);
        });

        // Add event listener to add to cart button
        addToCartBtn.addEventListener('click', () => {
            this.addToCart(item, rentalPeriodSelect.value);
        });

        priceContainer.appendChild(price);
        priceContainer.appendChild(addToCartBtn);

        // Assemble card
        cardBody.appendChild(title);
        cardBody.appendChild(description);
        cardBody.appendChild(rentalPeriodGroup);
        cardBody.appendChild(priceContainer);

        card.appendChild(img);
        card.appendChild(cardBody);

        col.appendChild(card);

        return col;
    },

    /**
     * Add item to cart
     * @param {Object} item - Jewelry item data
     * @param {string} rentalPeriod - Selected rental period
     */
    addToCart(item, rentalPeriod) {
        // Calculate price based on rental period
        const price = calculateRentalPrice(item.price, rentalPeriod);

        // Create cart item
        const cartItem = {
            id: item.id,
            name: item.name,
            image: item.image,
            price: price,
            rentalPeriod: rentalPeriod
        };

        // Get cart from local storage
        let cart = JSON.parse(localStorage.getItem('borrowed_cart')) || [];

        // Check if item is already in cart
        const existingItemIndex = cart.findIndex(i => i.id === item.id);

        if (existingItemIndex !== -1) {
            // Update existing item
            cart[existingItemIndex] = cartItem;
        } else {
            // Add new item
            cart.push(cartItem);
        }

        // Save cart to local storage
        localStorage.setItem('borrowed_cart', JSON.stringify(cart));

        // Show success message
        this.showAddToCartMessage(item.name);

        // Update cart badge
        this.updateCartBadge();
    },

    /**
     * Show add to cart success message
     * @param {string} itemName - Name of the item added to cart
     */
    showAddToCartMessage(itemName) {
        // Create toast container if it doesn't exist
        let toastContainer = document.querySelector('.toast-container');
        if (!toastContainer) {
            toastContainer = document.createElement('div');
            toastContainer.className = 'toast-container position-fixed bottom-0 end-0 p-3';
            document.body.appendChild(toastContainer);
        }

        // Create toast element
        const toastId = `toast-${Date.now()}`;
        const toast = document.createElement('div');
        toast.className = 'toast';
        toast.id = toastId;
        toast.setAttribute('role', 'alert');
        toast.setAttribute('aria-live', 'assertive');
        toast.setAttribute('aria-atomic', 'true');

        // Create toast content
        toast.innerHTML = `
            <div class="toast-header">
                <i class="fas fa-check-circle text-success me-2"></i>
                <strong class="me-auto">Added to Cart</strong>
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body">
                ${itemName} has been added to your cart.
                <div class="mt-2 pt-2 border-top">
                    <a href="cart.html" class="btn btn-primary btn-sm">View Cart</a>
                </div>
            </div>
        `;

        // Add toast to container
        toastContainer.appendChild(toast);

        // Initialize and show toast
        const bsToast = new bootstrap.Toast(toast);
        bsToast.show();

        // Remove toast after it's hidden
        toast.addEventListener('hidden.bs.toast', () => {
            toast.remove();
        });

        // Animate cart badge
        const cartBadge = document.getElementById('cart-badge');
        if (cartBadge) {
            cartBadge.classList.add('cart-animation');
            setTimeout(() => {
                cartBadge.classList.remove('cart-animation');
            }, 500);
        }
    },

    /**
     * Update cart badge with current number of items in cart
     */
    updateCartBadge() {
        const cartBadge = document.getElementById('cart-badge');
        if (cartBadge) {
            const cart = JSON.parse(localStorage.getItem('borrowed_cart')) || [];
            cartBadge.textContent = cart.length;
        }
    },

    /**
     * Set up event listeners
     */
    setupEventListeners() {
        // Scroll to jewelry section when "Browse Collection" button is clicked
        window.scrollToJewelry = () => {
            const jewelrySection = document.getElementById('jewelry-listings');
            if (jewelrySection) {
                jewelrySection.scrollIntoView({ behavior: 'smooth' });
            }
        };
    }
};

// Initialize main module when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    mainModule.init();
    mainModule.updateCartBadge();
});

// Export for use in other modules
window.mainModule = mainModule;

// Handle navbar background on scroll
window.addEventListener('scroll', function () {
    const navbar = document.querySelector('.navbar');
    if (window.scrollY > 50) {
        navbar.classList.add('scrolled');
    } else {
        navbar.classList.remove('scrolled');
    }
}); 