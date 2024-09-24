<?php
// Activer le tampon de sortie
ob_start();

// Inclure l'autoloader de Composer
require_once '../vendor/autoload.php';

// Connexion à la base de données
include 'db_connect.php';

// Requête pour récupérer les produits en stock
$query = "SELECT p.id, p.nom, p.quantite, p.prix, (p.prix * p.quantite) AS prix_total 
          FROM produits p";

// Exécution de la requête
$result = $conn->query($query);

// Tableau de stock
$stock = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $stock[] = [
            'id' => $row['id'],
            'nom' => $row['nom'],
            'quantite' => $row['quantite'],
            'prix' => $row['prix'],
            'prix_total' => $row['prix_total']
        ];
    }
}

$conn->close();

// Créer une nouvelle instance de TCPDF
$pdf = new TCPDF();
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Nom de l\'auteur');
$pdf->SetTitle('Fiche de Stock');
$pdf->SetSubject('Fiche de Stock');
$pdf->AddPage();

// Contenu du PDF
$html = '<h1 style="text-align:center;">Fiche de Stock</h1>';
$html .= '<table border="1" cellspacing="0" cellpadding="5" style="width:100%; border-collapse: collapse;">';
$html .= '<tr><th>ID</th><th>Nom du Produit</th><th>Quantité</th><th>Prix (€)</th><th>Prix Total (€)</th></tr>';

foreach ($stock as $item) {
    $html .= "<tr>
                <td>{$item['id']}</td>
                <td>{$item['nom']}</td>
                <td>{$item['quantite']}</td>
                <td>" . number_format($item['prix'], 2, ',', ' ') . "</td>
                <td>" . number_format($item['prix_total'], 2, ',', ' ') . "</td>
              </tr>";
}

$html .= '</table>';

// Générer le contenu HTML dans le PDF
$pdf->writeHTML($html, true, false, true, false, '');

// Vider le tampon de sortie
ob_end_clean();

// Fermer et envoyer le PDF
$pdf->Output('fiche_de_stock.pdf', 'D');
?>
