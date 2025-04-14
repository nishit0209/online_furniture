document.addEventListener('DOMContentLoaded', function() {
    // Update cart quantity
    document.querySelectorAll('.update-quantity').forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.dataset.productId;
            const quantity = document.querySelector(`#quantity-${productId}`).value;
            fetch('/pages/update_cart.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `product_id=${productId}&quantity=${quantity}`
            }).then(response => {
                window.location.reload();
            });
        });
    });
}); 