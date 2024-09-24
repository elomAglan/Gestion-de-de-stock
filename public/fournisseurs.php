<?php
// Inclure le fichier de connexion
include 'db_connect.php';

// Démarrer la session
session_start();

// Initialiser un message de statut
$message = '';

// Générer un jeton CSRF si non défini
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Ajouter un fournisseur
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_supplier'])) {
    $nom = $conn->real_escape_string($_POST['nom']);
    $adresse = $conn->real_escape_string($_POST['adresse']);
    $contact = $conn->real_escape_string($_POST['contact']);

    if ($nom && $adresse && $contact) {
        $stmt = $conn->prepare("INSERT INTO fournisseurs (nom, adresse, contact) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $nom, $adresse, $contact);
        
        if ($stmt->execute()) {
            $message = "Fournisseur ajouté avec succès!";
        } else {
            $message = "Erreur lors de l'ajout : " . htmlspecialchars($conn->error, ENT_QUOTES, 'UTF-8');
        }
        $stmt->close();
    } else {
        $message = "Erreur : valeurs invalides.";
    }
}

// Récupérer la liste complète des fournisseurs
$sql = "SELECT * FROM fournisseurs";
$result = $conn->query($sql);
$fournisseurs = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $fournisseurs[] = $row;
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Fournisseurs</title>
    <link href="css/styles.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        /* Ajoutez ce style pour la section principale */
        #main-content {
            position: fixed; /* Fixe la position */
            top: 0; /* Colle en haut */
            left: 256px; /* Laissez de l'espace pour la barre de navigation */
            width: calc(100% - 256px); /* Ajustez la largeur */
            height: 100vh; /* Hauteur pleine de la vue */
            overflow-y: auto; /* Permet le défilement vertical */
            padding-top: 80px; /* Espace pour éviter le chevauchement avec la nav */
        }
    </style>
</head>
<body class="bg-gray-100">

   <!-- Section des messages -->
   <div id="message-container" class="fixed top-4 left-1/2 transform -translate-x-1/2 z-50 w-96">
        <?php if ($message): ?>
            <div class="bg-green-500 text-white p-4 rounded shadow-lg relative">
                <?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?>
                <button onclick="closeMessage()" class="absolute top-2 right-2 text-white">X</button>
            </div>
        <?php endif; ?>
    </div>

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
    <div id="main-content" class="p-8">
        <!-- Titre de la page -->
        <h1 class="text-3xl font-bold text-center text-gray-800 mb-8">Gestion des Fournisseurs</h1>

        <!-- Formulaire d'ajout de fournisseur -->
        <div class="bg-white p-8 rounded-lg shadow-md">
            <h2 class="text-2xl font-bold mb-4">Ajouter un Fournisseur</h2>
            <form method="POST" action="">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="nom">Nom</label>
                    <input type="text" name="nom" id="nom" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="adresse">Adresse</label>
                    <input type="text" name="adresse" id="adresse" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="contact">Contact</label>
                    <input type="text" name="contact" id="contact" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                </div>
                <div class="flex items-center justify-between">
                    <button type="submit" name="add_supplier" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                        Ajouter le Fournisseur
                    </button>
                </div>
            </form>
        </div>

        <!-- Liste des fournisseurs -->
        <div class="mt-8">
            <h2 class="text-2xl font-bold mb-4">Liste des Fournisseurs</h2>
            <table class="min-w-full bg-white border rounded-lg shadow-md">
                <thead>
                    <tr>
                        <th class="py-2 px-4 bg-gray-200 text-gray-600 font-bold">Nom</th>
                        <th class="py-2 px-4 bg-gray-200 text-gray-600 font-bold">Adresse</th>
                        <th class="py-2 px-4 bg-gray-200 text-gray-600 font-bold">Contact</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($fournisseurs) > 0): ?>
                        <?php foreach ($fournisseurs as $fournisseur): ?>
                            <tr>
                                <td class="border-t py-2 px-4"><?php echo htmlspecialchars($fournisseur['nom'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td class="border-t py-2 px-4"><?php echo htmlspecialchars($fournisseur['adresse'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td class="border-t py-2 px-4"><?php echo htmlspecialchars($fournisseur['contact'], ENT_QUOTES, 'UTF-8'); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3" class="text-center text-gray-600 py-4">Aucun fournisseur trouvé.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        function closeMessage() {
            document.getElementById('message-container').style.display = 'none';
        }
    </script>
</body>
</html>
