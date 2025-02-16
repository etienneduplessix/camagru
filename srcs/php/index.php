<?php
session_start();

// Check if user is logged in, otherwise redirect to login page
if (!isset($_SESSION['user_id'])) {
    header("Location: /login.php");
    exit();
}

// If logged in, continue to load index page
?>
<?php

?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Photo Editor</title>
    <style>
        :root {
            --primary: #3b82f6;
            --success: #22c55e;
            --danger: #ef4444;
            --purple: #9333ea;
        }

        body {
            font-family: -apple-system, system-ui, sans-serif;
            margin: 0;
            padding: 20px;
            background: #f5f5f5;
        }

        .container {
            max-width: 1000px;
            margin: 0 auto;
        }

        .controls {
            display: flex;
            justify-content: center;
            gap: 16px;
            margin-bottom: 24px;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            border: none;
            border-radius: 6px;
            color: white;
            cursor: pointer;
            font-size: 16px;
            transition: opacity 0.2s;
        }

        .btn:hover {
            opacity: 0.9;
        }

        .btn-primary {
            background: var(--primary);
        }

        .btn-success {
            background: var(--success);
        }

        .btn-danger {
            background: var(--danger);
        }

        .btn-purple {
            background: var(--purple);
        }

        .editor {
            display: flex;
            gap: 24px;
        }

        .preview {
            flex: 1;
            background: white;
            padding: 16px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .preview-area {
            position: relative;
            min-height: 480px;
            background: #f8f9fa;
            border: 2px dashed #dee2e6;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        video, img {
            max-width: 100%;
            border-radius: 8px;
        }

        canvas {
            display: none;
        }

        .capture-btn {
            position: absolute;
            bottom: 16px;
            left: 50%;
            transform: translateX(-50%);
        }

        .sidebar {
            width: 260px;
        }

        .overlay-list {
            background: white;
            padding: 16px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .overlay-item {
            display: block;
            width: 100%;
            padding: 8px 12px;
            margin-bottom: 8px;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            background: none;
            text-align: left;
            cursor: pointer;
            transition: background 0.2s;
        }

        .overlay-item:hover {
            background: #f3f4f6;
        }

        .overlay-item.active {
            background: #dbeafe;
            border-color: var(--primary);
        }

        #fileInput {
            display: none;
        }

        .placeholder {
            text-align: center;
            color: #6b7280;
        }

        .placeholder svg {
            width: 48px;
            height: 48px;
            margin-bottom: 8px;
        }
    </style>
</head>
<body>
    <div class="container">
    <div class="controls">
    <button class="btn btn-primary" id="startCamera">
        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
            <path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
        </svg>
        Start Camera
    </button>
    
    <label class="btn btn-success">
        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
        </svg>
        Upload Photo
        <input type="file" id="fileInput" accept="image/*">
    </label>

    <!-- ✅ New Upload to Database Button -->
    <button class="btn btn-purple" id="uploadToDatabase" style="display: none;">
        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
        </svg>
        Upload to Database
    </button>
</div>

<script>
      const startCameraBtn = document.getElementById('startCamera');
      const fileInput = document.getElementById('fileInput');
      const uploadToDatabaseBtn = document.getElementById('uploadToDatabase');
      const video = document.getElementById('video');
      const preview = document.getElementById('preview');
      const canvas = document.getElementById('canvas');
      const previewArea = document.getElementById('previewArea');

      let stream = null;
      let lastCapturedImage = null; // Stores the last captured/uploaded image

      startCameraBtn.addEventListener('click', async () => {
      try {
            stream = await navigator.mediaDevices.getUserMedia({ video: true });
            video.srcObject = stream;
            video.style.display = 'block';
            preview.style.display = 'none';
            previewArea.style.display = 'none';

            // Add capture button
            const captureBtn = document.createElement('button');
            captureBtn.className = 'btn btn-danger capture-btn';
            captureBtn.textContent = 'Capture';
            captureBtn.onclick = capturePhoto;
            video.parentElement.appendChild(captureBtn);
      } catch (err) {
            console.error('Error accessing camera:', err);
      }
      });

            // ✅ Capture Photo from Camera
      function capturePhoto() {
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            const ctx = canvas.getContext('2d');
            ctx.drawImage(video, 0, 0);
            lastCapturedImage = canvas.toDataURL('image/png'); // Store the captured image

            preview.src = lastCapturedImage; // Show preview
            uploadToDatabaseBtn.style.display = 'inline-flex'; // Show Upload button

            // Stop camera
            if (stream) {
                  stream.getTracks().forEach(track => track.stop());
                  stream = null;
            }
            video.style.display = 'none';
            preview.style.display = 'block';
            document.querySelector('.capture-btn')?.remove();
            }

            // ✅ Handle Image Upload
            fileInput.addEventListener('change', function(event) {
            const file = event.target.files[0];
            if (file) {
                  const reader = new FileReader();
                  reader.onload = function(e) {
                        lastCapturedImage = e.target.result; // Store the uploaded image
                        preview.src = lastCapturedImage;
                        uploadToDatabaseBtn.style.display = 'inline-flex'; // Show Upload button
                  };
                  reader.readAsDataURL(file);
            }
            });

            // ✅ Upload Image to Database
            uploadToDatabaseBtn.addEventListener('click', function() {
            if (lastCapturedImage) {
                  sendImageToServer(lastCapturedImage);
            } else {
                  alert('No image to upload.');
            }
            });

            // ✅ Send Image to Server
      function sendImageToServer(imageData) {
            const base64Data = imageData.split(',')[1]; // Extract base64 part

            fetch('/upload_image.php', {
                  method: 'POST',
                  headers: { 'Content-Type': 'application/json' },
                  body: JSON.stringify({ image: base64Data })
            })
            .then(response => response.text())  // Get raw response
            .then(text => {
                  console.log("Raw Response:", text); // Log the raw response
                  return JSON.parse(text);  // Try to parse JSON
            })
            .then(data => {
                  console.log(data);
                  alert(data.message);
            })
            .catch(error => console.error('Error uploading image:', error));
            }
            </script>
</body>
</html>