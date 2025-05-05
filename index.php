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

$sql = "SELECT orderNumber, orderDate, shippedDate, status FROM orders ORDER BY orderNumber";
$orders = $pdo->query($sql);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des commandes</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9e7;
            margin: 20px;
        }

        h1 {
            color: #00796b;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            text-align: left;
            padding: 10px;
        }

        th {
            background-color: #00796b;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #e7e7e7;
        }

        tr:nth-child(odd) {
            background-color: #f9f9f9;
        }

        a {
            color: #00796b;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <h1>Bons de commande</h1>
    <table>
        <thead>
            <tr>
                <th>Commande</th>
                <th>Date de la commande</th>
                <th>Date de livraison</th>
                <th>Statut</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $orders->fetch(PDO::FETCH_ASSOC)): ?>
                <tr>
                    <td>
                        <a href="order_details.php?orderNumber=<?= htmlspecialchars($row['orderNumber']) ?>">
                            <?= htmlspecialchars($row['orderNumber']) ?>
                        </a>
                    </td>
                    <td><?= htmlspecialchars($row['orderDate']) ?></td>
                    <td><?= htmlspecialchars($row['shippedDate'] ?? 'Non livrée') ?></td>
                    <td><?= htmlspecialchars($row['status']) ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</body>
</html>
