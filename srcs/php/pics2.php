<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Gallery with Bottom-Left Like Button</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        /* Global Styling */
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
        }

        /* Gallery Container */
        #imageContainer {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            padding: 20px;
            max-height: 80vh;
            overflow-y: auto;
        }

        /* Wrapper for Image & Button */
        .wrapper {
            position: relative;
            padding: 10px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        /* Image Styling */
        .wrapper img {
            width: 100%;
            height: auto;
            border-radius: 8px;
            display: block;
        }

    </style>
</head>
<body>
    <div id="imageContainer">
        <!-- Images and buttons will be loaded dynamically here -->
    </div>
    
    <script src="script.js"></script> <!-- Link to the JavaScript file -->
</body>
</html>
