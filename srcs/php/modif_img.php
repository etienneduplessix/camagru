<?php
function overlayPngOnBase64($backgroundBase64, $overlayType) {
    // Strip the data URI scheme and decode
    $backgroundData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $backgroundBase64));
    
    // Build the path for the overlay image
    $overlayPath = __DIR__ . "/png/" . $overlayType . ".png";
    if (!file_exists($overlayPath)) {
        return json_encode(['error' => 'Overlay image not found.']);
    }
    
    // Create image resources from the background and overlay
    $background = imagecreatefromstring($backgroundData);
    if (!$background) {
        return json_encode(['error' => 'Invalid background image data.']);
    }
    
    $overlay = imagecreatefrompng($overlayPath);
    if (!$overlay) {
        return json_encode(['error' => 'Invalid overlay image data.']);
    }
    
    // Get dimensions
    $bgWidth = imagesx($background);
    $bgHeight = imagesy($background);
    $overlayWidth = imagesx($overlay);
    $overlayHeight = imagesy($overlay);
    
    // Create a new truecolor image with transparency support
    $finalImage = imagecreatetruecolor($bgWidth, $bgHeight);
    // Turn off alpha blending and enable saving of alpha channel
    imagealphablending($finalImage, false);
    imagesavealpha($finalImage, true);
    
    // Fill the final image with transparent color
    $transparent = imagecolorallocatealpha($finalImage, 100, 100, 100, 127);
    imagefilledrectangle($finalImage, 0, 0, $bgWidth, $bgHeight, $transparent);
    
    // Copy the background onto the final image
    imagecopy($finalImage, $background, 0, 0, 0, 0, $bgWidth, $bgHeight);
    
    // Calculate position to center the overlay
    $x = ($bgWidth - $overlayWidth) / 2;
    $y = ($bgHeight - $overlayHeight) / 2;
    
    // Ensure the overlay has proper alpha settings
    imagealphablending($finalImage, true);
    // Copy the overlay onto the final image
    imagecopy($finalImage, $overlay, $x, $y, 0, 0, $overlayWidth, $overlayHeight);
    
    // Output the final image to a buffer
    ob_start();
    imagepng($finalImage);
    $outputData = ob_get_clean();
    
    // Clean up
    imagedestroy($background);
    imagedestroy($overlay);
    imagedestroy($finalImage);
    
    return 'data:image/png;base64,' . base64_encode($outputData);
}
?>