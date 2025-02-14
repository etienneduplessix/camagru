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
        </div>

        <div class="editor">
            <div class="preview">
                <div class="preview-area" id="previewArea">
                    <div class="placeholder">
                        <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                        </svg>
                        <p>Start camera or upload a photo</p>
                    </div>
                </div>
                <video id="video" autoplay playsinline></video>
                <img id="preview" src="" alt="">
                <canvas id="canvas"></canvas>
            </div>

            <div class="sidebar">
                <div class="overlay-list">
                    <h3>Overlays</h3>
                    <button class="overlay-item" data-overlay="sunglasses">Sunglasses</button>
                    <button class="overlay-item" data-overlay="hat">Hat</button>
                    <button class="overlay-item" data-overlay="frame">Frame</button>
                </div>
            </div>
        </div>
    </div>

    <script>
      const startCameraBtn = document.getElementById('startCamera');
const fileInput = document.getElementById('fileInput');
const video = document.getElementById('video');
const preview = document.getElementById('preview');
const canvas = document.getElementById('canvas');
const previewArea = document.getElementById('previewArea');
let stream = null;

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

      function capturePhoto() {
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            const ctx = canvas.getContext('2d');
            ctx.drawImage(video, 0, 0);
            const imageData = canvas.toDataURL('image/png'); // Get image as base64

            preview.src = imageData; // Show preview

            // Stop camera and show preview
            if (stream) {
                  stream.getTracks().forEach(track => track.stop());
                  stream = null;
            }
            video.style.display = 'none';
            preview.style.display = 'block';
            document.querySelector('.capture-btn')?.remove();

            // ✅ Send captured image to server
            sendImageToServer(imageData);
            }

      function sendImageToServer(imageData) {
            const base64Data = imageData.split(',')[1]; // Extract base64 part

            fetch('/upload_image.php', {
                  method: 'POST',
                  headers: { 'Content-Type': ' application/json' },
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