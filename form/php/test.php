<?php
require_once 'FormHandler.php';

// Подключение к БД 
/*
try {
    $db = new PDO('mysql:host=localhost;dbname=your_database', 'username', 'password');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    $db = null; // Будет использоваться сохранение в файл
}
*/

$handler = new FormHandler(isset($db) ? $db : null);

$name = '';
$mood_color = '#0000ff';
$comment = '';
$radio = '';
$agreement = false;
$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $formData = [
        'name' => isset($_POST['name']) ? trim($_POST['name']) : '',
        'mood_color' => isset($_POST['mood_color']) ? $_POST['mood_color'] : '#0000ff',
        'comment' => isset($_POST['comment']) ? trim($_POST['comment']) : '',
        'radio' => isset($_POST['radio']) ? $_POST['radio'] : '',
        'agreement' => isset($_POST['agreement'])
    ];
    
    $errors = $handler->validate($formData);
    
    if (empty($errors)) {
        if ($handler->saveToDatabase($formData)) {
            $success = true;
            
            // Опционально: отправка на API
            // $handler->sendToAPI($formData, 'https://api.example.com/submit');
        } else {
            $errors[] = 'Ошибка при сохранении данных';
        }
    }
    
    // Сохраняем значения для отображения
    $name = $formData['name'];
    $mood_color = $formData['mood_color'];
    $comment = $formData['comment'];
    $radio = $formData['radio'];
    $agreement = $formData['agreement'];
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Заполните поля</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>

<header class="header">
    <h1>Заполните поля</h1>
    <span class="hint">ну пожалуйста</span>
</header>

<div class="container">
    <?php if ($success): ?>
        <div style="padding: 20px; background: #d4edda; color: #155724; margin: 20px; border-radius: 4px;">
            Форма успешно отправлена!
        </div>
    <?php endif; ?>
    
    <?php if (!empty($errors)): ?>
        <div style="padding: 20px; background: #f8d7da; color: #721c24; margin: 20px; border-radius: 4px;">
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
    
    <form class="form" method="POST" action="">
        <section class="section">
            <h2 class="section-title">Основное</h2>
            
            <div class="field-row">
                <label class="field-label">Имя</label>
                <div class="field-right">
                    <input 
                        type="text" 
                        name="name"
                        class="field-input" 
                        placeholder="по паспорту"
                        value="<?php echo htmlspecialchars($name); ?>"
                        required>
                </div>
            </div>
            
            <div class="field-row">
                <label class="field-label">Цвет вашего настроения</label>
                <div class="field-right">
                    <input type="color" name="mood_color" class="field-color" value="<?php echo htmlspecialchars($mood_color); ?>">
                </div>
            </div>
        </section>

        <hr class="divider">

        <section class="section section-yellow">
            <h2 class="section-title">Дополнительное</h2>
            
            <div class="field-row">
                <label class="field-label">Комментарий</label>
                <div class="field-right">
                    <textarea 
                        name="comment"
                        class="field-textarea" 
                        placeholder="Напишите хоть что-нибудь.&#10;Если хотите, конечно."><?php echo htmlspecialchars($comment); ?></textarea>
                </div>
            </div>
        </section>

        <div class="checkboxes">
            <label class="checkbox">
                <input type="radio" name="radio" value="option1" <?php echo ($radio === 'option1') ? 'checked' : ''; ?>>
                <span>Ну а тут просто полежит радиобатон</span>
            </label>
            
            <label class="checkbox checkbox-checked">
                <input type="checkbox" name="agreement" value="1" <?php echo $agreement ? 'checked' : ''; ?> required>
                <span>
                    Соглашаюсь на всё, что бы вы не придумали и осознаю,
                    что это может означать
                    <a href="#" class="link">что угодно</a>
                </span>
            </label>
        </div>

        <button class="submit-btn" type="submit">
            Отправить все мои данные
        </button>
    </form>
</div>

</body>
</html>
