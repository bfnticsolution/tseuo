<?php
session_start();
require 'config.php';

// Vérification de l'authentification
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: admin_login.php');
    exit;
}

// Fonction pour récupérer les produits
function getProducts($conn) {
    $products = [];
    $query = "SELECT * FROM products";
    
    try {
        $result = $conn->query($query);
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $products[] = $row;
            }
        }
    } catch (Exception $e) {
        error_log("Erreur dans getProducts(): " . $e->getMessage());
    }
    
    return $products;
}

// Fonction pour supprimer un produit
function deleteProduct($conn, $id) {
    $query = "DELETE FROM products WHERE id = ?";
    try {
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    } catch (Exception $e) {
        error_log("Erreur dans deleteProduct(): " . $e->getMessage());
        return false;
    }
}

// Gestion des actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'delete' && isset($_POST['id'])) {
        if (deleteProduct($conn, $_POST['id'])) {
            $_SESSION['message'] = "Produit supprimé avec succès";
        } else {
            $_SESSION['error'] = "Erreur lors de la suppression du produit";
        }
        header('Location: admin_products.php');
        exit;
    }
}

$products = getProducts($conn);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord | OUEST DEAL</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
    <style>
        :root {
            --primary: #4361ee;
            --primary-dark: #3a56d4;
            --secondary: #2b2d42;
            --accent: #f72585;
            --success: #4cc9f0;
            --danger: #ef233c;
            --warning: #ffc107;
            --light: #f8f9fa;
            --dark: #212529;
            --gray: #6c757d;
            --light-gray: #e9ecef;
        }
        
        body {
            background-color: #f5f7fa;
            font-family: 'Poppins', sans-serif;
            color: var(--secondary);
        }
        
        .admin-sidebar {
            background-color: var(--secondary);
            color: white;
            min-height: 100vh;
            position: fixed;
            width: 250px;
            box-shadow: 2px 0 15px rgba(0,0,0,0.1);
            transition: all 0.3s;
        }
        
        .admin-main {
            margin-left: 250px;
            padding: 30px;
            transition: all 0.3s;
        }
        
        .sidebar-brand {
            padding: 1.5rem 1rem;
            font-size: 1.3rem;
            font-weight: bold;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .sidebar-brand i {
            color: var(--accent);
            font-size: 1.5rem;
        }
        
        .sidebar-nav {
            padding: 0;
            list-style: none;
        }
        
        .sidebar-nav li a {
            color: rgba(255,255,255,0.8);
            padding: 0.75rem 1rem;
            display: block;
            text-decoration: none;
            transition: all 0.3s;
            border-left: 3px solid transparent;
            font-size: 0.95rem;
        }
        
        .sidebar-nav li a:hover, .sidebar-nav li a.active {
            color: white;
            background-color: rgba(255,255,255,0.05);
            border-left: 3px solid var(--accent);
        }
        
        .sidebar-nav li a i {
            margin-right: 0.75rem;
            width: 20px;
            text-align: center;
            font-size: 1.1rem;
        }
        
        .admin-header {
            background-color: white;
            padding: 1.25rem 1.5rem;
            border-radius: 10px;
            margin-bottom: 1.5rem;
            box-shadow: 0 4px 20px rgba(0,0,0,0.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .admin-title {
            margin: 0;
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--secondary);
        }
        
        .admin-profile {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .admin-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: var(--primary);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 1.1rem;
        }
        
        .admin-name {
            font-weight: 500;
        }
        
        /* Cartes de statistiques */
        .stat-card {
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            color: white;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s, box-shadow 0.3s;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        }
        
        .stat-card i {
            font-size: 2.5rem;
            opacity: 0.8;
        }
        
        .stat-card .stat-value {
            font-size: 2rem;
            font-weight: bold;
            margin: 0.5rem 0;
        }
        
        .stat-card .stat-label {
            opacity: 0.9;
            font-size: 0.95rem;
        }
        
        /* Cartes de contenu */
        .content-card {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.05);
            margin-bottom: 30px;
            overflow: hidden;
        }
        
        .card-header {
            background-color: white;
            border-bottom: 1px solid rgba(0,0,0,0.05);
            padding: 1.25rem 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .card-title {
            margin: 0;
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--secondary);
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .card-title i {
            color: var(--primary);
        }
        
        .card-body {
            padding: 1.5rem;
        }
        
        /* Tableaux */
        .table-responsive {
            border-radius: 10px;
            overflow: hidden;
        }
        
        .table {
            margin-bottom: 0;
        }
        
        .table thead th {
            background-color: var(--primary);
            color: white;
            padding: 1rem 1.5rem;
            font-weight: 500;
        }
        
        .table tbody td {
            padding: 1rem 1.5rem;
            vertical-align: middle;
            border-top: 1px solid #e9ecef;
        }
        
        .table-hover tbody tr:hover {
            background-color: rgba(67, 97, 238, 0.05);
        }
        
        .product-img {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 4px;
        }
        
        /* Badges */
        .badge {
            font-weight: 500;
            padding: 0.5em 0.8em;
            font-size: 0.85em;
            border-radius: 50px;
        }
        
        .badge-published {
            background-color: rgba(40, 167, 69, 0.1);
            color: #28a745;
        }
        
        .badge-pending {
            background-color: rgba(255, 193, 7, 0.1);
            color: #ffc107;
        }
        
        .badge-approved {
            background-color: rgba(40, 167, 69, 0.1);
            color: #28a745;
        }
        
        .badge-rejected {
            background-color: rgba(220, 53, 69, 0.1);
            color: #dc3545;
        }
        
        /* Boutons */
        .btn-custom {
            background-color: var(--primary);
            color: white;
            border: none;
            padding: 0.5rem 1.25rem;
            border-radius: 6px;
            font-weight: 500;
            transition: all 0.3s;
        }
        
        .btn-custom:hover {
            background-color: var(--primary-dark);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(67, 97, 238, 0.3);
        }
        
        .btn-outline-custom {
            border: 1px solid var(--primary);
            color: var(--primary);
            background-color: transparent;
        }
        
        .btn-outline-custom:hover {
            background-color: var(--primary);
            color: white;
        }
        
        /* Animation */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .fade-in {
            animation: fadeIn 0.5s ease-out;
        }
        
        /* Responsive */
        @media (max-width: 992px) {
            .admin-sidebar {
                width: 70px;
                overflow: hidden;
            }
            
            .admin-sidebar:hover {
                width: 250px;
            }
            
            .sidebar-brand span {
                display: none;
            }
            
            .admin-sidebar:hover .sidebar-brand span {
                display: inline;
            }
            
            .sidebar-nav li a span {
                display: none;
            }
            
            .admin-sidebar:hover .sidebar-nav li a span {
                display: inline;
            }
            
            .admin-main {
                margin-left: 70px;
            }
            
            .admin-sidebar:hover + .admin-main {
                margin-left: 250px;
            }
        }
        
        @media (max-width: 768px) {
            .stat-card {
                padding: 1rem;
            }
            
            .stat-card .stat-value {
                font-size: 1.5rem;
            }
            
            .table thead th, .table tbody td {
                padding: 0.75rem;
            }
        }
    </style>
</head>
<body>
    <div class="admin-sidebar">
        <div class="sidebar-brand">
            <i class="fas fa-crown"></i>
            <span>OUEST DEAL</span>
        </div>
        <ul class="sidebar-nav">
            <li><a href="admin.php" class="active"><i class="fas fa-tachometer-alt"></i> <span>Tableau de bord</span></a></li>
            <li><a href="admin_products.php"><i class="fas fa-box-open"></i> <span>Produits</span></a></li>
            <li><a href="admin_requests.php"><i class="fas fa-clipboard-list"></i> <span>Demandes</span></a></li>
            <?php if ($admin_role === 'superadmin'): ?>
                <li><a href="admin_users.php"><i class="fas fa-users"></i> <span>Utilisateurs</span></a></li>
                <li><a href="admin_settings.php"><i class="fas fa-cog"></i> <span>Paramètres</span></a></li>
            <?php endif; ?>
            <li><a href="admin_logout.php"><i class="fas fa-sign-out-alt"></i> <span>Déconnexion</span></a></li>
        </ul>
    </div>

    <div class="admin-main">
        <div class="admin-header">
            <h1 class="admin-title">Tableau de bord</h1>
            <div class="admin-profile">
                <span class="admin-name"><?= htmlspecialchars($admin_name) ?></span>
                <div class="admin-avatar">
                    <?= strtoupper(substr($admin_name, 0, 1)) ?>
                </div>
            </div>
        </div>

        <div class="row fade-in">
            <!-- Carte Statistique Produits Totaux -->
            <div class="col-md-3">
                <div class="stat-card" style="background: linear-gradient(135deg, var(--primary), #5a72e6);">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="stat-value"><?= $stats['total_products'] ?></div>
                            <div class="stat-label">Produits Totaux</div>
                        </div>
                        <i class="fas fa-boxes"></i>
                    </div>
                </div>
            </div>
            
            <!-- Carte Statistique Produits Publiés -->
            <div class="col-md-3">
                <div class="stat-card" style="background: linear-gradient(135deg, #4cc9f0, #3ab0d5);">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="stat-value"><?= $stats['published_products'] ?></div>
                            <div class="stat-label">Produits Publiés</div>
                        </div>
                        <i class="fas fa-check-circle"></i>
                    </div>
                </div>
            </div>
            
            <!-- Carte Statistique Produits en Attente -->
            <div class="col-md-3">
                <div class="stat-card" style="background: linear-gradient(135deg, #ffc107, #e6a800);">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="stat-value"><?= $stats['pending_products'] ?></div>
                            <div class="stat-label">Produits en Attente</div>
                        </div>
                        <i class="fas fa-clock"></i>
                    </div>
                </div>
            </div>
            
            <!-- Carte Statistique Demandes en Attente -->
            <div class="col-md-3">
                <div class="stat-card" style="background: linear-gradient(135deg, #f72585, #e5177b);">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="stat-value"><?= $stats['pending_requests'] ?></div>
                            <div class="stat-label">Demandes en Attente</div>
                        </div>
                        <i class="fas fa-clipboard-list"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="row fade-in">
            <!-- Derniers Produits -->
            <div class="col-lg-6">
                <div class="content-card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-box-open"></i> Derniers Produits</h3>
                        <a href="admin_products.php" class="btn btn-sm btn-outline-custom">Voir tous</a>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Produit</th>
                                        <th>Prix</th>
                                        <th>Statut</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($latest_products as $product): ?>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <?php if (!empty($product['image'])): ?>
                                                        <img src="<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['title']) ?>" class="product-img me-3">
                                                    <?php else: ?>
                                                        <div class="product-img bg-light text-center me-3 d-flex align-items-center justify-content-center">
                                                            <i class="fas fa-box text-muted"></i>
                                                        </div>
                                                    <?php endif; ?>
                                                    <div>
                                                        <div class="fw-bold"><?= htmlspecialchars($product['title']) ?></div>
                                                        <small class="text-muted">ID: <?= $product['id'] ?></small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><?= number_format($product['price'], 0, ',', ' ') ?> XOF</td>
                                            <td>
                                                <span class="badge <?= $product['status'] == 1 ? 'badge-published' : 'badge-pending' ?>">
                                                    <?= $product['status'] == 1 ? 'Publié' : 'En attente' ?>
                                                </span>
                                            </td>
                                            <td><?= date('d/m/Y', strtotime($product['date_added'])) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Dernières Demandes -->
            <div class="col-lg-6">
                <div class="content-card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-clipboard-list"></i> Dernières Demandes</h3>
                        <a href="admin_requests.php" class="btn btn-sm btn-outline-custom">Voir tous</a>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Produit</th>
                                        <th>Demandeur</th>
                                        <th>Statut</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($latest_requests as $request): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($request['product_title'] ?? 'N/A') ?></td>
                                            <td><?= htmlspecialchars($request['user_name']) ?></td>
                                            <td>
                                                <?php if ($request['status'] == 'approved'): ?>
                                                    <span class="badge badge-approved">Approuvé</span>
                                                <?php elseif ($request['status'] == 'rejected'): ?>
                                                    <span class="badge badge-rejected">Rejeté</span>
                                                <?php else: ?>
                                                    <span class="badge badge-pending">En attente</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?= date('d/m/Y', strtotime($request['submitted_at'])) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Section Utilisateurs pour les superadmins -->
        <?php if ($admin_role === 'superadmin'): ?>
        <div class="row fade-in">
            <div class="col-12">
                <div class="content-card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-users"></i> Statistiques Utilisateurs</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="stat-card" style="background: linear-gradient(135deg, #7209b7, #5e08a0);">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <div class="stat-value"><?= $stats['total_users'] ?></div>
                                            <div class="stat-label">Utilisateurs Totaux</div>
                                        </div>
                                        <i class="fas fa-users"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="stat-card" style="background: linear-gradient(135deg, #3a0ca3, #2f0a8c);">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <div class="stat-value"><?= $stats['active_users'] ?></div>
                                            <div class="stat-label">Utilisateurs Actifs</div>
                                        </div>
                                        <i class="fas fa-user-check"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="stat-card" style="background: linear-gradient(135deg, #4361ee, #3a56d4);">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <div class="stat-value"><?= $stats['total_requests'] ?></div>
                                            <div class="stat-label">Demandes Totales</div>
                                        </div>
                                        <i class="fas fa-clipboard"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script>
        $(document).ready(function() {
            // Initialisation des DataTables
            $('.table').DataTable({
                responsive: true,
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/fr-FR.json'
                },
                dom: '<"top"f>rt<"bottom"lip><"clear">',
                pageLength: 5,
                lengthMenu: [5, 10, 20, 50],
                order: [[3, 'desc']]
            });
            
            // Animation au chargement
            $('.fade-in').css('opacity', '0');
            setTimeout(function() {
                $('.fade-in').css('opacity', '1');
            }, 100);
        });
    </script>
</body>
</html>