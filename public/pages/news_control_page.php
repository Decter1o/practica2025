<?php 
include_once(__DIR__ . '/../../src/security/PermissionMiddleware.php');
include_once(__DIR__ . '/../../src/models/News.php');
include_once(__DIR__ . '/../../src/models/DB.php');
PermissionMiddleware::handle('news_control');
$pdo = DB::DBConnect();
$news = News::GetAll($pdo);

$addNews = false;
if (isset($_GET['add_news'])) {
    $addNews = true;
}

$editNews = null;
if (isset($_GET['edit_news'])) {
    $editNews = News::GetById($_GET['edit_news'], $pdo);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Новости</title>
    <link rel="stylesheet" href="/public/css/Semantic/semantic.min.css">
</head>
<body>
    <div class="ui container" style="margin-top: 2em; max-width: 900px;">
        <?php if(!$editNews && !$addNews): ?>
        <div class="ui segment">
            <h2 class="ui header">Новости</h2>
            <form method="get" class="ui form" style="margin-bottom: 1em">
                <button type="submit" name="add_news" value="1" class="ui primary button">
                    <i class="plus icon"></i> Добавить новость
                </button>
            </form>
            <div class="ui divider"></div>
            <table class="ui celled striped table">
                <thead class="full-width">
                    <tr>
                        <th>ID</th>
                        <th>URL</th>
                        <th>Заголовок</th>
                        <th>Статус</th>
                        <th>Дата создания</th>
                        <th>Дата публикации</th>
                        <th>Действия</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach($news as $item): ?>
                <tr>
                    <td><?= htmlspecialchars((string)$item->GetId() ?? '') ?></td>
                    <td><div class="ui label"><?= htmlspecialchars((string)$item->GetUrl() ?? '') ?></div></td>
                    <td><b><?= htmlspecialchars((string)$item->GetAnounce() ?? '') ?></b></td>
                    <td>
                        <?php if ($item->GetStatus() == 'deleted'): ?>
                            <span class="ui red label">Удалена</span>
                        <?php else: ?>
                            <span class="ui green label">Активна</span>
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars((string)$item->GetCreatedAt() ?? '') ?></td>
                    <td><?= htmlspecialchars((string)$item->GetPublishedAt() ?? '') ?></td>
                    <td>
                        <form method="get" style="display:inline">
                            <input type="hidden" name="edit_news" value="<?= $item->GetId() ?>">
                            <button type="submit" class="ui icon button" title="Редактировать">
                                <i class="edit icon"></i>
                            </button>
                        </form>
                        <?php if ($item->GetStatus() == 'deleted'): ?>
                            <form method="post" action="/src/controllers/news_controller.php" style="display:inline">
                                <input type="hidden" name="action" value="restore">
                                <input type="hidden" name="id" value="<?= $item->GetId() ?>">
                                <button type="submit" class="ui green icon button" title="Восстановить">
                                    <i class="undo icon"></i>
                                </button>
                            </form>
                        <?php else: ?>
                            <form method="post" action="/src/controllers/news_controller.php" style="display:inline"
                                onsubmit="return confirm('Вы уверены? Страницу можно восстановить в течении 1 дня');">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?= $item->GetId() ?>">
                                <button type="submit" class="ui red icon button" title="Удалить">
                                    <i class="trash alternate outline icon"></i>
                                </button>
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?elseif($editNews): ?>
        <div class="ui raised very padded segment">
            <h2 class="ui header">Редактирование новости</h2>
            <form class="ui form" method="post" action="/src/controllers/news_controller.php" enctype="multipart/form-data">
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="id" value="<?= htmlspecialchars((string)$editNews->GetId() ?? '') ?>">
                <div class="field required">
                    <label>Псевдоним (URL)</label>
                    <input type="text" name="url" value="<?= htmlspecialchars((string)$editNews->GetUrl() ?? '') ?>" required>
                </div>
                <div class="field">
                    <label>Анонс новости</label>
                    <textarea name="anounce" rows="3"><?= htmlspecialchars((string)$editNews->GetAnounce() ?? '') ?></textarea>
                </div>
                <div class="field">
                    <label>Превью изображения</label>
                    <?php if($editNews->GetPreview()): ?>
                        <img src="<?= htmlspecialchars($editNews->GetPreview()) ?>" class="ui small image" style="margin-bottom: 1em;">
                        <input type="hidden" name="existing_preview" value="<?= htmlspecialchars($editNews->GetPreview()) ?>">
                    <?php endif; ?>
                    <input type="file" name="preview_image" accept="image/*">
                </div>
                <div class="field required">
                    <label>Содержимое новости</label>
                    <textarea id="mytextarea" name="content" rows="10"><?= htmlspecialchars((string)$editNews->GetContent() ?? '') ?></textarea>
                </div>
                <button type="submit" class="ui primary button"><i class="save icon"></i> Сохранить</button>
                <a href="/public/pages/news_control_page.php" class="ui button"><i class="cancel icon"></i> Отмена</a>
            </form>
        </div>
        <?elseif($addNews): ?>
        <div class="ui raised very padded segment">
            <h2 class="ui header">Добавление новости</h2>
            <form class="ui form" method="post" action="/src/controllers/news_controller.php" enctype="multipart/form-data">
                <input type="hidden" name="action" value="create">
                <div class="field required">
                    <label>Псевдоним (URL)</label>
                    <input type="text" name="url" required>
                    <div class="ui pointing label">
                        Например: summer-news-2025
                    </div>
                </div>
                <div class="field">
                    <label>Анонс новости</label>
                    <textarea name="anounce" rows="3" placeholder="Краткое описание новости..."></textarea>
                </div>
                <div class="field">
                    <label>Превью изображения</label>
                    <input type="file" name="preview_image" accept="image/*">
                </div>
                <div class="field required">
                    <label>Содержимое новости</label>
                    <textarea id="mytextarea" name="content"></textarea>
                </div>
                <button type="submit" class="ui primary button"><i class="save icon"></i> Сохранить</button>
                <a href="/public/pages/news_control_page.php" class="ui button"><i class="cancel icon"></i> Отмена</a>
            </form>
        </div>
        <?php endif; ?>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="/public/css/Semantic/semantic.min.js"></script>
    <script src="/tinymce/tinymce.min.js"></script>
    <script>
        tinymce.init({
            selector: '#mytextarea',
            plugins: 'code link image lists table',
            toolbar: 'undo redo | formatselect | bold italic | alignleft aligncenter alignright | bullist numlist | link image | table | code',
            height: 400,
            language: 'ru',
            language_url: '/tinymce/langs/ru.js',
            menubar: true,
            relative_urls: false,
            remove_script_host: false
        });
    </script>
</body>
</html>