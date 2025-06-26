<?php 
include_once(__DIR__ . '/../../src/security/PermissionMiddleware.php');
include_once(__DIR__ . '/../../src/models/Article.php');
include_once(__DIR__ . '/../../src/models/DB.php');
PermissionMiddleware::handle('pages_control');
$pdo = DB::DBConnect();
$json = file_get_contents(__DIR__ . '/../../public/data/menu.json');
$menu = json_decode($json, true);
$articles = Article::GetAll($pdo);

function renderMenu($menu, $level = 0){
    foreach($menu as $item){
        $GLOBALS['articles'] = array_filter($GLOBALS['articles'], function($article) use ($item) {
            return $article->getId() !== $item['id'];
        });
        echo '<li data-id="' . htmlspecialchars((string)$item['id']) . '">';
        echo htmlspecialchars($item['title']);
        if(isset($item['url'])){
            echo ', <small>' . htmlspecialchars($item['url']) . '</small>';
        }
        if(isset($item['children']) && is_array($item['children']) && count($item['children']) > 0){
            echo '<ul class="nested-sortable">';
            renderMenu($item['children'], $level + 1);
            echo '</ul>';
        }
        echo '</li>';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Настройка меню</title>
    <link rel="stylesheet" href="/public/css/Semantic/semantic.min.css">
    <style>
        ul {
            list-style-type: none;
            padding-left: 1em;
        }
        li {
            margin: 5px 0;
            padding: 6px 10px;
            background: #f9f9f9;
            border: 1px solid #ccc;
            border-radius: 4px;
            cursor: grab;
        }
    </style>
</head>
<body>
    <div class="ui container">
        <h2 class="ui dividing header">Настройка меню</h2>
        <div class="ui grid">
            <div class="six wide column">
                <h4 class="ui header">Меню</h4>
                <ul class="nested-sortable ui segment" id="menu">
                    <?php renderMenu($menu); ?>
                </ul>
            </div>

            <div class="ten wide column">
                <h4 class="ui header">Список страниц</h4>
                <ul class="ui segment article-source">
                    <?php foreach($articles as $article): ?>
                        <li data-id="<?= htmlspecialchars((string)$article->getId()) ?>">
                            <?= htmlspecialchars($article->getTitle()) ?>, <small><?= htmlspecialchars($article->getUrl()) ?></small>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
        <button type="submit" class="ui positive button"><i class="save icon"></i> Сохранить</button>
        <a href="role_control_page.php" class="ui button">Отмена</a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
    <script>
        // Инициализация источника (статьи, нельзя менять порядок)
        new Sortable(document.querySelector('.article-source'), {
            group: {
                name: 'shared',
                pull: true,
                put: true
            },
            animation: 150,
            sort: false
        });

        // Функция инициализации nested sortable
        function initNestedSortables(root) {
            // Включаем сам корневой ul тоже
            if (root.tagName === 'UL') {
                new Sortable(root, {
                    group: 'shared',
                    animation: 150,
                    fallbackOnBody: true,
                    swapThreshold: 0.65,
                    onAdd: function (evt) {
                        const item = evt.item;

                        if (!item.querySelector('ul')) {
                            const newUl = document.createElement('ul');
                            newUl.classList.add('nested-sortable');
                            item.appendChild(newUl);
                            initNestedSortables(item); // рекурсивно
                        }
                    }
                });
            }

            // Инициализируем все вложенные <ul> внутри root
            root.querySelectorAll('ul').forEach(ul => {
                new Sortable(ul, {
                    group: 'shared',
                    animation: 150,
                    fallbackOnBody: true,
                    swapThreshold: 0.65,
                    onAdd: function (evt) {
                        const item = evt.item;

                        if (!item.querySelector('ul')) {
                            const newUl = document.createElement('ul');
                            newUl.classList.add('nested-sortable');
                            item.appendChild(newUl);
                            initNestedSortables(item);
                        }
                    }

                });
            });
        }

        initNestedSortables(document.getElementById('menu'));

        function getTitle(li) {
            const titleText = li.childNodes[0]?.textContent?.trim() || '';
            
            return titleText;
        }

        function getUrl(li) {
            const url = li.querySelector('small')?.textContent?.trim() || '';
            return url;
        }

        function serializeMenu(ul) {
            const items = [];

            ul.querySelectorAll(':scope > li').forEach(li => {
                const item = {
                    title: getTitle(li),
                    url: getUrl(li),
                    id: li.dataset.id || null,
                    children: []
                };

                const childUl = li.querySelector('ul');
                if (childUl) {
                    item.children = serializeMenu(childUl); // рекурсивно
                }

                items.push(item);
            });

            return items;
        }

        document.getElementById('submit').addEventListener('click', function() {
            const menuData = serializeMenu(document.getElementById('menu'));
            console.log(JSON.stringify(menuData, null, 2));

            fetch('/src/controllers/menu_controller.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(menuData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Меню успешно сохранено!');
                    window.location.reload();
                } else {
                    alert('Ошибка при сохранении меню: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Ошибка:', error);
                alert('Произошла ошибка при сохранении меню.');
            });
        });
    </script>
</body>
</html>
