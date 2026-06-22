<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - Retro Revival</title>
    <style>
        /* Native CSS styling to meet assignment constraints (No UI Frameworks) */
        body {
            font-family: sans-serif;
            background-color: #faf8f5; 
            color: #333;
            margin: 0;
            padding: 0;
        }
        h1, h2 {
            font-family: serif; /* Vintage character for headings */
            color: #8B4513;
            text-align: center;
        }
        .navbar {
            background-color: #333;
            color: #fff;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
        }
        .navbar a {
            color: white;
            text-decoration: none;
            padding: 0 10px;
        }
        .navbar a:hover {
            text-decoration: underline;
        }
        .container {
            max-width: 1000px;
            margin: 40px auto;
            padding: 20px;
        }
        .project-description {
            text-align: center;
            max-width: 800px;
            margin: 0 auto 40px auto;
            line-height: 1.6;
            font-size: 1.1em;
            color: #555;
        }
        
        /* Flexbox grid for the team member cards */
        .team-grid {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 25px;
        }
        
        /* Individual Team Member Card Styling */
        .team-card {
            background-color: #fff;
            border: 2px solid #e0d8c8;
            border-radius: 8px;
            width: 220px;
            padding: 25px 15px;
            text-align: center;
            box-shadow: 0 4px 8px rgba(0,0,0,0.05);
            transition: transform 0.2s;
        }
        .team-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 12px rgba(0,0,0,0.1);
        }
        .avatar-placeholder {
            width: 100px;
            height: 100px;
            background-color: #fdf5e6;
            border: 2px solid #8B4513;
            border-radius: 50%;
            margin: 0 auto 15px auto;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #8B4513;
            font-family: serif;
            font-size: 1.5em;
            font-weight: bold;
        }
        .team-card h3 {
            margin: 10px 0 5px 0;
            color: #333;
            font-family: sans-serif;
            font-size: 1.1em;
        }
        .team-card .role {
            font-weight: bold;
            color: #d2691e;
            margin-bottom: 10px;
            font-size: 0.9em;
            text-transform: uppercase;
        }
        .team-card .student-id {
            color: #666;
            font-size: 0.9em;
            background-color: #eee;
            padding: 4px 8px;
            border-radius: 4px;
            display: inline-block;
        }
    </style>
</head>
<body>
    <header class="navbar">
        <div class="logo">Retro Revival</div>
        <nav class="links">
            <a href="index.php">Home</a> | 
            <a href="products.php">Search</a> | 
            <a href="cart.php">Cart</a> | 
            <a href="profile.php">Profile</a> | 
            <a href="about.php" style="font-weight: bold;">About Us</a>
        </nav>
    </header>
    
    <div class="container">
        <h1>About Retro Revival</h1>
        <p class="project-description">
            Retro Revival is a dynamic online thrift store and marketplace dedicated to prolonging the life of gently used clothing, including contemporary styles and traditional Malaysian fashion like Batik and Baju Kurung. This platform was developed for the Web Application Development (CIT6224) course.
        </p>

        <h2>Meet Our Team (Group 7)</h2>
        <div class="team-grid">
            
            <!-- Member 1: Kubenthran -->
            <div class="team-card">
                <div class="avatar-placeholder">KU</div>
                <h3>Kubenthran Udayar</h3>
                <div class="role">Front-end Developer</div>
                <div class="student-id">ID: 241UC25188</div>
            </div>

            <!-- Member 2: Kalam -->
            <div class="team-card">
                <div class="avatar-placeholder">AK</div>
                <h3>Abdul Kalam Bin Akbar</h3>
                <div class="role">Integration & Testing</div>
                <div class="student-id">ID: 251UC2519R</div>
            </div>

            <!-- Member 3: A'liah -->
            <div class="team-card">
                <div class="avatar-placeholder">NA</div>
                <h3>Nur A'liah Athirah</h3>
                <div class="role">Database Designer</div>
                <div class="student-id">ID: 251UC2513Z</div>
            </div>

            <!-- Member 4: Imran -->
            <div class="team-card">
                <div class="avatar-placeholder">IF</div>
                <h3>Imran Farhan</h3>
                <div class="role">Back-end Developer</div>
                <div class="student-id">ID: 243UC247DW</div>
            </div>

        </div>
    </div>
</body>
</html>