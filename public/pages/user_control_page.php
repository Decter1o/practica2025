<?php
require_once(__DIR__ . '/../../src/controllers/user_controller.php');
require_once(__DIR__ . '/../../src/models/Role.php');
require_once(__DIR__ . '/../../src/security/PermissionMiddleware.php');
PermissionMiddleware::handle('users_control');
$db = DB::DBConnect();
$users = User::GetAll($db);

$editUser = null;
if (isset($_GET['edit_user'])) {
    $editUser = User::GetById($_GET['edit_user'], $db);
}
?>

<!DOCTYPE html>
<html>
<head>    
    <meta charset="UTF-8">
    <title>Управление пользователями</title>
    <link rel="stylesheet" href="/public/css/Semantic/semantic.min.css">
</head>
<body>
    <div class="ui container" style="margin-top: 2em; max-width: 900px;">
        <?php if (!$editUser): ?>
        <div class="ui segment">
            <h2 class="ui header">Управление пользователями</h2>
            <table class="ui celled striped table">
                <thead class="full-width">
                    <tr>
                        <th>ID</th>
                        <th>Email</th>
                        <th>Статус</th>
                        <th>Роль</th>
                        <th>Дата регистрации</th>
                        <th>Последняя активность</th>
                        <th>Действия</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach($users as $user): ?>
                <tr>
                    <td><?= htmlspecialchars((string)$user->getId() ?? '') ?></td>
                    <td><b><?= htmlspecialchars((string)$user->getEmail() ?? '') ?></b></td>
                    <td>
                        <?php if ($user->getStatus() == 'deleted'): ?>
                            <span class="ui red label">Удалён</span>
                        <?php elseif ($user->getStatus() == 'blocked'): ?>
                            <span class="ui orange label">Заблокирован</span>
                        <?php else: ?>
                            <span class="ui green label">Активен</span>
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars(Role::GetNameById($user->getRoleId(), $db)) ?></td>
                    <td><?= htmlspecialchars((string)$user->getRegisteredAt() ?? '') ?></td>
                    <td><?= htmlspecialchars((string)$user->getLastActiveAt() ?? '') ?></td>
                    <td>
                        <form method="get" style="display:inline">
                            <input type="hidden" name="edit_user" value="<?= $user->getId() ?>">
                            <button type="submit" class="ui icon button" title="Редактировать">
                                <i class="edit icon"></i>
                            </button>
                        </form>
                        <form method="post" action="/src/controllers/user_controller.php" style="display:inline" 
                            onsubmit="return confirm('Вы уверены? Пользователя можно восстановить в течении 1 дня');">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?= $user->getId() ?>">
                            <button type="submit" class="ui red icon button" title="Удалить">
                                <i class="trash alternate outline icon"></i>
                            </button>
                        </form>
                        <?php if ($user->getStatus() == 'deleted'): ?>
                        <form method="post" action="/src/controllers/user_controller.php" style="display:inline">
                            <input type="hidden" name="action" value="restore">
                            <input type="hidden" name="id" value="<?= $user->getId() ?>">
                            <button type="submit" class="ui blue icon button" title="Восстановить">
                                <i class="undo icon"></i>
                            </button>
                        </form>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <div class="ui raised very padded segment">
            <h2 class="ui header">Редактирование пользователя</h2>
            <form class="ui form" method="post" action="/src/controllers/user_controller.php">
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="id" value="<?= htmlspecialchars($editUser->getId()) ?>">
                <div class="field required">
                    <label>Email</label>
                    <input type="email" name="email" value="<?= htmlspecialchars($editUser->getEmail()) ?>">
                </div>
                <div class="field required">
                    <label>Статус</label>
                    <select name="status" class="ui dropdown">
                        <option value="active" <?= $editUser->getStatus() == 'active' ? 'selected' : '' ?>>Активный</option>
                        <option value="blocked" <?= $editUser->getStatus() == 'blocked' ? 'selected' : '' ?>>Заблокирован</option>
                        <option value="deleted" <?= $editUser->getStatus() == 'deleted' ? 'selected' : '' ?>>Удалён</option>
                    </select>
                </div>
                <div class="field required">
                    <label>Роль</label>
                    <select name="role_id" class="ui dropdown">
                        <?php 
                        $roles = Role::GetAll($db);
                        foreach($roles as $role): 
                        ?>
                            <option value="<?= $role->getId() ?>" <?= $editUser->getRoleId() == $role->getId() ? 'selected' : '' ?>>
                                <?= htmlspecialchars($role->getName()) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" class="ui positive button"><i class="save icon"></i> Сохранить</button>
                <a href="user_control_page.php" class="ui button"><i class="cancel icon"></i> Отмена</a>
            </form>
        </div>
        <?php endif; ?>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="/public/css/Semantic/semantic.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.ui.dropdown').dropdown();
        });
    </script>
</body>
</html>