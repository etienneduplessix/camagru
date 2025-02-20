<?php
function activeTab($requestUri) {
    $current_uri = strtok($_SERVER['REQUEST_URI'], '?');
    $current_file_name = basename($current_uri, '.php');
    return ($current_file_name === $requestUri) ? 'active' : '';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>YOUR WEBSITE</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Local CSS -->
    <link rel="stylesheet" href="<?php echo ROOT_DIR; ?>assets/css/style.css">
</head>

<body>

    <nav class="navbar">
        <div class="container">
            <button class="menu-toggle" onclick="toggleMenu()">☰</button>
            <ul class="nav-links" id="nav-links">
                <li class="<?php echo activeTab(""); ?>"><a href="<?php echo ROOT_DIR; ?>">Home</a></li>
                <li class="<?php echo activeTab("pics"); ?>"><a href="<?php echo ROOT_DIR; ?>pics.php">Pics</a></li>
                <li><a href="<?php echo ROOT_DIR; ?>logout.php">Logout</a></li>
            </ul>
        </div>
    </nav>

    <script>
        function toggleMenu() {
            document.getElementById("nav-links").classList.toggle("active");
        }
    </script>

    <style>
        /* General Reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        /* Navbar Styling */
        .navbar {
            background: #333;
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
        }

        .navbar-brand img {
            max-height: 40px;
        }

        .nav-links {
            list-style: none;
            display: flex;
        }

        .nav-links li {
            margin: 0 10px;
        }

        .nav-links a {
            text-decoration: none;
            color: white;
            padding: 8px 12px;
            border-radius: 4px;
            transition: background 0.3s;
        }

        .nav-links a:hover,
        .nav-links .active a {
            background: #555;
        }

        /* Mobile Menu Button */
        .menu-toggle {
            display: none;
            background: none;
            border: none;
            font-size: 24px;
            color: white;
            cursor: pointer;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .nav-links {
                display: none;
                flex-direction: column;
                width: 100%;
                position: absolute;
                top: 50px;
                left: 0;
                background: #333;
                padding: 10px;
            }

            .nav-links.active {
                display: flex;
            }

            .menu-toggle {
                display: block;
            }
        }
    </style>

</body>
</html>
