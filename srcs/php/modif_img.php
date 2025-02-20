<?php

function overlayPngOnBase64($backgroundBase64, $overlayType) {
    $backgroundData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $backgroundBase64));
    
    $overlayPath = __DIR__ . "/png/" . $overlayType . ".png";
    if (!file_exists($overlayPath)) {
        return json_encode(['error' => 'Overlay image not found.']);
    }
    
    $background = imagecreatefromstring($backgroundData);
    $overlay = imagecreatefrompng($overlayPath);
    
    if (!$background || !$overlay) {
        return json_encode(['error' => 'Invalid image data.']);
    }
    
    $bgWidth = imagesx($background);
    $bgHeight = imagesy($background);
    $overlayWidth = imagesx($overlay);
    $overlayHeight = imagesy($overlay);
    
    $x = $bgWidth - $overlayWidth - 10; // Position in the right corner with 10px margin
    $y = $bgHeight - $overlayHeight - 10;
    
    imagealphablending($background, true);
    imagesavealpha($background, true);
    
    imagecopy($background, $overlay, $x, $y, 0, 0, $overlayWidth, $overlayHeight);
    
    ob_start();
    imagepng($background);
    $outputData = ob_get_clean();
    
    imagedestroy($background);
    imagedestroy($overlay);
    
    return 'data:image/png;base64,' . base64_encode($outputData);
}
?>