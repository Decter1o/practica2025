<?php
include_once (__DIR__ . '/../models/News.php');
include_once (__DIR__ . '/../models/DB.php');
require_once (__DIR__ . '/../../vendor/autoload.php');
include_once (__DIR__ . '/../security/PermissionMiddleware.php');
include_once (__DIR__ . '/../models/Logger.php');
Permission::Check('news_control');
use Ramsey\Uuid\Uuid;

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $pdo = DB::DBConnect();

    switch($action) {
        case 'create':
            $id = Uuid::uuid7()->toString();
            $url = $_POST['url'] ?? '';
            $anounce = $_POST['anounce'] ?? '';
            $preview_image = $_FILES['preview_image'] ?? '';
            $content = $_POST['content'] ?? '';

            if (isset($_FILES['preview_image']) && $_FILES['preview_image']['error'] === UPLOAD_ERR_OK) {
                $tmpPath = $_FILES['preview_image']['tmp_name'];
                $originalName = $_FILES['preview_image']['name'];
                $fileSize = $_FILES['preview_image']['size'];

                $check = getimagesize($tmpPath);
                if ($check === false) {
                    die("Файл не является изображением.");
                }

                if ($fileSize > 5 * 1024 * 1024) {
                    die("Файл слишком большой. Максимум — 5MB.");
                }

                $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

                if (!in_array($ext, $allowedExtensions)) {
                    die("Недопустимое расширение файла.");
                }

                $newName = uniqid('img_', true) . '.' . $ext;
                $uploadDir = __DIR__ . '/../../public/img/';
                $destination = $uploadDir . $newName;

                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }

                if (move_uploaded_file($tmpPath, $destination)) {
                Logger::success("Файл успешно загружен: $originalName");
                $preview_image = '/public/img/' . $newName;
                } else {
                    Logger::error("Не удалось переместить загруженный файл: $originalName");
                    die("Не удалось сохранить файл.");
                }
            } else {
                Logger::error("Ошибка загрузки файла.");
                die("Ошибка загрузки файла.");
            }

            if(News::Create($id, $url, $anounce, $preview_image, $content, $pdo)) {
                header('Location: /public/pages/news_control_page.php?message=created');
            } else {
                header('Location: /public/pages/news_control_page.php?error=create_failed');
            }
            exit;

        case 'update':
            $id = $_POST['id'] ?? '';
            $url = $_POST['url'] ?? '';
            $anounce = $_POST['anounce'] ?? '';
            $preview_image = $_FILES['preview_image'] ?? '';
            $content = $_POST['content'] ?? '';
            $existing_preview = $_POST['existing_preview'] ?? '';

            if($existing_preview){
                $image_path = $_SERVER['DOCUMENT_ROOT'] . $existing_preview;
                if(file_exists($image_path)) {
                    unlink($image_path);
                    Logger::success("Существующее изображение удалено: $existing_preview");
                } else {
                    Logger::error("Не удалось удалить существующее изображение: $existing_preview");
                }
            }

            if (isset($_FILES['preview_image']) && $_FILES['preview_image']['error'] === UPLOAD_ERR_OK) {
                $tmpPath = $_FILES['preview_image']['tmp_name'];
                $originalName = $_FILES['preview_image']['name'];
                $fileSize = $_FILES['preview_image']['size'];

                $check = getimagesize($tmpPath);
                if ($check === false) {
                    die("Файл не является изображением.");
                }

                if ($fileSize > 5 * 1024 * 1024) {
                    die("Файл слишком большой. Максимум — 5MB.");
                }

                $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

                if (!in_array($ext, $allowedExtensions)) {
                    die("Недопустимое расширение файла.");
                }

                $newName = uniqid('img_', true) . '.' . $ext;
                $uploadDir = __DIR__ . '/../../public/img/';
                $destination = $uploadDir . $newName;

                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }

                if (move_uploaded_file($tmpPath, $destination)) {
                Logger::success("Файл успешно загружен: $originalName");
                $preview_image = '/public/img/' . $newName;
                } else {
                    Logger::error("Не удалось переместить загруженный файл: $originalName");
                    die("Не удалось сохранить файл.");
                }
            } else {
                Logger::error("Ошибка загрузки файла.");
                die("Ошибка загрузки файла.");
            }

            if(News::Update($id, $url, $anounce, $preview_image, $content, $pdo)) {
                header('Location: /public/pages/news_control_page.php?message=updated');
            } else {
                header('Location: /public/pages/news_control_page.php?error=update_failed');
            }

            break;

        case 'delete':
            $id = $_POST['id'] ?? '';
            if(News::Delete($id, $pdo)) {
                header('Location: /public/pages/news_control_page.php?message=deleted');
            } else {
                header('Location: /public/pages/news_control_page.php?error=delete_failed');
            }
            exit;
        case 'restore':
            $id = $_POST['id'] ?? '';
            if(News::Restore($id, $pdo)) {
                header('Location: /public/pages/news_control_page.php?message=restored');
            } else {
                header('Location: /public/pages/news_control_page.php?error=restore_failed');
            }
            exit;
    }
}
