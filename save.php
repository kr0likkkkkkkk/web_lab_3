<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// ===== НАСТРОЙКИ БД - ИЗМЕНИТЕ НА СВОИ! =====
$db_host = 'localhost';
$db_name = 'u12345';        // ЗАМЕНИТЕ на имя вашей БД
$db_user = 'u12345';        // ЗАМЕНИТЕ на ваш логин
$db_pass = 'your_password'; // ЗАМЕНИТЕ на ваш пароль
// ============================================

// Проверяем метод запроса
if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    header('Location: index.php');
    exit();
}

// Сохраняем старые значения
$_SESSION['old'] = $_POST;
$errors = [];

// 1. Валидация ФИО
$full_name = trim($_POST['full_name'] ?? '');
if (empty($full_name)) {
    $errors[] = "ФИО обязательно для заполнения";
} elseif (strlen($full_name) > 150) {
    $errors[] = "ФИО не должно превышать 150 символов";
} elseif (!preg_match('/^[a-zA-Zа-яА-ЯёЁ\s\-]+$/u', $full_name)) {
    $errors[] = "ФИО может содержать только буквы, пробелы и дефисы";
}

// 2. Валидация телефона
$phone = trim($_POST['phone'] ?? '');
if (empty($phone)) {
    $errors[] = "Телефон обязателен для заполнения";
} else {
    $cleanPhone = preg_replace('/[^0-9+]/', '', $phone);
    if (strlen($cleanPhone) < 10 || strlen($cleanPhone) > 15) {
        $errors[] = "Неверный формат телефона";
    }
}

// 3. Валидация email
$email = trim($_POST['email'] ?? '');
if (empty($email)) {
    $errors[] = "Email обязателен для заполнения";
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Неверный формат email";
} elseif (strlen($email) > 100) {
    $errors[] = "Email не должен превышать 100 символов";
}

// 4. Валидация даты рождения
$birth_date = $_POST['birth_date'] ?? '';
if (empty($birth_date)) {
    $errors[] = "Дата рождения обязательна для заполнения";
} else {
    $timestamp = strtotime($birth_date);
    if (!$timestamp) {
        $errors[] = "Неверный формат даты";
    } else {
        $age = date('Y') - date('Y', $timestamp);
        if (date('md') < date('md', $timestamp)) $age--;
        if ($age < 16 || $age > 120) {
            $errors[] = "Возраст должен быть от 16 до 120 лет";
        }
    }
}

// 5. Валидация пола
$gender = $_POST['gender'] ?? '';
$allowed_genders = ['male', 'female', 'other'];
if (empty($gender) || !in_array($gender, $allowed_genders)) {
    $errors[] = "Выберите корректный пол";
}

// 6. Валидация языков
$languages = $_POST['languages'] ?? [];
if (empty($languages)) {
    $errors[] = "Выберите хотя бы один язык программирования";
}

// 7. Валидация контракта
$agreed = $_POST['agreed_to_contract'] ?? '';
if ($agreed != '1') {
    $errors[] = "Вы должны ознакомиться с контрактом";
}

// Если есть ошибки - возвращаемся
if (!empty($errors)) {
    $_SESSION['errors'] = $errors;
    header('Location: index.php');
    exit();
}

// Сохранение в БД
try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $pdo->beginTransaction();
    
    // Вставка в applications
    $stmt = $pdo->prepare("
        INSERT INTO applications (full_name, phone, email, birth_date, gender, biography, agreed_to_contract) 
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");
    
    $biography = trim($_POST['biography'] ?? '');
    $agreed_value = ($agreed == '1') ? 1 : 0;
    
    $stmt->execute([$full_name, $phone, $email, $birth_date, $gender, $biography, $agreed_value]);
    $application_id = $pdo->lastInsertId();
    
    // Вставка языков
    $lang_stmt = $pdo->prepare("INSERT INTO application_languages (application_id, language_id) VALUES (?, ?)");
    foreach ($languages as $lang_id) {
        $lang_stmt->execute([$application_id, $lang_id]);
    }
    
    $pdo->commit();
    
    // Успех
    unset($_SESSION['old']);
    $_SESSION['success_message'] = "Данные успешно сохранены! Номер заявки: " . $application_id;
    
} catch (PDOException $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    $_SESSION['errors'] = ["Ошибка базы данных: " . $e->getMessage()];
}

header('Location: index.php');
exit();
?>
