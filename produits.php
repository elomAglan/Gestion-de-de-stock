<?php
// src/php/produits.php

require_once 'db.php';

// Ajouter un produit
if (isset($_POST['action']) && $_POST['action'] === 'ajouter') {
    $nom = $_POST['nom'];
    $description = $_POST['description'];
    $quantite = $_POST['quantite'];
    $prix = $_POST['prix'];

    $sql = "INSERT INTO produits (nom, description, quantite, prix) VALUES (:nom, :description, :quantite, :prix)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':nom' => $nom,
        ':description' => $description,
        ':quantite' => $quantite,
        ':prix' => $prix
    ]);

    echo "Produit ajouté avec succès.";
}

// Afficher tous les produits
$sql = "SELECT * FROM produits";
$stmt = $pdo->query($sql);
$produits = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
