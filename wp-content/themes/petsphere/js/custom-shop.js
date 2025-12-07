jQuery(document).ready(function($) {
    // 1. Handle "Add to Cart" -> Switch to +/- Control
    $(document.body).on('added_to_cart', function(event, fragments, cart_hash, $button) {
        $button.hide(); // Hide the "Add to Cart" button
        
        // Remove "View Cart" link if it appears (WooCommerce appends it after the button)
        // We use a timeout to ensure we catch it if it's added slightly later
        setTimeout(function() {
            $button.siblings('a.added_to_cart').remove();
        }, 10);
        
        // Find the control. It might be next sibling, or separated by the inserted link.
        var $control = $button.siblings('.petsphere-qty-control');
        if ($control.length) {
            $control.css('display', 'flex'); // Ensure it uses flex display
            $control.find('.qty-val').text('1'); // Reset to 1 on first add
        }

        showToast('Zboží bylo přidáno do košíku');
    });

    // 2. Handle +/- Buttons
    $(document).on('click', '.qty-btn', function(e) {
        e.preventDefault();
        var $btn = $(this);
        var $control = $btn.closest('.petsphere-qty-control');
        // Use siblings to find the button, as "View Cart" link might have been inserted in between
        var $addBtn = $control.siblings('.petsphere-add-btn');
        var productId = $control.data('product_id');
        var isPlus = $btn.hasClass('plus');
        var change = isPlus ? 1 : -1;

        // Disable buttons during request
        $control.find('.qty-btn').prop('disabled', true);

        $.ajax({
            url: petsphere_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'petsphere_update_qty',
                product_id: productId,
                change: change
            },
            success: function(response) {
                if (response.success) {
                    var newQty = response.data.new_qty;
                    
                    if (newQty === 0) {
                        // Item removed
                        $control.hide();
                        // Show the add button and remove 'added' class so it looks fresh
                        $addBtn.removeClass('added').show();
                        showToast('Zboží odebráno z košíku');
                    } else {
                        // Quantity updated
                        $control.find('.qty-val').text(newQty);
                        showToast('Množství upraveno: ' + newQty + ' ks');
                    }
                    
                    // Update Cart Badge Directly
                    if (response.data.cart_count !== undefined) {
                        $('.cart-count-badge').text(response.data.cart_count);
                    }
                    
                    // Trigger fragment refresh to update cart widget (optional backup)
                    $(document.body).trigger('wc_fragment_refresh');
                } else {
                    showToast('Chyba při aktualizaci košíku');
                }
            },
            complete: function() {
                $control.find('.qty-btn').prop('disabled', false);
            }
        });
    });


    // Toast Function
    function showToast(message) {
        // Create toast if it doesn't exist
        if ($('#petsphere-toast').length === 0) {
            $('body').append('<div id="petsphere-toast">' + message + '</div>');
        } else {
            $('#petsphere-toast').text(message);
        }

        var $toast = $('#petsphere-toast');
        $toast.addClass('show');

        // Hide after 3 seconds
        setTimeout(function() {
            $toast.removeClass('show');
        }, 3000);
    }

    // Optional: Quantity Buttons Logic (Frontend Only - Visual Prototype)
    // This is complex to hook into standard Woo AJAX without modifying the loop template.
    // For now, we focus on the notification and cleaning up the UI.
});
