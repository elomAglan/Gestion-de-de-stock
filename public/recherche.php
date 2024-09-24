<?php 
// Inclure le fichier de connexion
include 'db_connect.php';

$message = '';
$error = '';

// Récupération des produits correspondant à la recherche
if (isset($_GET['search'])) {
    $search = $conn->real_escape_string($_GET['search']);
    
    // Requête de recherche
    $sql = "SELECT * FROM produits WHERE nom LIKE '%$search%'";
    $result = $conn->query($sql);

    $produits = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $produits[] = $row;
        }
    }

    // Retourner les produits sous forme de JSON
    header('Content-Type: application/json');
    echo json_encode($produits);
    exit;
}

// Ajout, modification, suppression des produits ici...

// Récupérer la liste complète des produits
$sql = "SELECT * FROM produits";
$result = $conn->query($sql);
$produits = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $produits[] = $row;
    }
}

// Récupérer les fournisseurs depuis la base de données
$query = "SELECT id, nom FROM fournisseurs";
$result = mysqli_query($conn, $query);

$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Produits</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">

<!-- Navbar -->
<!-- Code de la Navbar -->

<!-- Main Content -->
<div class="ml-64 p-6">
    <h1 class="text-2xl font-semibold mb-6">Gestion des Produits</h1>

    <?php if ($message): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4" role="alert">
            <strong class="font-bold"><?php echo htmlspecialchars($message); ?></strong>
        </div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4" role="alert">
            <strong class="font-bold"><?php echo htmlspecialchars($error); ?></strong>
        </div>
    <?php endif; ?>

    <!-- Formulaire de recherche -->
    <div class="mb-6">
        <label for="search" class="block text-gray-700 font-bold mb-2">Rechercher un produit</label>
        <input type="text" id="search" name="search" class="w-full border border-gray-300 p-2 rounded" placeholder="Rechercher...">
    </div>

    <hr class="my-6">

    <!-- Liste des produits -->
    <h2 class="text-xl font-semibold mb-4">Liste des Produits</h2>
    <table class="min-w-full bg-white border border-gray-300">
        <thead>
            <tr>
                <th class="py-2 px-4 border-b">Nom</th>
                <th class="py-2 px-4 border-b">Description</th>
                <th class="py-2 px-4 border-b">Quantité</th>
                <th class="py-2 px-4 border-b">Prix</th>
                <th class="py-2 px-4 border-b">Type de Vente</th>
                <th class="py-2 px-4 border-b">Fournisseur</th>
                <th class="py-2 px-4 border-b">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($produits as $produit): ?>
                <tr>
                    <td class="py-2 px-4 border-b"><?php echo htmlspecialchars($produit['nom']); ?></td>
                    <td class="py-2 px-4 border-b"><?php echo htmlspecialchars($produit['description']); ?></td>
                    <td class="py-2 px-4 border-b"><?php echo htmlspecialchars($produit['quantite']); ?></td>
                    <td class="py-2 px-4 border-b"><?php echo htmlspecialchars($produit['prix']); ?></td>
                    <td class="py-2 px-4 border-b"><?php echo htmlspecialchars($produit['type_vente']); ?></td>
                    <td class="py-2 px-4 border-b"><?php echo htmlspecialchars($produit['fournisseur_id']); ?></td>
                    <td class="py-2 px-4 border-b">
                        <a href="edit_product.php?id=<?php echo $produit['id']; ?>" class="text-blue-600 hover:underline">Modifier</a> | 
                        <a href="delete_product.php?id=<?php echo $produit['id']; ?>" class="text-red-600 hover:underline">Supprimer</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('search');
    const productsTable = document.querySelector('tbody'); // Sélecteur pour le corps du tableau des produits

    searchInput.addEventListener('input', function () {
        const searchQuery = searchInput.value;

        fetch(`produits.php?search=${encodeURIComponent(searchQuery)}`)
            .then(response => response.json())
            .then(data => {
                // Vider le tableau des produits
                productsTable.innerHTML = '';

                // Remplir le tableau avec les nouveaux produits
                data.forEach(produit => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td class="py-2 px-4 border-b">${produit.nom}</td>
                        <td class="py-2 px-4 border-b">${produit.description}</td>
                        <td class="py-2 px-4 border-b">${produit.quantite}</td>
                        <td class="py-2 px-4 border-b">${produit.prix}</td>
                        <td class="py-2 px-4 border-b">${produit.type_vente}</td>
                        <td class="py-2 px-4 border-b">${produit.fournisseur_id}</td>
                        <td class="py-2 px-4 border-b">
                            <a href="edit_product.php?id=${produit.id}" class="text-blue-600 hover:underline">Modifier</a> | 
                            <a href="delete_product.php?id=${produit.id}" class="text-red-600 hover:underline">Supprimer</a>
                        </td>
                    `;
                    productsTable.appendChild(row);
                });
            })
            .catch(error => console.error('Erreur lors de la recherche:', error));
    });
});
</script>

</body>
</html>
