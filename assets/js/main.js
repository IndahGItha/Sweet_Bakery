// Sweet Bakery - Main JavaScript
// Freshly Baked Every Day

document.addEventListener('DOMContentLoaded', function() {
    
    // Quantity Control
    initQuantityControl();
    
    // Alert Auto Hide
    initAlertAutoHide();
    
    // Image Preview
    initImagePreview();
    
    // Modal
    initModal();
});

// Quantity Control for Cart
function initQuantityControl() {
    const quantityControls = document.querySelectorAll('.quantity-control');
    
    quantityControls.forEach(control => {
        const minusBtn = control.querySelector('.qty-minus');
        const plusBtn = control.querySelector('.qty-plus');
        const qtyDisplay = control.querySelector('.qty-value');
        const qtyInput = control.querySelector('input[type="hidden"]');
        
        if (minusBtn && plusBtn && qtyDisplay) {
            minusBtn.addEventListener('click', function() {
                let currentVal = parseInt(qtyDisplay.textContent);
                if (currentVal > 1) {
                    currentVal--;
                    qtyDisplay.textContent = currentVal;
                    if (qtyInput) qtyInput.value = currentVal;
                    updateCartItem(this.closest('tr'), currentVal);
                }
            });
            
            plusBtn.addEventListener('click', function() {
                let currentVal = parseInt(qtyDisplay.textContent);
                const maxStock = parseInt(this.dataset.max) || 99;
                if (currentVal < maxStock) {
                    currentVal++;
                    qtyDisplay.textContent = currentVal;
                    if (qtyInput) qtyInput.value = currentVal;
                    updateCartItem(this.closest('tr'), currentVal);
                }
            });
        }
    });
}

function updateCartItem(row, quantity) {
    // This function can be extended to update cart via AJAX
    const priceElement = row.querySelector('.item-price');
    const subtotalElement = row.querySelector('.item-subtotal');
    
    if (priceElement && subtotalElement) {
        const price = parseInt(priceElement.dataset.price);
        const subtotal = price * quantity;
        subtotalElement.textContent = formatRupiah(subtotal);
        
        // Update total
        updateCartTotal();
    }
}

function updateCartTotal() {
    const subtotalElements = document.querySelectorAll('.item-subtotal');
    let total = 0;
    
    subtotalElements.forEach(el => {
        const value = el.textContent.replace(/[^0-9]/g, '');
        total += parseInt(value);
    });
    
    const totalElement = document.querySelector('.cart-total-amount');
    if (totalElement) {
        totalElement.textContent = formatRupiah(total);
    }
}

function formatRupiah(angka) {
    return 'Rp ' + angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
}

// Alert Auto Hide
function initAlertAutoHide() {
    const alerts = document.querySelectorAll('.alert');
    
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.opacity = '0';
            alert.style.transition = 'opacity 0.5s ease';
            setTimeout(() => {
                alert.remove();
            }, 500);
        }, 5000);
    });
}

// Image Preview

function initImagePreview() {
    const fileInputs = document.querySelectorAll('input[type="file"][data-preview]');
    
    fileInputs.forEach(input => {
        input.addEventListener('change', function() {
            const previewId = this.dataset.preview;
            const preview = document.getElementById(previewId);
            
            if (preview && this.files && this.files[0]) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                };
                
                reader.readAsDataURL(this.files[0]);
            }
        });
    });
}

// Modal
function initModal() {
    const modalTriggers = document.querySelectorAll('[data-modal]');
    const modalCloses = document.querySelectorAll('.modal-close, [data-close-modal]');
    
    modalTriggers.forEach(trigger => {
        trigger.addEventListener('click', function() {
            const modalId = this.dataset.modal;
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.classList.add('active');
            }
        });
    });
    
    modalCloses.forEach(close => {
        close.addEventListener('click', function() {
            const modal = this.closest('.modal');
            if (modal) {
                modal.classList.remove('active');
            }
        });
    });
    
    // Close modal on outside click
    document.querySelectorAll('.modal').forEach(modal => {
        modal.addEventListener('click', function(e) {
            if (e.target === this) {
                this.classList.remove('active');
            }
        });
    });
}

// Confirm Delete
function confirmDelete(message) {
    return confirm(message || 'Apakah Anda yakin ingin menghapus data ini?');
}

// Validate Form
function validateForm(formId) {
    const form = document.getElementById(formId);
    if (!form) return true;
    
    const requiredFields = form.querySelectorAll('[required]');
    let isValid = true;
    
    requiredFields.forEach(field => {
        if (!field.value.trim()) {
            isValid = false;
            field.classList.add('error');
        } else {
            field.classList.remove('error');
        }
    });
    
    return isValid;
}

// Toggle Password Visibility
function togglePassword(inputId) {
    const input = document.getElementById(inputId);
    const icon = document.querySelector(`[data-toggle="${inputId}"]`);
    
    if (input) {
        if (input.type === 'password') {
            input.type = 'text';
            if (icon) icon.textContent = '🙈';
        } else {
            input.type = 'password';
            if (icon) icon.textContent = '👁️';
        }
    }
}

// Search Functionality
function initSearch() {
    const searchInput = document.getElementById('search-input');
    const searchResults = document.getElementById('search-results');
    
    if (searchInput && searchResults) {
        let debounceTimer;
        
        searchInput.addEventListener('input', function() {
            clearTimeout(debounceTimer);
            const query = this.value.trim();
            
            if (query.length < 2) {
                searchResults.innerHTML = '';
                return;
            }
            
            debounceTimer = setTimeout(() => {
                // AJAX search can be implemented here
                console.log('Searching for:', query);
            }, 300);
        });
    }
}

// Smooth Scroll
function smoothScroll(target) {
    const element = document.querySelector(target);
    if (element) {
        element.scrollIntoView({
            behavior: 'smooth',
            block: 'start'
        });
    }
}

// Print Function
function printPage() {
    window.print();
}

// Export to CSV
function exportToCSV(filename, data) {
    const csvContent = "data:text/csv;charset=utf-8," + data;
    const encodedUri = encodeURI(csvContent);
    const link = document.createElement("a");
    link.setAttribute("href", encodedUri);
    link.setAttribute("download", filename);
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}
