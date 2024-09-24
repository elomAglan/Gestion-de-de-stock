<?php
include 'db_connect.php'; // Inclure le fichier de connexion

// Vérifier si l'ID du produit est passé en paramètre GET
if (isset($_GET['id'])) {
    $product_id = $conn->real_escape_string($_GET['id']);

    // Exécuter la requête de suppression
    $sql = "DELETE FROM produits WHERE id = $product_id";
    if ($conn->query($sql) === TRUE) {
        // Rediriger vers produit.php avec un message de succès
        header('Location: produits.php?message=Produit supprimé avec succès');
        exit();
    } else {
        // Rediriger avec un message d'erreur
        header('Location: produits.php?message=Erreur lors de la suppression du produit');
        exit();
    }
} else {
    // Rediriger avec un message d'erreur si l'ID n'est pas défini
    header('Location: produits.php?message=ID du produit non défini');
    exit();
}

// Fermer la connexion à la base de données
$conn->close();
?>
