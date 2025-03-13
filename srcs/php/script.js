document.addEventListener("DOMContentLoaded", () => {
    // Existing element selections
    const startCameraBtn = document.getElementById('startCamera');
    const fileInput = document.getElementById('fileInput');
    const uploadLabel = fileInput.parentElement;
    const sendToDBBtn = document.getElementById('sendToDB');
    const effectButtons = document.querySelectorAll('.button-group .btn-success');
    const video = document.getElementById('video');
    const preview = document.getElementById('preview');
    const canvas = document.getElementById('canvas');
    const sidebarContainer = document.getElementById('sidebar-container');
    const buttons = document.querySelectorAll(".btn-success");
    let stream = null;
    let overlay = '';

    // Initially disable file upload
    uploadLabel.style.opacity = '0.5';
    uploadLabel.style.cursor = 'not-allowed';
    fileInput.disabled = true;

    // Start camera and create capture button if needed
    startCameraBtn.addEventListener('click', async () => {
        try {
            stream = await navigator.mediaDevices.getUserMedia({ video: true });
            video.srcObject = stream;
            video.style.display = 'block';
            preview.style.display = 'none';

            if (!document.querySelector('.capture-btn')) {
                const captureBtn = document.createElement('button');
                captureBtn.className = 'btn btn-danger capture-btn';
                captureBtn.textContent = 'Capture';
                captureBtn.onclick = capturePhoto;
                captureBtn.disabled = true;
                captureBtn.style.opacity = 0.5;
                video.parentElement.appendChild(captureBtn);
            }
        } catch (err) {
            console.error('Error accessing camera:', err);
            alert('Camera access denied or unavailable.');
        }
    });

    // Consolidated effect buttons event listener
    effectButtons.forEach(button => {
        button.addEventListener("click", () => {
            // Remove selected class from all buttons
            effectButtons.forEach(btn => btn.classList.remove("selected"));
            
            // Add selected class to clicked button
            button.classList.add("selected");
            
            // Set the overlay based on selected filter
            if (button.id === 'cat') {
                overlay = 'cat';
            } else if (button.id === 'sun') {
                overlay = 'sun';
            } else if (button.id === 'flower') {
                overlay = 'flower';
            }

            // Enable both capture button and file upload
            const captureBtn = document.querySelector('.capture-btn');
            if (captureBtn) {
                captureBtn.disabled = false;
                captureBtn.style.opacity = 1;
            }
            
            // Enable file upload
            uploadLabel.style.opacity = '1';
            uploadLabel.style.cursor = 'pointer';
            fileInput.disabled = false;
        });
    });

    function capturePhoto() {
        // Define the selected effect button
        const selectedButton = document.querySelector('.button-group .selected');
        if (!selectedButton) {
            alert("Please select an effect before capturing.");
            return;
        }

        // Capture image from video stream
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        const ctx = canvas.getContext('2d');
        ctx.drawImage(video, 0, 0);
        const imageData = canvas.toDataURL('image/png');

        preview.src = imageData;
        preview.style.display = 'block';

        // Stop the camera stream and hide video element
        if (stream) {
            stream.getTracks().forEach(track => track.stop());
            stream = null;
        }
        video.style.display = 'none';

        // Remove capture button after capturing
        const captureBtn = document.querySelector('.capture-btn');
        if (captureBtn) captureBtn.remove();
    }

    function loadSidebar() {
        fetch('/pics2.php', { cache: "no-store" })
            .then(response => response.text())
            .then(data => {
                sidebarContainer.innerHTML = data;
                sidebarContainer.style.display = 'block';
                sidebarContainer.style.backgroundColor = '#fff';
                sidebarContainer.style.boxShadow = '0 4px 8px rgba(0, 0, 0, 0.1)';
                sidebarContainer.style.padding = '20px';
                sidebarContainer.style.borderRadius = '8px';
            })
            .catch(error => console.error('Error loading sidebar:', error));
    }

   function loadImages() {
    fetch('pics_api2.php')
        .then(response => response.text()) // Read as text first
        .then(text => {
            console.log("Raw response from server:", text); // Log raw response

            try {
                const data = JSON.parse(text); // Try parsing JSON
                console.log("Parsed JSON:", data); // Log parsed JSON

                const container = document.getElementById('imageContainer');
                if (!container) {
                    console.error("imageContainer element not found!");
                    return;
                }
                container.innerHTML = "<h2>Images</h2>";

                if (Array.isArray(data) && data.length > 0) {
                    data.forEach(imageData => {
                        const wrapper = document.createElement('div');
                        wrapper.className = 'wrapper';
                        wrapper.style.position = "relative";

                        const img = new Image();
                        img.src = imageData.src;
                        img.onerror = () => console.error("Error loading:", img.src);
                        img.style.width = "100%";
                        img.style.display = "block";

                        wrapper.appendChild(img);
                        container.appendChild(wrapper);
                    });
                } else {
                    container.innerHTML += "<p>No images found.</p>";
                }
            } catch (jsonError) {
                console.error("JSON parse error:", jsonError.message);
                console.error("Raw response was not valid JSON.");
                document.getElementById('previewArea').innerHTML = "<p>Error loading images.</p>";
            }
        })
        .catch(error => {
            console.error("Error fetching images:", error);
            document.getElementById('previewArea').innerHTML = "<p>Error loading images.</p>";
        });
}


    // Additional event listener for any button clicks to toggle "selected" state
    buttons.forEach(button => {
        button.addEventListener("click", () => {
            buttons.forEach(btn => btn.classList.remove("selected"));
            button.classList.add("selected");
        });
    });
    
    // File upload handler
    fileInput.addEventListener('change', (event) => {
        const selectedButton = document.querySelector('.button-group .selected');
        if (overlay === '') {
            alert('Please select a filter first!');
            event.target.value = ''; // Clear the file input
            return;
        }

        const file = event.target.files[0];
        if (!file) return;

        const reader = new FileReader();
        reader.onload = function (e) {
            // First display the original image
            preview.src = e.target.result;
            preview.style.display = 'block';
            video.style.display = 'none';

            // Then apply the overlay
            const img = new Image();
            img.onload = function() {
                canvas.width = img.width;
                canvas.height = img.height;
                const ctx = canvas.getContext('2d');
                ctx.drawImage(img, 0, 0);

                const overlayImg = new Image();
                overlayImg.onload = function() {
                    ctx.drawImage(overlayImg, 0, 0, canvas.width, canvas.height);
                    preview.src = canvas.toDataURL('image/png');
                };
            };
            img.src = e.target.result;
        };
        reader.readAsDataURL(file);
    });

    // Image upload button event listener
    sendToDBBtn.addEventListener('click', () => {
        const selectedButton = document.querySelector('.button-group .selected');
        if (overlay === '') {
            alert("Please select a filter before sending to database.");
            return;
        }

        const imageData = preview.src;
        if (!imageData || !imageData.startsWith('data:image')) {
            alert("No valid image found. Capture or upload an image first.");
            return;
        }
        sendImageToServer(imageData);
    });

    function sendImageToServer(imageData) {
        const selectedButton = document.querySelector('.button-group .selected');
        
        if (typeof overlay === 'undefined' || overlay === '') {
            alert("Please select a filter before sending to the database.");
            return;
        }
    
        const payload = {
            image: imageData,
            effect: overlay
        };
    
        fetch('/upload_image.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            credentials: 'include',
            body: JSON.stringify(payload)
        })
        .then(response => {
            console.log("Raw Response:", response);
            return response.json().catch(() => {
                throw new Error("Invalid JSON response from server");
            });
        })
        .then(data => {
            console.log("Parsed Response:", data);
            if (data.status === 'success') {
                alert("Image uploaded successfully!");
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            } else {
                alert(`Error: ${data.message}`);
            }
        })
        .catch(error => {
            console.error("Error uploading image:", error);
            alert("Upload failed: " + error.message);
        });
    }
    
    


    loadSidebar();
    loadImages();

    // User Management Form Validation
    document.addEventListener('DOMContentLoaded', function() {
        const profileForm = document.getElementById('profile-form');
        if (profileForm) {
            profileForm.addEventListener('submit', function(e) {
                const newPassword = document.getElementById('new_password').value;
                const confirmPassword = document.getElementById('confirm_password').value;
                
                // If new password is provided, validate it
                if (newPassword) {
                    if (newPassword.length < 8) {
                        e.preventDefault();
                        alert('New password must be at least 8 characters long');
                        return;
                    }
                    
                    if (newPassword !== confirmPassword) {
                        e.preventDefault();
                        alert('New passwords do not match');
                        return;
                    }
                }
                
                // If new password is provided, confirm password is required
                if (newPassword && !confirmPassword) {
                    e.preventDefault();
                    alert('Please confirm your new password');
                    return;
                }
            });
        }
    });
});
