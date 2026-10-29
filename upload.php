<?php
if (!empty($_FILES)) {
    error_log("Received audio upload\r\n", 3, "Log.log");
}

if (isset($_FILES['audio_data']) && $_FILES['audio_data']['error'] === UPLOAD_ERR_OK) {
    $size     = $_FILES['audio_data']['size'];
    $input    = $_FILES['audio_data']['tmp_name'];

    // recordings/ folder mein save karo — pehle folder banao agar nahi hai
    $dir = 'recordings/';
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }

    // Unique filename: original name + .wav
    $safeName = basename($_FILES['audio_data']['name']);
    $output   = $dir . $safeName . '.wav';

    move_uploaded_file($input, $output);
}
?>
