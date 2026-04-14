<?php
/**
 * Utils Class
 * Miscellaneous helpers
 */

class Utils {
    
    // Slug generation
    public static function slugify($text) {
        // Replace non-alphanumeric with hyphen
        $text = preg_replace('~[^\pL\d]+~u', '-', $text);
        // Transliterate
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
        // Remove unwanted characters
        $text = preg_replace('~[^-\w]+~', '', $text);
        // Trim
        $text = trim($text, '-');
        // Remove duplicate hyphens
        $text = preg_replace('~-+~', '-', $text);
        // Lowercase
        $text = strtolower($text);

        if (empty($text)) {
            return 'n-a';
        }

        return $text;
    }

    // Input sanitization
    public static function sanitize($data) {
        return htmlspecialchars(strip_tags(trim($data)));
    }

    // Image Upload helper
    public static function uploadImage($file, $targetDir) {
        $imageFileType = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));
        $newFileName = uniqid() . '.' . $imageFileType;
        $targetFile = $targetDir . $newFileName;
        
        // Check if image file is an actual image
        $check = getimagesize($file["tmp_name"]);
        if($check === false) return false;

        // Check file size (max 5MB)
        if ($file["size"] > 5000000) return false;

        // Allow certain file formats
        if(!in_array($imageFileType, ['jpg', 'png', 'jpeg', 'gif'])) return false;

        if (move_uploaded_file($file["tmp_name"], $targetFile)) {
            return $newFileName;
        } else {
            return false;
        }
    }

    // Download Image from URL helper
    public static function downloadImageFromUrl($url, $targetDir) {
        if (filter_var($url, FILTER_VALIDATE_URL)) {
            $imageContent = @file_get_contents($url);
            if ($imageContent !== false) {
                // Determine extension from URL or default to jpg
                $path = parse_url($url, PHP_URL_PATH);
                $ext = pathinfo($path, PATHINFO_EXTENSION);
                if (!in_array(strtolower($ext), ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                    $ext = 'jpg';
                }
                
                $newFileName = 'imported_' . uniqid() . '.' . $ext;
                $targetFile = $targetDir . $newFileName;
                
                if (file_put_contents($targetFile, $imageContent)) {
                    return $newFileName;
                }
            }
        }
        return false;
    }

    // Format money
    public static function formatPrice($amount) {
        return '$' . number_format($amount, 2);
    }
}
