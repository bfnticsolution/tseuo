<?php
include 'config.php';

// Récupérer les produits depuis la base de données
$sqlProducts = "SELECT * FROM products";
$resultProducts = $conn->query($sqlProducts);

// Récupérer les catégories depuis la nouvelle table
$sqlHomeCategories = "SELECT * FROM home_categories";
$resultHomeCategories = $conn->query($sqlHomeCategories);

if ($resultProducts === false || $resultHomeCategories === false) {
    die("Erreur dans la requête SQL : " . $conn->error);
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OUEST DEAL | Vente & Location en Afrique de l'Ouest</title>
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
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: var(--dark);
            background-color: #f5f5f5;
        }
        a {
            text-decoration: none;
            color: inherit;
        }
        img {
            max-width: 100%;
            height: auto;
        }
        /* Header */
        header {
            background-color: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 5%;
            max-width: 1400px;
            margin: 0 auto;
            position: relative;
        }
        .logo {
            display: flex;
            align-items: center;
        }
        .logo img {
            height: 50px;
            width: auto;
        }
        .logo h1 {
            color: var(--primary);
            margin-left: 10px;
            font-size: 1.5rem;
        }
        .nav-links {
            display: flex;
            list-style: none;
            transition: var(--transition);
        }
        .nav-links li {
            margin-left: 2rem;
        }
        .nav-links a {
            font-weight: 500;
            transition: color 0.3s;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .nav-links a:hover {
            color: var(--primary);
        }
        .cta-button {
            background-color: var(--primary);
            color: white;
            padding: 0.5rem 1.5rem;
            border-radius: 5px;
            font-weight: bold;
            transition: var(--transition);
        }
        .cta-button:hover {
            background-color: #e05d00;
            transform: translateY(-2px);
        }
        .menu-toggle {
            display: none;
            flex-direction: column;
            justify-content: space-between;
            width: 30px;
            height: 21px;
            cursor: pointer;
        }
        .menu-toggle span {
            display: block;
            height: 3px;
            width: 100%;
            background-color: var(--dark);
            border-radius: 3px;
            transition: var(--transition);
        }
        /* Hero Section */
        .hero {
            background: linear-gradient(rgba(2, 78, 122, 0.7), rgba(241, 99, 4, 0.7)), url('https://www2.0zz0.com/2025/06/27/08/377184974.png');
            background-size: cover;
            background-position: center;
            min-height: 60vh;
            display: flex;
            align-items: center;
            text-align: center;
            color: white;
            padding: 2rem;
            position: relative;
        }
        .hero-content {
            max-width: 800px;
            margin: 0 auto;
            padding: 2rem;
            background-color: rgba(0,0,0,0.5);
            border-radius: 10px;
        }
        .hero h1 {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            line-height: 1.3;
        }
        .hero-subtitle {
            font-size: 1.2rem;
            margin-bottom: 2rem;
            display: block;
        }
        .hero-cta {
            display: flex;
            justify-content: center;
            gap: 1rem;
            margin-bottom: 2rem;
        }
        .cta-primary {
            background-color: var(--primary);
            color: white;
            padding: 0.8rem 2rem;
            border-radius: 5px;
            font-weight: bold;
        }
        .cta-secondary {
            background-color: transparent;
            color: white;
            border: 2px solid white;
            padding: 0.8rem 2rem;
            border-radius: 5px;
            font-weight: bold;
        }
        .trust-badges {
            display: flex;
            justify-content: center;
            gap: 1.5rem;
            margin-top: 1rem;
            flex-wrap: wrap;
        }
        .badge {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.9rem;
        }
        /* Comment ça marche */
        .how-it-works {
            padding: 4rem 5%;
            background-color: white;
        }
        .section-title {
            text-align: center;
            margin-bottom: 3rem;
        }
        .section-title h2 {
            font-size: 2rem;
            color: var(--secondary);
            margin-bottom: 0.5rem;
        }
        .section-title p {
            color: #666;
        }
        .steps-container {
            display: flex;
            justify-content: space-between;
            max-width: 1200px;
            margin: 0 auto;
            gap: 2rem;
        }
        .step {
            flex: 1;
            text-align: center;
            padding: 2rem 1rem;
            background-color: var(--light);
            border-radius: 10px;
            transition: var(--transition);
        }
        .step:hover {
            transform: translateY(-10px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        .step-number {
            background-color: var(--primary);
            color: white;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            font-weight: bold;
        }
        .step-icon {
            font-size: 2rem;
            color: var(--primary);
            margin-bottom: 1rem;
            display: block;
        }
        .step h3 {
            margin-bottom: 1rem;
            color: var(--dark);
        }
        /* Barre de recherche */
        .search-container {
            padding: 2rem 5%;
            background-color: var(--secondary);
        }
        .search-bar {
            display: flex;
            max-width: 1000px;
            margin: 0 auto;
            background-color: white;
            border-radius: 5px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .search-bar input, .search-bar select {
            flex: 1;
            padding: 1rem;
            border: none;
            font-size: 1rem;
        }
        .search-bar select {
            appearance: none;
            background-color: white;
            background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 1rem center;
            background-size: 1em;
        }
        .search-bar button {
            background-color: var(--primary);
            color: white;
            border: none;
            padding: 0 1.5rem;
            cursor: pointer;
            font-weight: bold;
            transition: var(--transition);
        }
        .search-bar button:hover {
            background-color: #e05d00;
        }
        .search-suggestions {
            display: flex;
            justify-content: center;
            gap: 1rem;
            margin-top: 1rem;
            flex-wrap: wrap;
        }
        .search-suggestions a {
            color: white;
            font-size: 0.9rem;
        }
        .search-suggestions a:hover {
            text-decoration: underline;
        }
        /* Produits phares */
        .featured {
            padding: 4rem 5%;
            background-color: var(--light);
        }
        .product-filters {
            display: flex;
            justify-content: space-between;
            max-width: 1400px;
            margin: 0 auto 2rem;
            gap: 1rem;
            flex-wrap: wrap;
        }
        .filter-group {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .filter-group label {
            font-weight: 500;
        }
        .filter-group select,
        .filter-group input {
            padding: 0.5rem;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        #price-range {
            flex: 1;
            min-width: 150px;
        }
        #reset-filters {
            background-color: #ddd;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 5px;
            cursor: pointer;
        }
        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 2rem;
            max-width: 1400px;
            margin: 0 auto;
        }
        .product-card {
            background-color: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: var(--transition);
        }
        .product-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.15);
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
            padding: 0.3rem 0.8rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: bold;
        }
        .product-info {
            padding: 1.5rem;
        }
        .product-info h3 {
            margin-bottom: 0.5rem;
            font-size: 1.1rem;
        }
        .product-meta {
            display: flex;
            justify-content: space-between;
            color: #666;
            font-size: 0.9rem;
            margin-bottom: 1rem;
            flex-wrap: wrap;
            gap: 0.5rem;
        }
        .product-meta span {
            display: flex;
            align-items: center;
            gap: 0.3rem;
        }
        .product-price {
            font-size: 1.3rem;
            font-weight: bold;
            color: var(--primary);
            margin: 1rem 0;
        }
        .product-actions {
            display: flex;
            gap: 0.5rem;
        }
        .view-btn {
            background-color: var(--secondary);
            color: white;
            border: none;
            padding: 0.5rem;
            border-radius: 5px;
            cursor: pointer;
            flex: 1;
            transition: var(--transition);
        }
        .view-btn:hover {
            background-color: #1a5fc7;
        }
        .contact-btn {
            background-color: var(--primary);
            color: white;
            border: none;
            padding: 0.5rem;
            border-radius: 5px;
            cursor: pointer;
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            transition: var(--transition);
        }
        .contact-btn:hover {
            background-color: #e05d00;
        }
        /* Avis */
        .product-reviews {
            background-color: white;
            padding: 2rem;
            border-radius: 10px;
            margin-top: 2rem;
            max-width: 1400px;
            margin-left: auto;
            margin-right: auto;
        }
        .product-reviews h3 {
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .rating {
            background-color: var(--success);
            color: white;
            padding: 0.3rem 0.8rem;
            border-radius: 20px;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 0.3rem;
        }
        .reviews-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        .review {
            background-color: var(--light);
            padding: 1.5rem;
            border-radius: 8px;
        }
        .review-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.5rem;
        }
        .review-author {
            font-weight: bold;
        }
        .review-stars {
            color: #FFD700;
        }
        .review-content {
            font-style: italic;
            margin-bottom: 0.5rem;
        }
        .review-date {
            font-size: 0.8rem;
            color: #666;
        }
        .add-review {
            background-color: var(--primary);
            color: white;
            border: none;
            padding: 0.8rem 1.5rem;
            border-radius: 5px;
            cursor: pointer;
            display: block;
            margin: 0 auto;
            transition: var(--transition);
        }
        .add-review:hover {
            background-color: #e05d00;
        }
        /* Catégories */
        .categories {
            padding: 4rem 5%;
            background-color: white;
        }
        .category-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 2rem;
            max-width: 1400px;
            margin: 0 auto;
        }
        .category-card {
            background-color: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: var(--transition);
            border: 1px solid #eee;
        }
        .category-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.15);
        }
        .category-img {
            height: 180px;
            overflow: hidden;
        }
        .category-img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s;
        }
        .category-card:hover .category-img img {
            transform: scale(1.1);
        }
        .category-info {
            padding: 1.5rem;
        }
        .category-info h3 {
            margin-bottom: 0.5rem;
        }
        .category-info p {
            color: #666;
            margin-bottom: 1rem;
            font-size: 0.9rem;
        }
        .category-link {
            color: var(--primary);
            font-weight: bold;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: var(--transition);
        }
        .category-link:hover {
            gap: 1rem;
        }
        /* Newsletter */
        .newsletter {
            background-color: var(--secondary);
            color: white;
            padding: 4rem 5%;
            text-align: center;
        }
        .newsletter h2 {
            margin-bottom: 1rem;
        }
        .newsletter p {
            max-width: 600px;
            margin: 0 auto 2rem;
        }
        .newsletter-form {
            display: flex;
            max-width: 500px;
            margin: 0 auto;
        }
        .newsletter-form input {
            flex: 1;
            padding: 1rem;
            border: none;
            border-radius: 5px 0 0 5px;
            font-size: 1rem;
        }
        .newsletter-form button {
            background-color: var(--primary);
            color: white;
            border: none;
            padding: 0 1.5rem;
            border-radius: 0 5px 5px 0;
            cursor: pointer;
            font-weight: bold;
            transition: var(--transition);
        }
        .newsletter-form button:hover {
            background-color: #e05d00;
        }
        /* Footer */
        footer {
            background-color: var(--dark);
            color: white;
            padding: 4rem 5% 2rem;
        }
        .footer-content {
            max-width: 1400px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
        }
        .footer-column h3 {
            margin-bottom: 1.5rem;
            font-size: 1.2rem;
            color: var(--primary);
        }
        .footer-column ul {
            list-style: none;
        }
        .footer-column ul li {
            margin-bottom: 0.8rem;
        }
        .footer-column ul li a {
            color: #ddd;
            transition: color 0.3s;
        }
        .footer-column ul li a:hover {
            color: var(--primary);
        }
        .social-links {
            display: flex;
            margin-top: 1rem;
            gap: 0.8rem;
        }
        .social-links a {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            background-color: rgba(255,255,255,0.1);
            border-radius: 50%;
            color: white;
            transition: background-color 0.3s;
        }
        .social-links a:hover {
            background-color: var(--primary);
        }
        .language-switcher {
            position: relative;
            margin-top: 1rem;
        }
        #language-btn {
            background-color: rgba(255,255,255,0.1);
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 5px;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            cursor: pointer;
        }
        .language-dropdown {
            position: absolute;
            bottom: 100%;
            left: 0;
            background-color: white;
            border-radius: 5px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            padding: 0.5rem 0;
            width: 100%;
            display: none;
        }
        .language-dropdown li {
            padding: 0.5rem 1rem;
        }
        .language-dropdown a {
            color: var(--dark);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .language-dropdown a:hover {
            color: var(--primary);
        }
        .language-switcher:hover .language-dropdown {
            display: block;
        }
        .copyright {
            text-align: center;
            padding-top: 2rem;
            margin-top: 2rem;
            border-top: 1px solid rgba(255,255,255,0.1);
            color: #aaa;
            font-size: 0.9rem;
        }
        /* Responsive */
        @media (max-width: 1024px) {
            .nav-links {
                position: fixed;
                top: 80px;
                left: -100%;
                width: 80%;
                height: calc(100vh - 80px);
                background-color: white;
                flex-direction: column;
                align-items: center;
                padding: 2rem 0;
                box-shadow: 0 5px 10px rgba(0,0,0,0.1);
                z-index: 999;
            }
            .nav-links.active {
                left: 0;
            }
            .nav-links li {
                margin: 1.5rem 0;
            }
            .menu-toggle {
                display: flex;
            }
            .steps-container {
                flex-direction: column;
            }
            .step {
                margin-bottom: 2rem;
            }
        }
        @media (max-width: 768px) {
            .hero h1 {
                font-size: 2rem;
            }
            .hero-subtitle {
                font-size: 1rem;
            }
            .search-bar {
                flex-direction: column;
                background-color: transparent;
                gap: 0.5rem;
            }
            .search-bar input,
            .search-bar select,
            .search-bar button {
                width: 100%;
                border-radius: 5px;
            }
            .product-filters {
                flex-direction: column;
                gap: 1rem;
            }
            .filter-group {
                width: 100%;
            }
            .newsletter-form {
                flex-direction: column;
                gap: 0.5rem;
            }
            .newsletter-form input,
            .newsletter-form button {
                border-radius: 5px;
            }
        }
        @media (max-width: 480px) {
            .logo h1 {
                font-size: 1.2rem;
            }
            .hero-content {
                padding: 1.5rem;
            }
            .hero-cta {
                flex-direction: column;
                gap: 0.5rem;
            }
            .trust-badges {
                flex-direction: column;
                gap: 1rem;
                align-items: center;
            }
            .product-grid,
            .category-grid {
                grid-template-columns: 1fr;
            }
        }
        /* Accessibilité */
        @media (prefers-reduced-motion: reduce) {
            * {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
                scroll-behavior: auto !important;
            }
        }
        /* Animations */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate {
            animation: fadeIn 0.6s ease forwards;
        }
              /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.5);
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
        :root {
            --primary: #FF6B00;
            --secondary: #2b77e7;
            --light: #f8f9fa;
            --dark: #212529;
            --success: #28a745;
            --transition: all 0.3s ease;
        }
        /* Ajoutez ici le reste de vos styles CSS */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.5);
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
    <!-- Header -->
    <header>
        <nav class="navbar" role="navigation" aria-label="Menu principal">
            <div class="logo">
                <img src="https://www2.0zz0.com/2025/06/27/08/377184974.png" alt="OUEST DEAL Logo" width="50" height="50">
                <h1>OUEST DEAL</h1>
            </div>
            <div class="menu-toggle" id="mobile-menu" aria-label="Menu mobile" aria-expanded="false">
                <span></span>
                <span></span>
                <span></span>
            </div>
            <ul class="nav-links" id="nav-links">
                <li><a href="#"><i class="fas fa-home"></i> Accueil</a></li>
                <li><a href="immo.php"><i class="fas fa-building"></i> Immobilier</a></li>
                <li><a href="vehicules.php"><i class="fas fa-car"></i> Véhicules</a></li>
                <li><a href="electromenager.php"><i class="fas fa-blender"></i> Électroménager</a></li>
                <li><a href="terrains.php"><i class="fas fa-tractor"></i> Terrains</a></li>
                <li><a href="electronique.php"><i class="fas fa-laptop"></i> Electronique</a></li>
                <li><a href="publier.php" class="cta-button"><i class="fas fa-plus-circle"></i> Publier une annonce</a></li>
            </ul>
        </nav>
    </header>

    <!-- Hero Section -->
    <section class="hero" role="banner">
        <div class="hero-content animate">
            <h1>Votre marché en ligne pour l'Afrique de l'Ouest</h1>
            <span class="hero-subtitle">+10 000 transactions sécurisées chaque mois • Paiements protégés • Expert local à votre service</span>
            <div class="hero-cta">
                <a href="#how-it-works" class="cta-button cta-primary">Comment ça marche ?</a>
                <a href="#featured" class="cta-button cta-secondary">Voir les offres</a>
            </div>
            <div class="trust-badges">
                <div class="badge"><i class="fas fa-shield-alt"></i> Garantie de sécurité</div>
                <div class="badge"><i class="fas fa-headset"></i> Support 24/7</div>
                <div class="badge"><i class="fas fa-map-marker-alt"></i> Couverture régionale</div>
            </div>
        </div>
    </section>

    <!-- Comment ça marche -->
    <section class="how-it-works" id="how-it-works">
        <div class="section-title">
            <h2>Comment utiliser OUEST DEAL</h2>
            <p>3 étapes simples pour trouver ou proposer une offre</p>
        </div>
        <div class="steps-container">
            <div class="step animate" style="animation-delay: 0.1s;">
                <div class="step-number">1</div>
                <i class="fas fa-search step-icon"></i>
                <h3>Trouvez ou proposez</h3>
                <p>Parcourez nos catégories ou publiez votre annonce en 2 minutes</p>
            </div>
            <div class="step animate" style="animation-delay: 0.2s;">
                <div class="step-number">2</div>
                <i class="fas fa-comments step-icon"></i>
                <h3>Contactez directement</h3>
                <p>Échangez avec le vendeur ou l'acheteur via notre messagerie sécurisée</p>
            </div>
            <div class="step animate" style="animation-delay: 0.3s;">
                <div class="step-number">3</div>
                <i class="fas fa-handshake step-icon"></i>
                <h3>Finalisez en sécurité</h3>
                <p>Utilisez notre service de paiement sécurisé ou rencontrez-vous en personne</p>
            </div>
        </div>
    </section>

    <!-- Barre de recherche -->
    <section class="search-container">
        <div class="search-bar">
            <input type="text" placeholder="Que recherchez-vous ?" id="main-search" aria-label="Rechercher des annonces">
            <select id="search-category" aria-label="Catégorie">
                <option value="all">Toutes catégories</option>
                <option value="immobilier">Immobilier</option>
                <option value="vehicules">Véhicules</option>
                <option value="electromenager">Électroménager</option>
                <option value="terrains">Terrains</option>
                <option value="electronique">Electronique</option>
            </select>
            <select id="search-location" aria-label="Localisation">
                <option value="all">Toute l'Afrique de l'Ouest</option>
                <option value="bf">Burkina Faso</option>
                <option value="ci">Côte d'Ivoire</option>
                <option value="sn">Sénégal</option>
                <option value="ml">Mali</option>
                <option value="ne">Niger</option>
            </select>
            <button id="search-button"><i class="fas fa-search"></i> Rechercher</button>
        </div>
        <div class="search-suggestions">
            <span>Suggestions :</span>
            <a href="#">Appartement Ouaga</a>
            <a href="#">Toyota d'occasion</a>
            <a href="#">Terrain agricole</a>
            <a href="#">Réfrigérateur neuf</a>
        </div>
    </section>

    <!-- Produits phares -->
    <section class="featured" id="featured">
        <div class="section-title">
            <h2>Produits Phares</h2>
            <p>Découvrez nos meilleures offres du moment</p>
        </div>
        <div class="product-filters">
            <div class="filter-group">
                <label for="price-range">Prix :</label>
                <input type="range" id="price-range" min="0" max="10000000" step="50000">
                <span id="price-value">0 - 10 000 000 XOF</span>
            </div>
            <div class="filter-group">
                <label for="location-filter">Localisation :</label>
                <select id="location-filter">
                    <option value="all">Toutes</option>
                    <option value="ouaga">Ouagadougou</option>
                    <option value="bobo">Bobo-Dioulasso</option>
                    <option value="koudougou">Koudougou</option>
                    <option value="ouahigouya">Ouahigouya</option>
                </select>
            </div>
            <div class="filter-group">
                <label for="sort-by">Trier par :</label>
                <select id="sort-by">
                    <option value="newest">Plus récent</option>
                    <option value="price-asc">Prix croissant</option>
                    <option value="price-desc">Prix décroissant</option>
                    <option value="popular">Plus populaires</option>
                </select>
            </div>
            <button id="reset-filters">Réinitialiser</button>
        </div>
        <div class="product-grid">
            <?php
            if ($resultProducts->num_rows > 0) {
                while($row = $resultProducts->fetch_assoc()) {
                    echo '<div class="product-card animate" style="animation-delay: 0.1s;">';
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
                echo "<p>Aucun produit trouvé.</p>";
            }
            ?>
        </div>
    </section>

    <!-- Modal -->
    <div id="productModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <div id="modalContent"></div>
        </div>
    </div>

    <!-- Catégories -->
    <section class="categories">
        <div class="section-title">
            <h2>Nos Catégories</h2>
            <p>Parcourez nos principales catégories de produits</p>
        </div>
        <div class="category-grid">
            <?php
            if ($resultHomeCategories->num_rows > 0) {
                while($row = $resultHomeCategories->fetch_assoc()) {
                    echo '<div class="category-card animate" style="animation-delay: 0.1s;">';
                    echo '<div class="category-img">';
                    echo '<img src="image/' . htmlspecialchars($row["image"]) . '" alt="' . htmlspecialchars($row["nom"]) . '" loading="lazy">';
                    echo '</div>';
                    echo '<div class="category-info">';
                    echo '<h3>' . htmlspecialchars($row["nom"]) . '</h3>';
                    echo '<p>' . htmlspecialchars($row["description"]) . '</p>';
                    echo '<a href="' . htmlspecialchars($row["lien"]) . '" class="category-link">Voir plus <i class="fas fa-arrow-right"></i></a>';
                    echo '</div>';
                    echo '</div>';
                }
            } else {
                echo "<p>Aucune catégorie trouvée.</p>";
            }
            ?>
        </div>
    </section>

    <!-- Newsletter -->
    <section class="newsletter">
        <h2>Restez informé</h2>
        <p>Abonnez-vous à notre newsletter pour recevoir les meilleures offres en avant-première</p>
        <form class="newsletter-form">
            <input type="email" placeholder="Votre email" required aria-label="Votre adresse email">
            <button type="submit">S'abonner</button>
        </form>
    </section>

    <!-- Footer -->
    <footer>
        <div class="footer-content">
            <div class="footer-column">
                <h3>OUEST DEAL</h3>
                <p>La plateforme de confiance pour acheter, vendre et louer en Afrique de l'Ouest.</p>
                <div class="social-links">
                    <a href="#" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" aria-label="WhatsApp"><i class="fab fa-whatsapp"></i></a>
                    <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                    <a href="#" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                </div>
            </div>
            <div class="footer-column">
                <h3>Catégories</h3>
                <ul>
                    <li><a href="immo.php">Immobilier</a></li>
                    <li><a href="vehicules.php">Véhicules</a></li>
                    <li><a href="electromenager.php">Électroménager</a></li>
                    <li><a href="terrains.php">Terrains</a></li>
                    <li><a href="electronique.php">Electronique</a></li>
                </ul>
            </div>
            <div class="footer-column">
                <h3>Entreprise</h3>
                <ul>
                    <li><a href="#">À propos</a></li>
                    <li><a href="#">Comment ça marche</a></li>
                    <li><a href="#">Devenir Partenaire</a></li>
                    <li><a href="#">Carrières</a></li>
                    <li><a href="#">Contact</a></li>
                </ul>
            </div>
            <div class="footer-column">
                <h3>Assistance</h3>
                <ul>
                    <li><a href="#">Centre d'aide</a></li>
                    <li><a href="#">Sécurité</a></li>
                    <li><a href="#">Conditions d'utilisation</a></li>
                    <li><a href="#">Politique de confidentialité</a></li>
                </ul>
            </div>
        </div>
        <div class="copyright">
            <p>&copy; 2025 OUEST DEAL. Tous droits réservés.</p>
        </div>
    </footer>

    <!-- Scripts -->
    <script>
        // Gestion du menu hamburger
        const menuToggle = document.getElementById('mobile-menu');
        const navLinks = document.getElementById('nav-links');

        menuToggle.addEventListener('click', () => {
            navLinks.classList.toggle('active');
            const isExpanded = navLinks.classList.contains('active');
            menuToggle.setAttribute('aria-expanded', isExpanded);
        });

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

        // Fermer le modal lorsque l'utilisateur clique en dehors du modal
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
