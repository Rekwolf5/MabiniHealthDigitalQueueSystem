<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mabini Health Center - Queue System</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            max-width: 800px;
            width: 100%;
            padding: 60px 40px;
            text-align: center;
        }
        .logo {
            font-size: 80px;
            color: #667eea;
            margin-bottom: 20px;
        }
        h1 {
            color: #333;
            font-size: 2.5em;
            margin-bottom: 15px;
        }
        p {
            color: #666;
            font-size: 1.2em;
            margin-bottom: 40px;
        }
        .links {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-top: 40px;
        }
        .link-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px 20px;
            border-radius: 15px;
            text-decoration: none;
            transition: transform 0.3s, box-shadow 0.3s;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 15px;
        }
        .link-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.4);
        }
        .link-card i {
            font-size: 2.5em;
        }
        .link-card span {
            font-size: 1.1em;
            font-weight: 600;
        }
        .status {
            margin-top: 30px;
            padding: 20px;
            background: #f0f9ff;
            border-left: 4px solid #3b82f6;
            border-radius: 8px;
        }
        .status h3 {
            color: #1e40af;
            margin-bottom: 10px;
        }
        .status ul {
            list-style: none;
            text-align: left;
            display: inline-block;
        }
        .status li {
            color: #374151;
            margin: 5px 0;
        }
        .status li i {
            color: #10b981;
            margin-right: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">
            <i class="fas fa-hospital"></i>
        </div>
        <h1>Mabini Health Center</h1>
        <p>Queue Management System</p>
        
        <div class="links">
            <a href="{{ route('front-desk.index') }}" class="link-card">
                <i class="fas fa-clipboard-list"></i>
                <span>Front Desk Queue</span>
            </a>
            <a href="{{ route('login') }}" class="link-card">
                <i class="fas fa-user-md"></i>
                <span>Staff Login</span>
            </a>
            <a href="{{ route('queue.display') }}" class="link-card">
                <i class="fas fa-tv"></i>
                <span>Queue Display</span>
            </a>
            <a href="{{ route('login') }}" class="link-card">
                <i class="fas fa-users-cog"></i>
                <span>Admin Login</span>
            </a>
        </div>

        <div class="status">
            <h3><i class="fas fa-check-circle"></i> System Status</h3>
            <ul>
                <li><i class="fas fa-check"></i> Server is running successfully</li>
                <li><i class="fas fa-check"></i> PWA features enabled</li>
                <li><i class="fas fa-check"></i> All routes functional</li>
            </ul>
        </div>
    </div>
</body>
</html>
