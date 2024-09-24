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

    if (isset($_GET['search'])) {
        $search = $conn->real_escape_string($_GET['search']);
        echo "Search term: " . $search; // Affichez le terme de recherche
    
        $sql = "SELECT * FROM produits WHERE nom LIKE '%$search%'";
        $result = $conn->query($sql);
    
        if (!$result) {
            die("Query failed: " . $conn->error); // Affichez l'erreur SQL
        }
    
        $produits = [];
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $produits[] = $row;
            }
        }
    
        echo json_encode($produits);
        exit;
    }
    

    // Retourne les produits sous forme de JSON
    echo json_encode($produits);
    exit;
}

// Ajout d'un produit
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_product'])) {
    $nom = $conn->real_escape_string($_POST['nom']);
    $description = $conn->real_escape_string($_POST['description']);
    $quantite = isset($_POST['quantite']) ? (int)$_POST['quantite'] : 0;
    $prix = isset($_POST['prix']) ? (float)$_POST['prix'] : 0.0;
    $type_vente = $conn->real_escape_string($_POST['type_vente']);
    $fournisseur_id = isset($_POST['fournisseur']) ? (int)$_POST['fournisseur'] : 0;
    if ($nom && $description && $prix > 0 && $type_vente && $fournisseur_id > 0) {
        $sql = "INSERT INTO produits (nom, description, quantite, prix, type_vente, fournisseur_id) 
                VALUES ('$nom', '$description', $quantite, $prix, '$type_vente', $fournisseur_id)";
        if ($conn->query($sql) === TRUE) {
            $message = "Produit ajouté avec succès!";
        } else {
            $error = "Erreur lors de l'ajout: " . $conn->error;
        }
    } else {
        $error = "Erreur: valeurs invalides.";
    }
}
    
    



// Modification d'un produit
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['edit_product'])) {
    $product_id = (int)$_POST['product_id'];
    $nom = $conn->real_escape_string($_POST['nom']);
    $description = $conn->real_escape_string($_POST['description']);
    $quantite = isset($_POST['quantite']) ? (int)$_POST['quantite'] : 0;
    $prix = isset($_POST['prix']) ? (float)$_POST['prix'] : 0.0;
    $type_vente = $conn->real_escape_string($_POST['type_vente']);

    if ($product_id > 0 && $nom && $description && $prix > 0 && $type_vente) {
        $sql = "UPDATE produits SET nom = '$nom', description = '$description', quantite = $quantite, prix = $prix, type_vente = '$type_vente' WHERE id = $product_id";
        if ($conn->query($sql) === TRUE) {
            header("Location: produits.php");
            exit;
        } else {
            $error = "Erreur lors de la modification: " . $conn->error;
        }
    } else {
        $error = "Erreur: valeurs invalides.";
    }
    
}

// Suppression d'un produit
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_product'])) {
    $product_id = (int)$_POST['product_id'];

    if ($product_id > 0) {
        $sql = "DELETE FROM produits WHERE id = $product_id";
        if ($conn->query($sql) === TRUE) {
            $message = "Produit supprimé avec succès!";
        } else {
            $error = "Erreur lors de la suppression: " . $conn->error;
        }
    } else {
        $error = "Erreur: ID de produit invalide.";
    }
}

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

if (isset($_POST['fournisseur']) && !empty($_POST['fournisseur'])) {
    $fournisseur_id = $_POST['fournisseur'];
} else {
    $fournisseur_id = NULL; // Si aucun fournisseur n'a été sélectionné
}

$conn->close();
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Produits</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">

    <style>
        /* Style pour le bouton de fermeture */
        .close-button {
            cursor: pointer;
            position: absolute;
            top: 0.5rem;
            right: 0.5rem;
            font-size: 1.25rem;
            color: inherit;
        }
    </style>
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

<!-- Main Content -->
<div class="ml-64 p-6">
    <h1 class="text-2xl font-semibold mb-6">Gestion des Produits</h1>

    <?php if ($message): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4" role="alert">
            <strong class="font-bold"><?php echo $message; ?></strong>
        </div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4" role="alert">
            <strong class="font-bold"><?php echo $error; ?></strong>
        </div>
    <?php endif; ?>

    <!-- Formulaire d'ajout/modification des produits -->
    <form method="POST" action="" class="bg-white p-6 rounded shadow-md">
        <div class="mb-4">
            <label for="nom" class="block text-gray-700 font-bold mb-2">Nom</label>
            <input type="text" id="nom" name="nom" class="w-full border border-gray-300 p-2 rounded" required>
        </div>
        <div class="mb-4">
            <label for="description" class="block text-gray-700 font-bold mb-2">Description</label>
            <textarea id="description" name="description" class="w-full border border-gray-300 p-2 rounded" required></textarea>
        </div>
        <div class="mb-4">
        <label for="quantite">Quantité:</label>
        <input type="number" id="quantite" name="quantite" value="0" readonly>

        </div>
        <div class="mb-4">
            <label for="prix" class="block text-gray-700 font-bold mb-2">Prix</label>
            <input type="number" id="prix" name="prix" class="w-full border border-gray-300 p-2 rounded" step="0.01" required>
        </div>
        <div class="mb-4">
            <label for="type_vente" class="block text-gray-700 font-bold mb-2">Type de Vente</label>
            <select id="type_vente" name="type_vente" class="w-full border border-gray-300 p-2 rounded" required>
                <option value="Carton">Carton</option>
                <option value="Unité">Unité</option>
            </select>
        </div>
        <div class="mb-4">
            <label for="fournisseur" class="block text-gray-700 font-bold mb-2">Fournisseur</label>
            <select id="fournisseur" name="fournisseur" class="w-full border border-gray-300 p-2 rounded" required>
                <option value="">Sélectionner un fournisseur</option>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <option value="<?php echo $row['id']; ?>"><?php echo $row['nom']; ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="flex items-center justify-between">
            <button type="submit" name="add_product" class="bg-blue-500 text-white px-4 py-2 rounded">Ajouter Produit</button>
        </div>
    </form>

    <!-- Formulaire de recherche -->
<div class="mb-6">
    <label for="search" class="block text-gray-700 font-bold mb-2">Rechercher un produit</label>
    <input type="text" id="search" placeholder="Rechercher un produit..." class="mb-4 p-2 border border-gray-300 rounded">
    </div>


      <!-- Afficher le message de succès ou d'erreur -->
      <?php
        if (isset($_GET['message'])) {
            $message = htmlspecialchars($_GET['message']);
            echo "<div class='relative mb-4 p-4 bg-green-100 text-green-800 border border-green-300 rounded'>
                    <span class='close-button' onclick='this.parentElement.style.display=\"none\"'>&times;</span>
                    $message
                  </div>";
        }
        ?>


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
                        <a href="delete_product.php?id=<?php echo $produit['id']; ?>" class="text-red-600 hover:underline">Supprimer</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
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

<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('search');
    const productsTable = document.querySelector('tbody');

    searchInput.addEventListener('input', function () {
        const searchQuery = searchInput.value;
        console.log("Recherche: " + searchQuery); // Affichez la requête de recherche

        fetch(`produits.php?search=${encodeURIComponent(searchQuery)}`)
            .then(response => {
                console.log("Réponse reçue");
                return response.json();
            })
            .then(data => {
                console.log("Données reçues:", data); // Affichez les données reçues
                productsTable.innerHTML = '';

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

</html>
