<?php
include_once (__DIR__ . '/../models/Article.php');
include_once(__DIR__ . '/../models/DB.php');
require_once(__DIR__ . '/../../vendor/autoload.php');
include_once(__DIR__ . '/../models/Permission.php');
Permission::Check('pages_control');
use Ramsey\Uuid\Uuid;


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $db = DB::DBConnect();

    switch ($action) {
        case 'create':
            $id = Uuid::uuid7()->toString();
            $url = $_POST['url'] ?? '';
            $title = $_POST['title'] ?? '';
            $content = $_POST['content'] ?? '';

            if (Article::Add($id, $url, $title, $content, $db)) {
                header('Location: /public/pages/article_control_page.php?message=created');
            } else {
                header('Location: /public/pages/article_control_page.php?error=create_failed');
            }
            exit;

        case 'update':
            $id = $_POST['id'] ?? '';
            $url = $_POST['url'] ?? '';
            $title = $_POST['title'] ?? '';
            $content = $_POST['content'] ?? '';
            
            if(Article::Update($id, $url, $title, $content, $db)) {
                header('Location: /public/pages/article_control_page.php?message=updated');
            } else {
                header('Location: /public/pages/article_control_page.php?error=update_failed');
            }
            break;

        case 'delete':
            $id = $_POST['id'] ?? '';

            if (Article::Delete($id, $db)) {
                header('Location: /public/pages/article_control_page.php?message=deleted');
            } else {
                header('Location: /public/pages/article_control_page.php?error=delete_failed');
            }
            break;

        case 'restore':
            $id = $_POST['id'] ?? '';

            if (Article::Restore($id, $db)) {
                header('Location: /public/pages/article_control_page.php?message=restored');
            } else {
                header('Location: /public/pages/article_control_page.php?error=restore_failed');
            }
            break;

        default:
            echo "Неизвестное действие";
            exit;
    }
}
