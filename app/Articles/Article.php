<?php
class Article {
    private $db;

    public function __construct(){
        $this->db = new Database;
    }

    public function getArticles(){
        $this->db->query('SELECT 
                            articles.*, 
                            users.id as userId, 
                            users.username as authorName, 
                            categories.id as categoryId, 
                            categories.name as categoryName
                          FROM articles
                          INNER JOIN users ON articles.author_id = users.id
                          INNER JOIN categories ON articles.category_id = categories.id
                          ORDER BY articles.created_at DESC');

        $results = $this->db->resultSet();

        return $results;
    }

    public function addArticle($data){
        $this->db->query('INSERT INTO articles 
                          (title, slug, content, excerpt, meta_description, category_id, featured_image, status, author_id, scheduled_date) 
                          VALUES (:title, :slug, :content, :excerpt, :meta_description, :category_id, :featured_image, :status, :author_id, :scheduled_date)');

        // Bind values
        $this->db->bind(':title', $data['title']);
        $this->db->bind(':slug', $data['slug']);
        $this->db->bind(':content', $data['content']);
        $this->db->bind(':excerpt', $data['excerpt']);
        $this->db->bind(':meta_description', $data['meta_description']);
        $this->db->bind(':category_id', $data['category_id']);
        $this->db->bind(':featured_image', $data['featured_image']);
        $this->db->bind(':status', $data['status']);
        $this->db->bind(':author_id', $data['author_id']);
        $this->db->bind(':scheduled_date', $data['scheduled_date']);

        // Execute
        return $this->db->execute();
    }

    public function updateArticle($data){
        $this->db->query('UPDATE articles 
                          SET title = :title, slug = :slug, content = :content, excerpt = :excerpt, meta_description = :meta_description, 
                              category_id = :category_id, featured_image = :featured_image, status = :status, scheduled_date = :scheduled_date
                          WHERE id = :id');

        // Bind values
        $this->db->bind(':id', $data['id']);
        $this->db->bind(':title', $data['title']);
        $this->db->bind(':slug', $data['slug']);
        $this->db->bind(':content', $data['content']);
        $this->db->bind(':excerpt', $data['excerpt']);
        $this->db->bind(':meta_description', $data['meta_description']);
        $this->db->bind(':category_id', $data['category_id']);
        $this->db->bind(':featured_image', $data['featured_image']);
        $this->db->bind(':status', $data['status']);
        $this->db->bind(':scheduled_date', $data['scheduled_date']);

        // Execute
        return $this->db->execute();
    }

    public function getArticleById($id){
        $this->db->query('SELECT 
                            articles.*, 
                            users.username as authorName, 
                            categories.name as categoryName
                          FROM articles
                          INNER JOIN users ON articles.author_id = users.id
                          INNER JOIN categories ON articles.category_id = categories.id
                          WHERE articles.id = :id');
                          
        $this->db->bind(':id', $id);

        $row = $this->db->single();

        return $row;
    }

    public function deleteArticle($id){
        $this->db->query('DELETE FROM articles WHERE id = :id');
        $this->db->bind(':id', $id);

        // Execute
        return $this->db->execute();
    }

    public function addTagToArticle($articleId, $tagId){
        $this->db->query('INSERT INTO article_tags (article_id, tag_id) VALUES (:article_id, :tag_id)');
        $this->db->bind(':article_id', $articleId);
        $this->db->bind(':tag_id', $tagId);

        return $this->db->execute();
    }
}
