/**
 * Jewelry Rental Website - Authentication Module
 * Handles user registration, login, and session management
 */

// DOM Elements
const authButtons = document.getElementById('auth-buttons');
const userProfile = document.getElementById('user-profile');
const usernameDisplay = document.getElementById('username-display');
const logoutBtn = document.getElementById('logout-btn');
const loginForm = document.getElementById('login-form');
const registerForm = document.getElementById('register-form');
const loginError = document.getElementById('login-error');
const registerError = document.getElementById('register-error');

// Local Storage Keys
const USER_KEY = 'borrowed_user';
const USERS_KEY = 'borrowed_users';

/**
 * User class to manage user data and authentication
 */
class UserAuth {
    constructor() {
        this.currentUser = null;
        this.users = [];
        this.init();
    }

    /**
     * Initialize the authentication module
     */
    init() {
        // Load users from local storage
        this.loadUsers();
        
        // Check if user is logged in
        this.checkSession();
        
        // Set up event listeners
        this.setupEventListeners();
        
        // Update UI based on auth state
        this.updateUI();
    }

    /**
     * Load users from local storage
     */
    loadUsers() {
        const storedUsers = localStorage.getItem(USERS_KEY);
        this.users = storedUsers ? JSON.parse(storedUsers) : [];
    }

    /**
     * Save users to local storage
     */
    saveUsers() {
        localStorage.setItem(USERS_KEY, JSON.stringify(this.users));
    }

    /**
     * Check if a user session exists
     */
    checkSession() {
        const storedUser = localStorage.getItem(USER_KEY);
        if (storedUser) {
            this.currentUser = JSON.parse(storedUser);
        }
    }

    /**
     * Set up event listeners for auth-related elements
     */
    setupEventListeners() {
        // Logout button
        if (logoutBtn) {
            logoutBtn.addEventListener('click', (e) => {
                e.preventDefault();
                this.logout();
            });
        }

        // Login form
        if (loginForm) {
            loginForm.addEventListener('submit', (e) => {
                e.preventDefault();
                const email = document.getElementById('login-email').value;
                const password = document.getElementById('login-password').value;
                this.login(email, password);
            });
        }

        // Register form
        if (registerForm) {
            registerForm.addEventListener('submit', (e) => {
                e.preventDefault();
                const name = document.getElementById('register-name').value;
                const email = document.getElementById('register-email').value;
                const password = document.getElementById('register-password').value;
                const confirmPassword = document.getElementById('register-confirm-password').value;
                this.register(name, email, password, confirmPassword);
            });
        }
    }

    /**
     * Update UI based on authentication state
     */
    updateUI() {
        if (this.currentUser) {
            // User is logged in
            if (authButtons) authButtons.classList.add('d-none');
            if (userProfile) {
                userProfile.classList.remove('d-none');
                usernameDisplay.textContent = this.currentUser.name;
            }
            
            // Update cart badge if cart module is loaded
            if (window.cartModule && typeof window.cartModule.updateCartBadge === 'function') {
                window.cartModule.updateCartBadge();
            }
            
            // Show orders if on orders page
            if (window.location.pathname.includes('orders.html') && window.ordersModule) {
                window.ordersModule.showOrders();
            }
        } else {
            // User is not logged in
            if (authButtons) authButtons.classList.remove('d-none');
            if (userProfile) userProfile.classList.add('d-none');
            
            // Show login required message if on orders page
            if (window.location.pathname.includes('orders.html')) {
                const loginRequiredMessage = document.getElementById('login-required-message');
                const ordersContainer = document.getElementById('orders-container');
                if (loginRequiredMessage) loginRequiredMessage.classList.remove('d-none');
                if (ordersContainer) ordersContainer.classList.add('d-none');
            }
        }
    }

    /**
     * Register a new user
     * @param {string} name - User's full name
     * @param {string} email - User's email address
     * @param {string} password - User's password
     * @param {string} confirmPassword - Password confirmation
     * @returns {boolean} Success status
     */
    register(name, email, password, confirmPassword) {
        // Validate inputs
        if (!name || !email || !password || !confirmPassword) {
            this.showError(registerError, 'All fields are required');
            return false;
        }

        // Validate email format
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
            this.showError(registerError, 'Please enter a valid email address');
            return false;
        }

        // Validate password length
        if (password.length < 6) {
            this.showError(registerError, 'Password must be at least 6 characters long');
            return false;
        }

        // Check if passwords match
        if (password !== confirmPassword) {
            this.showError(registerError, 'Passwords do not match');
            return false;
        }

        // Check if email is already registered
        if (this.users.some(user => user.email === email)) {
            this.showError(registerError, 'Email is already registered');
            return false;
        }

        // Create new user
        const newUser = {
            id: Date.now().toString(),
            name,
            email,
            password, // In a real app, this should be hashed
            orders: []
        };

        // Add user to users array
        this.users.push(newUser);
        this.saveUsers();

        // Log in the new user
        this.currentUser = {
            id: newUser.id,
            name: newUser.name,
            email: newUser.email
        };
        localStorage.setItem(USER_KEY, JSON.stringify(this.currentUser));

        // Update UI
        this.updateUI();

        // Close modal
        const registerModal = bootstrap.Modal.getInstance(document.getElementById('registerModal'));
        if (registerModal) {
            registerModal.hide();
        }

        return true;
    }

    /**
     * Log in a user
     * @param {string} email - User's email address
     * @param {string} password - User's password
     * @returns {boolean} Success status
     */
    login(email, password) {
        // Validate inputs
        if (!email || !password) {
            this.showError(loginError, 'Email and password are required');
            return false;
        }

        // Find user by email
        const user = this.users.find(user => user.email === email);
        if (!user) {
            this.showError(loginError, 'Email not found');
            return false;
        }

        // Check password
        if (user.password !== password) {
            this.showError(loginError, 'Incorrect password');
            return false;
        }

        // Set current user
        this.currentUser = {
            id: user.id,
            name: user.name,
            email: user.email
        };
        localStorage.setItem(USER_KEY, JSON.stringify(this.currentUser));

        // Update UI
        this.updateUI();

        // Close modal
        const loginModal = bootstrap.Modal.getInstance(document.getElementById('loginModal'));
        if (loginModal) {
            loginModal.hide();
        }

        return true;
    }

    /**
     * Log out the current user
     */
    logout() {
        this.currentUser = null;
        localStorage.removeItem(USER_KEY);
        this.updateUI();
        
        // Redirect to home page if on orders page
        if (window.location.pathname.includes('orders.html')) {
            window.location.href = 'index.html';
        }
    }

    /**
     * Show error message
     * @param {HTMLElement} element - Error message element
     * @param {string} message - Error message text
     */
    showError(element, message) {
        if (element) {
            element.textContent = message;
            element.classList.remove('d-none');
            
            // Hide error after 3 seconds
            setTimeout(() => {
                element.classList.add('d-none');
            }, 3000);
        }
    }

    /**
     * Get current user
     * @returns {Object|null} Current user object or null if not logged in
     */
    getCurrentUser() {
        return this.currentUser;
    }

    /**
     * Check if user is logged in
     * @returns {boolean} True if user is logged in, false otherwise
     */
    isLoggedIn() {
        return this.currentUser !== null;
    }

    /**
     * Get user by ID
     * @param {string} userId - User ID
     * @returns {Object|null} User object or null if not found
     */
    getUserById(userId) {
        return this.users.find(user => user.id === userId) || null;
    }

    /**
     * Add order to user's order history
     * @param {Object} order - Order object
     * @returns {boolean} Success status
     */
    addOrder(order) {
        if (!this.currentUser) return false;
        
        const userIndex = this.users.findIndex(user => user.id === this.currentUser.id);
        if (userIndex === -1) return false;
        
        if (!this.users[userIndex].orders) {
            this.users[userIndex].orders = [];
        }
        
        this.users[userIndex].orders.push(order);
        this.saveUsers();
        
        return true;
    }

    /**
     * Get user's orders
     * @returns {Array} Array of user's orders or empty array if not logged in
     */
    getUserOrders() {
        if (!this.currentUser) return [];
        
        const user = this.getUserById(this.currentUser.id);
        return user && user.orders ? user.orders : [];
    }
}

// Initialize authentication module
const userAuth = new UserAuth();

// Export for use in other modules
window.userAuth = userAuth; 