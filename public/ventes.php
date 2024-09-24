<?php
session_start();
require_once 'db_connect.php';

// Traitement de l'ajout de vente
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Traitement des ventes
    if (isset($_POST['action']) && $_POST['action'] === 'add_sale') {
        $produit_id = $_POST['produit_id'];
        $quantite = $_POST['quantite'];
        $date_vente = $_POST['date_vente'];

        // Récupérer le prix unitaire du produit et la quantité en stock
        $query_prix = "SELECT prix, quantite FROM produits WHERE id = ?";
        $stmt = $conn->prepare($query_prix);
        $stmt->bind_param("i", $produit_id);
        $stmt->execute();
        $stmt->bind_result($prix_unitaire, $stock);
        $stmt->fetch();
        $stmt->close();

        // Vérifier si la quantité vendue est disponible en stock
        if ($quantite > $stock) {
            echo "<script>alert('Quantité insuffisante en stock.');</script>";
        } else {
            $prix_total = $prix_unitaire * $quantite;

            // Insertion de la vente dans la base de données
            $query_vente = "INSERT INTO ventes (produit_id, quantite, prix_total, date_vente) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($query_vente);
            $stmt->bind_param("iids", $produit_id, $quantite, $prix_total, $date_vente);
            $stmt->execute();
            $stmt->close();

            // Mise à jour de la quantité en stock
            $nouvelle_quantite = $stock - $quantite;
            $query_update = "UPDATE produits SET quantite = ? WHERE id = ?";
            $stmt = $conn->prepare($query_update);
            $stmt->bind_param("ii", $nouvelle_quantite, $produit_id);
            $stmt->execute();
            $stmt->close();
        }
    }
   // Traitement pour vider l'historique
elseif (isset($_POST['action']) && $_POST['action'] === 'clear_history') {
    $query_clear = "DELETE FROM ventes";
    if ($conn->query($query_clear) === TRUE) {
        // Suppression de l'alerte
        // echo "<script>alert('Historique des ventes vidé avec succès.');</script>";
    } else {
        echo "<script>alert('Erreur lors de la suppression des ventes: " . $conn->error . "');</script>";
    }
}

}

// Requête pour récupérer les produits avec la quantité en stock
$query_produits = "SELECT id, nom, prix, quantite FROM produits";
$result_produits = $conn->query($query_produits);

// Vérifier la connexion
if (!$result_produits) {
    die("Erreur lors de l'exécution de la requête : " . $conn->error);
}

// Récupérer les produits
$produits = [];
while ($row = $result_produits->fetch_assoc()) {
    $produits[] = $row;
}

// Requête pour récupérer les ventes
$query = "SELECT v.id, p.nom AS produit, v.quantite, v.prix_total AS prix, v.date_vente
          FROM ventes v
          JOIN produits p ON v.produit_id = p.id";
$result = $conn->query($query);

// Vérifier la connexion
if (!$result) {
    die("Erreur lors de l'exécution de la requête : " . $conn->error);
}

// Récupérer les données des ventes
$ventes = [];
while ($row = $result->fetch_assoc()) {
    $ventes[] = $row;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Ventes</title>
    <link href="css/styles.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">

    <div class="flex">
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
                        <!-- Icône Stock Complet -->
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
        <div class="ml-64 p-8 w-full">
            <h1 class="text-3xl font-bold text-center text-gray-800 mb-8">Gestion des Ventes</h1>

            <h2 class="text-2xl font-bold text-gray-800 mb-4">Ajouter une Vente</h2>
            <form action="" method="POST">
                <input type="hidden" name="action" value="add_sale">
                <div class="mb-4">
                    <label class="block text-gray-700">Produit:</label>
                    <select name="produit_id" class="w-full p-2 border border-gray-300 rounded mt-1" required>
                        <?php foreach ($produits as $produit): ?>
                            <option value="<?php echo $produit['id']; ?>">
                                <?php echo htmlspecialchars($produit['nom']); ?> - <?php echo htmlspecialchars($produit['prix']); ?> cfa - En stock: <?php echo htmlspecialchars($produit['quantite']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700">Quantité:</label>
                    <input type="number" name="quantite" class="w-full p-2 border border-gray-300 rounded mt-1" required>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700">Prix Total:</label>
                    <input type="text" name="prix_total" class="w-full p-2 border border-gray-300 rounded mt-1" readonly>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700">Date de Vente:</label>
                    <input type="date" name="date_vente" class="w-full p-2 border border-gray-300 rounded mt-1" required>
                </div>
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Ajouter Vente</button>
            </form>

            <form action="" method="POST">
                <input type="hidden" name="action" value="clear_history">
                <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600 mt-4">Vider l'Historique</button>
            </form>

            <h2 class="text-2xl font-bold text-gray-800 mb-4">Liste des Ventes</h2>
            <ul id="saleList" class="bg-white p-6 rounded-lg shadow-md">
                <?php if (count($ventes) > 0): ?>
                    <?php foreach ($ventes as $vente): ?>
                        <li class="border-b border-gray-200 py-2">
                            <p><strong>Produit:</strong> <?php echo htmlspecialchars($vente['produit']); ?></p>
                            <p><strong>Quantité:</strong> <?php echo htmlspecialchars($vente['quantite']); ?></p>
                            <p><strong>Prix Total:</strong> <?php echo htmlspecialchars($vente['prix']); ?> cfa</p>
                            <p><strong>Date de Vente:</strong> <?php echo date('d/m/Y', strtotime($vente['date_vente'])); ?></p>
                            </li>
                    <?php endforeach; ?>
                <?php else: ?>
                    <li>Aucune vente trouvée.</li>
                <?php endif; ?>
            </ul>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const quantiteInput = document.querySelector('input[name="quantite"]');
        const produitSelect = document.querySelector('select[name="produit_id"]');
        const prixTotalInput = document.querySelector('input[name="prix_total"]');

        function updatePrixTotal() {
            const selectedOption = produitSelect.options[produitSelect.selectedIndex];
            const prixUnitaire = parseFloat(selectedOption.textContent.split(' - ')[1].replace('cfa', ''));
            const quantite = parseInt(quantiteInput.value) || 0;
            prixTotalInput.value = (prixUnitaire * quantite).toFixed(2);
        }

        produitSelect.addEventListener('change', updatePrixTotal);
        quantiteInput.addEventListener('input', updatePrixTotal);
    });
    </script>
</body>
</html>
