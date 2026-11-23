<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome - Digital Eqqub</title>
    
    <!-- Dependencies -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;700&display=swap" rel="stylesheet">
    
    <style>
        /* FULL SCREEN BACKGROUND */
        body {
            font-family: 'Poppins', sans-serif;
            
            /* LOCAL IMAGE: Ensure 'welcome_bg.jpg' is in your folder */
            background: url('welcome_bg.jpg') no-repeat center center fixed;
            
            background-size: cover;
            height: 100vh;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: white;
            background-color: #000; /* Fallback color */
        }
        
        /* DARK OVERLAY */
        body::before {
            content: ''; position: absolute; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0, 0, 0, 0.7); /* Adjust darkness here (0.7 is dark, 0.3 is light) */
            z-index: -1;
        }

        /* ANIMATED CONTENT BOX */
        .hero-content {
            animation: fadeUp 1.2s ease-out;
            max-width: 800px;
            padding: 20px;
        }

        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(50px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* BIG TITLE */
        .main-title {
            font-size: 4rem;
            font-weight: 800;
            background: linear-gradient(to right, #4facfe 0%, #00f2fe 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 20px;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        /* SUBTITLE */
        .sub-title {
            font-size: 1.5rem;
            color: #d1d5db;
            margin-bottom: 40px;
            font-weight: 300;
        }

        /* GLOWING BUTTON */
        .btn-start {
            padding: 15px 50px;
            font-size: 1.2rem;
            font-weight: bold;
            color: white;
            background: linear-gradient(45deg, #00c6ff, #0072ff);
            border: none;
            border-radius: 50px;
            box-shadow: 0 0 20px rgba(0, 114, 255, 0.5);
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }

        .btn-start:hover {
            transform: scale(1.1);
            box-shadow: 0 0 40px rgba(0, 114, 255, 0.8);
            color: white;
        }

        .icon-box {
            font-size: 5rem;
            color: #ffd700;
            margin-bottom: 20px;
            animation: float 3s ease-in-out infinite;
        }

        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
            100% { transform: translateY(0px); }
        }
    </style>
</head>
<body>

    <div class="hero-content">
        <!-- Floating Icon -->
        <div class="icon-box">
            <i class="fa-solid fa-sack-dollar"></i>
        </div>

        <!-- Main Title -->
        <h1 class="main-title">Digital Eqqub</h1>
        
        <!-- Description -->
        <p class="sub-title">
            Modern, Secure, and Transparent Saving System.<br>
            Join your community and grow together.
        </p>

        <!-- Call to Action Button -->
        <a href="login.php" class="btn-start">
            GET STARTED <i class="fa-solid fa-arrow-right ms-2"></i>
        </a>
    </div>

</body>
</html>