<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page Not Found | 404</title>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Nunito', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #fff;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            position: relative;
        }
        
        .container {
            text-align: center;
            max-width: 800px;
            padding: 2rem;
            z-index: 2;
        }
        
        h1 {
            font-size: 10rem;
            margin-bottom: 1rem;
            text-shadow: 2px 2px 15px rgba(0, 0, 0, 0.2);
            font-weight: 700;
            position: relative;
            animation: pulse 3s infinite;
        }
        
        h2 {
            font-size: 2rem;
            margin-bottom: 2rem;
            font-weight: 600;
            opacity: 0.9;
        }
        
        p {
            font-size: 1.2rem;
            margin-bottom: 2rem;
            opacity: 0.8;
        }
        
        .btn {
            display: inline-block;
            background-color: #fff;
            color: #6a57d5;
            padding: 0.8rem 2rem;
            border-radius: 30px;
            text-decoration: none;
            font-weight: 600;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-size: 0.9rem;
        }
        
        .btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 7px 20px rgba(0, 0, 0, 0.2);
        }
        
        .shapes {
            position: absolute;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            z-index: 1;
            overflow: hidden;
        }
        
        .shape {
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
        }
        
        .shape1 {
            width: 150px;
            height: 150px;
            left: 10%;
            top: 20%;
            animation: float 8s infinite;
        }
        
        .shape2 {
            width: 80px;
            height: 80px;
            right: 15%;
            top: 15%;
            animation: float 6s infinite 1s;
        }
        
        .shape3 {
            width: 200px;
            height: 200px;
            left: 15%;
            bottom: 10%;
            animation: float 9s infinite 2s;
        }
        
        .shape4 {
            width: 120px;
            height: 120px;
            right: 10%;
            bottom: 20%;
            animation: float 7s infinite 3s;
        }
        
        @keyframes float {
            0% {
                transform: translateY(0) rotate(0deg);
            }
            50% {
                transform: translateY(-20px) rotate(5deg);
            }
            100% {
                transform: translateY(0) rotate(0deg);
            }
        }
        
        @keyframes pulse {
            0% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.05);
            }
            100% {
                transform: scale(1);
            }
        }
        
        @media (max-width: 768px) {
            h1 {
                font-size: 8rem;
            }
            
            h2 {
                font-size: 1.5rem;
            }
            
            p {
                font-size: 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="shapes">
        <div class="shape shape1"></div>
        <div class="shape shape2"></div>
        <div class="shape shape3"></div>
        <div class="shape shape4"></div>
    </div>
    
    <div class="container">
        <h1>404</h1>
        <h2>Oops! Page Not Found</h2>
        <p>The page you're looking for doesn't exist or has been moved.</p>
        <a href="{{ url('/') }}" class="btn">Back to Home</a>
    </div>
    
    <script>
        // Add interactive floating elements
        document.addEventListener('mousemove', function(e) {
            const shapes = document.querySelectorAll('.shape');
            let x = e.clientX / window.innerWidth;
            let y = e.clientY / window.innerHeight;
            
            shapes.forEach(shape => {
                const speed = parseFloat(Math.random() * 2).toFixed(2);
                shape.style.transform = `translate(${x * 20}px, ${y * 20}px) scale(${1 + x * 0.1})`;
            });
        });
    </script>
</body>
</html>