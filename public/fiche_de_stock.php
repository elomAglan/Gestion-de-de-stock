<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fiche de Stock</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>

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

<body class="bg-gray-100 ml-64">
    <div class="container mx-auto p-6">
        <h1 class="text-2xl font-bold mb-4">Fiche de Stock</h1>

        <input type="text" id="search" placeholder="Rechercher un produit..." class="mb-4 p-2 border border-gray-300 rounded">

        <table class="min-w-full bg-white border border-gray-300">
            <thead>
                <tr>
                    <th class="py-2 px-4 border-b">ID</th>
                    <th class="py-2 px-4 border-b">Nom du Produit</th>
                    <th class="py-2 px-4 border-b">Quantité</th>
                    <th class="py-2 px-4 border-b">Prix</th>
                    <th class="py-2 px-4 border-b">Prix Total</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Connexion à la base de données
                $conn = new mysqli("localhost", "root", "", "gestion_stock");
                if ($conn->connect_error) {
                    die("Erreur de connexion : " . $conn->connect_error);
                }

                $query = "SELECT p.id, p.nom, p.quantite, p.prix, (p.prix * p.quantite) AS prix_total 
                          FROM produits p";
                $result = $conn->query($query);

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>
                            <td class='py-2 px-4 border-b'>{$row['id']}</td>
                            <td class='py-2 px-4 border-b'>{$row['nom']}</td>
                            <td class='py-2 px-4 border-b'>{$row['quantite']}</td>
                            <td class='py-2 px-4 border-b'>{$row['prix']}</td>
                            <td class='py-2 px-4 border-b'>{$row['prix_total']}</td>
                        </tr>";
                    }
                } else {
                    echo "<tr><td colspan='5' class='text-center py-4'>Aucun produit trouvé.</td></tr>";
                }

                $conn->close();
                ?>
            </tbody>
        </table>

        <div class="flex justify-center mt-6">
    <form action="fiche_de_stock_pdf.php" method="post">
        <button type="submit" class="bg-blue-500 text-white py-2 px-4 rounded">Télécharger PDF</button>
    </form>
</div>
    </div>

    <script>
    const searchInput = document.getElementById('search');
    searchInput.addEventListener('keyup', function() {
        const filter = searchInput.value.toLowerCase();
        const rows = document.querySelectorAll('tbody tr');

        rows.forEach(row => {
            const cells = row.getElementsByTagName('td');
            let match = false;
            for (let cell of cells) {
                if (cell.innerText.toLowerCase().includes(filter)) {
                    match = true;
                    break;
                }
            }
            row.style.display = match ? '' : 'none';
        });
    });
    </script>

</body>
</html>
