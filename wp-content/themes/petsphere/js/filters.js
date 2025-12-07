document.addEventListener('DOMContentLoaded', function() {
    const filterButton = document.getElementById('petsphere-filter-submit');
    if (!filterButton) return;

    // Show the button since JS is active
    filterButton.style.display = 'block';

    // Selectors for filter links
    // WooCommerce Layered Nav, Rating, Status, Price (Price is tricky, let's stick to links first)
    const filterLinks = document.querySelectorAll('.widget_layered_nav ul li a, .widget_rating_filter ul li a, .widget_status ul li a');
    
    // State to track changes
    // Map of attribute -> Set of values to ADD or REMOVE
    // Actually, simpler: maintain a copy of current params and modify it.
    let currentParams = new URLSearchParams(window.location.search);
    
    // We need to know what the original state of each link implies.
    // But since we can click multiple things, we need to calculate the "diff" of each click relative to the PAGE LOAD state.
    
    // Store pending changes: { 'filter_color': { 'red': 'add', 'blue': 'remove' } }
    let pendingChanges = {};

    filterLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            
            const li = this.closest('li');
            li.classList.toggle('chosen'); // Visual toggle
            
            // Determine if we are selecting or deselecting based on the NEW visual state
            const isSelected = li.classList.contains('chosen');
            
            // Analyze the link's URL to find what parameter it manipulates
            const linkUrl = new URL(this.href);
            const linkParams = new URLSearchParams(linkUrl.search);
            const pageParams = new URLSearchParams(window.location.search);
            
            // Find the difference
            // We look for filter_ keys
            let changedKey = null;
            let changedValue = null;
            let action = null; // 'add' or 'remove'

            // Heuristic:
            // If the link ADDS a filter, it will have a param that pageParams doesn't have (or has different value).
            // If the link REMOVES a filter, pageParams has it and linkParams doesn't.
            
            // Iterate over all keys in both
            const allKeys = new Set([...linkParams.keys(), ...pageParams.keys()]);
            
            for (const key of allKeys) {
                if (!key.startsWith('filter_') && !key.startsWith('query_type_') && key !== 'min_price' && key !== 'max_price' && key !== 'rating_filter') continue;
                
                const linkVal = linkParams.get(key);
                const pageVal = pageParams.get(key);
                
                if (linkVal !== pageVal) {
                    // Found a difference
                    changedKey = key;
                    
                    // If we are visually SELECTING, we want to ADD this value.
                    // But wait, if I select "Red", the link is `?filter_color=red`.
                    // If I deselect "Red" (was chosen), the link is `?`.
                    
                    // We can't easily extract just "Red" from "Red,Blue" string diffing without logic.
                    // But we know the INTENT from `isSelected`.
                    
                    // If `isSelected` is true:
                    // We need to find what value this link represents.
                    // If the link adds it, it's in linkVal but not pageVal (or linkVal is longer).
                    // If the link removes it (because it was chosen), then the link is the REMOVAL link.
                    // But we just toggled it back to chosen? No.
                    
                    // Let's simplify.
                    // We don't need to diff. We need to know the "Identity" of the filter item.
                    // Can we get it from the DOM?
                    // .wc-layered-nav-term usually has no data attributes.
                    
                    // Let's go back to Diffing.
                    // Case 1: Item was NOT chosen. Click -> Chosen.
                    // Link href contains the new value.
                    // Page: `?`
                    // Link: `?filter_color=red`
                    // Diff: `filter_color` gained `red`.
                    // Value = `red`.
                    
                    // Case 2: Item WAS chosen. Click -> Unchosen.
                    // Link href is the removal URL.
                    // Page: `?filter_color=red`
                    // Link: `?`
                    // Diff: `filter_color` lost `red`.
                    // Value = `red`.
                    
                    // So in both cases, the "symmetric difference" between Page and Link params for that key reveals the Value.
                    
                    const val1 = linkVal ? linkVal.split(',') : [];
                    const val2 = pageVal ? pageVal.split(',') : [];
                    
                    // Find the value that is in one but not the other
                    const diff = val1.filter(x => !val2.includes(x)).concat(val2.filter(x => !val1.includes(x)));
                    
                    if (diff.length > 0) {
                        changedValue = diff[0]; // Assume one value per link click
                    }
                    break;
                }
            }
            
            if (changedKey && changedValue) {
                if (!pendingChanges[changedKey]) {
                    pendingChanges[changedKey] = {};
                }
                
                // If visually selected, we want to ensure it's in the final set.
                // If visually unselected, we want to ensure it's NOT.
                pendingChanges[changedKey][changedValue] = isSelected ? 'add' : 'remove';
            }
        });
    });

    filterButton.addEventListener('click', function() {
        // Reconstruct URL
        let finalParams = new URLSearchParams(window.location.search);
        
        // Apply pending changes
        for (const [key, changes] of Object.entries(pendingChanges)) {
            let currentValues = finalParams.get(key) ? finalParams.get(key).split(',') : [];
            
            for (const [value, action] of Object.entries(changes)) {
                if (action === 'add') {
                    if (!currentValues.includes(value)) {
                        currentValues.push(value);
                    }
                } else if (action === 'remove') {
                    currentValues = currentValues.filter(v => v !== value);
                }
            }
            
            if (currentValues.length > 0) {
                finalParams.set(key, currentValues.join(','));
            } else {
                finalParams.delete(key);
            }
        }

        // Handle Price Filter
        // WooCommerce price filter widget uses inputs with names 'min_price' and 'max_price'
        // These are usually hidden or text inputs updated by the slider.
        const minPriceInput = document.querySelector('.price_slider_amount #min_price');
        const maxPriceInput = document.querySelector('.price_slider_amount #max_price');

        if (minPriceInput && maxPriceInput) {
            const minPrice = minPriceInput.value;
            const maxPrice = maxPriceInput.value;

            if (minPrice) {
                finalParams.set('min_price', minPrice);
            } else {
                finalParams.delete('min_price');
            }

            if (maxPrice) {
                finalParams.set('max_price', maxPrice);
            } else {
                finalParams.delete('max_price');
            }
        }
        
        // Handle query_type: Enforce OR logic for all attribute filters
        // This ensures that selecting multiple values (e.g. Nike AND Adidas) shows products with EITHER brand.
        const keys = Array.from(finalParams.keys());
        for (const key of keys) {
            if (key.startsWith('filter_')) {
                const attributeName = key.replace('filter_', '');
                // Set query_type_{attribute} = 'or'
                finalParams.set(`query_type_${attributeName}`, 'or');
            }
        }
        
        // Cleanup orphaned query_type params (optional, but cleaner)
        for (const key of keys) {
            if (key.startsWith('query_type_')) {
                const attributeName = key.replace('query_type_', '');
                if (!finalParams.has(`filter_${attributeName}`)) {
                    finalParams.delete(key);
                }
            }
        }
        
        window.location.search = finalParams.toString();
    });
});
