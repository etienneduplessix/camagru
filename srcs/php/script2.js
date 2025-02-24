document.addEventListener("DOMContentLoaded", () => {
      /** Load Images and Associated Comments **/
      function loadImages() {
          fetch('pics_api.php')
          .then(response => response.json())
          .then(imagesData => {
              fetch('get_comment.php')
              .then(response => response.json())
              .then(commentsData => {
                  const container = document.getElementById('imageContainer');
                  if (!container) {
                      console.error("imageContainer element not found!");
                      return;
                  }
                  container.innerHTML = "<h2>Images</h2>";
  
                  imagesData.forEach(imageData => {
                      // Create wrapper for each image
                      const wrapper = document.createElement('div');
                      wrapper.className = 'wrapper';
                      wrapper.style.marginBottom = "20px";
                      wrapper.style.border = "1px solid #ddd";
                      wrapper.style.padding = "10px";
                      wrapper.style.borderRadius = "8px";
  
                      // Create image element
                      const img = new Image();
                      img.src = imageData.src;
                      img.onerror = () => console.error("Error loading:", img.src);
                      img.style.width = "100%";
                      img.style.display = "block";
  
                      // Create like button
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
  
                      // Comment Section
                      const commentSection = document.createElement('div');
                      commentSection.style.marginTop = "10px";
  
                      // Comment input field
                      const commentInput = document.createElement('input');
                      commentInput.type = 'text';
                      commentInput.placeholder = 'Write a comment...';
                      commentInput.style.width = "80%";
                      commentInput.style.padding = "5px";
                      commentInput.style.marginRight = "5px";
                      commentInput.setAttribute("data-image-id", imageData.id);
  
                      // Submit comment button
                      const submitCommentBtn = document.createElement('button');
                      submitCommentBtn.innerText = 'Post';
                      submitCommentBtn.className = 'comment-button';
                      submitCommentBtn.style.cursor = "pointer";
  
                      submitCommentBtn.addEventListener('click', () => {
                          const commentText = commentInput.value.trim();
                          if (commentText !== "") {
                              putComment(imageData.id, commentText, commentsContainer);
                              commentInput.value = "";
                          }
                      });
  
                      // Comments display container
                      const commentsContainer = document.createElement('div');
                      commentsContainer.style.marginTop = "10px";
                      commentsContainer.style.fontSize = "14px";
                      commentsContainer.style.border = "1px solid #ccc";
                      commentsContainer.style.padding = "5px";
                      commentsContainer.style.borderRadius = "5px";
                      commentsContainer.style.backgroundColor = "#f9f9f9";
  
                      // Load existing comments for this image
                      if (commentsData[imageData.id]) {
                          commentsData[imageData.id].forEach(comment => {
                              displayComment(comment.comment_text, commentsContainer);
                          });
                      }
  
                      // Append elements
                      commentSection.appendChild(commentInput);
                      commentSection.appendChild(submitCommentBtn);
                      wrapper.appendChild(img);
                      wrapper.appendChild(likeBtn);
                      wrapper.appendChild(commentsContainer);
                      wrapper.appendChild(commentSection);
                      container.appendChild(wrapper);
                  });
              })
              .catch(error => console.error("Error fetching comments:", error));
          })
          .catch(error => console.error("Error fetching images:", error));
      }
  
      /** Function to Post a Comment **/
      function putComment(imageId, comment, commentContainer) {
          fetch("http://localhost:8000/put_comment.php", {
              method: "POST",
              headers: {
                  "Content-Type": "application/json"
              },
              credentials: "include",
              body: JSON.stringify({
                  image_id: imageId,
                  comment: comment
              })
          })
          .then(response => response.json())
          .then(data => {
              if (data.success) {
                  console.log("Comment posted:", data.success);
                  displayComment(comment, commentContainer);
              } else {
                  console.error("Error posting comment:", data.error);
              }
          })
          .catch(error => console.error("Error:", error));
      }
  
      /** Function to Display a Comment in the UI **/
      function displayComment(commentText, commentContainer) {
          const commentItem = document.createElement('p');
          commentItem.innerText = commentText;
          commentItem.style.margin = "5px 0";
          commentItem.style.padding = "5px";
          commentItem.style.borderBottom = "1px solid #ddd";
          commentContainer.appendChild(commentItem);
      }
  
      // Load images and comments when the page is ready
      loadImages();
  });
  