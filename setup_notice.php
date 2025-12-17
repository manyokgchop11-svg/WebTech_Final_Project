<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setup Required - QuickBite</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            margin: 0;
        }
        .setup-container {
            background: #333;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.5);
            text-align: center;
            max-width: 500px;
            border: 2px solid #ffd700;
        }
        h1 {
            color: #ffd700;
            margin-bottom: 20px;
            font-size: 2.5rem;
        }
        p {
            font-size: 1.6rem;
            margin-bottom: 30px;
            line-height: 1.6;
        }
        .btn {
            display: inline-block;
            background: #ffd700;
            color: #000;
            padding: 15px 30px;
            text-decoration: none;
            border-radius: 8px;
            font-size: 1.6rem;
            font-weight: bold;
            margin: 10px;
            transition: all 0.3s ease;
        }
        .btn:hover {
            background: #ffed4e;
            transform: translateY(-2px);
        }
        .steps {
            text-align: left;
            margin: 30px 0;
            background: #222;
            padding: 20px;
            border-radius: 10px;
        }
        .steps h3 {
            color: #ffd700;
            margin-bottom: 15px;
        }
        .steps ol {
            padding-left: 20px;
        }
        .steps li {
            margin-bottom: 10px;
            font-size: 1.4rem;
        }
    </style>
</head>
<body>
    <div class="setup-container">
        <h1>⚠️ Database Setup Required</h1>
        <p>Welcome to QuickBite! Before you can use the system, you need to set up the database.</p>
        
        <div class="steps">
            <h3>Quick Setup Steps:</h3>
            <ol>
                <li>Click "Setup Database" below</li>
                <li>Wait for setup to complete</li>
                <li>Return to website and login</li>
            </ol>
        </div>
        
        <a href="setup_tables.php" class="btn">🚀 Setup Database Now</a>
        <br>
        <a href="index.html" class="btn">← Back to Website</a>
        
        <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #555; font-size: 1.4rem; color: #ccc;">
            <p><strong>After setup, use these credentials:</strong></p>
            <p>Admin: admin@quickbite.com / admin123</p>
            <p>Customer: customer@test.com / customer123</p>
        </div>
    </div>
</body>
</html>