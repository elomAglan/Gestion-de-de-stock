// public/js/script.js

document.addEventListener('DOMContentLoaded', function () {
    fetchProducts();

    function fetchProducts() {
        fetch('../src/php/produits.php')
            .then(response => response.json())
            .then(data => {
                const productList = document.getElementById('productList');
                productList.innerHTML = '';
                data.forEach(product => {
                    const li = document.createElement('li');
                    li.textContent = `${product.nom} - ${product.quantite} en stock - Prix: ${product.prix}`;
                    productList.appendChild(li);
                });
            })
            .catch(error => console.error('Erreur:', error));
    }
});
