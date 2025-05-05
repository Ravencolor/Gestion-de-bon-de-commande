<?php
$host = 'localhost';
$dbname = 'classicmodels';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur : " . $e->getMessage());
}

if (!isset($_GET['orderNumber']) || empty($_GET['orderNumber'])) {
    die("Numéro de commande manquant.");
}

$orderNumber = (int) $_GET['orderNumber'];

$sql = "SELECT products.productName, orderdetails.priceEach, orderdetails.quantityOrdered, 
               (orderdetails.priceEach * orderdetails.quantityOrdered) AS total
        FROM orderdetails
        INNER JOIN products ON orderdetails.productCode = products.productCode
        WHERE orderdetails.orderNumber = :orderNumber";
$stmt = $pdo->prepare($sql);
$stmt->execute(['orderNumber' => $orderNumber]);
$details = $stmt->fetchAll(PDO::FETCH_ASSOC);

$sql_order = "SELECT orders.orderNumber, customers.customerName, customers.contactFirstName, customers.contactLastName,
              customers.addressLine1, customers.city, orders.status
              FROM orders
              INNER JOIN customers ON orders.customerNumber = customers.customerNumber
              WHERE orders.orderNumber = :orderNumber";
$stmt_order = $pdo->prepare($sql_order);
$stmt_order->execute(['orderNumber' => $orderNumber]);
$orderInfo = $stmt_order->fetch(PDO::FETCH_ASSOC);

if (!$orderInfo) {
    die("Commande introuvable.");
}

$totalHT = array_sum(array_column($details, 'total'));
$tva = $totalHT * 0.2;
$totalTTC = $totalHT + $tva;
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bon de commande #<?= htmlspecialchars($orderInfo['orderNumber']) ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8fbe9;
            margin: 20px;
        }
        h1, h2 {
            color: #2a7a5e;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #dff0d8;
            color: #2a7a5e;
        }
        td {
            background-color: #f9f9f9;
        }
        .summary {
            margin-top: 20px;
            font-size: 1.2em;
            color: #333;
        }
        .summary strong {
            color: #2a7a5e;
        }
    </style>
</head>
<body>
    <h1>Bons de commande</h1>
    <p><a href="index.php" style="color: #2a7a5e; text-decoration: none;">Retourner à l'accueil</a></p>

    <h2>Bon de commande n° <?= htmlspecialchars($orderInfo['orderNumber']) ?></h2>
    <p><strong><?= htmlspecialchars($orderInfo['customerName']) ?></strong><br>
    <?= htmlspecialchars($orderInfo['addressLine1']) ?><br>
    <?= htmlspecialchars($orderInfo['city']) ?></p>

    <table>
        <thead>
            <tr>
                <th>Produit</th>
                <th>Prix Unitaire</th>
                <th>Quantité</th>
                <th>Prix Total</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($details as $detail): ?>
                <tr>
                    <td><?= htmlspecialchars($detail['productName']) ?></td>
                    <td><?= number_format($detail['priceEach'], 2, ',', ' ') ?> €</td>
                    <td><?= htmlspecialchars($detail['quantityOrdered']) ?></td>
                    <td><?= number_format($detail['total'], 2, ',', ' ') ?> €</td>
                </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3" style="text-align: right;"><strong>Montant Total HT</strong></td>
                <td><?= number_format($totalHT, 2, ',', ' ') ?> €</td>
            </tr>
            <tr>
                <td colspan="3" style="text-align: right;"><strong>TVA (20 %)</strong></td>
                <td><?= number_format($tva, 2, ',', ' ') ?> €</td>
            </tr>
            <tr>
                <td colspan="3" style="text-align: right;"><strong>Montant Total TTC</strong></td>
                <td><?= number_format($totalTTC, 2, ',', ' ') ?> €</td>
            </tr>
        </tfoot>
    </table>
</body>
</html>
