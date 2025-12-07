document.addEventListener('DOMContentLoaded', function() {
    const menuToggle = document.querySelector('.menu-toggle');
    const mainNavigation = document.querySelector('.main-navigation');
    const body = document.body;

    if (menuToggle && mainNavigation) {
        menuToggle.addEventListener('click', function() {
            const isExpanded = menuToggle.getAttribute('aria-expanded') === 'true';
            menuToggle.setAttribute('aria-expanded', !isExpanded);
            mainNavigation.classList.toggle('toggled');
            body.classList.toggle('menu-open');
        });

        // Zavření menu při kliknutí mimo (volitelné)
        document.addEventListener('click', function(event) {
            if (!mainNavigation.contains(event.target) && !menuToggle.contains(event.target) && mainNavigation.classList.contains('toggled')) {
                mainNavigation.classList.remove('toggled');
                menuToggle.setAttribute('aria-expanded', 'false');
                body.classList.remove('menu-open');
            }
        });
    }

    // Mobile Search Toggle
    // Note: The toggle is now a menu item with class 'mobile-search-trigger'
    const mobileSearchContainer = document.querySelector('.mobile-search-container');
    
    // Use event delegation because the menu item might be dynamic
    document.addEventListener('click', function(e) {
        if (e.target.closest('.mobile-search-trigger')) {
            e.preventDefault();
            if (mobileSearchContainer) {
                if (mobileSearchContainer.style.display === 'none' || mobileSearchContainer.style.display === '') {
                    mobileSearchContainer.style.display = 'block';
                    mobileSearchContainer.querySelector('input').focus();
                } else {
                    mobileSearchContainer.style.display = 'none';
                }
            }
        }
    });

    // Desktop Header Search Toggle
    const searchToggle = document.querySelector('.header-search-toggle');
    const searchDropdown = document.querySelector('.header-search-dropdown');

    if (searchToggle && searchDropdown) {
        searchToggle.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            searchDropdown.classList.toggle('active');
            if (searchDropdown.classList.contains('active')) {
                const input = searchDropdown.querySelector('input');
                if (input) input.focus();
            }
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!searchDropdown.contains(e.target) && !searchToggle.contains(e.target)) {
                searchDropdown.classList.remove('active');
            }
        });
    }
});