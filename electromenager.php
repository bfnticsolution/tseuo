<?php
include 'config.php';

// Récupérer les produits de la catégorie "Électroménager" depuis la base de données
$sql = "SELECT * FROM products WHERE category = 'Électroménager'";
$result = $conn->query($sql);

if ($result === false) {
    die("Erreur dans la requête SQL : " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Électroménager | OUEST DEAL</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        :root {
            --primary: #FF6B00;
            --secondary: #2b77e7;
            --light: #f8f9fa;
            --dark: #212529;
            --success: #28a745;
            --transition: all 0.3s ease;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: var(--dark);
            background-color: #f5f5f5;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            background-color: white;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            text-align: center;
            margin-bottom: 30px;
        }

        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
        }

        .product-card {
            background-color: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            transition: var(--transition);
        }

        .product-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.15);
        }

        .product-img {
            height: 200px;
            overflow: hidden;
            position: relative;
        }

        .product-img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s;
        }

        .product-card:hover .product-img img {
            transform: scale(1.05);
        }

        .product-tag {
            position: absolute;
            top: 10px;
            right: 10px;
            background-color: var(--primary);
            color: white;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: bold;
        }

        .product-info {
            padding: 20px;
        }

        .product-info h3 {
            margin-bottom: 10px;
            font-size: 1.2rem;
        }

        .product-meta {
            display: flex;
            justify-content: space-between;
            color: #666;
            font-size: 0.9rem;
            margin-bottom: 15px;
        }

        .product-price {
            font-size: 1.3rem;
            font-weight: bold;
            color: var(--primary);
            margin: 15px 0;
        }

        .product-actions {
            display: flex;
            gap: 10px;
        }

        .view-btn, .contact-btn {
            flex: 1;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-align: center;
        }

        .view-btn {
            background-color: var(--secondary);
            color: white;
        }

        .contact-btn {
            background-color: var(--primary);
            color: white;
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.5);
        }

        .modal-content {
            background-color: #fefefe;
            margin: 5% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 800px;
            border-radius: 10px;
            position: relative;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }

        .close:hover {
            color: black;
        }
    </style>
</head>
<body>
    <div class="container">
        <header class="header">
            <h1>Nos Produits Électroménagers</h1>
            <p>Découvrez nos meilleures offres d'électroménager du moment</p>
        </header>

        <div class="product-grid">
            <?php
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo '<div class="product-card">';
                    echo '<div class="product-img">';
                    echo '<img src="' . htmlspecialchars($row["image"]) . '" alt="' . htmlspecialchars($row["title"]) . '">';
                    echo '<span class="product-tag">' . htmlspecialchars($row["tag"]) . '</span>';
                    echo '</div>';
                    echo '<div class="product-info">';
                    echo '<h3>' . htmlspecialchars($row["title"]) . '</h3>';
                    echo '<div class="product-meta">';
                    echo '<span><i class="fas fa-map-marker-alt"></i> ' . htmlspecialchars($row["location"]) . '</span>';
                    echo '<span><i class="fas fa-calendar-alt"></i> ' . htmlspecialchars($row["date_added"]) . '</span>';
                    echo '</div>';
                    echo '<p>' . htmlspecialchars($row["description"]) . '</p>';
                    echo '<div class="product-price">' . htmlspecialchars($row["price"]) . ' XOF</div>';
                    echo '<div class="product-actions">';
                    echo '<button class="view-btn" onclick="openModal(' . htmlspecialchars(json_encode($row)) . ')">Voir détails</button>';
                    echo '<button class="contact-btn" onclick="callSeller(\'' . htmlspecialchars($row["telephone"]) . '\')"><i class="fas fa-phone-alt"></i> Appeler</button>';
                    echo '</div>';
                    echo '</div>';
                    echo '</div>';
                }
            } else {
                echo "<p>Aucun produit électroménager trouvé.</p>";
            }
            ?>
        </div>
    </div>

    <!-- Modal -->
    <div id="productModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <div id="modalContent"></div>
        </div>
    </div>

    <script>
        function openModal(product) {
            const modal = document.getElementById('productModal');
            const modalContent = document.getElementById('modalContent');

            modalContent.innerHTML = `
                <h2>${product.title}</h2>
                <img src="${product.image}" alt="${product.title}" style="width:100%; height:auto;">
                <p>${product.description}</p>
                <p><strong>Prix:</strong> ${product.price} XOF</p>
                <p><strong>Localisation:</strong> ${product.location}</p>
                <p><strong>Date d'ajout:</strong> ${product.date_added}</p>
                <p><strong>Téléphone:</strong> ${product.telephone}</p>
            `;

            modal.style.display = "block";
        }

        function closeModal() {
            document.getElementById('productModal').style.display = "none";
        }

        function callSeller(phoneNumber) {
            alert('Pour contacter le vendeur, appelez le ' + phoneNumber);
        }

        window.onclick = function(event) {
            const modal = document.getElementById('productModal');
            if (event.target == modal) {
                closeModal();
            }
        }
    </script>
</body>
</html>

<?php
$conn->close();
?>
