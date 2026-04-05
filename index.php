<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Анкета программиста</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="form-container">
        <h1>Анкета программиста</h1>
        <p class="subtitle">Заполните все поля формы</p>

        <form action="save.php" method="POST">
            <div class="form-group">
                <label for="full_name">ФИО *</label>
                <input type="text" id="full_name" name="full_name" 
                       value="<?php echo htmlspecialchars($_POST['full_name'] ?? ''); ?>"
                       placeholder="Иванов Иван Иванович" required>
            </div>

            <div class="form-row">
                <div class="form-group half-width">
                    <label for="phone">Телефон *</label>
                    <input type="tel" id="phone" name="phone" 
                           value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>"
                           placeholder="+7 (999) 123-45-67" required>
                </div>
                <div class="form-group half-width">
                    <label for="email">Email *</label>
                    <input type="email" id="email" name="email" 
                           value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                           placeholder="example@mail.ru" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group half-width">
                    <label for="birth_date">Дата рождения *</label>
                    <input type="date" id="birth_date" name="birth_date" 
                           value="<?php echo htmlspecialchars($_POST['birth_date'] ?? ''); ?>" required>
                </div>
                <div class="form-group half-width">
                    <label>Пол *</label>
                    <div class="radio-group">
                        <label class="radio-label">
                            <input type="radio" name="gender" value="male" 
                                   <?php echo (($_POST['gender'] ?? '') == 'male') ? 'checked' : ''; ?>> Мужской
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="gender" value="female" 
                                   <?php echo (($_POST['gender'] ?? '') == 'female') ? 'checked' : ''; ?>> Женский
                        </label>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="languages">Любимые языки программирования *</label>
                <select id="languages" name="languages[]" multiple size="4" required>
                    <option value="1" <?php echo (isset($_POST['languages']) && in_array('1', $_POST['languages'])) ? 'selected' : ''; ?>>Pascal</option>
                    <option value="2" <?php echo (isset($_POST['languages']) && in_array('2', $_POST['languages'])) ? 'selected' : ''; ?>>C</option>
                    <option value="3" <?php echo (isset($_POST['languages']) && in_array('3', $_POST['languages'])) ? 'selected' : ''; ?>>C++</option>
                    <option value="4" <?php echo (isset($_POST['languages']) && in_array('4', $_POST['languages'])) ? 'selected' : ''; ?>>JavaScript</option>
                    <option value="5" <?php echo (isset($_POST['languages']) && in_array('5', $_POST['languages'])) ? 'selected' : ''; ?>>PHP</option>
                    <option value="6" <?php echo (isset($_POST['languages']) && in_array('6', $_POST['languages'])) ? 'selected' : ''; ?>>Python</option>
                    <option value="7" <?php echo (isset($_POST['languages']) && in_array('7', $_POST['languages'])) ? 'selected' : ''; ?>>Java</option>
                    <option value="8" <?php echo (isset($_POST['languages']) && in_array('8', $_POST['languages'])) ? 'selected' : ''; ?>>Haskell</option>
                    <option value="9" <?php echo (isset($_POST['languages']) && in_array('9', $_POST['languages'])) ? 'selected' : ''; ?>>Clojure</option>
                    <option value="10" <?php echo (isset($_POST['languages']) && in_array('10', $_POST['languages'])) ? 'selected' : ''; ?>>Prolog</option>
                    <option value="11" <?php echo (isset($_POST['languages']) && in_array('11', $_POST['languages'])) ? 'selected' : ''; ?>>Scala</option>
                    <option value="12" <?php echo (isset($_POST['languages']) && in_array('12', $_POST['languages'])) ? 'selected' : ''; ?>>Go</option>
                </select>
                <small class="hint">Удерживайте Ctrl (Cmd на Mac) для выбора нескольких</small>
            </div>

            <div class="form-group">
                <label for="bio">Биография</label>
                <textarea id="bio" name="bio" rows="5" 
                          placeholder="Расскажите о себе..."><?php echo htmlspecialchars($_POST['bio'] ?? ''); ?></textarea>
            </div>

            <div class="form-group checkbox-group">
                <label class="checkbox-label">
                    <input type="checkbox" name="contract" 
                           <?php echo (isset($_POST['contract']) && $_POST['contract'] == 'on') ? 'checked' : ''; ?> required>
                    <span class="checkmark"></span>
                    Я ознакомлен(а) с контрактом *
                </label>
            </div>

            <div class="form-group">
                <button type="submit" class="submit-btn">Сохранить</button>
            </div>

            <p class="required-note">* — обязательные поля</p>
        </form>
    </div>
</body>
</html>
