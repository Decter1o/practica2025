<?php 
include_once(__DIR__ . '/../../src/security/PermissionMiddleware.php');
include_once(__DIR__ . '/../../src/models/Article.php');
include_once(__DIR__ . '/../../src/models/DB.php');
PermissionMiddleware::handle('pages_control');
$pdo = DB::DBConnect();
$articles = Article::GetAll($pdo);

$addArticle = false;
if (isset($_GET['add_article'])) {
    $addArticle = true;
}

$editArticle = null;
if (isset($_GET['edit_article'])) {
    $editArticle = Article::GetByURL($_GET['edit_article'], $pdo);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Контентные страницы</title>
    <link rel="stylesheet" href="/public/css/Semantic/semantic.min.css">
</head>
<body>
    <div class="ui container">
        <?php if(!$editArticle && !$addArticle): ?>
        <h2 class="ui header">Контентные страницы</h2>
        <form method="get" style="margin-bottom: 1em">
            <button type="submit" name="add_article" value="1" class="ui primary button"><i class="add icon"></i> Добавить страницу</button>
        </form>
        <table class="ui celled table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>URL</th>
                    <th>Заголовок</th>
                    <th>Дата создания</th>
                    <th>Статус</th>
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($articles as $article): ?>
                <tr>
                    <td><?= htmlspecialchars((string)$article->getId() ?? '') ?></td>
                    <td><?= htmlspecialchars((string)$article->getUrl() ?? '') ?></td>
                    <td><?= htmlspecialchars((string)$article->getTitle() ?? '') ?></td>
                    <td><?= htmlspecialchars((string)$article->getCreatedAt() ?? '') ?></td>
                    <td><?= htmlspecialchars((string)$article->getStatus() ?? '') ?></td>
                    <td>
                        <form method="get" style="display:inline">
                            <input type="hidden" name="edit_article" value="<?= $article->getUrl() ?>">
                            <button type="submit" class="ui icon button" title="Редактировать">
                                <i class="edit icon"></i>
                            </button>
                        </form>
                        <?php if ($article->getStatus() == 'deleted'): ?>
                            <form method="post" action="/src/controllers/article_controller.php" style="display:inline">
                                <input type="hidden" name="action" value="restore">
                                <input type="hidden" name="id" value="<?= $article->getId() ?>">
                                <button type="submit" class="ui blue icon button" title="Восстановить">
                                    <i class="undo icon"></i>
                                </button>
                            </form>
                        <?php else: ?>
                            <form method="post" action="/src/controllers/article_controller.php" style="display:inline"
                                onsubmit="return confirm('Вы уверены? Страницу можно восстановить в течении 1 дня');">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?= $article->getId() ?>">
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
        <?elseif($editArticle): ?>
        <h2 class="ui header">Редактирование статьи</h2>
        <form class="ui form" method="post" action="/src/controllers/article_controller.php">
            <input type="hidden" name="action" value="update">
            <input type="hidden" name="id" value="<?= htmlspecialchars((string)$editArticle->getId() ?? '') ?>">
            <div class="field">
                <label>URL</label>
                <input type="text" name="url" value="<?= htmlspecialchars((string)$editArticle->getUrl() ?? '') ?>" required>
            </div>
            <div class="field">
                <label>Заголовок</label>
                <input type="text" name="title" value="<?= htmlspecialchars((string)$editArticle->getTitle() ?? '') ?>" required>
            </div>
            <div class="field">
                <label>Содержимое</label>
                <textarea id="mytextarea" name="content" rows="10"><?= htmlspecialchars((string)$editArticle->getContent() ?? '') ?></textarea>
            </div>
            <button type="submit" class="ui primary button"><i class="save icon"></i> Сохранить</button>
            <a href="/public/pages/article_edit_page.php" class="ui button">Отмена</a>
        </form>
        <?elseif($addArticle): ?>
        <h2 class="ui header">Добавление статьи</h2>
        <form class="ui form" method="post" action="/src/controllers/article_controller.php">
            <input type="hidden" name="action" value="create">
            
            <div class="field">
                <label>Заголовок:</label>
                <input type="text" name="title" required>
            </div>

            <div class="field">
                <label>URL (например, about):</label>
                <input type="text" name="url" required>
            </div>

            <div class="field">
                <label>Содержимое:</label>
                <textarea id="mytextarea" name="content"></textarea>
            </div>

            <button type="submit" class="ui primary button"><i class="save icon"></i> Сохранить</button>
            <a href="/public/pages/article_page.php" class="ui button">Отмена</a>
        </form>
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