<?php
$host = 'localhost';
$dbname = 'u82192';      // ЗАМЕНИТЕ на свой логин
$username = 'u82192';     // ЗАМЕНИТЕ на свой логин
$password = '2307509';    // ЗАМЕНИТЕ на свой пароль

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Ошибка подключения к БД: " . $e->getMessage());
}

$full_name = $_POST['full_name'] ?? '';
$phone = $_POST['phone'] ?? '';
$email = $_POST['email'] ?? '';
$birth_date = $_POST['birth_date'] ?? '';
$gender = $_POST['gender'] ?? '';
$bio = $_POST['bio'] ?? '';
$contract = isset($_POST['contract']) ? 1 : 0;
$languages = $_POST['languages'] ?? [];

$errors = [];

if (!preg_match('/^[а-яА-ЯёЁa-zA-Z\s]{1,150}$/u', $full_name)) {
    $errors[] = 'ФИО должно содержать только буквы и пробелы (не длиннее 150 символов)';
}

$phone_clean = preg_replace('/[^0-9+]/', '', $phone);
if (strlen($phone_clean) < 10) {
    $errors[] = 'Телефон должен содержать минимум 10 цифр';
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Введите корректный email';
}

if (empty($birth_date)) {
    $errors[] = 'Укажите дату рождения';
}

if (!in_array($gender, ['male', 'female'])) {
    $errors[] = 'Выберите пол';
}

if (empty($languages)) {
    $errors[] = 'Выберите хотя бы один язык программирования';
}

if (!$contract) {
    $errors[] = 'Необходимо подтвердить ознакомление с контрактом';
}

if (!empty($errors)) {
    echo '<div style="background: #ffebee; color: #c33; padding: 15px; border-radius: 5px; margin: 20px; border-left: 4px solid #c33;">';
    echo '<h3>Ошибки при заполнении формы:</h3>';
    echo '<ul>';
    foreach ($errors as $error) {
        echo "<li>$error</li>";
    }
    echo '</ul>';
    echo '<p><a href="index.html">Вернуться к форме</a></p>';
    echo '</div>';
    exit;
}

try {
    $pdo->beginTransaction();
    
    $stmt = $pdo->prepare("
        INSERT INTO applications 
        (full_name, phone, email, birth_date, gender, bio, contract_accepted) 
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");
    
    $stmt->execute([
        $full_name, $phone, $email, $birth_date, $gender, $bio, $contract
    ]);
    
    $application_id = $pdo->lastInsertId();
    
    $stmt = $pdo->prepare("
        INSERT INTO application_languages (application_id, language_id) 
        VALUES (?, ?)
    ");
    
    foreach ($languages as $lang_id) {
        $stmt->execute([$application_id, $lang_id]);
    }
    
    $pdo->commit();
    
    echo '<div style="background: #e8f5e9; color: #2e7d32; padding: 15px; border-radius: 5px; margin: 20px; border-left: 4px solid #2e7d32;">';
    echo '<h3>✅ Данные успешно сохранены!</h3>';
    echo '<p>Спасибо за заполнение анкеты.</p>';
    echo '<p><a href="index.html" style="color: #2e7d32;">Вернуться к форме</a></p>';
    echo '</div>';
    
} catch (PDOException $e) {
    $pdo->rollBack();
    
    echo '<div style="background: #ffebee; color: #c33; padding: 15px; border-radius: 5px; margin: 20px;">';
    echo '<h3>❌ Ошибка при сохранении в БД</h3>';
    echo '<p>' . $e->getMessage() . '</p>';
    echo '</div>';
}
?>