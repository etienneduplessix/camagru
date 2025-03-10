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
                    likeBtn.className = 'like-button like-bottom-left';
                    likeBtn.innerText = '❤️';
                    likeBtn.setAttribute("data-image-id", imageData.id);

                    // Set button position
                    likeBtn.style.position = "absolute";
                    likeBtn.style.bottom = "8px";
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
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    console.error("Like failed:", data.error);
                    alert(data.error); // Display error to user
                    localStorage.setItem(`liked_${imageId}`, isLiked ? "true" : "false");
                    button.style.backgroundColor = isLiked ? "red" : "rgba(200,200,200,0.8)";
                } else {
                    console.log("Like successful:", data.success);
                }
            })
            .catch(error => {
                console.error("Error:", error);
                alert("An error occurred. Please try again.");
                // Revert UI on failure
                localStorage.setItem(`liked_${imageId}`, isLiked ? "true" : "false");
                button.style.backgroundColor = isLiked ? "red" : "rgba(200,200,200,0.8)";
            });
        }


    /** Function to Post a Comment **/
    function putComment(imageId, comment, commentContainer) {
        fetch("put_comment.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            credentials: "include",
            body: JSON.stringify({ image_id: imageId, comment: comment })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
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
