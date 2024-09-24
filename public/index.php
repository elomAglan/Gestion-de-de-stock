<?php
// Connexion à la base de données
include 'db_connect.php';

// Requête pour récupérer les ventes par mois
$query = "SELECT MONTH(date_vente) as mois, SUM(prix_total) as total FROM ventes GROUP BY mois";
$result = $conn->query($query);

$ventes = array_fill(0, 12, 0); // Initialiser un tableau de 12 mois avec 0
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $ventes[$row['mois'] - 1] = $row['total']; // Associer le montant aux mois (index 0 pour janvier)
    }
}

// Convertir les données en JSON pour JavaScript
$ventes_json = json_encode($ventes);
?>
 
 
 <!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion de Stock - Dashboard</title>
    <link href="css/styles.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
        <!-- Inclure Chart.js -->
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-gray-100">

    <!-- Navbar -->
    <div class="h-screen w-64 bg-gray-800 text-white fixed top-0 left-0">
        <div class="p-6">
            <h1 class="text-3xl font-semibold">Gestion Stock</h1>
        </div>
        <nav class="mt-10">
            <ul>
                <li class="mb-4">
                    <a href="index.php" class="block py-2 px-4 hover:bg-gray-700 rounded flex items-center">
                        <!-- Icône Tableau de bord -->
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 3v6.75H3V3h6.75zm7.5 0v6.75H14.25V3h3zm-7.5 9.75v6.75H3v-6.75h6.75zm7.5 0v6.75H14.25v-6.75h3z" />
                        </svg>
                        Tableau de bord
                    </a>
                </li>
                <li class="mb-4">
                    <a href="produits.php" class="block py-2 px-4 hover:bg-gray-700 rounded flex items-center">
                        <!-- Icône Produits -->
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h18v6H3V3zm0 8h18v10H3V11z" />
                        </svg>
                        Produits
                    </a>
                </li>
              
                <li class="mb-4">
                    <a href="fournisseurs.php" class="block py-2 px-4 hover:bg-gray-700 rounded flex items-center">
                        <!-- Icône Fournisseurs -->
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3h14a2 2 0 012 2v14a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2zm3 6h8M7 13h10m-7 4h4" />
                        </svg>
                        Fournisseurs
                    </a>
                </li>
                <li class="mb-4">
                    <a href="mouvements.php" class="block py-2 px-4 hover:bg-gray-700 rounded flex items-center">
                        <!-- Icône Mouvements de Stock -->
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 13l-6 6m0 0l-6-6m6 6V3" />
                        </svg>
                        Mouvements de Stock
                    </a>
                </li>
                <li class="mb-4">
                    <a href="ventes.php" class="block py-2 px-4 hover:bg-gray-700 rounded flex items-center">
                        <!-- Icône Ventes -->
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Ventes
                    </a>
                </li>
                <li class="mb-4">
                    <a href="fiche_de_stock.php" class="block py-2 px-4 hover:bg-gray-700 rounded flex items-center">
                        <!-- Icône Fiche de stock -->
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2M12 12v7m0 0H9m3 0h3m0 0V9m0 0l2 2m0 0l-2-2m2 2L9 5" />
                        </svg>
                        Fiche de stock
                    </a>
                </li>
                <li class="mb-4">
                    <a href="stock.php" class="block py-2 px-4 hover:bg-gray-700 rounded flex items-center">
                        <!-- Icône Fiche de stock -->
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2M12 12v7m0 0H9m3 0h3m0 0V9m0 0l2 2m0 0l-2-2m2 2L9 5" />
                        </svg>
                        Stock Complet
                    </a>
                </li>
            </ul>
        </nav>
    </div>

    <!-- Contenu principal -->
    <div class="ml-64 p-8">
        <h1 class="text-3xl font-bold text-center text-gray-800 mb-8">Tableau de Bord</h1>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <a href="produits.php" class="block p-6 bg-white rounded-lg shadow hover:bg-gray-100 transition duration-300">
                <h2 class="text-xl font-semibold text-gray-800">Produits</h2>
                <p class="text-gray-600">Gérez les produits en stock</p>
            </a>
           
            <a href="fournisseurs.php" class="block p-6 bg-white rounded-lg shadow hover:bg-gray-100 transition duration-300">
                <h2 class="text-xl font-semibold text-gray-800">Fournisseurs</h2>
                <p class="text-gray-600">Gérez les fournisseurs</p>
            </a>
            <a href="mouvements.php" class="block p-6 bg-white rounded-lg shadow hover:bg-gray-100 transition duration-300">
                <h2 class="text-xl font-semibold text-gray-800">Mouvements de Stock</h2>
                <p class="text-gray-600">Suivez les mouvements de stock</p>
            </a>
            <a href="ventes.php" class="block p-6 bg-white rounded-lg shadow hover:bg-gray-100 transition duration-300">
                <h2 class="text-xl font-semibold text-gray-800">Ventes de produits</h2>
                <p class="text-gray-600">Gérez les ventes de produit</p>
            </a>
            <a href="fiche_de_stock.php" class="block p-6 bg-white rounded-lg shadow hover:bg-gray-100 transition duration-300">
            <h2 class="text-xl font-semibold text-gray-800">Fiche de Stock</h2>
            <p class="text-gray-600">Consultez la fiche de stock</p>
            </a>
            </a>
            <a href="stock.php" class="block p-6 bg-white rounded-lg shadow hover:bg-gray-100 transition duration-300">
            <h2 class="text-xl font-semibold text-gray-800">Stock</h2>
            <p class="text-gray-600">Consultez votre stock</p>
            </a>
</div>
</div>

    <div class="ml-64 p-8">
        <h1 class="text-3xl font-bold text-center text-gray-800 mb-8">Diagrammes</h1>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Les cartes de navigation ici -->
            <!-- Code non modifié pour les cartes de navigation -->
        </div>
        
        <!-- Conteneur pour le diagramme -->
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Diagramme des Ventes</h2>
            <canvas id="salesChart" class="w-full h-64"></canvas>
        </div>
    </div>

    <!-- Script pour initialiser le diagramme -->
    <script>
    // Données pour le diagramme
    const ctx = document.getElementById('salesChart').getContext('2d');
    const salesData = <?php echo $ventes_json; ?>; // Insérer les données de ventes

    const salesChart = new Chart(ctx, {
        type: 'bar', // Type de diagramme : barres
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'], // Labels pour les mois
            datasets: [{
                label: 'Ventes',
                data: salesData, // Utiliser les données dynamiques
                backgroundColor: 'rgba(75, 192, 192, 0.2)', // Couleur de fond des barres
                borderColor: 'rgba(75, 192, 192, 1)', // Couleur des bordures des barres
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>




</body>
</html>
