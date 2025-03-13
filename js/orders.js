/**
 * Jewelry Rental Website - Orders Module
 * Handles order history functionality
 */

// DOM Elements
const loginRequiredMessage = document.getElementById('login-required-message');
const noOrdersMessage = document.getElementById('no-orders-message');
const ordersContainer = document.getElementById('orders-container');
const ordersAccordion = document.getElementById('ordersAccordion');

// Order details modal elements
const modalOrderId = document.getElementById('modal-order-id');
const modalOrderDate = document.getElementById('modal-order-date');
const modalOrderStatus = document.getElementById('modal-order-status');
const modalDeliveryMethod = document.getElementById('modal-delivery-method');
const modalOrderItems = document.getElementById('modal-order-items');
const modalShippingAddress = document.getElementById('modal-shipping-address');
const modalSubtotal = document.getElementById('modal-subtotal');
const modalDeliveryCost = document.getElementById('modal-delivery-cost');
const modalTotal = document.getElementById('modal-total');

/**
 * Orders module to manage order history
 */
const ordersModule = {
    /**
     * Initialize the orders module
     */
    init() {
        // Check if user is logged in
        if (userAuth.isLoggedIn()) {
            this.showOrders();
        } else {
            this.showLoginRequired();
        }
        
        // Set up event listeners
        this.setupEventListeners();
    },

    /**
     * Show login required message
     */
    showLoginRequired() {
        if (loginRequiredMessage) loginRequiredMessage.classList.remove('d-none');
        if (noOrdersMessage) noOrdersMessage.classList.add('d-none');
        if (ordersContainer) ordersContainer.classList.add('d-none');
    },

    /**
     * Show orders
     */
    showOrders() {
        // Hide login required message
        if (loginRequiredMessage) loginRequiredMessage.classList.add('d-none');
        
        // Get user's orders
        const orders = userAuth.getUserOrders();
        
        // Check if user has orders
        if (orders.length === 0) {
            if (noOrdersMessage) noOrdersMessage.classList.remove('d-none');
            if (ordersContainer) ordersContainer.classList.add('d-none');
            return;
        }
        
        // Show orders container
        if (noOrdersMessage) noOrdersMessage.classList.add('d-none');
        if (ordersContainer) ordersContainer.classList.remove('d-none');
        
        // Clear orders accordion
        if (ordersAccordion) ordersAccordion.innerHTML = '';
        
        // Sort orders by date (newest first)
        const sortedOrders = [...orders].sort((a, b) => new Date(b.date) - new Date(a.date));
        
        // Display each order
        sortedOrders.forEach((order, index) => {
            const orderElement = this.createOrderElement(order, index);
            if (ordersAccordion) ordersAccordion.appendChild(orderElement);
        });
    },

    /**
     * Create order element
     * @param {Object} order - Order data
     * @param {number} index - Order index
     * @returns {HTMLElement} Order element
     */
    createOrderElement(order, index) {
        // Create accordion item
        const accordionItem = document.createElement('div');
        accordionItem.className = 'accordion-item';
        
        // Format date
        const orderDate = new Date(order.date);
        const formattedDate = formatDate(orderDate);
        
        // Create accordion header
        const headerId = `order-heading-${index}`;
        const contentId = `order-collapse-${index}`;
        
        const header = document.createElement('h2');
        header.className = 'accordion-header';
        header.id = headerId;
        
        header.innerHTML = `
            <button class="accordion-button ${index === 0 ? '' : 'collapsed'}" type="button" data-bs-toggle="collapse" data-bs-target="#${contentId}" aria-expanded="${index === 0 ? 'true' : 'false'}" aria-controls="${contentId}">
                <div class="d-flex justify-content-between align-items-center w-100">
                    <div>
                        <strong>Order #${order.id}</strong>
                    </div>
                    <div class="d-flex align-items-center">
                        <span class="me-3">${formattedDate}</span>
                        <span class="badge bg-success">${order.status}</span>
                    </div>
                </div>
            </button>
        `;
        
        // Create accordion content
        const content = document.createElement('div');
        content.id = contentId;
        content.className = `accordion-collapse collapse ${index === 0 ? 'show' : ''}`;
        content.setAttribute('aria-labelledby', headerId);
        content.setAttribute('data-bs-parent', '#ordersAccordion');
        
        // Create order summary
        const body = document.createElement('div');
        body.className = 'accordion-body';
        
        // Create order items summary
        const itemsSummary = document.createElement('div');
        itemsSummary.className = 'row mb-3';
        
        // Show up to 3 items with images
        const itemsToShow = order.items.slice(0, 3);
        const remainingItems = order.items.length - itemsToShow.length;
        
        itemsToShow.forEach(item => {
            const itemCol = document.createElement('div');
            itemCol.className = 'col-md-4 mb-2';
            
            itemCol.innerHTML = `
                <div class="d-flex align-items-center">
                    <img src="${item.image}" alt="${item.name}" class="order-item-img rounded me-2" onerror="this.src='https://via.placeholder.com/60?text=Jewelry'">
                    <div>
                        <h6 class="mb-0">${item.name}</h6>
                        <small class="text-muted">${item.rentalPeriod}</small>
                    </div>
                </div>
            `;
            
            itemsSummary.appendChild(itemCol);
        });
        
        // Add remaining items count if needed
        if (remainingItems > 0) {
            const remainingCol = document.createElement('div');
            remainingCol.className = 'col-md-4 mb-2 d-flex align-items-center';
            remainingCol.innerHTML = `<p class="mb-0">+${remainingItems} more item${remainingItems > 1 ? 's' : ''}</p>`;
            itemsSummary.appendChild(remainingCol);
        }
        
        // Create order details footer
        const footer = document.createElement('div');
        footer.className = 'd-flex justify-content-between align-items-center';
        
        footer.innerHTML = `
            <div>
                <p class="mb-0"><strong>Total:</strong> ${formatPrice(order.total)}</p>
                <p class="mb-0 text-muted">Delivery: ${order.delivery.name}</p>
            </div>
            <button class="btn btn-outline-primary view-details-btn" data-order-id="${order.id}">
                View Details
            </button>
        `;
        
        // Assemble accordion item
        body.appendChild(itemsSummary);
        body.appendChild(footer);
        content.appendChild(body);
        accordionItem.appendChild(header);
        accordionItem.appendChild(content);
        
        return accordionItem;
    },

    /**
     * Show order details in modal
     * @param {string} orderId - Order ID
     */
    showOrderDetails(orderId) {
        // Get user's orders
        const orders = userAuth.getUserOrders();
        
        // Find order by ID
        const order = orders.find(order => order.id === orderId);
        if (!order) return;
        
        // Format date
        const orderDate = new Date(order.date);
        const formattedDate = formatDate(orderDate);
        
        // Set modal content
        if (modalOrderId) modalOrderId.textContent = order.id;
        if (modalOrderDate) modalOrderDate.textContent = formattedDate;
        if (modalOrderStatus) {
            modalOrderStatus.textContent = order.status;
            modalOrderStatus.className = 'badge bg-success';
        }
        if (modalDeliveryMethod) modalDeliveryMethod.textContent = `${order.delivery.name} (${order.delivery.description})`;
        
        // Set order items
        if (modalOrderItems) {
            modalOrderItems.innerHTML = '';
            
            order.items.forEach(item => {
                const row = document.createElement('tr');
                
                row.innerHTML = `
                    <td>
                        <img src="${item.image}" alt="${item.name}" class="order-item-img rounded" onerror="this.src='https://via.placeholder.com/60?text=Jewelry'">
                    </td>
                    <td>${item.name}</td>
                    <td>${item.rentalPeriod}</td>
                    <td>${formatPrice(item.price)}</td>
                `;
                
                modalOrderItems.appendChild(row);
            });
        }
        
        // Set shipping address
        if (modalShippingAddress) {
            const { firstName, lastName, address, address2, state, zip, country } = order.shippingAddress;
            modalShippingAddress.innerHTML = `
                ${firstName} ${lastName}<br>
                ${address}<br>
                ${address2 ? address2 + '<br>' : ''}
                ${state}, ${zip}<br>
                ${country}
            `;
        }
        
        // Set order totals
        if (modalSubtotal) modalSubtotal.textContent = formatPrice(order.subtotal);
        if (modalDeliveryCost) modalDeliveryCost.textContent = formatPrice(order.delivery.price);
        if (modalTotal) modalTotal.textContent = formatPrice(order.total);
        
        // Show modal
        const orderDetailsModal = new bootstrap.Modal(document.getElementById('orderDetailsModal'));
        orderDetailsModal.show();
    },

    /**
     * Set up event listeners
     */
    setupEventListeners() {
        // View details buttons
        if (ordersAccordion) {
            ordersAccordion.addEventListener('click', (e) => {
                if (e.target.closest('.view-details-btn')) {
                    const button = e.target.closest('.view-details-btn');
                    const orderId = button.dataset.orderId;
                    this.showOrderDetails(orderId);
                }
            });
        }
    }
};

// Initialize orders module when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    ordersModule.init();
});

// Export for use in other modules
window.ordersModule = ordersModule; 