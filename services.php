<?php
include 'db.php';
session_start();
$result = $conn->query("SELECT * FROM services");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Browse Services | UrbanServe</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f8f9fa;
            margin: 0;
            padding: 20px;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        h2 {
            color: #2d3748;
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 2px solid #f76d2b;
        }
        
        .services-list {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 25px;
            margin-bottom: 40px;
        }
        
        .service-card {
            background: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .service-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .service-name {
            color: #f76d2b;
            font-size: 1.2rem;
            margin-bottom: 8px;
        }
        
        .service-category {
            display: inline-block;
            background: #e2e8f0;
            color: #4a5568;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 0.8rem;
            margin-bottom: 10px;
        }
        
        .service-description {
            color: #4a5568;
            font-size: 0.95rem;
        }
        
        .back-link {
            display: inline-block;
            margin-top: 20px;
            color: #f76d2b;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s;
        }
        
        .back-link:hover {
            color: #e05b1a;
            text-decoration: underline;
        }
        
        @media (max-width: 768px) {
            .services-list {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Browse Services</h2>
        
        <div class="services-list">
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="service-card">
                    <h3 class="service-name"><?= htmlspecialchars($row['name']) ?></h3>
                    <span class="service-category"><?= htmlspecialchars($row['category']) ?></span>
                    <p class="service-description"><?= htmlspecialchars($row['description']) ?></p>
                </div>
            <?php endwhile; ?>
        </div>
        
        <a href="index.php" class="back-link">← Back to Home</a>
    </div>
</body>
</html>