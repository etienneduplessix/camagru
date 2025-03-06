<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Responsive Gallery</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <style>
    /* Global styling */
    body {
      margin: 0;
      font-family: Arial, sans-serif;
      background-color: #f5f5f5;
      overflow-x: hidden; /* Prevent horizontal scroll */
    }

    /* Gallery container using CSS Grid */
    #imageContainer {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 20px;
      padding: 20px;
      max-height: 80vh; /* Limit height for scrolling */
      overflow-y: auto; /* Enable vertical scrolling */
    }

    /* Wrapper for image and like button */
    .wrapper {
      position: relative;
      display: inline-block;
      width: 100%;
    }

    /* Image styling */
    .wrapper img {
      width: 100%;
      height: auto;
      border: 1px solid #ddd;
      border-radius: 8px;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
      transition: transform 0.3s;
    }

    /* Optional hover effect */
    .wrapper img:hover {
      transform: scale(1.03);
    }
  </style>
</head>
<script src="script.js"></script>
<body>
  <div id="imageContainer">
    <!-- Gallery images will be loaded here -->
  </div>
</body>
</html>
