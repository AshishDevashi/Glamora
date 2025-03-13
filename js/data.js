/**
 * Jewelry Rental Website - Data Module
 * Contains jewelry data and utility functions for data management
 */

// Sample jewelry data
const jewelryData = [
    {
        id: 1,
        name: "Diamond Tennis Bracelet",
        description: "Elegant tennis bracelet featuring 3.00 carats of round brilliant diamonds set in 14K white gold.",
        price: 89.99,
        image: "https://images.unsplash.com/photo-1611652022419-a9419f74343d?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80",
        category: "Bracelets",
        rentalPeriods: ["3 Days", "7 Days", "14 Days"]
    },
    {
        id: 2,
        name: "Pearl Necklace",
        description: "Classic 18-inch strand of AAA-grade white South Sea pearls with a 14K gold clasp.",
        price: 65.99,
        image: "https://images.unsplash.com/photo-1599643477877-530eb83abc8e?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80",
        category: "Necklaces",
        rentalPeriods: ["3 Days", "7 Days", "14 Days"]
    },
    {
        id: 3,
        name: "Sapphire Stud Earrings",
        description: "Stunning blue sapphire stud earrings totaling 2.00 carats set in 18K white gold with diamond halos.",
        price: 75.99,
        image: "https://images.unsplash.com/photo-1588444837495-c6cfeb53f32d?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80",
        category: "Earrings",
        rentalPeriods: ["3 Days", "7 Days", "14 Days"]
    },
    {
        id: 4,
        name: "Emerald Statement Ring",
        description: "Bold emerald statement ring featuring a 3.50 carat Colombian emerald surrounded by diamonds in platinum.",
        price: 99.99,
        image: "https://images.unsplash.com/photo-1605100804763-247f67b3557e?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80",
        category: "Rings",
        rentalPeriods: ["3 Days", "7 Days", "14 Days"]
    },
    {
        id: 5,
        name: "Gold Chain Necklace",
        description: "Heavy 24K gold Cuban link chain necklace, 22 inches in length.",
        price: 79.99,
        image: "https://images.unsplash.com/photo-1589128777073-263566ae5e4d?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80",
        category: "Necklaces",
        rentalPeriods: ["3 Days", "7 Days", "14 Days"]
    },
    {
        id: 6,
        name: "Ruby Pendant",
        description: "Exquisite ruby pendant featuring a 2.00 carat Burmese ruby surrounded by diamonds on an 18K gold chain.",
        price: 85.99,
        image: "https://images.unsplash.com/photo-1602173574767-37ac01994b2a?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80",
        category: "Necklaces",
        rentalPeriods: ["3 Days", "7 Days", "14 Days"]
    },
    {
        id: 7,
        name: "Diamond Hoop Earrings",
        description: "Sparkling diamond hoop earrings with 1.50 carats of diamonds set in 14K white gold.",
        price: 69.99,
        image: "https://images.unsplash.com/photo-1629224316810-9d8805b95e76?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80",
        category: "Earrings",
        rentalPeriods: ["3 Days", "7 Days", "14 Days"]
    },
    {
        id: 8,
        name: "Vintage Brooch",
        description: "Stunning vintage-inspired brooch featuring diamonds, sapphires, and rubies in a floral design.",
        price: 59.99,
        image: "https://images.unsplash.com/photo-1620656798932-902cbe3e3f1d?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80",
        category: "Brooches",
        rentalPeriods: ["3 Days", "7 Days", "14 Days"]
    },
    {
        id: 9,
        name: "Men's Luxury Watch",
        description: "Sophisticated men's luxury watch with automatic movement, sapphire crystal, and stainless steel bracelet.",
        price: 129.99,
        image: "https://images.unsplash.com/photo-1587836374828-4dbafa94cf0e?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80",
        category: "Watches",
        rentalPeriods: ["3 Days", "7 Days", "14 Days"]
    },
    {
        id: 10,
        name: "Diamond Tennis Necklace",
        description: "Breathtaking diamond tennis necklace featuring 10.00 carats of round brilliant diamonds in 18K white gold.",
        price: 149.99,
        image: "https://images.unsplash.com/photo-1599643478518-a784e5dc4c8f?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80",
        category: "Necklaces",
        rentalPeriods: ["3 Days", "7 Days", "14 Days"]
    },
    {
        id: 11,
        name: "Amethyst Cocktail Ring",
        description: "Bold amethyst cocktail ring featuring a 10.00 carat amethyst surrounded by diamonds in 14K rose gold.",
        price: 69.99,
        image: "https://images.unsplash.com/photo-1603561591411-07134e71a2a9?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80",
        category: "Rings",
        rentalPeriods: ["3 Days", "7 Days", "14 Days"]
    },
    {
        id: 12,
        name: "Pearl Drop Earrings",
        description: "Elegant pearl drop earrings featuring South Sea pearls suspended from diamond-set 18K white gold.",
        price: 79.99,
        image: "https://images.unsplash.com/photo-1611107683227-e9060eccd846?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80",
        category: "Earrings",
        rentalPeriods: ["3 Days", "7 Days", "14 Days"]
    }
];

// Rental period multipliers (for pricing)
const rentalPeriodMultipliers = {
    "3 Days": 1,
    "7 Days": 2,
    "14 Days": 3.5
};

// Delivery options
const deliveryOptions = {
    standard: {
        name: "Standard Delivery",
        description: "5-7 business days",
        price: 5.99
    },
    express: {
        name: "Express Delivery",
        description: "2-3 business days",
        price: 12.99
    }
};

/**
 * Get all jewelry items
 * @returns {Array} Array of jewelry items
 */
function getAllJewelry() {
    return jewelryData;
}

/**
 * Get jewelry item by ID
 * @param {number} id - The jewelry item ID
 * @returns {Object|null} The jewelry item or null if not found
 */
function getJewelryById(id) {
    return jewelryData.find(item => item.id === id) || null;
}

/**
 * Calculate rental price based on base price and rental period
 * @param {number} basePrice - The base price of the jewelry item
 * @param {string} rentalPeriod - The rental period (e.g., "3 Days")
 * @returns {number} The calculated rental price
 */
function calculateRentalPrice(basePrice, rentalPeriod) {
    const multiplier = rentalPeriodMultipliers[rentalPeriod] || 1;
    return basePrice * multiplier;
}

/**
 * Format price as currency string
 * @param {number} price - The price to format
 * @returns {string} Formatted price string (e.g., "$99.99")
 */
function formatPrice(price) {
    return `$${price.toFixed(2)}`;
}

/**
 * Generate a random order ID
 * @returns {string} Random order ID
 */
function generateOrderId() {
    return 'ORD-' + Math.random().toString(36).substring(2, 8).toUpperCase();
}

/**
 * Format date as a readable string
 * @param {Date} date - The date to format
 * @returns {string} Formatted date string
 */
function formatDate(date) {
    const options = { year: 'numeric', month: 'long', day: 'numeric' };
    return date.toLocaleDateString('en-US', options);
}

// Export functions and data for use in other modules
window.jewelryData = jewelryData;
window.rentalPeriodMultipliers = rentalPeriodMultipliers;
window.deliveryOptions = deliveryOptions;
window.getAllJewelry = getAllJewelry;
window.getJewelryById = getJewelryById;
window.calculateRentalPrice = calculateRentalPrice;
window.formatPrice = formatPrice;
window.generateOrderId = generateOrderId;
window.formatDate = formatDate; 