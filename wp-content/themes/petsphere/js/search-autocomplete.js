jQuery(document).ready(function($) {
    // Target both desktop and mobile search fields
    var searchInput = $('.header-search .search-field, .mobile-search-container .search-field');
    
    // We need to handle results container for each form separately
    searchInput.each(function() {
        var input = $(this);
        var form = input.closest('.search-form');
        
        // Create results container if it doesn't exist
        if (form.find('.petsphere-search-results').length === 0) {
            form.append('<div class="petsphere-search-results"></div>');
        }
    });

    var searchTimeout;
    
    searchInput.on('input', function() {
        var input = $(this);
        var form = input.closest('.search-form');
        var resultsContainer = form.find('.petsphere-search-results');
        var term = input.val();
        
        clearTimeout(searchTimeout);
        
        if (term.length < 3) {
            resultsContainer.hide().empty();
            return;
        }
        
        searchTimeout = setTimeout(function() {
            $.ajax({
                url: petsphere_search.ajax_url,
                type: 'GET',
                data: {
                    action: 'petsphere_search_autocomplete',
                    term: term
                },
                success: function(response) {
                    if (response.success && response.data.length > 0) {
                        var html = '<ul>';
                        $.each(response.data, function(index, item) {
                            var imageHtml = item.image ? '<img src="' + item.image + '" alt="' + item.title + '">' : '';
                            html += '<li><a href="' + item.url + '">';
                            html += '<div class="search-item-image">' + imageHtml + '</div>';
                            html += '<div class="search-item-info">';
                            html += '<span class="search-item-title">' + item.title + '</span>';
                            html += '<span class="search-item-price">' + item.price + '</span>';
                            html += '</div>';
                            html += '</a></li>';
                        });
                        html += '</ul>';
                        resultsContainer.html(html).show();
                    } else {
                        resultsContainer.html('<div class="no-results">Žádné produkty nenalezeny.</div>').show();
                    }
                }
            });
        }, 300);
    });
    
    // Close on click outside
    $(document).on('click', function(e) {
        if (!$(e.target).closest('.header-search').length) {
            resultsContainer.hide();
        }
    });
});
