document.addEventListener('DOMContentLoaded', function() {
    const fadeElements = document.querySelectorAll('.fade-in');
    fadeElements.forEach(element => {
        element.style.opacity = '0';
    });
    setTimeout(() => {
        fadeElements.forEach(element => {
            element.style.transition = 'opacity 0.6s ease';
            element.style.opacity = '1';
        });
    }, 100);
    const mobileMenuToggle = document.querySelector('.mobile-menu-toggle');
    const nav = document.querySelector('.nav');
    
    if (mobileMenuToggle) {
        mobileMenuToggle.addEventListener('click', function() {
            nav.classList.toggle('active');
            this.classList.toggle('active');
        });
    }
    window.showToast = function(message, type = 'success') {
        const toast = document.createElement('div');
        toast.className = toast ${type};
        toast.textContent = message;
        document.body.appendChild(toast);
        
        setTimeout(() => {
            toast.classList.add('show');
        }, 100);
        
        setTimeout(() => {
            toast.classList.remove('show');
            setTimeout(() => {
                document.body.removeChild(toast);
            }, 300);
        }, 3000);
    };
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const requiredFields = this.querySelectorAll('[required]');
            let valid = true;
            
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    valid = false;
                    field.style.borderColor = '#e74c3c';
                } else {
                    field.style.borderColor = '#ddd';
                }
            });
            
            if (!valid) {
                e.preventDefault();
                showToast('Veuillez remplir tous les champs obligatoires', 'error');
            }
        });
    });
    const searchInput = document.querySelector('.search-box input');
    if (searchInput) {
        let timeout;
        
        searchInput.addEventListener('input', function() {
            clearTimeout(timeout);
            timeout = setTimeout(() => {
                const query = this.value.trim();
                if (query.length >= 2) {
                    fetchSearchSuggestions(query);
                }
            }, 300);
        });
    }
});
async function fetchSearchSuggestions(query) {
    try {
        const response = await fetch(php/search-suggestions.php?q=${encodeURIComponent(query)});
        const suggestions = await response.json();
        displaySearchSuggestions(suggestions);
    } catch (error) {
        console.error('Erreur lors de la récupération des suggestions:', error);
    }
}
function displaySearchSuggestions(suggestions) {
    // Implémentation de l'affichage des suggestions
    console.log('Suggestions:', suggestions);
}
const cart = {
    addItem: function(bookId, quantity = 1) {
        this.updateCart(bookId, quantity);
    },
    
    removeItem: function(bookId) {
        this.updateCart(bookId, 0);
    },
    
    updateCart: function(bookId, quantity) {
        fetch('php/process/cart-process.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                book_id: bookId,
                quantity: quantity
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast('Panier mis à jour', 'success');
                this.updateCartDisplay();
            } else {
                showToast(data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            showToast('Erreur lors de la mise à jour du panier', 'error');
        });
    },
    
    updateCartDisplay: function() {
        const cartCount = document.querySelector('.cart-count');
        if (cartCount) {
        }
    }
};