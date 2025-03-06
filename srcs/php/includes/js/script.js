document.addEventListener('DOMContentLoaded', function() {
    const startCameraBtn = document.getElementById('startCameraBtn');
    if (startCameraBtn) {
        startCameraBtn.addEventListener('click', function() {
            // Your camera initialization code here
        });
    }

    const fileInput = document.getElementById('fileInput');
    const uploadLabel = fileInput.parentElement;
    const filterButtons = document.querySelectorAll('.button-group button');
    let selectedFilter = null;

    // Disable file upload initially
    uploadLabel.style.opacity = '0.5';
    uploadLabel.style.cursor = 'not-allowed';
    fileInput.disabled = true;

    // Handle filter selection
    filterButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Remove active state from all buttons
            filterButtons.forEach(btn => btn.classList.remove('active'));
            
            // Add active state to clicked button
            this.classList.add('active');
            selectedFilter = this.id;
            
            // Enable file upload
            uploadLabel.style.opacity = '1';
            uploadLabel.style.cursor = 'pointer';
            fileInput.disabled = false;
        });
    });

    // Handle file selection
    fileInput.addEventListener('change', function(e) {
        if (!selectedFilter) {
            alert('Please select a filter first!');
            this.value = ''; // Clear the file input
            return;
        }

        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.getElementById('preview');
                preview.src = e.target.result;
                preview.style.display = 'block';
                document.getElementById('video').style.display = 'none';
            };
            reader.readAsDataURL(file);
        }
    });
}); 