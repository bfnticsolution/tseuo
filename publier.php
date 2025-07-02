<?php
include 'config.php';
// Vérifier si le formulaire a été soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Créer le dossier uploads s'il n'existe pas
    $targetDir = "uploads/";
    if (!file_exists($targetDir)) {
        mkdir($targetDir, 0755, true);
    }

    // Récupérer les données du formulaire
    $title = $_POST['title'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $category = $_POST['category'];
    $location = $_POST['location'];
    $telephone = $_POST['telephone'];
    $condition_product = $_POST['condition'];
    $negotiable = isset($_POST['negotiable']) ? 1 : 0;
    
    // Gestion de l'upload de l'image
    $originalFileName = basename($_FILES["image"]["name"]);
    $fileType = pathinfo($originalFileName, PATHINFO_EXTENSION);
    $newFileName = md5(time() . $originalFileName) . '.' . $fileType;
    $targetFilePath = $targetDir . $newFileName;
    
    // Vérifier si le fichier est une image
    $allowTypes = array('jpg', 'png', 'jpeg', 'gif');
    if (in_array(strtolower($fileType), $allowTypes)) {
        // Upload du fichier sur le serveur
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFilePath)) {
            // Insérer les données dans la base de données
            $sql = "INSERT INTO publications (title, description, price, category, location, telephone, image, condition_product, negotiable) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssdsssssi", $title, $description, $price, $category, $location, $telephone, $newFileName, $condition_product, $negotiable);
            
            if ($stmt->execute()) {
                $success = "Votre produit a été soumis avec succès et est en attente de validation.";
            } else {
                $error = "Une erreur s'est produite lors de la soumission de votre produit.";
                // Supprimer l'image uploadée en cas d'échec
                if (file_exists($targetFilePath)) {
                    unlink($targetFilePath);
                }
            }
        } else {
            $error = "Désolé, une erreur s'est produite lors du téléchargement de votre image. Vérifiez les permissions du dossier uploads.";
        }
    } else {
        $error = "Seuls les fichiers JPG, JPEG, PNG et GIF sont autorisés.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Publier une annonce - OUEST DEAL</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        :root {
            --primary: #FF6B00;
            --secondary: #2b77e7;
            --light: #f8f9fa;
            --dark: #212529;
            --success: #28a745;
            --danger: #dc3545;
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
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }
        
        .publish-header {
            text-align: center;
            margin-bottom: 3rem;
        }
        
        .publish-header h1 {
            font-size: 2.5rem;
            color: var(--primary);
            margin-bottom: 1rem;
        }
        
        .publish-header p {
            color: #666;
            font-size: 1.1rem;
        }
        
        .publish-form {
            background-color: white;
            border-radius: 10px;
            padding: 2rem;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
        }
        
        .form-group.required label::after {
            content: " *";
            color: var(--danger);
        }
        
        .form-control {
            width: 100%;
            padding: 0.8rem;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
            transition: var(--transition);
        }
        
        .form-control:focus {
            border-color: var(--primary);
            outline: none;
            box-shadow: 0 0 0 3px rgba(255, 107, 0, 0.2);
        }
        
        textarea.form-control {
            min-height: 150px;
            resize: vertical;
        }
        
        .form-row {
            display: flex;
            gap: 1.5rem;
            margin-bottom: 1.5rem;
        }
        
        .form-col {
            flex: 1;
        }
        
        .image-upload {
            border: 2px dashed #ddd;
            border-radius: 5px;
            padding: 2rem;
            text-align: center;
            cursor: pointer;
            transition: var(--transition);
        }
        
        .image-upload:hover {
            border-color: var(--primary);
        }
        
        .image-upload i {
            font-size: 3rem;
            color: var(--primary);
            margin-bottom: 1rem;
        }
        
        .image-upload p {
            color: #666;
        }
        
        .image-preview {
            display: none;
            margin-top: 1rem;
            text-align: center;
        }
        
        .image-preview img {
            max-width: 200px;
            max-height: 200px;
            border-radius: 5px;
        }
        
        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .checkbox-group input[type="checkbox"] {
            width: auto;
        }
        
        .btn {
            display: inline-block;
            padding: 0.8rem 1.5rem;
            border: none;
            border-radius: 5px;
            font-size: 1rem;
            font-weight: bold;
            cursor: pointer;
            transition: var(--transition);
        }
        
        .btn-primary {
            background-color: var(--primary);
            color: white;
        }
        
        .btn-primary:hover {
            background-color: #e05d00;
            transform: translateY(-2px);
        }
        
        .btn-block {
            display: block;
            width: 100%;
        }
        
        .alert {
            padding: 1rem;
            border-radius: 5px;
            margin-bottom: 1.5rem;
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
        
        @media (max-width: 768px) {
            .form-row {
                flex-direction: column;
                gap: 1rem;
            }
            
            .publish-header h1 {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
  
    
    <div class="container">
        <div class="publish-header">
            <h1>Publier une annonce</h1>
            <p>Remplissez ce formulaire pour publier votre produit ou service sur OUEST DEAL</p>
        </div>
        
        <?php if (isset($success)): ?>
            <div class="alert alert-success">
                <?php echo $success; ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-danger">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <form class="publish-form" method="POST" enctype="multipart/form-data">
            <div class="form-row">
                <div class="form-col">
                    <div class="form-group required">
                        <label for="title">Titre de l'annonce</label>
                        <input type="text" id="title" name="title" class="form-control" placeholder="Ex: Appartement 3 pièces à Ouaga 200" required>
                    </div>
                </div>
                <div class="form-col">
                    <div class="form-group required">
                        <label for="category">Catégorie</label>
                        <select id="category" name="category" class="form-control" required>
                            <option value="">Sélectionnez une catégorie</option>
                            <option value="immobilier">Immobilier</option>
                            <option value="vehicules">Véhicules</option>
                            <option value="electromenager">Électroménager</option>
                            <option value="terrains">Terrains</option>
                            <option value="electronique">Electronique</option>
                            <option value="materiel-professionnel">Matériel Professionnel</option>
                            <option value="mode-beaute">Mode & Beauté</option>
                            <option value="services">Services</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <div class="form-group required">
                <label for="description">Description détaillée</label>
                <textarea id="description" name="description" class="form-control" placeholder="Décrivez votre produit/service en détail..." required></textarea>
            </div>
            
            <div class="form-row">
                <div class="form-col">
                    <div class="form-group required">
                        <label for="price">Prix (XOF)</label>
                        <input type="number" id="price" name="price" class="form-control" placeholder="Ex: 500000" required>
                    </div>
                </div>
                <div class="form-col">
                    <div class="form-group required">
                        <label for="condition">État du produit</label>
                        <select id="condition" name="condition" class="form-control" required>
                            <option value="neuf">Neuf</option>
                            <option value="occasion">Occasion</option>
                            <option value="reconditionne">Reconditionné</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <div class="form-group">
                <div class="checkbox-group">
                    <input type="checkbox" id="negotiable" name="negotiable">
                    <label for="negotiable">Prix négociable</label>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-col">
                    <div class="form-group required">
                        <label for="location">Localisation</label>
                        <select id="location" name="location" class="form-control" required>
                            <option value="">Sélectionnez une ville</option>
                            <option value="Ouagadougou">Ouagadougou</option>
                            <option value="Bobo-Dioulasso">Bobo-Dioulasso</option>
                            <option value="Koudougou">Koudougou</option>
                            <option value="Ouahigouya">Ouahigouya</option>
                            <option value="Banfora">Banfora</option>
                            <option value="Autre">Autre ville</option>
                        </select>
                    </div>
                </div>
                <div class="form-col">
                    <div class="form-group required">
                        <label for="telephone">Numéro de téléphone</label>
                        <input type="tel" id="telephone" name="telephone" class="form-control" placeholder="Ex: 70123456" required>
                    </div>
                </div>
            </div>
            
            <div class="form-group required">
                <label>Image du produit</label>
                <div class="image-upload" onclick="document.getElementById('image').click()">
                    <i class="fas fa-cloud-upload-alt"></i>
                    <p>Cliquez pour télécharger une image</p>
                    <p><small>Formats acceptés: JPG, PNG, GIF (max 2MB)</small></p>
                    <input type="file" id="image" name="image" accept="image/*" style="display: none;" required onchange="previewImage(this)">
                </div>
                <div class="image-preview" id="imagePreview">
                    <img id="preview" src="#" alt="Prévisualisation de l'image">
                </div>
            </div>
            
            <div class="form-group">
                <button type="submit" class="btn btn-primary btn-block">
                    <i class="fas fa-paper-plane"></i> Publier l'annonce
                </button>
            </div>
            
            <div class="form-group text-center">
                <p><small>En publiant cette annonce, vous acceptez nos <a href="#">conditions d'utilisation</a> et notre <a href="#">politique de confidentialité</a>.</small></p>
            </div>
        </form>
    </div>
    
   
    
    <script>
        function previewImage(input) {
            const preview = document.getElementById('preview');
            const imagePreview = document.getElementById('imagePreview');
            
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    imagePreview.style.display = 'block';
                }
                
                reader.readAsDataURL(input.files[0]);
            }
        }
        
        // Validation du formulaire
        document.querySelector('form').addEventListener('submit', function(e) {
            const telephone = document.getElementById('telephone').value;
            const phoneRegex = /^(?:\+226|00226|226)?[0-9]{8}$/;
            
            if (!phoneRegex.test(telephone)) {
                alert('Veuillez entrer un numéro de téléphone valide (8 chiffres)');
                e.preventDefault();
            }
        });
    </script>
</body>
</html>