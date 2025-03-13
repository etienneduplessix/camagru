document.addEventListener("DOMContentLoaded", () => {
    let userLoggedIn = false; // Default to false, will be updated by API check

    /** Function to Check If User is Logged In **/
    function checkUserLoginStatus() {
        return fetch("check_login.php", { credentials: "include" })
            .then(response => response.json())
            .then(data => {
                userLoggedIn = data.logged_in; // Set global variable
            })
            .catch(error => {
                console.error("Error checking login status:", error);
            });
    }

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

                    // Add Like Button
                    const likeBtn = document.createElement('button');
                    likeBtn.className = 'like-button';
                    likeBtn.innerText = '❤️';
                    likeBtn.setAttribute("data-image-id", imageData.id);
                    likeBtn.style.marginRight = "5px";
                    likeBtn.style.position = "absolute";
                    likeBtn.style.top = "8px";
                    likeBtn.style.left = "8px";

                    // Restore like state from localStorage
                    if (localStorage.getItem(`liked_${imageData.id}`) === "true") {
                        likeBtn.style.backgroundColor = "white";
                        likeBtn.style.color = "red";
                    }

                    likeBtn.addEventListener('click', () => {
                        if (!userLoggedIn) {
                            alert("You must be logged in to like an image.");
                            return;
                        }
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
                    submitCommentBtn.style.cursor = "pointer";
                    submitCommentBtn.style.left = "1000px";  

                    submitCommentBtn.addEventListener('click', () => {
                        if (!userLoggedIn) {
                            alert("You must be logged in to post a comment.");
                            return;
                        }
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
    function likeImage(imageId, button) {
        if (!userLoggedIn) {
            alert("You must be logged in to like an image.");
            return;
        }

        // Send like request to API first, then update UI if successful
        fetch("likes_api.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            credentials: "include",
            body: JSON.stringify({ image_id: imageId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Toggle the like state only if the request is successful
                const isLiked = localStorage.getItem(`liked_${imageId}`) === "true";
                const newLikeStatus = !isLiked;

                // Update UI
                button.style.backgroundColor = newLikeStatus ? "red" : "rgba(200,200,200,0.8)";
                localStorage.setItem(`liked_${imageId}`, newLikeStatus ? "true" : "false");
            } else {
                alert("Failed to like image.");
            }
        })
        .catch(error => {
            console.error("Error liking image:", error);
        });
    }

    /** Function to Post a Comment **/
    function putComment(imageId, comment, commentContainer) {
        if (!userLoggedIn) {
            alert("You must be logged in to post a comment.");
            return;
        }

        fetch("put_comment.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            credentials: "include",
            body: JSON.stringify({ image_id: imageId, comment: comment })
        })
        .then(response => response.json())
        .then(() => {
            location.reload();
        })
        .catch(error => console.error("Error posting comment:", error));
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

    // First, check if the user is logged in before loading images
    checkUserLoginStatus().then(() => {
        loadImages();
    });
});
