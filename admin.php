<?php
include 'config.php';
session_start();

// Vérification de l'authentification admin
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.php");
    exit;
}

// Traitement des actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ajout d'un produit
    if (isset($_POST['add_product'])) {
        $title = $conn->real_escape_string($_POST['title']);
        $description = $conn->real_escape_string($_POST['description']);
        $price = floatval($_POST['price']);
        $category = $conn->real_escape_string($_POST['category']);
        $tag = $conn->real_escape_string($_POST['tag']);
        $location = $conn->real_escape_string($_POST['location']);
        $image = $conn->real_escape_string($_POST['image']);
        $telephone = $conn->real_escape_string($_POST['telephone']);
        $is_featured = isset($_POST['is_featured']) ? 1 : 0;
        
        $sql = "INSERT INTO products (title, description, price, category, tag, location, image, telephone, is_featured) 
                VALUES ('$title', '$description', $price, '$category', '$tag', '$location', '$image', '$telephone', $is_featured)";
        
        if ($conn->query($sql)) {
            $_SESSION['message'] = "Produit ajouté avec succès!";
            $_SESSION['message_type'] = "success";
        } else {
            $_SESSION['message'] = "Erreur lors de l'ajout: " . $conn->error;
            $_SESSION['message_type'] = "danger";
        }
    }
    
    // Mise à jour d'un produit
    if (isset($_POST['update_product'])) {
        $id = intval($_POST['id']);
        $title = $conn->real_escape_string($_POST['title']);
        $description = $conn->real_escape_string($_POST['description']);
        $price = floatval($_POST['price']);
        $category = $conn->real_escape_string($_POST['category']);
        $tag = $conn->real_escape_string($_POST['tag']);
        $location = $conn->real_escape_string($_POST['location']);
        $image = $conn->real_escape_string($_POST['image']);
        $telephone = $conn->real_escape_string($_POST['telephone']);
        $is_featured = isset($_POST['is_featured']) ? 1 : 0;
        
        $sql = "UPDATE products SET 
                title = '$title',
                description = '$description',
                price = $price,
                category = '$category',
                tag = '$tag',
                location = '$location',
                image = '$image',
                telephone = '$telephone',
                is_featured = $is_featured
                WHERE id = $id";
        
        if ($conn->query($sql)) {
            $_SESSION['message'] = "Produit mis à jour avec succès!";
            $_SESSION['message_type'] = "success";
        } else {
            $_SESSION['message'] = "Erreur lors de la mise à jour: " . $conn->error;
            $_SESSION['message_type'] = "danger";
        }
    }
    
    // Suppression d'un produit
    if (isset($_POST['delete_product'])) {
        $id = intval($_POST['id']);
        
        $sql = "DELETE FROM products WHERE id = $id";
        
        if ($conn->query($sql)) {
            $_SESSION['message'] = "Produit supprimé avec succès!";
            $_SESSION['message_type'] = "success";
        } else {
            $_SESSION['message'] = "Erreur lors de la suppression: " . $conn->error;
            $_SESSION['message_type'] = "danger";
        }
    }
    
    header("Location: admin.php");
    exit;
}

// Récupérer tous les produits
$sql = "SELECT * FROM products ORDER BY date_added DESC";
$result = $conn->query($sql);
$products = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
}

// Récupérer les catégories uniques pour le filtre
$categories = [];
$catResult = $conn->query("SELECT DISTINCT category FROM products");
if ($catResult->num_rows > 0) {
    while($row = $catResult->fetch_assoc()) {
        $categories[] = $row['category'];
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - OUEST DEAL</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        :root {
            --primary: #FF6B00;
            --secondary: #2b77e7;
            --light: #f8f9fa;
            --dark: #212529;
            --success: #28a745;
            --danger: #dc3545;
            --warning: #ffc107;
            --info: #17a2b8;
            --transition: all 0.3s ease;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: #f5f5f5;
            color: var(--dark);
        }

        .admin-container {
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar */
        .sidebar {
            width: 250px;
            background-color: var(--dark);
            color: white;
            padding: 20px 0;
            transition: var(--transition);
        }

        .sidebar-header {
            padding: 0 20px 20px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }

        .sidebar-header h2 {
            color: var(--primary);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .sidebar-menu {
            margin-top: 20px;
        }

        .menu-item {
            padding: 12px 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
            transition: var(--transition);
        }

        .menu-item:hover, .menu-item.active {
            background-color: rgba(255,255,255,0.1);
            color: var(--primary);
        }

        .menu-item i {
            width: 20px;
            text-align: center;
        }

        /* Main Content */
        .main-content {
            flex: 1;
            padding: 20px;
            overflow-x: auto;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 1px solid #ddd;
        }

        .header h1 {
            color: var(--primary);
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .user-info img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
        }

        /* Alert Messages */
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .alert-warning {
            background-color: #fff3cd;
            color: #856404;
            border: 1px solid #ffeeba;
        }

        .close-alert {
            background: none;
            border: none;
            font-size: 1.2rem;
            cursor: pointer;
        }

        /* Dashboard Cards */
        .dashboard-cards {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .card {
            background-color: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            transition: var(--transition);
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 15px rgba(0,0,0,0.1);
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        .card-icon {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }

        .card-icon.primary {
            background-color: rgba(67, 97, 238, 0.1);
            color: var(--primary);
        }

        .card-icon.success {
            background-color: rgba(40, 167, 69, 0.1);
            color: var(--success);
        }

        .card-icon.warning {
            background-color: rgba(255, 193, 7, 0.1);
            color: var(--warning);
        }

        .card-icon.danger {
            background-color: rgba(220, 53, 69, 0.1);
            color: var(--danger);
        }

        .card-title {
            font-size: 0.9rem;
            color: #6c757d;
            text-transform: uppercase;
            font-weight: 600;
        }

        .card-value {
            font-size: 1.8rem;
            font-weight: bold;
            margin: 5px 0;
        }

        .card-footer {
            font-size: 0.8rem;
            color: #6c757d;
        }

        /* Table Styles */
        .table-container {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            padding: 20px;
            overflow-x: auto;
        }

        .table-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .table-title {
            font-size: 1.2rem;
            font-weight: 600;
        }

        .btn {
            padding: 8px 15px;
            border-radius: 5px;
            border: none;
            cursor: pointer;
            font-weight: 500;
            transition: var(--transition);
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .btn-primary {
            background-color: var(--primary);
            color: white;
        }

        .btn-primary:hover {
            background-color: #e05d00;
            transform: translateY(-2px);
        }

        .btn-secondary {
            background-color: var(--secondary);
            color: white;
        }

        .btn-secondary:hover {
            background-color: #1a5fc7;
            transform: translateY(-2px);
        }

        .btn-danger {
            background-color: var(--danger);
            color: white;
        }

        .btn-danger:hover {
            background-color: #c82333;
            transform: translateY(-2px);
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #f8f9fa;
            font-weight: 600;
            color: #495057;
        }

        tr:hover {
            background-color: #f8f9fa;
        }

        .product-img {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 5px;
        }

        .badge {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .badge-primary {
            background-color: rgba(255, 107, 0, 0.1);
            color: var(--primary);
        }

        .badge-success {
            background-color: rgba(40, 167, 69, 0.1);
            color: var(--success);
        }

        .badge-warning {
            background-color: rgba(255, 193, 7, 0.1);
            color: var(--warning);
        }

        .badge-danger {
            background-color: rgba(220, 53, 69, 0.1);
            color: var(--danger);
        }

        .action-buttons {
            display: flex;
            gap: 5px;
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
            z-index: 1000;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .modal-content {
            background-color: white;
            border-radius: 10px;
            width: 100%;
            max-width: 800px;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            animation: modalFadeIn 0.3s ease-out;
        }

        .modal-header {
            padding: 20px;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-title {
            font-size: 1.3rem;
            font-weight: 600;
            color: var(--primary);
        }

        .close-modal {
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: #6c757d;
        }

        .modal-body {
            padding: 20px;
        }

        .modal-footer {
            padding: 15px 20px;
            border-top: 1px solid #eee;
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
        }

        .form-control {
            width: 100%;
            padding: 10px 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
            transition: var(--transition);
        }

        .form-control:focus {
            border-color: var(--primary);
            outline: none;
            box-shadow: 0 0 0 0.2rem rgba(255, 107, 0, 0.25);
        }

        textarea.form-control {
            min-height: 120px;
            resize: vertical;
        }

        .image-preview {
            width: 100%;
            height: 200px;
            background-color: #f8f9fa;
            border: 1px dashed #ddd;
            border-radius: 5px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 15px;
            overflow: hidden;
        }

        .image-preview img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }

        .image-preview-text {
            color: #6c757d;
            font-size: 0.9rem;
        }

        .form-row {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }

        .form-check {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .form-check-input {
            width: 18px;
            height: 18px;
        }

        /* Responsive */
        @media (max-width: 992px) {
            .admin-container {
                flex-direction: column;
            }

            .sidebar {
                width: 100%;
                position: static;
            }

            .form-row {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .dashboard-cards {
                grid-template-columns: 1fr 1fr;
            }

            .action-buttons {
                flex-direction: column;
            }

            .btn {
                width: 100%;
                justify-content: center;
            }
        }

        @media (max-width: 576px) {
            .dashboard-cards {
                grid-template-columns: 1fr;
            }

            .table-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }

            th, td {
                padding: 8px 10px;
                font-size: 0.9rem;
            }
        }

        /* Animations */
        @keyframes modalFadeIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="sidebar-header">
                <h2><i class="fas fa-store"></i> OUEST DEAL</h2>
            </div>
            <div class="sidebar-menu">
                <div class="menu-item active">
                    <i class="fas fa-box-open"></i>
                    <span>Produits</span>
                </div>
                <div class="menu-item">
                    <i class="fas fa-users"></i>
                    <span>Utilisateurs</span>
                </div>
                <div class="menu-item">
                    <i class="fas fa-chart-line"></i>
                    <span>Statistiques</span>
                </div>
                <div class="menu-item">
                    <i class="fas fa-cog"></i>
                    <span>Paramètres</span>
                </div>
               <a href="logout.php" class="menu-item">
            <i class="fas fa-sign-out-alt"></i>
            <span>Déconnexion</span>
        </a>
            </div>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <div class="header">
                <h1>Tableau de bord</h1>
                <div class="user-info">
                    <img src="https://ui-avatars.com/api/?name=Admin&background=FF6B00&color=fff" alt="Admin">
                    <span>Administrateur</span>
                </div>
            </div>

            <!-- Alert Messages -->
            <?php if (isset($_SESSION['message'])): ?>
                <div class="alert alert-<?php echo $_SESSION['message_type']; ?>">
                    <?php echo $_SESSION['message']; ?>
                    <button class="close-alert">&times;</button>
                </div>
                <?php unset($_SESSION['message']); unset($_SESSION['message_type']); ?>
            <?php endif; ?>

            <!-- Dashboard Cards -->
            <div class="dashboard-cards">
                <div class="card">
                    <div class="card-header">
                        <div>
                            <div class="card-title">Produits</div>
                            <div class="card-value"><?php echo count($products); ?></div>
                        </div>
                        <div class="card-icon primary">
                            <i class="fas fa-box-open"></i>
                        </div>
                    </div>
                    <div class="card-footer">
                        <i class="fas fa-arrow-up text-success"></i> 5% depuis hier
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <div>
                            <div class="card-title">Vues totales</div>
                            <div class="card-value">
                                <?php 
                                    $totalViews = array_sum(array_column($products, 'views'));
                                    echo number_format($totalViews, 0, ',', ' ');
                                ?>
                            </div>
                        </div>
                        <div class="card-icon success">
                            <i class="fas fa-eye"></i>
                        </div>
                    </div>
                    <div class="card-footer">
                        <i class="fas fa-arrow-up text-success"></i> 12% depuis hier
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <div>
                            <div class="card-title">Produits vedettes</div>
                            <div class="card-value">
                                <?php 
                                    $featuredCount = count(array_filter($products, function($p) { return $p['is_featured'] == 1; }));
                                    echo $featuredCount;
                                ?>
                            </div>
                        </div>
                        <div class="card-icon warning">
                            <i class="fas fa-star"></i>
                        </div>
                    </div>
                    <div class="card-footer">
                        <i class="fas fa-arrow-down text-danger"></i> 2% depuis hier
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <div>
                            <div class="card-title">Nouveaux produits</div>
                            <div class="card-value">
                                <?php 
                                    $newProducts = count(array_filter($products, function($p) { 
                                        return strtotime($p['date_added']) > strtotime('-7 days'); 
                                    }));
                                    echo $newProducts;
                                ?>
                            </div>
                        </div>
                        <div class="card-icon danger">
                            <i class="fas fa-bell"></i>
                        </div>
                    </div>
                    <div class="card-footer">
                        <i class="fas fa-arrow-up text-success"></i> 8% depuis hier
                    </div>
                </div>
            </div>

            <!-- Products Table -->
            <div class="table-container">
                <div class="table-header">
                    <h3 class="table-title">Gestion des produits</h3>
                    <button class="btn btn-primary" onclick="showAddProductModal()">
                        <i class="fas fa-plus"></i> Ajouter un produit
                    </button>
                </div>

                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Image</th>
                            <th>Nom</th>
                            <th>Catégorie</th>
                            <th>Prix</th>
                            <th>Statut</th>
                            <th>Vues</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($products as $product): ?>
                        <tr>
                            <td><?php echo $product['id']; ?></td>
                            <td>
                                <img src="<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['title']); ?>" class="product-img">
                            </td>
                            <td><?php echo htmlspecialchars($product['title']); ?></td>
                            <td><?php echo htmlspecialchars($product['category']); ?></td>
                            <td><?php echo number_format($product['price'], 0, ',', ' '); ?> XOF</td>
                            <td>
                                <?php if ($product['is_featured'] == 1): ?>
                                    <span class="badge badge-success">Vedette</span>
                                <?php else: ?>
                                    <span class="badge badge-primary">Standard</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo $product['views']; ?></td>
                            <td>
                                <div class="action-buttons">
                                    <button class="btn btn-secondary" onclick="showEditProductModal(<?php echo $product['id']; ?>)">
                                        <i class="fas fa-edit"></i> Modifier
                                    </button>
                                    <button class="btn btn-danger" onclick="confirmDeleteProduct(<?php echo $product['id']; ?>)">
                                        <i class="fas fa-trash"></i> Supprimer
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Add/Edit Product Modal -->
    <div class="modal" id="product-modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title" id="modal-product-title">Ajouter un produit</h3>
                <button class="close-modal">&times;</button>
            </div>
            <form id="product-form" method="POST" action="admin.php">
                <input type="hidden" id="product-id" name="id" value="">
                <input type="hidden" id="action-type" name="add_product" value="1">
                
                <div class="modal-body">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="product-title">Titre du produit</label>
                            <input type="text" id="product-title" name="title" class="form-control" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="product-category">Catégorie</label>
                            <select id="product-category" name="category" class="form-control" required>
                                <option value="">Sélectionnez une catégorie</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?php echo htmlspecialchars($cat); ?>"><?php echo htmlspecialchars($cat); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="product-price">Prix (XOF)</label>
                            <input type="number" id="product-price" name="price" class="form-control" step="0.01" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="product-tag">Tag</label>
                            <input type="text" id="product-tag" name="tag" class="form-control" required>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="product-location">Localisation</label>
                            <input type="text" id="product-location" name="location" class="form-control" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="product-telephone">Téléphone</label>
                            <input type="text" id="product-telephone" name="telephone" class="form-control" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="product-image">URL de l'image</label>
                        <div class="image-preview" id="image-preview">
                            <span class="image-preview-text">Aperçu de l'image</span>
                        </div>
                        <input type="text" id="product-image" name="image" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="product-description">Description</label>
                        <textarea id="product-description" name="description" class="form-control" required></textarea>
                    </div>
                    
                    <div class="form-check">
                        <input type="checkbox" id="product-featured" name="is_featured" class="form-check-input">
                        <label for="product-featured">Produit vedette</label>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" onclick="closeModal()">Annuler</button>
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal" id="delete-modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Confirmer la suppression</h3>
                <button class="close-modal">&times;</button>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir supprimer ce produit ? Cette action est irréversible.</p>
            </div>
            <div class="modal-footer">
                <form id="delete-form" method="POST" action="admin.php">
                    <input type="hidden" id="delete-id" name="id" value="">
                    <input type="hidden" name="delete_product" value="1">
                    <button type="button" class="btn btn-secondary" onclick="closeModal()">Annuler</button>
                    <button type="submit" class="btn btn-danger">Supprimer</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Gestion des modals
        function showAddProductModal() {
            document.getElementById('modal-product-title').textContent = 'Ajouter un produit';
            document.getElementById('product-form').reset();
            document.getElementById('action-type').name = 'add_product';
            document.getElementById('product-id').value = '';
            document.getElementById('image-preview').innerHTML = '<span class="image-preview-text">Aperçu de l\'image</span>';
            openModal('product-modal');
        }

        function showEditProductModal(productId) {
            fetch(`get_product.php?id=${productId}`)
                .then(response => response.json())
                .then(product => {
                    document.getElementById('modal-product-title').textContent = 'Modifier le produit';
                    document.getElementById('product-id').value = product.id;
                    document.getElementById('product-title').value = product.title;
                    document.getElementById('product-description').value = product.description;
                    document.getElementById('product-price').value = product.price;
                    document.getElementById('product-category').value = product.category;
                    document.getElementById('product-tag').value = product.tag;
                    document.getElementById('product-location').value = product.location;
                    document.getElementById('product-image').value = product.image;
                    document.getElementById('product-telephone').value = product.telephone;
                    document.getElementById('product-featured').checked = product.is_featured == 1;
                    
                    // Afficher l'aperçu de l'image
                    const imagePreview = document.getElementById('image-preview');
                    if (product.image) {
                        imagePreview.innerHTML = `<img src="${product.image}" alt="${product.title}">`;
                    } else {
                        imagePreview.innerHTML = '<span class="image-preview-text">Aucune image</span>';
                    }
                    
                    document.getElementById('action-type').name = 'update_product';
                    openModal('product-modal');
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Erreur lors du chargement du produit');
                });
        }

        function confirmDeleteProduct(productId) {
            document.getElementById('delete-id').value = productId;
            openModal('delete-modal');
        }

        function openModal(modalId) {
            document.getElementById(modalId).style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }

        function closeModal() {
            document.querySelectorAll('.modal').forEach(modal => {
                modal.style.display = 'none';
            });
            document.body.style.overflow = 'auto';
        }

        // Fermer les modals en cliquant à l'extérieur
        window.addEventListener('click', function(event) {
            if (event.target.classList.contains('modal')) {
                closeModal();
            }
        });

        // Fermer les alertes
        document.querySelectorAll('.close-alert').forEach(btn => {
            btn.addEventListener('click', function() {
                this.parentElement.style.display = 'none';
            });
        });

        // Aperçu de l'image en temps réel
        document.getElementById('product-image').addEventListener('input', function() {
            const imageUrl = this.value.trim();
            const preview = document.getElementById('image-preview');
            
            if (imageUrl) {
                // Créer une image pour tester si elle est valide
                const img = new Image();
                img.onload = function() {
                    preview.innerHTML = `<img src="${imageUrl}" alt="Aperçu">`;
                };
                img.onerror = function() {
                    preview.innerHTML = '<span class="image-preview-text">URL d\'image invalide</span>';
                };
                img.src = imageUrl;
            } else {
                preview.innerHTML = '<span class="image-preview-text">Aperçu de l\'image</span>';
            }
        });
    </script>
</body>
</html>