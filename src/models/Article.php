<?php

class Article{
    private $id;
    private $url;
    private $title;
    private $content;
    private $status;
    private $created_at;

    public function init($id, $url, $title, $content, $status, $createdAt)
    {
        $this->id = $id;
        $this->url = $url;
        $this->title = $title;
        $this->content = $content;
        $this->status = $status;
        $this->createdAt = $created_at;
        return $this;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getUrl()
    {
        return $this->url;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function getContent()
    {
        return $this->content;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function getCreatedAt()
    {
        return $this->created_at;
    }

    public static function GetByURL($url, $pdo)
    {
        try {
            $stmt = $pdo->prepare("SELECT * FROM articles WHERE url = ?");
            $stmt->execute([$url]);
            $article = $stmt->fetchObject('Article');
            if ($article) {
                return $article;
            } else {
                Logger::error("Статья с URL $url не найдена.");
                return null;
            }
        } catch (PDOException $e) {
            Logger::error("Ошибка при получении статьи: " . $e->getMessage());
            return null;
        }
    }

    public static function GetAll($pdo)
    {
        try {
            $stmt = $pdo->query("SELECT * FROM articles");
            $articles = $stmt->fetchAll(PDO::FETCH_CLASS, 'Article');
            if ($articles) {
                return $articles;
            } else {
                Logger::success("Нет доступных статей.");
                return [];
            }
        } catch (PDOException $e) {
            Logger::error("Ошибка при получении статей: " . $e->getMessage());
            return [];
        }
    }

    public static function Add($id, $url, $title, $content, $pdo)
    {
        try {
            $stmt = $pdo->prepare("INSERT INTO articles (id, url, title, content) VALUES (?, ?, ?, ?)");
            $result = $stmt->execute([$id, $url, $title, $content]);
            Logger::success("Статья успешно добавлена.");
        } catch (PDOException $e) {
            Logger::error("Ошибка при добавлении статьи: " . $e->getMessage());
            return false;
        }        
    }

    public static function Update($id, $url, $title, $content, $pdo)
    {
        try {
            $stmt = $pdo->prepare("UPDATE articles SET url = ?, title = ?, content = ? WHERE id = ?");
            $result = $stmt->execute([$url, $title, $content, $id]);
            Logger::success("Статья успешно обновлена.");
        } catch (PDOException $e) {
            Logger::error("Ошибка при обновлении статьи: " . $e->getMessage());
            return false;
        }
    }

    public static function Delete($id, $pdo){
        try {
            $stmt = $pdo->prepare("CALL delete_article(?)");
            $result = $stmt->execute([$id]);
            Logger::success("Статья успешно удалена.");
            return true;
        } catch (PDOException $e) {
            Logger::error("Ошибка при удалении статьи: " . $e->getMessage());
            return false;
        }
    }

    public static function Restore($id, $pdo)
    {
        try {
            $stmt = $pdo->prepare("CALL restore_article(?)");
            $result = $stmt->execute([$id]);
            Logger::success("Статья успешно восстановлена.");
            return true;
        } catch (PDOException $e) {
            Logger::error("Ошибка при восстановлении статьи: " . $e->getMessage());
            return false;
        }
    }
}