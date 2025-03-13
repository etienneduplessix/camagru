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
                    wrapper.style.position = "relative";
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
                    img.style.borderRadius = "8px";

                    // Append image to wrapper
                    wrapper.appendChild(img);

                    // Add Like Button (Bottom-Left Only)
                    const likeBtn = document.createElement('button');
                    likeBtn.className = 'like-button';
                    likeBtn.innerText = '❤️';
                    likeBtn.setAttribute("data-image-id", imageData.id);
                    likeBtn.style.marginRight = "5px"

                    // Set button position
                    likeBtn.style.position = "absolute";
                    likeBtn.style.top = "8px";
                    likeBtn.style.left = "8px";

                    // Restore like state from localStorage
                    if (localStorage.getItem(`liked_${imageData.id}`) === "true") {
                        likeBtn.style.backgroundColor = "red";
                    }

                    likeBtn.addEventListener('click', () => {
                        likeImage(imageData.id, likeBtn);
                    });

                    // Comment Section
                    const commentSection = document.createElement('div');
                    commentSection.className = "comment-section";

                    // Comment input field
                    const commentInput = document.createElement('input');
                    commentInput.type = 'text';
                    commentInput.placeholder = 'Write a comment...';
                    commentInput.style.width = "80%";
                    commentInput.setAttribute("data-image-id", imageData.id);

                    // Submit comment button
                    const submitCommentBtn = document.createElement('button');
                    submitCommentBtn.innerText = 'Post';
                    submitCommentBtn.className = 'comment-button';
                    submitCommentBtn.style.cursor = "pointer";// Positioning
                    submitCommentBtn.style.left = "1000px";  // Anchors to the left

                    submitCommentBtn.addEventListener('click', () => {
                        const commentText = commentInput.value.trim();
                        if (commentText !== "") {
                            putComment(imageData.id, commentText, commentsContainer);
                            commentInput.value = "";
                        }
                    });

                    // Comments display container
                    const commentsContainer = document.createElement('div');
                    commentsContainer.className = "comments-container";

                    // Load existing comments for this image
                    if (commentsData[imageData.id]) {
                        commentsData[imageData.id].forEach(comment => {
                            displayComment(comment.comment_text, commentsContainer);
                        });
                    }

                    // Append elements
                    commentSection.appendChild(commentInput);
                    commentSection.appendChild(submitCommentBtn);
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

    /** Function to Like an Image **/
/** Function to Like an Image **/
        function likeImage(imageId, button) {
            const isLiked = localStorage.getItem(`liked_${imageId}`) === "true";

            // Toggle the like state
            const newLikeStatus = !isLiked;

            // Update UI immediately
            button.style.backgroundColor = newLikeStatus ? "red" : "rgba(200,200,200,0.8)";

            // Save like status in localStorage
            localStorage.setItem(`liked_${imageId}`, newLikeStatus ? "true" : "false");

            // Send the like/unlike request to the API
            fetch("likes_api.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json"
                },
                credentials: "include", // Ensures the session is passed
                body: JSON.stringify({ image_id: imageId })
            })
        }


        /** Function to Post a Comment **/
/** Function to Post a Comment **/
    function putComment(imageId, comment, commentContainer) {
        fetch("put_comment.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            credentials: "include",
            body: JSON.stringify({ image_id: imageId, comment: comment })
        })
        location.reload();
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
