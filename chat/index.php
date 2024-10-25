<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Redirecting</title>
    <style>
        body {
            background-color: black;
            color: lightgrey;
            font-family: monospace;
            text-align: center;
            padding-top: 20%;
            margin: 0;
            font-size: 20px;
        }
        .prompt {
            color: #00ff00;
        }
        .command {
            color: lightgrey;
        }
        .dots {
            font-weight: bold;
            color: #00ff00;
        }
        .dot {
            animation: blink 0.5s step-start infinite;
        }
        .dot:nth-child(2) {
            animation-delay: 0.1s;
        }
        .dot:nth-child(3) {
            animation-delay: 0.2s;
        }
        @keyframes blink {
            50% {
                opacity: 0;
            }
        }
    </style>
    <script>
        setTimeout(function() {
            window.location.href = "index.html";
        }, 2000);
    </script>
</head>
<body>
    <div>
        <span class="prompt">silverflag@website:~$</span> 
        <span class="command">Redirecting <span class="dots">
            <span class="dot">.</span>
            <span class="dot">.</span>
            <span class="dot">.</span>
        </span></span>
    </div>
</body>
</html>
