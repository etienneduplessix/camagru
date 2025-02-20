document.addEventListener("DOMContentLoaded", () => {
      // Ensure all DOM elements exist before adding event listeners
      const startCameraBtn = document.getElementById('startCamera');
      const fileInput = document.getElementById('fileInput');
      const sendToDBBtn = document.getElementById('sendToDB');
      const video = document.getElementById('video');
      const preview = document.getElementById('preview');
      const canvas = document.getElementById('canvas');
      const sidebarContainer = document.getElementById('sidebar-container');
      const buttons = document.querySelectorAll(".btn-success");
      const selectedText = document.getElementById("selected-item");
      let stream = null;
  
      /** Load Sidebar **/
      function loadSidebar() {
          fetch('/pics.php', { cache: "no-store" })
              .then(response => response.text())
              .then(data => {
                  sidebarContainer.innerHTML = data;
              })
              .catch(error => console.error('Error loading sidebar:', error));
      }

      /** Load Images **/
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
                        // Create a wrapper div to hold the image and like button
                        const wrapper = document.createElement('div');
                        wrapper.className = 'wrapper';
                        wrapper.style.position = "relative"; 
      
                        // Create image element
                        const img = new Image();
                        img.src = imageData.src;
                        img.onerror = () => console.error("Error loading:", img.src);
                        img.style.width = "100%";
                        img.style.display = "block"; 
      
                        // Create the like button
                        const likeBtn = document.createElement('button');
                        likeBtn.className = 'like-button';
                        likeBtn.innerText = '❤️';
                        likeBtn.style.position = "absolute";
                        likeBtn.style.top = "8px";
                        likeBtn.style.left = "8px";
                        likeBtn.style.backgroundColor = "rgba(200,200,200,0.8)"; // Default grey
                        likeBtn.style.border = "none";
                        likeBtn.style.borderRadius = "50%";
                        likeBtn.style.padding = "8px";
                        likeBtn.style.cursor = "pointer";
                        likeBtn.setAttribute("data-image-id", imageData.id); 
      
                        // Check if the image was already liked (from localStorage)
                        if (localStorage.getItem(`liked_${imageData.id}`) === "true") {
                        likeBtn.style.backgroundColor = "red"; // Keep red if previously liked
                        }
      
                        // Like button event listener
                        likeBtn.addEventListener('click', () => {
                        likeImage(imageData.id, likeBtn);
                        });
      
                        // Append image and like button to the wrapper
                        wrapper.appendChild(img);
                        wrapper.appendChild(likeBtn);
      
                        // Append the wrapper to the container
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
  
      /** Function to send like request to the server **/
      function likeImage(imageId, button) {
            fetch('/likes_api.php', {
            method: 'POST',
            headers: {
                  'Content-Type': 'application/json'
            },
            credentials: 'include',
            body: JSON.stringify({ image_id: imageId })
            })
            .then(response => response.json())
            .then(data => {
            if (data.success) {
                  console.log("Like saved:", data.success);
                  button.style.backgroundColor = "red"; // Change to red on success
                  localStorage.setItem(`liked_${imageId}`, "true"); // Store liked state
            } else {
                  console.error("Like failed:", data.error);
            }
            })
            .catch(error => console.error('Error liking image:', error));
      }
      
  
      /** Start Camera **/
      startCameraBtn.addEventListener('click', async () => {
          try {
              stream = await navigator.mediaDevices.getUserMedia({ video: true });
              video.srcObject = stream;
              video.style.display = 'block';
              preview.style.display = 'none';
  
              // Create capture button only if it doesn't exist
              if (!document.querySelector('.capture-btn')) {
                  const captureBtn = document.createElement('button');
                  captureBtn.className = 'btn btn-danger capture-btn';
                  captureBtn.textContent = 'Capture';
                  captureBtn.onclick = capturePhoto;
                  video.parentElement.appendChild(captureBtn);
              }
          } catch (err) {
              console.error('Error accessing camera:', err);
              alert('Camera access denied or unavailable.');
          }
      });
  
      /** Capture Photo **/
      function capturePhoto() {
          canvas.width = video.videoWidth;
          canvas.height = video.videoHeight;
          const ctx = canvas.getContext('2d');
          ctx.drawImage(video, 0, 0);
          const imageData = canvas.toDataURL('image/png');
  
          preview.src = imageData;
          preview.style.display = 'block';
  
          // Stop camera stream
          if (stream) {
              stream.getTracks().forEach(track => track.stop());
              stream = null;
          }
          video.style.display = 'none';
  
          // Remove capture button after use
          const captureBtn = document.querySelector('.capture-btn');
          if (captureBtn) captureBtn.remove();
      }
      


      buttons.forEach(button => {
          button.addEventListener("click", () => {
              // Remove selected class from all buttons
              buttons.forEach(btn => btn.classList.remove("selected"));

              // Add selected class to the clicked button
              button.classList.add("selected");

              // Display selected button text
              selectedText.textContent = button.textContent;
          });
      });
      
      /** Handle File Upload **/
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
  
      /** Send Image to Database **/
      sendToDBBtn.addEventListener('click', () => {
          const imageData = preview.src;
          if (!imageData || !imageData.startsWith('data:image')) {
              alert("No valid image found. Capture or upload an image first.");
              return;
          }
          sendImageToServer(imageData);
      });
  
      /** Upload Image to Server **/
      function sendImageToServer(imageData) {
          const payload = { image: imageData }; // Create a payload object containing the image data
          fetch('/upload_image.php', {
              method: 'POST',
              headers: {
                  'Content-Type': 'application/json'
              },
              credentials: 'include', // Ensures PHPSESSID cookie is sent
              body: JSON.stringify(payload)
          })
          .then(response => response.json())
          .then(data => {
              console.log(data);
              alert(data.message);
          })
          .catch(error => console.error('Error uploading image:', error));
      }
              
  
      // Ensure sidebar and images are loaded when the page is ready
      loadSidebar();
      loadImages();
  });
  