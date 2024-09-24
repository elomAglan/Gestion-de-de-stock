<?php
// Inclure le fichier de connexion
include 'db_connect.php';

// Initialiser un message de statut
$message = '';

// Récupérer la liste des produits pour le menu déroulant
$produits = [];
$sql_produits = "SELECT id, nom FROM produits";
$result_produits = $conn->query($sql_produits);
if ($result_produits->num_rows > 0) {
    while ($row = $result_produits->fetch_assoc()) {
        $produits[] = $row;
    }
}

// Ajouter un mouvement
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['vider_historique'])) {
    // Récupérer les données du formulaire
    $produit_id = $_POST['produit_id'];
    $type_mouvement = $_POST['type_mouvement'];
    $quantite = $_POST['quantite'];
    $date_mouvement = $_POST['date_mouvement'];
    $fournisseur_id = $_POST['fournisseur_id']; // Assurez-vous que ceci est aussi récupéré

    // Validation basique
    if (!empty($produit_id) && !empty($type_mouvement) && !empty($quantite) && !empty($date_mouvement) && !empty($fournisseur_id)) {
        // Vérifier le format de la date
        $date_obj = DateTime::createFromFormat('Y-m-d', $date_mouvement);
        if ($date_obj && $date_obj->format('Y-m-d') === $date_mouvement) {
            // Préparer la requête d'insertion
            $sql = "INSERT INTO mouvements (produit_id, type_mouvement, quantite, date_mouvement, fournisseur_id) 
                    VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);

            // Lier les paramètres et exécuter la requête
            $stmt->bind_param('ssisi', $produit_id, $type_mouvement, $quantite, $date_mouvement, $fournisseur_id);

            if ($stmt->execute()) {
                // Mettre à jour le stock
                if ($type_mouvement === 'Entrée') {
                    $sql_update_stock = "UPDATE produits SET quantite = quantite + ? WHERE id = ?";
                } elseif ($type_mouvement === 'Sortie') {
                    $sql_update_stock = "UPDATE produits SET quantite = quantite - ? WHERE id = ?";
                }

                if (isset($sql_update_stock)) {
                    $stmt_update_stock = $conn->prepare($sql_update_stock);
                    $stmt_update_stock->bind_param('ii', $quantite, $produit_id);
                    $stmt_update_stock->execute();
                }

                $message = "<div class='bg-green-500 text-white p-4 rounded shadow-lg relative'>
                                Mouvement ajouté avec succès!
                                <button onclick='closeMessage()' class='absolute top-2 right-2 text-white'>X</button>
                            </div>";
            } else {
                $message = "<div class='bg-red-500 text-white p-4 rounded shadow-lg relative'>
                                Erreur lors de l'ajout du mouvement : " . $conn->error . "
                                <button onclick='closeMessage()' class='absolute top-2 right-2 text-white'>X</button>
                            </div>";
            }
        } else {
            $message = "<div class='bg-red-500 text-white p-4 rounded shadow-lg relative'>
                            Erreur : la date est invalide.
                            <button onclick='closeMessage()' class='absolute top-2 right-2 text-white'>X</button>
                        </div>";
        }
    } else {
        $message = "<div class='bg-red-500 text-white p-4 rounded shadow-lg relative'>
                        Erreur : valeurs invalides.
                        <button onclick='closeMessage()' class='absolute top-2 right-2 text-white'>X</button>
                    </div>";
    }
}

// Vider l'historique
if (isset($_POST['vider_historique'])) {
    $sql = "TRUNCATE TABLE mouvements";
    if ($conn->query($sql) === TRUE) {
        $message = "<div class='bg-red-500 text-white p-4 rounded shadow-lg relative'>
                        Historique vidé avec succès!
                        <button onclick='closeMessage()' class='absolute top-2 right-2 text-white'>X</button>
                    </div>";
    } else {
        $message = "<div class='bg-red-500 text-white p-4 rounded shadow-lg relative'>
                        Erreur lors de la suppression de l'historique : " . $conn->error . "
                        <button onclick='closeMessage()' class='absolute top-2 right-2 text-white'>X</button>
                    </div>";
    }
}

// Récupérer la liste des fournisseurs
$fournisseurs = [];
$sql_fournisseurs = "SELECT id, nom FROM fournisseurs";
$result_fournisseurs = $conn->query($sql_fournisseurs);
if ($result_fournisseurs->num_rows > 0) {
    while ($row = $result_fournisseurs->fetch_assoc()) {
        $fournisseurs[] = $row;
    }
}

// Récupérer l'historique des mouvements
$sql = "SELECT mouvements.*, produits.nom AS nom_produit, 
               COALESCE(fournisseurs.nom, 'N/A') AS nom_fournisseur
        FROM mouvements
        JOIN produits ON mouvements.produit_id = produits.id
        LEFT JOIN fournisseurs ON mouvements.fournisseur_id = fournisseurs.id
        ORDER BY mouvements.date_mouvement DESC";

$result = $conn->query($sql);
$mouvements = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $mouvements[] = $row;
    }
}

$conn->close();

?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Mouvements de Stock</title>
    <link href="css/styles.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">

      <!-- Navbar -->
      <div class="h-screen w-64 bg-gray-800 text-white fixed top-0 left-0 flex flex-col justify-between">
            <div>
                <div class="p-6">
                    <h1 class="text-3xl font-semibold">Gestion Stock</h1>
                </div>
                <nav class="mt-10">
                    <ul>
                        <!-- Liens de la barre de navigation -->
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
        </div>

  <div class="ml-64 p-8">
    <!-- Affichage du message -->
    <?php echo $message; ?>

    <h1 class="text-2xl font-bold mb-4">Ajouter un Mouvement</h1>
    <form method="post" action="">
      <div class="mb-4">
        <label for="produit_id" class="block text-gray-700">Produit</label>
        <select id="produit_id" name="produit_id" class="w-full p-2 border border-gray-300 rounded">
          <option value="">Sélectionner un produit</option>
          <?php foreach ($produits as $produit): ?>
            <option value="<?php echo htmlspecialchars($produit['id']); ?>"><?php echo htmlspecialchars($produit['nom']); ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="mb-4">
    <label for="type_mouvement" class="block text-gray-700">Type de Mouvement</label>
    <select id="type_mouvement" name="type_mouvement" class="w-full p-2 border border-gray-300 rounded" required>
        <option value="">Sélectionner un type</option>
        <option value="Entrée">Entrée</option>
        <option value="Sortie">Sortie</option>
    </select>
</div>

      <div class="mb-4">
        <label for="quantite" class="block text-gray-700">Quantité</label>
        <input type="number" id="quantite" name="quantite" class="w-full p-2 border border-gray-300 rounded" required>
      </div>
      <div class="mb-4">
        <label for="date_mouvement" class="block text-gray-700">Date du Mouvement</label>
        <input type="date" id="date_mouvement" name="date_mouvement" class="w-full p-2 border border-gray-300 rounded" required>
      </div>
      <div class="mb-4">
        <label for="fournisseur_id" class="block text-gray-700">Fournisseur</label>
        <select id="fournisseur_id" name="fournisseur_id" class="w-full p-2 border border-gray-300 rounded">
          <option value="">Sélectionner un fournisseur</option>
          <?php foreach ($fournisseurs as $fournisseur): ?>
            <option value="<?php echo htmlspecialchars($fournisseur['id']); ?>"><?php echo htmlspecialchars($fournisseur['nom']); ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <button type="submit" class="bg-blue-500 text-white p-2 rounded">Ajouter</button>
    </form>

    <h1 class="text-2xl font-bold mt-8 mb-4">Historique des Mouvements</h1>

                <!-- Ajouter le bouton "Vider l'Historique" -->
<form method="post" action="" class="mb-4">
  <button type="submit" name="vider_historique" class="bg-red-500 text-white p-2 rounded">Vider l'Historique</button>
</form>

    <table class="w-full border border-gray-300">
      <thead>
        <tr>
          <th class="border border-gray-300 p-2">Produit</th>
          <th class="border border-gray-300 p-2">Type</th>
          <th class="border border-gray-300 p-2">Quantité</th>
          <th class="border border-gray-300 p-2">Date du Mouvement</th>
          <th class="border border-gray-300 p-2">Fournisseur</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($mouvements as $mouvement): ?>
          <tr>
            <td class="border border-gray-300 p-2"><?php echo htmlspecialchars($mouvement['nom_produit']); ?></td>
            <td class="border border-gray-300 p-2"><?php echo htmlspecialchars($mouvement['type_mouvement']); ?></td>
            <td class="border border-gray-300 p-2"><?php echo htmlspecialchars($mouvement['quantite']); ?></td>
            <td class="border border-gray-300 p-2"><?php echo htmlspecialchars(date('d-m-Y', strtotime($mouvement['date_mouvement']))); ?></td>
            <td class="border border-gray-300 p-2"><?php echo htmlspecialchars($mouvement['nom_fournisseur']); ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <script>
    function closeMessage() {
      const message = document.querySelector('.relative');
      if (message) {
        message.style.display = 'none';
      }
    }
  </script>
</body>
</html>
