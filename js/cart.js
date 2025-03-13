/**
 * Jewelry Rental Website - Cart Module
 * Handles cart functionality and checkout process
 */

// DOM Elements
const cartItemsContainer = document.getElementById('cart-items');
const emptyCartMessage = document.getElementById('empty-cart-message');
const cartItemsSection = document.getElementById('cart-items-container');
const cartSummary = document.getElementById('cart-summary');
const cartSubtotal = document.getElementById('cart-subtotal');
const cartTotal = document.getElementById('cart-total');
const checkoutBtn = document.getElementById('checkout-btn');

// Checkout form elements
const shippingForm = document.getElementById('shipping-form');
const shippingFormContainer = document.getElementById('shipping-form-container');
const deliveryOptionsContainer = document.getElementById('delivery-options-container');
const orderReviewContainer = document.getElementById('order-review-container');
const backToShippingBtn = document.getElementById('back-to-shipping');
const continueToReviewBtn = document.getElementById('continue-to-review');
const backToDeliveryBtn = document.getElementById('back-to-delivery');
const placeOrderBtn = document.getElementById('place-order-btn');

// Progress bar and steps
const progressBar = document.querySelector('.progress-bar');
const shippingStep = document.getElementById('step-shipping');
const deliveryStep = document.getElementById('step-delivery');
const reviewStep = document.getElementById('step-review');

// Review elements
const reviewItems = document.getElementById('review-items');
const reviewAddress = document.getElementById('review-address');
const reviewDelivery = document.getElementById('review-delivery');
const reviewSubtotal = document.getElementById('review-subtotal');
const reviewDeliveryCost = document.getElementById('review-delivery-cost');
const reviewTotal = document.getElementById('review-total');

// Local Storage Keys
const CART_KEY = 'borrowed_cart';

/**
 * Cart module to manage cart functionality
 */
const cartModule = {
    // Cart data
    cart: [],
    shippingAddress: {},
    deliveryOption: 'standard',
    
    /**
     * Initialize the cart module
     */
    init() {
        // Load cart from local storage
        this.loadCart();
        
        // Display cart items
        this.displayCart();
        
        // Set up event listeners
        this.setupEventListeners();
    },

    /**
     * Load cart from local storage
     */
    loadCart() {
        const storedCart = localStorage.getItem(CART_KEY);
        this.cart = storedCart ? JSON.parse(storedCart) : [];
    },

    /**
     * Save cart to local storage
     */
    saveCart() {
        localStorage.setItem(CART_KEY, JSON.stringify(this.cart));
    },

    /**
     * Display cart items
     */
    displayCart() {
        // Update cart badge
        this.updateCartBadge();
        
        // Check if cart is empty
        if (this.cart.length === 0) {
            if (emptyCartMessage) emptyCartMessage.classList.remove('d-none');
            if (cartItemsSection) cartItemsSection.classList.add('d-none');
            if (cartSummary) cartSummary.classList.add('d-none');
            return;
        }
        
        // Show cart items and summary
        if (emptyCartMessage) emptyCartMessage.classList.add('d-none');
        if (cartItemsSection) cartItemsSection.classList.remove('d-none');
        if (cartSummary) cartSummary.classList.remove('d-none');
        
        // Clear cart items container
        if (cartItemsContainer) cartItemsContainer.innerHTML = '';
        
        // Calculate subtotal
        let subtotal = 0;
        
        // Add each item to cart
        this.cart.forEach(item => {
            subtotal += item.price;
            
            if (cartItemsContainer) {
                const row = document.createElement('tr');
                
                row.innerHTML = `
                    <td>
                        <img src="${item.image}" alt="${item.name}" class="cart-item-img rounded" onerror="this.src='https://via.placeholder.com/80?text=Jewelry'">
                    </td>
                    <td>${item.name}</td>
                    <td>${item.rentalPeriod}</td>
                    <td>${formatPrice(item.price)}</td>
                    <td>
                        <button class="btn btn-sm remove-item-btn" data-id="${item.id}">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                `;
                
                cartItemsContainer.appendChild(row);
            }
        });
        
        // Update subtotal and total
        if (cartSubtotal) cartSubtotal.textContent = formatPrice(subtotal);
        if (cartTotal) cartTotal.textContent = formatPrice(subtotal);
    },

    /**
     * Update cart badge with current number of items in cart
     */
    updateCartBadge() {
        const cartBadge = document.getElementById('cart-badge');
        if (cartBadge) {
            cartBadge.textContent = this.cart.length;
        }
    },

    /**
     * Remove item from cart
     * @param {number} itemId - ID of item to remove
     */
    removeItem(itemId) {
        // Find item index
        const itemIndex = this.cart.findIndex(item => item.id === parseInt(itemId));
        
        // Remove item if found
        if (itemIndex !== -1) {
            this.cart.splice(itemIndex, 1);
            this.saveCart();
            this.displayCart();
        }
    },

    /**
     * Handle shipping form submission
     * @param {Event} e - Form submission event
     */
    handleShippingSubmit(e) {
        e.preventDefault();
        
        // Get form data
        const firstName = document.getElementById('firstName').value;
        const lastName = document.getElementById('lastName').value;
        const address = document.getElementById('address').value;
        const address2 = document.getElementById('address2').value;
        const country = document.getElementById('country').value;
        const state = document.getElementById('state').value;
        const zip = document.getElementById('zip').value;
        
        // Save shipping address
        this.shippingAddress = {
            firstName,
            lastName,
            address,
            address2,
            country,
            state,
            zip
        };
        
        // Move to delivery step
        this.goToDeliveryStep();
    },

    /**
     * Go to delivery step
     */
    goToDeliveryStep() {
        // Hide shipping form, show delivery options
        shippingFormContainer.classList.add('d-none');
        deliveryOptionsContainer.classList.remove('d-none');
        orderReviewContainer.classList.add('d-none');
        
        // Update progress bar and steps
        progressBar.style.width = '66%';
        progressBar.setAttribute('aria-valuenow', '66');
        
        shippingStep.classList.remove('active');
        shippingStep.classList.add('completed');
        deliveryStep.classList.add('active');
        reviewStep.classList.remove('active');
    },

    /**
     * Go back to shipping step
     */
    goToShippingStep() {
        // Show shipping form, hide other steps
        shippingFormContainer.classList.remove('d-none');
        deliveryOptionsContainer.classList.add('d-none');
        orderReviewContainer.classList.add('d-none');
        
        // Update progress bar and steps
        progressBar.style.width = '33%';
        progressBar.setAttribute('aria-valuenow', '33');
        
        shippingStep.classList.add('active');
        shippingStep.classList.remove('completed');
        deliveryStep.classList.remove('active');
        reviewStep.classList.remove('active');
    },

    /**
     * Continue to review step
     */
    continueToReview() {
        // Get selected delivery option
        const deliveryRadios = document.querySelectorAll('input[name="deliveryOption"]');
        deliveryRadios.forEach(radio => {
            if (radio.checked) {
                this.deliveryOption = radio.value;
            }
        });
        
        // Populate review section
        this.populateReviewSection();
        
        // Hide delivery options, show review
        shippingFormContainer.classList.add('d-none');
        deliveryOptionsContainer.classList.add('d-none');
        orderReviewContainer.classList.remove('d-none');
        
        // Update progress bar and steps
        progressBar.style.width = '100%';
        progressBar.setAttribute('aria-valuenow', '100');
        
        shippingStep.classList.remove('active');
        shippingStep.classList.add('completed');
        deliveryStep.classList.remove('active');
        deliveryStep.classList.add('completed');
        reviewStep.classList.add('active');
    },

    /**
     * Populate review section with order details
     */
    populateReviewSection() {
        // Clear review items
        reviewItems.innerHTML = '';
        
        // Calculate subtotal
        let subtotal = 0;
        
        // Add each item to review
        this.cart.forEach(item => {
            subtotal += item.price;
            
            const itemDiv = document.createElement('div');
            itemDiv.className = 'd-flex justify-content-between align-items-center mb-2';
            
            itemDiv.innerHTML = `
                <div class="d-flex align-items-center">
                    <img src="${item.image}" alt="${item.name}" class="order-item-img rounded me-2" onerror="this.src='https://via.placeholder.com/60?text=Jewelry'">
                    <div>
                        <h6 class="mb-0">${item.name}</h6>
                        <small class="text-muted">Rental Period: ${item.rentalPeriod}</small>
                    </div>
                </div>
                <span>${formatPrice(item.price)}</span>
            `;
            
            reviewItems.appendChild(itemDiv);
        });
        
        // Set delivery details
        const deliveryDetails = deliveryOptions[this.deliveryOption];
        const deliveryCost = deliveryDetails.price;
        const total = subtotal + deliveryCost;
        
        // Update review address
        const { firstName, lastName, address, address2, city, state, zip, country } = this.shippingAddress;
        reviewAddress.innerHTML = `
            ${firstName} ${lastName}<br>
            ${address}<br>
            ${address2 ? address2 + '<br>' : ''}
            ${state}, ${zip}<br>
            ${country}
        `;
        
        // Update review delivery
        reviewDelivery.innerHTML = `
            ${deliveryDetails.name} (${deliveryDetails.description})<br>
            ${formatPrice(deliveryCost)}
        `;
        
        // Update review totals
        reviewSubtotal.textContent = formatPrice(subtotal);
        reviewDeliveryCost.textContent = formatPrice(deliveryCost);
        reviewTotal.textContent = formatPrice(total);
    },

    /**
     * Place order
     */
    placeOrder() {
        // Check if user is logged in
        if (!userAuth.isLoggedIn()) {
            // Show login modal
            const loginModal = new bootstrap.Modal(document.getElementById('loginModal'));
            loginModal.show();
            return;
        }
        
        // Calculate subtotal
        let subtotal = 0;
        this.cart.forEach(item => {
            subtotal += item.price;
        });
        
        // Get delivery details
        const deliveryDetails = deliveryOptions[this.deliveryOption];
        const deliveryCost = deliveryDetails.price;
        const total = subtotal + deliveryCost;
        
        // Create order object
        const order = {
            id: generateOrderId(),
            date: new Date(),
            items: [...this.cart],
            shippingAddress: { ...this.shippingAddress },
            delivery: {
                option: this.deliveryOption,
                name: deliveryDetails.name,
                description: deliveryDetails.description,
                price: deliveryCost
            },
            subtotal: subtotal,
            total: total,
            status: 'Completed'
        };
        
        // Add order to user's order history
        userAuth.addOrder(order);
        
        // Clear cart
        this.cart = [];
        this.saveCart();
        
        // Close checkout modal
        const checkoutModal = bootstrap.Modal.getInstance(document.getElementById('checkoutModal'));
        if (checkoutModal) {
            checkoutModal.hide();
        }
        
        // Show order confirmation modal
        const orderConfirmationModal = new bootstrap.Modal(document.getElementById('orderConfirmationModal'));
        orderConfirmationModal.show();
        
        // Update cart display
        this.displayCart();
    },

    /**
     * Set up event listeners
     */
    setupEventListeners() {
        // Remove item buttons
        if (cartItemsContainer) {
            cartItemsContainer.addEventListener('click', (e) => {
                if (e.target.closest('.remove-item-btn')) {
                    const button = e.target.closest('.remove-item-btn');
                    const itemId = button.dataset.id;
                    this.removeItem(itemId);
                }
            });
        }
        
        // Shipping form
        if (shippingForm) {
            shippingForm.addEventListener('submit', (e) => this.handleShippingSubmit(e));
        }
        
        // Back to shipping button
        if (backToShippingBtn) {
            backToShippingBtn.addEventListener('click', () => this.goToShippingStep());
        }
        
        // Continue to review button
        if (continueToReviewBtn) {
            continueToReviewBtn.addEventListener('click', () => this.continueToReview());
        }
        
        // Back to delivery button
        if (backToDeliveryBtn) {
            backToDeliveryBtn.addEventListener('click', () => this.goToDeliveryStep());
        }
        
        // Place order button
        if (placeOrderBtn) {
            placeOrderBtn.addEventListener('click', () => this.placeOrder());
        }
    }
};

// Initialize cart module when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    cartModule.init();
});

// Export for use in other modules
window.cartModule = cartModule; 