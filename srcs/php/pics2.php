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

        /* Like Button Styling */
        .like-button {
            position: absolute;
            background-color: rgba(200, 200, 200, 0.8);
            border: none;
            border-radius: 50%;
            padding: 8px;
            width: 30px;
            height: 30px;
            text-align: center;
            cursor: pointer;
            transition: background-color 0.3s;
            font-size: 16px;
        }

        .like-button.liked {
            background-color: red;
            color: white;
        }

        /* Positioning Like Button (Only Bottom Left) */
        .like-bottom-left {
            bottom: 8px;
            left: 8px;
        }

        /* Comment Section */
        .comment-section {
            margin-top: 10px;
        }

        .comment-section input {
            width: 80%;
            padding: 5px;
            margin-right: 5px;
        }

        .comment-button {
            cursor: pointer;
            background: #007BFF;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 4px;
        }

        .comments-container {
            margin-top: 10px;
            font-size: 14px;
            border: 1px solid #ccc;
            padding: 5px;
            border-radius: 5px;
            background-color: #f9f9f9;
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
