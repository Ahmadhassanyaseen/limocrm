<?php
session_start();
$user_id = $_SESSION['user']['id'];

// Handle logo upload
$logo_url = '';
if(isset($_FILES['logo']) && $_FILES['logo']['error'] === 0) {
    $upload_dir = 'uploads/logos/';
    $filename = uniqid() . '_' . basename($_FILES['logo']['name']);
    $target = $upload_dir . $filename;
    if(move_uploaded_file($_FILES['logo']['tmp_name'], $target)) {
        $logo_url = 'https://zabrin.xyz/' . $target;
    }
}

$data = [
    'user_id' => $user_id,
    'primary_color' => $_POST['primary_color'] ?? '#ec268f',
    'logo_url' => $logo_url ?: ($_POST['existing_logo'] ?? ''),
    'button_text' => $_POST['button_text'] ?? 'Search Vehicles'
];

// Save to DB (example with PDO)
$stmt = $pdo->prepare("
    INSERT INTO user_form_customizations (user_id, primary_color, logo_url, button_text) 
    VALUES (?, ?, ?, ?)
    ON DUPLICATE KEY UPDATE 
    primary_color = VALUES(primary_color),
    logo_url = VALUES(logo_url),
    button_text = VALUES(button_text)
");
$stmt->execute([$data['user_id'], $data['primary_color'], $data['logo_url'], $data['button_text']]);

header("Location: client-customize-form.php?saved=1");
exit;