/**
 * Fix for product option price calculation with "None" radio selection
 *
 * Problem: When selecting a negative price option then selecting "None" (empty value),
 * the price doesn't reset to zero because the "None" radio doesn't properly trigger
 * the price recalculation in Hyvä's initOptions() function.
 *
 * Solution: Listen for radio changes and when "None" is selected (empty value),
 * manually dispatch the update-custom-option-active event for the option group
 * to ensure prices are properly reset.
 */
(function() {
    'use strict';

    /**
     * Initialize the fix after DOM is loaded
     */
    function init() {
        // Wait for Hyvä's initOptions to be available
        if (typeof window.initOptions !== 'function') {
            // Retry after a short delay if initOptions not yet loaded
            setTimeout(init, 100);
            return;
        }

        // Listen for changes on all radio inputs for custom options
        document.addEventListener('change', function(event) {
            const target = event.target;

            // Only handle radio inputs for custom options
            if (target.type !== 'radio' || !target.classList.contains('product-custom-option')) {
                return;
            }

            // Check if this is a "None" selection (empty value)
            if (target.checked && target.value === '') {
                handleNoneSelection(target);
            }
        }, true); // Use capture to ensure we catch the event early
    }

    /**
     * Handle "None" radio selection to properly reset prices
     *
     * @param {HTMLInputElement} target - The "None" radio input that was selected
     */
    function handleNoneSelection(target) {
        // Get all radio buttons in the same option group
        const radioGroup = document.querySelectorAll('input[name="' + target.name + '"]');

        radioGroup.forEach(function(radio) {
            // Skip the "None" radio itself
            if (radio === target) {
                return;
            }

            // Dispatch update-custom-option-active event for each sibling with active: false
            if (radio.dataset && radio.dataset.optionId) {
                const event = new CustomEvent('update-custom-option-active', {
                    bubbles: true,
                    detail: {
                        customOptionId: radio.dataset.optionId,
                        active: false
                    }
                });
                radio.dispatchEvent(event);
            }
        });

        // If the "None" radio has a data-option-id, activate it with zero price
        // This ensures the price calculator properly resets to zero
        if (target.dataset && target.dataset.optionId) {
            const activateEvent = new CustomEvent('update-custom-option-active', {
                bubbles: true,
                detail: {
                    customOptionId: target.dataset.optionId,
                    active: true,
                    price: 0
                }
            });
            target.dispatchEvent(activateEvent);
        }

        // Also dispatch event for form to recalculate total price
        const form = target.closest('form');
        if (form) {
            const updateEvent = new CustomEvent('update-product-price', {
                bubbles: true
            });
            form.dispatchEvent(updateEvent);
        }
    }

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
