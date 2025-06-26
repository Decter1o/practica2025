<?php
include_once(__DIR__ . '/../../src/controllers/role_controller.php');
include_once(__DIR__ . '/../../src/models/Role.php');
include_once(__DIR__ . '/../../src/models/Permission.php');
include_once(__DIR__ . '/../../src/security/PermissionMiddleware.php');
PermissionMiddleware::handle('roles_control');
$db = DB::DBConnect();
$roles = Role::GetAll($db);

$addRole = false;
if (isset($_GET['add_role'])) {
    $addRole = true;
}

$editRole = null;
if (isset($_GET['edit_role'])) {
    $editRole = Role::GetById($_GET['edit_role'], $db);
    $rolePermissions = Permission::GetByRoleId($_GET['edit_role'], $db);
}
?>

<!DOCTYPE html>
<html>
<head>    
    <meta charset="UTF-8">
    <title>Управление ролями</title>
    <link rel="stylesheet" href="/public/css/Semantic/semantic.min.css">
</head>
<body>
    <div class="ui container">
        <h2 class="ui header">Управление ролями</h2>
        <form method="get" style="margin-bottom: 1em">
            <button type="submit" name="add_role" value="1" class="ui primary button"><i class="add icon"></i>Добавить роль</button>
        </form>
        <table class="ui celled table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Название</th>
                    <th>Код</th>
                    <th>Статус</th>
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($roles as $role): ?>
                <tr>
                    <td><?= htmlspecialchars((string)$role->getId() ?? '') ?></td>
                    <td><?= htmlspecialchars((string)$role->getName() ?? '') ?></td>
                    <td><?= htmlspecialchars((string)$role->getCode() ?? '') ?></td>
                    <td><?= htmlspecialchars((string)$role->getStatus() ?? '') ?></td>
                    <td>
                        <form method="get" style="display:inline">
                            <input type="hidden" name="edit_role" value="<?= $role->getId() ?>">
                            <button type="submit" class="ui icon button" title="Редактировать">
                                <i class="edit icon"></i>
                            </button>
                        </form>
                        
                        <?php if ($role->getStatus() == 'deleted'): ?>
                            <form method="post" action="/src/controllers/role_controller.php" style="display:inline">
                                <input type="hidden" name="action" value="restore">
                                <input type="hidden" name="id" value="<?= $role->getId() ?>">
                                <button type="submit" class="ui blue icon button" title="Восстановить">
                                    <i class="undo icon"></i>
                                </button>
                            </form>
                        <?php else: ?>
                            <form method="post" action="/src/controllers/role_controller.php" style="display:inline" 
                                onsubmit="return confirm('Вы уверены? Роль можно восстановить в течении 1 дня');">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?= $role->getId() ?>">
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
        <?php if ($editRole): ?>
            <div class="ui segment">
                <h2 class="ui header">Редактирование роли</h2>
                <form class="ui form" method="post" action="/src/controllers/role_controller.php">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="id" value="<?= htmlspecialchars($editRole->getId()) ?>">
                    
                    <div class="field">
                        <label>Название</label>
                        <input type="text" name="name" value="<?= htmlspecialchars($editRole->getName()) ?>">
                    </div>

                    <div class="field">
                        <label>Код</label>
                        <input type="text" name="code" value="<?= htmlspecialchars($editRole->getCode()) ?>">
                    </div>

                    <div class="field">
                        <label>Статус</label>
                        <select name="status" class="ui dropdown">
                            <option value="active" <?= $editRole->getStatus() == 'active' ? 'selected' : '' ?>>Активная</option>
                            <option value="blocked" <?= $editRole->getStatus() == 'blocked' ? 'selected' : '' ?>>Неактивная</option>
                        </select>
                    </div>
                    
                    <div class="field">
                        <label>Права доступа</label>
                        <div class="ui segment">
                            <div class="ui two column grid">
                                <div class="column">
                                    <div class="field">
                                        <div class="ui checkbox">
                                            <input type="checkbox" name="permissions[]" value="auth"
                                            <?= in_array('auth', $rolePermissions) ? 'checked' : '' ?>>
                                            <label>Вход в админ панель</label>
                                        </div>
                                    </div>
                                    <div class="field">
                                        <div class="ui checkbox">
                                            <input type="checkbox" name="permissions[]" value="users_control"
                                            <?= in_array('users_control', $rolePermissions) ? 'checked' : '' ?>>
                                            <label>Управление пользователями</label>
                                        </div>
                                    </div>
                                    <div class="field">
                                        <div class="ui checkbox">
                                            <input type="checkbox" name="permissions[]" value="roles_control"
                                            <?= in_array('roles_control', $rolePermissions) ? 'checked' : '' ?>>
                                            <label>Управление ролями</label>
                                        </div>
                                    </div>
                                    <div class="field">
                                        <div class="ui checkbox">
                                            <input type="checkbox" name="permissions[]" value="pages_control"
                                            <?= in_array('pages_control', $rolePermissions) ? 'checked' : '' ?>>
                                            <label>Управление страницами</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="column">
                                    <div class="field">
                                        <div class="ui checkbox">
                                            <input type="checkbox" name="permissions[]" value="news_control"
                                            <?= in_array('news_control', $rolePermissions) ? 'checked' : '' ?>>
                                            <label>Управление новостями</label>
                                        </div>
                                    </div>
                                    <div class="field">
                                        <div class="ui checkbox">
                                            <input type="checkbox" name="permissions[]" value="directories_control"
                                            <?= in_array('directories_control', $rolePermissions) ? 'checked' : '' ?>>
                                            <label>Управление справочниками</label>
                                        </div>
                                    </div>
                                    <div class="field">
                                        <div class="ui checkbox">
                                            <input type="checkbox" name="permissions[]" value="navigation_control"
                                            <?= in_array('navigation_control', $rolePermissions) ? 'checked' : '' ?>>
                                            <label>Управление навигацией</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="ui primary button"><i class="save icon"></i> Сохранить</button>
                    <a href="role_control_page.php" class="ui button">Отмена</a>
                </form>
            </div>
        <?php endif; ?>
        <?php if ($addRole): ?>
            <div class="ui segment">
                <h2 class="ui header">Добавление роли</h2>
                <form class="ui form" method="post" action="/src/controllers/role_controller.php">
                    <input type="hidden" name="action" value="create">
                    
                    <div class="field">
                        <label>Название</label>
                        <input type="text" name="name" required>
                    </div>

                    <div class="field">
                        <label>Код</label>
                        <input type="text" name="code" required>
                    </div>                    
                    
                    <div class="field">
                        <label>Права доступа</label>
                        <div class="ui segment">
                            <div class="ui two column grid">
                                <div class="column">
                                    <div class="field">
                                        <div class="ui checkbox">
                                            <input type="checkbox" name="permissions[]" value="auth">
                                            <label>Вход в админ панель</label>
                                        </div>
                                    </div>
                                    <div class="field">
                                        <div class="ui checkbox">
                                            <input type="checkbox" name="permissions[]" value="users_control">
                                            <label>Управление пользователями</label>
                                        </div>
                                    </div>
                                    <div class="field">
                                        <div class="ui checkbox">
                                            <input type="checkbox" name="permissions[]" value="roles_control">
                                            <label>Управление ролями</label>
                                        </div>
                                    </div>
                                    <div class="field">
                                        <div class="ui checkbox">
                                            <input type="checkbox" name="permissions[]" value="pages_control">
                                            <label>Управление страницами</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="column">
                                    <div class="field">
                                        <div class="ui checkbox">
                                            <input type="checkbox" name="permissions[]" value="news_control">
                                            <label>Управление новостями</label>
                                        </div>
                                    </div>
                                    <div class="field">
                                        <div class="ui checkbox">
                                            <input type="checkbox" name="permissions[]" value="directories_control">
                                            <label>Управление справочниками</label>
                                        </div>
                                    </div>
                                    <div class="field">
                                        <div class="ui checkbox">
                                            <input type="checkbox" name="permissions[]" value="navigation_control">
                                            <label>Управление навигацией</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="ui primary button"><i class="save icon"></i> Добавить</button>
                    <a href="role_control_page.php" class="ui button">Отмена</a>
                </form>
            </div>
        <?php endif; ?>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="/public/css/Semantic/semantic.min.js"></script>
    <script>        $(document).ready(function() {
            $('.ui.dropdown').dropdown();
            $('.ui.checkbox').checkbox();
        });
    </script>
</body>
</html>