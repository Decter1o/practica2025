<?php
require_once(__DIR__ . '/../models/Logger.php');

class News{

    private $id;
    private $url;
    private $anounce;
    private $preview;
    private $content;
    private $status;
    private $created_at;
    private $published_at;

    public function init($id, $url, $anounce, $preview, $content, $status, $created_at, $published_at) {
        $this->id = $id;
        $this->url = $url;
        $this->anounce = $anounce;
        $this->preview = $preview;
        $this->content = $content;
        $this->status = $status;
        $this->created_at = $created_at;
        $this->published_at = $published_at;
        return $this;
    }

    public function GetId() {
        return $this->id;
    }

    public function GetUrl() {
        return $this->url;
    }

    public function GetAnounce() {
        return $this->anounce;
    }

    public function GetPreview() {
        return $this->preview;
    }

    public function GetContent() {
        return $this->content;
    }

    public function GetStatus() {
        return $this->status;
    }

    public function GetCreatedAt() {
        return $this->created_at;
    }

    public function GetPublishedAt() {
        return $this->published_at;
    }

    public static function GetAll($pdo) {
        try {
            $stmt = $pdo->query("SELECT * FROM news");
            Logger::success("Все новости успешно получены.");
            return $stmt->fetchAll(PDO::FETCH_CLASS, 'News');
        } catch (PDOException $e) {
            Logger::error("Ошибка при получении всех новостей: " . $e->getMessage());
            return [];
        }
    }

    public static function GetById($id, $pdo) {
        try {
            $stmt = $pdo->prepare("SELECT * FROM news WHERE id = ?");
            $stmt->execute([$id]);
            $news = $stmt->fetchObject('News');
            if ($news) {
                Logger::success("Новость с ID $id успешно получена.");
                return $news;
            } else {
                Logger::error("Новость с ID $id не найдена.");
                return null;
            }
        } catch (PDOException $e) {
            Logger::error("Ошибка при получении новости с ID $id: " . $e->getMessage());
            return null;
        }
    }

    public static function Create($id, $url, $anounce, $preview, $content, $pdo) {
        try {
            $stmt = $pdo->prepare("INSERT INTO news (id, url, anounce, preview, content) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$id, $url, $anounce, $preview, $content]);
            Logger::success("Новость с ID $id успешно создана.");
            return true;
        } catch (PDOException $e) {
            Logger::error("Ошибка при создании новости: " . $e->getMessage());
            return false;
        }
    }

    public static function Update($id, $url, $anounce, $preview, $content, $pdo) {
        try {
            $stmt = $pdo->prepare("UPDATE news SET url = ?, anounce = ?, preview = ?, content = ? WHERE id = ?");
            $stmt->execute([$url, $anounce, $preview, $content, $id]);
            Logger::success("Новость с ID $id успешно обновлена.");
            return true;
        } catch (PDOException $e) {
            Logger::error("Ошибка при обновлении новости: " . $e->getMessage());
            return false;
        }
    }

    public static function Delete($id, $pdo) {
        try {
            $stmt = $pdo->prepare("CALL delete_news(?)");
            $stmt->execute([$id]);
            Logger::success("Новость с ID $id успешно удалена.");
            return true;
        } catch (PDOException $e) {
            Logger::error("Ошибка при удалении новости: " . $e->getMessage());
            return false;
        }
    }

    public static function Restore($id, $pdo) {
        try {
            $stmt = $pdo->prepare("CALL restore_news(?)");
            $stmt->execute([$id]);
            Logger::success("Новость с ID $id успешно восстановлена.");
            return true;
        } catch (PDOException $e) {
            Logger::error("Ошибка при восстановлении новости: " . $e->getMessage());
            return false;
        }
    }
}