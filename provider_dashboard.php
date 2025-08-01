<?php
session_start();
if ($_SESSION['user']['role'] !== 'provider') {
    header("Location: login.php");
    exit;
}

// Include database connection
include 'db.php';

// Fetch provider details
$provider_id = $_SESSION['user']['id'];
$provider_query = $conn->query("SELECT * FROM providers WHERE user_id = $provider_id");
$provider = $provider_query->fetch_assoc();

// Fetch provider's services
$services_query = $conn->query("SELECT s.* FROM services s 
                              JOIN provider_services ps ON s.id = ps.service_id 
                              WHERE ps.provider_id = $provider_id");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Provider Dashboard | UrbanServe</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #f76d2b;
            --primary-dark: #e05b1a;
            --secondary: #2d3748;
            --accent: #f0f4f8;
            --text: #2d3748;
            --light-text: #718096;
            --border: #e2e8f0;
            --white: #ffffff;
            --black: #000000;
            --success: #38a169;
            --error: #e53e3e;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8f9fa;
            color: var(--text);
            line-height: 1.6;
        }

        .dashboard-container {
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar Styles */
        .sidebar {
            width: 250px;
            background-color: var(--secondary);
            color: var(--white);
            padding: 20px 0;
        }

        .sidebar-header {
            padding: 0 20px 20px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            text-align: center;
        }

        .profile-img {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
            margin: 0 auto 10px;
            display: block;
            border: 3px solid var(--primary);
        }

        .sidebar-header h3 {
            color: var(--white);
            margin-bottom: 5px;
        }

        .sidebar-header p {
            color: var(--light-text);
            font-size: 14px;
        }

        .nav-menu {
            padding: 20px 0;
        }

        .nav-item {
            padding: 12px 20px;
            color: #cbd5e0;
            text-decoration: none;
            display: block;
            transition: all 0.3s;
        }

        .nav-item:hover, .nav-item.active {
            background-color: rgba(255,255,255,0.1);
            color: var(--white);
        }

        .nav-item i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }

        /* Main Content Styles */
        .main-content {
            flex: 1;
            padding: 30px;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid var(--border);
        }

        .header h1 {
            font-size: 24px;
            color: var(--secondary);
        }

        .user-actions {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .btn {
            padding: 8px 15px;
            border-radius: 5px;
            text-decoration: none;
            font-size: 14px;
            transition: all 0.3s;
            cursor: pointer;
            border: none;
        }

        .btn-primary {
            background-color: var(--primary);
            color: white;
        }

        .btn-primary:hover {
            background-color: var(--primary-dark);
        }

        .btn-outline {
            background-color: transparent;
            border: 1px solid var(--primary);
            color: var(--primary);
        }

        .btn-outline:hover {
            background-color: rgba(247, 109, 43, 0.1);
        }

        /* Dashboard Cards */
        .dashboard-cards {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .card {
            background-color: var(--white);
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }

        .card h3 {
            font-size: 14px;
            color: var(--light-text);
            margin-bottom: 10px;
        }

        .card p {
            font-size: 24px;
            font-weight: 700;
            color: var(--secondary);
        }

        /* Profile Section */
        .profile-section {
            background-color: var(--white);
            border-radius: 8px;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            margin-bottom: 30px;
        }

        .profile-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid var(--border);
        }

        .profile-header h2 {
            font-size: 20px;
        }

        .profile-details {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
        }

        .detail-group {
            margin-bottom: 15px;
        }

        .detail-group label {
            display: block;
            font-size: 14px;
            color: var(--light-text);
            margin-bottom: 5px;
        }

        .detail-group p {
            font-size: 16px;
            color: var(--text);
            padding: 8px 12px;
            background-color: var(--accent);
            border-radius: 5px;
        }

        /* Services Section */
        .services-section {
            background-color: var(--white);
            border-radius: 8px;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }

        .services-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }

        .service-card {
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 15px;
            transition: all 0.3s;
        }

        .service-card:hover {
            border-color: var(--primary);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .service-card h4 {
            margin-bottom: 10px;
            color: var(--secondary);
        }

        .service-card p {
            font-size: 14px;
            color: var(--light-text);
            margin-bottom: 10px;
        }

        .service-price {
            font-weight: 600;
            color: var(--primary);
        }

        @media (max-width: 768px) {
            .dashboard-container {
                flex-direction: column;
            }
            
            .sidebar {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar Navigation -->
        <div class="sidebar">
            <div class="sidebar-header">
                <img src="https://ui-avatars.com/api/?name=<?= urlencode($_SESSION['user']['name']) ?>&background=f76d2b&color=fff" 
                     alt="Profile" class="profile-img">
                <h3><?= htmlspecialchars($_SESSION['user']['name']) ?></h3>
                <p>Service Provider</p>
            </div>
            
            <div class="nav-menu">
                <a href="index.php" class="nav-item">
                    <i class="fas fa-home"></i> Home
                </a>
                <a href="#" class="nav-item active">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
                <a href="#profile" class="nav-item">
                    <i class="fas fa-user"></i> My Profile
                </a>
                <a href="#services" class="nav-item">
                    <i class="fas fa-concierge-bell"></i> My Services
                </a>
                <a href="bookings.php" class="nav-item">
                    <i class="fas fa-calendar-check"></i> Bookings
                </a>
                <a href="earnings.php" class="nav-item">
                    <i class="fas fa-rupee-sign"></i> Earnings
                </a>
                <a href="settings.php" class="nav-item">
                    <i class="fas fa-cog"></i> Settings
                </a>
                <a href="logout.php" class="nav-item">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>

            </div>
        </div>

        <!-- Main Content Area -->
        <div class="main-content">
            <div class="header">
                <h1>Provider Dashboard</h1>
                <div class="user-actions">
                    <span>Welcome back, <?= htmlspecialchars($_SESSION['user']['name']) ?></span>
                    <a href="logout.php" class="btn btn-outline">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                </div>
            </div>

            <!-- Dashboard Stats -->
            <div class="dashboard-cards">
                <div class="card">
                    <h3>Total Bookings</h3>
                    <p>24</p>
                </div>
                <div class="card">
                    <h3>Upcoming Jobs</h3>
                    <p>5</p>
                </div>
                <div class="card">
                    <h3>Earnings</h3>
                    <p>₹12,500</p>
                </div>
                <div class="card">
                    <h3>Rating</h3>
                    <p>4.8 ★</p>
                </div>
            </div>

            <!-- Profile Section -->
            <div id="profile" class="profile-section">
                <div class="profile-header">
                    <h2>My Profile</h2>
                    <a href="edit_profile.php" class="btn btn-primary">
                        <i class="fas fa-edit"></i> Edit Profile
                    </a>
                </div>
                
                <div class="profile-details">
                    <div>
                        <div class="detail-group">
                            <label>Full Name</label>
                            <p><?= htmlspecialchars($_SESSION['user']['name']) ?></p>
                        </div>
                        <div class="detail-group">
                            <label>Email</label>
                            <p><?= htmlspecialchars($_SESSION['user']['email']) ?></p>
                        </div>
                        <div class="detail-group">
                            <label>Phone</label>
                            <p><?= isset($provider['phone']) ? htmlspecialchars($provider['phone']) : 'Not provided' ?></p>
                        </div>
                    </div>
                    <div>
                        <div class="detail-group">
                            <label>Service Category</label>
                            <p><?= isset($provider['service_category']) ? htmlspecialchars($provider['service_category']) : 'Not specified' ?></p>
                        </div>
                        <div class="detail-group">
                            <label>Experience</label>
                            <p><?= isset($provider['experience']) ? htmlspecialchars($provider['experience']) : 'Not specified' ?></p>
                        </div>
                        <div class="detail-group">
                            <label>Location</label>
                            <p><?= isset($provider['location']) ? htmlspecialchars($provider['location']) : 'Not specified' ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Services Section -->
            <div id="services" class="services-section">
                <div class="profile-header">
                    <h2>My Services</h2>
                    <a href="add_service.php" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Add Service
                    </a>
                </div>
                
                <?php if ($services_query->num_rows > 0): ?>
                    <div class="services-grid">
                        <?php while ($service = $services_query->fetch_assoc()): ?>
                            <div class="service-card">
                                <h4><?= htmlspecialchars($service['name']) ?></h4>
                                <p><?= htmlspecialchars($service['description']) ?></p>
                                <div class="service-price">₹<?= number_format($service['base_price'], 2) ?></div>
                                <div style="margin-top: 10px;">
                                    <a href="edit_service.php?id=<?= $service['id'] ?>" class="btn btn-outline" style="padding: 5px 10px; font-size: 12px;">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                <?php else: ?>
                    <p>You haven't added any services yet.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
