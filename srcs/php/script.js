document.addEventListener("DOMContentLoaded", () => {
    // Existing element selections
    const startCameraBtn = document.getElementById('startCamera');
    const fileInput = document.getElementById('fileInput');
    const sendToDBBtn = document.getElementById('sendToDB');
    const effectButtons = document.querySelectorAll('.button-group .btn-success');
    const video = document.getElementById('video');
    const preview = document.getElementById('preview');
    const canvas = document.getElementById('canvas');
    const sidebarContainer = document.getElementById('sidebar-container');
    const buttons = document.querySelectorAll(".btn-success");
    let stream = null;
    let overlay = '';

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

    // Effect buttons event listeners
    effectButtons.forEach(button => {
        button.addEventListener("click", () => {
            effectButtons.forEach(btn => btn.classList.remove("selected"));
            button.classList.add("selected");

            const captureBtn = document.querySelector('.capture-btn');
            if (captureBtn) {
                captureBtn.disabled = false;
                captureBtn.style.opacity = 1;
            }
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

        // Use the selected button to map the overlay image
        if (selectedButton.id === 'cat') {
            overlay = 'cat';
        } else if (selectedButton.id === 'sun') {
            overlay = 'sun';
        } else if (selectedButton.id === 'flower') {
            overlay = 'flower';
        }

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
        fetch('/pics.php', { cache: "no-store" })
            .then(response => response.text())
            .then(data => {
                sidebarContainer.innerHTML = data;
            })
            .catch(error => console.error('Error loading sidebar:', error));
    }

    function loadImages() {
        fetch('pics_api.php')
          .then(response => response.json())
          .then(data => {
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

                      const likeBtn = document.createElement('button');
                      likeBtn.className = 'like-button';
                      likeBtn.innerText = '❤️';
                      likeBtn.style.position = "absolute";
                      likeBtn.style.top = "8px";
                      likeBtn.style.left = "8px";
                      likeBtn.style.backgroundColor = "rgba(200,200,200,0.8)";
                      likeBtn.style.border = "none";
                      likeBtn.style.borderRadius = "50%";
                      likeBtn.style.padding = "8px";
                      likeBtn.style.cursor = "pointer";
                      likeBtn.setAttribute("data-image-id", imageData.id);

                      if (localStorage.getItem(`liked_${imageData.id}`) === "true") {
                          likeBtn.style.backgroundColor = "red";
                      }

                      likeBtn.addEventListener('click', () => {
                          likeImage(imageData.id, likeBtn);
                      });

                      wrapper.appendChild(img);
                      wrapper.appendChild(likeBtn);
                      container.appendChild(wrapper);
                  });
              } else {
                  container.innerHTML += "<p>No images found.</p>";
              }
          })
          .catch(error => {
              console.error("Error fetching images:", error);
              document.getElementById('previewArea').innerHTML = "<p>Error loading images.</p>";
          });
    }

    function likeImage(imageId, button) {
        fetch('/likes_api.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            credentials: 'include',
            body: JSON.stringify({ image_id: imageId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                console.log("Like saved:", data.success);
                button.style.backgroundColor = "red";
                localStorage.setItem(`liked_${imageId}`, "true");
            } else {
                console.error("Like failed:", data.error);
            }
        })
        .catch(error => console.error('Error liking image:', error));
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
        const file = event.target.files[0];
        if (!file) return;

        const reader = new FileReader();
        reader.onload = function (e) {
            preview.src = e.target.result;
            preview.style.display = 'block';
            video.style.display = 'none';
        };
        reader.readAsDataURL(file);
    });

    // Image upload button event listener
    sendToDBBtn.addEventListener('click', () => {
        const imageData = preview.src;
        if (!imageData || !imageData.startsWith('data:image')) {
            alert("No valid image found. Capture or upload an image first.");
            return;
        }
        sendImageToServer(imageData);
    });

    // Updated sendImageToServer: define selectedButton before using it
    function sendImageToServer(imageData) {
        const selectedButton = document.querySelector('.button-group .selected');

        let effect = overlay;
        const payload = {
            image: imageData,
            effect: effect
        };
    
        fetch('/upload_image.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            credentials: 'include',
            body: JSON.stringify(payload)
        })
        .then(response => response.json())
        .then(data => {
            console.log("Upload response:", data);
            alert(data.message);
            loadImages();
        })
        .catch(error => {
            console.error('Error uploading image:', error);
            alert('Failed to upload image. Please try again.');
        });
    }

    loadSidebar();
    loadImages();
});
