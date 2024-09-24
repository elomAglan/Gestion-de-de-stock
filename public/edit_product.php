<?php
// Inclure le fichier de connexion
include 'db_connect.php';

$message = '';
$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_product'])) {
    $product_id = (int)$_POST['product_id'];
    $nom = $conn->real_escape_string($_POST['nom']);
    $description = $conn->real_escape_string($_POST['description']);
    $quantite = isset($_POST['quantite']) ? (int)$_POST['quantite'] : 0;
    $prix = isset($_POST['prix']) ? (float)$_POST['prix'] : 0.0;
    $type_vente = $conn->real_escape_string($_POST['type_vente']);
    $fournisseur_id = isset($_POST['fournisseur']) ? (int)$_POST['fournisseur'] : 0;

    // Vérification des valeurs
    if ($product_id <= 0 || !$nom || !$description || $quantite < 0 || $prix < 0 || !$type_vente || $fournisseur_id <= 0) {
        $error = "Erreur: valeurs invalides.";
    } else {
        // Mise à jour du produit
        $sql = "UPDATE produits SET nom = '$nom', description = '$description', quantite = $quantite, prix = $prix, type_vente = '$type_vente', fournisseur_id = $fournisseur_id WHERE id = $product_id";
        if ($conn->query($sql) === TRUE) {
            // Redirection après mise à jour réussie
            header("Location: produits.php");
            exit;
        } else {
            $error = "Erreur lors de la modification: " . $conn->error;
        }
    }
}

// Récupération des informations du produit à modifier
$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$product = null;
if ($product_id > 0) {
    $sql = "SELECT * FROM produits WHERE id = $product_id";
    $result = $conn->query($sql);
    if ($result->num_rows == 1) {
        $product = $result->fetch_assoc();
    } else {
        $error = "Produit non trouvé.";
    }
}

// Récupérer les fournisseurs depuis la base de données
$query = "SELECT id, nom FROM fournisseurs";
$fournisseurs = $conn->query($query);

$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier Produit</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
<div class="ml-64 p-6">
    <h1 class="text-2xl font-semibold mb-6">Modifier Produit</h1>

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

    <?php if ($product): ?>
        <form method="POST" action="" class="bg-white p-6 rounded shadow-md">
            <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
            <div class="mb-4">
                <label for="nom" class="block text-gray-700 font-bold mb-2">Nom</label>
                <input type="text" id="nom" name="nom" value="<?php echo htmlspecialchars($product['nom']); ?>" class="w-full border border-gray-300 p-2 rounded" required>
            </div>
            <div class="mb-4">
                <label for="description" class="block text-gray-700 font-bold mb-2">Description</label>
                <textarea id="description" name="description" class="w-full border border-gray-300 p-2 rounded" required><?php echo htmlspecialchars($product['description']); ?></textarea>
            </div>
            <div class="mb-4">
                <label for="quantite" class="block text-gray-700 font-bold mb-2">Quantité</label>
                <input type="number" id="quantite" name="quantite" value="<?php echo htmlspecialchars($product['quantite']); ?>" class="w-full border border-gray-300 p-2 rounded" required>
            </div>
            <div class="mb-4">
                <label for="prix" class="block text-gray-700 font-bold mb-2">Prix</label>
                <input type="number" id="prix" name="prix" value="<?php echo htmlspecialchars($product['prix']); ?>" class="w-full border border-gray-300 p-2 rounded" step="0.01" required>
            </div>
            <div class="mb-4">
                <label for="type_vente" class="block text-gray-700 font-bold mb-2">Type de Vente</label>
                <select id="type_vente" name="type_vente" class="w-full border border-gray-300 p-2 rounded" required>
                    <option value="carton" <?php if ($product['type_vente'] == 'carton') echo 'selected'; ?>>Carton</option>
                    <option value="unite" <?php if ($product['type_vente'] == 'unite') echo 'selected'; ?>>Unité</option>
                </select>
            </div>
            <div class="mb-4">
    <label for="type_vente" class="block text-gray-700 font-bold mb-2">Type de Vente</label>
    <select id="type_vente" name="type_vente" class="w-full border border-gray-300 p-2 rounded" required>
        <option value="carton" <?php if ($product['type_vente'] == 'carton') echo 'selected'; ?>>Carton</option>
        <option value="unite" <?php if ($product['type_vente'] == 'unite') echo 'selected'; ?>>Unité</option>
    </select>
</div>

            <div class="flex items-center justify-between">
                <button type="submit" name="update_product" class="bg-blue-500 text-white px-4 py-2 rounded">Mettre à Jour</button>
            </div>
        </form>
    <?php else: ?>
        <p class="text-red-500">Produit non trouvé.</p>
    <?php endif; ?>
</div>
</body>
</html>
