<?php

namespace Src;
require_once __DIR__ . '/../config/Database.php';
use App\Config\Database;
use PDO;

class Article{
    private $id;
    private $title;
    private $slug;
    private $content;
    private $excerpt;
    private $metaDescription;
    private $categoryId;
    private $authorId;
    private $featuredImage;
    private $scheduledDate;
    private $createdAt;
    private $updatedAt;
    private $views;
    private static ?PDO $db = null;

    public function __construct(){
        self::$db = Database::connect();
    }

    // Getter et Setter methods
    public function getId(){
        return $this->id;
    }
    public function setId($id){
        $this->id = $id;
    }

    public function getTitle(){
        return $this->title;
    }
    public function setTitle($title){
        $this->title = $title;
    }

    public function getSlug(){
        return $this->slug;
    }
    public function setSlug($slug){
        $this->slug = $slug;
    }

    public function getContent(){
        return $this->content;
    }
    public function setContent($content){
        $this->content = $content;
    }

    public function getExcerpt(){
        return $this->excerpt;
    }
    public function setExcerpt($excerpt){
        $this->excerpt = $excerpt;
    }

    public function getMetaDescription(){
        return $this->metaDescription;
    }
    public function setMetaDescription($metaDescription){
        $this->metaDescription = $metaDescription;
    }

    public function getCategoryId(){
        return $this->categoryId;
    }
    public function setCategoryId($categoryId){
        $this->categoryId = $categoryId;
    }

    public function getAuthorId(){
        return $this->authorId;
    }
    public function setAuthorId($authorId){
        $this->authorId = $authorId;
    }

    public function getFeaturedImage(){
        return $this->featuredImage;
    }
    public function setFeaturedImage($featuredImage){
        $this->featuredImage = $featuredImage;
    }

    public function getScheduledDate(){
        return $this->scheduledDate;
    }
    public function setScheduledDate($scheduledDate){
        $this->scheduledDate = $scheduledDate;
    }

    public function getCreatedAt(){
        return $this->createdAt;
    }
    public function setCreatedAt($createdAt){
        $this->createdAt = $createdAt;
    }

    public function getUpdatedAt(){
        return $this->updatedAt;
    }
    public function setUpdatedAt($updatedAt){
        $this->updatedAt = $updatedAt;
    }

    public function getViews(){
        return $this->views;
    }
    public function setViews($views){
        $this->views = $views;
    }

    // Create
    public function create($tagIds = []){
        $slug = $this->generateSlug($this->title);   // slugs
        $slugQuery = self::$db->prepare("SELECT COUNT(*) FROM articles WHERE slug = :slug");
        $slugQuery->bindParam(':slug', $slug);
        $slugQuery->execute();
    
        if ($slugQuery->fetchColumn() > 0) {
            $slug = $this->generateUniqueSlug($slug);
        }
    
        // Categories
        $categoryQuery = self::$db->prepare("SELECT COUNT(*) FROM categories WHERE id = :category_id");
        $categoryQuery->bindParam(':category_id', $this->categoryId);
        $categoryQuery->execute();
    
        if ($categoryQuery->fetchColumn() == 0) {
            die('cette categorie ni existe pas.');
        }
        
        // Img Validation
        if (isset($_POST['featured_image'])) {
            $featuredImageUrl = trim($_POST['featured_image']);
            if (!filter_var($featuredImageUrl, FILTER_VALIDATE_URL)) {
                die('l url de l image ni pas valide.');
            }
            $this->featuredImage = $featuredImageUrl;
        }
    
        $sql = "INSERT INTO articles (title, slug, content, excerpt, meta_description, category_id, featured_image, scheduled_date)
                VALUES (:title, :slug, :content, :excerpt, :meta_description, :category_id, :featured_image, :scheduled_date)";
        $stmt = self::$db->prepare($sql);
        $stmt->bindParam(':title', $this->title);
        $stmt->bindParam(':slug', $slug);
        $stmt->bindParam(':content', $this->content);
        $stmt->bindParam(':excerpt', $this->excerpt);
        $stmt->bindParam(':meta_description', $this->metaDescription);
        $stmt->bindParam(':category_id', $this->categoryId);
        $stmt->bindParam(':featured_image', $this->featuredImage);
        $stmt->bindParam(':scheduled_date', $this->scheduledDate);
    
        if ($stmt->execute()) {
            $articleId = self::$db->lastInsertId();
            if (!empty($tagIds)) {
                foreach ($tagIds as $tagId) {
                    $this->addTagToArticle($articleId, $tagId);
                }
            }
            return true;
        }
        return false;
    } 

    // edit the Article
    public function update($tagIds = []){ // see if the article is already regitered
        $categoryQuery = self::$db->prepare("SELECT COUNT(*) FROM categories WHERE id = :category_id");
        $categoryQuery->bindParam(':category_id', $this->categoryId);
        $categoryQuery->execute();

        if ($categoryQuery->fetchColumn() == 0) {
            die('cette categorie ni existe pas.');
        }

        $slug = $this->generateSlug($this->title);
        if ($this->slugExists($slug)) {
            $slug = $this->generateUniqueSlug($slug);
        }

        if (!empty($this->featuredImage) && !filter_var($this->featuredImage, FILTER_VALIDATE_URL)) {
            die('le URL de cette image non valide.');
        }

        // update article
        $sql = "UPDATE articles 
                SET title = :title, slug = :slug, content = :content, excerpt = :excerpt, meta_description = :meta_description, 
                    category_id = :category_id, featured_image = :featured_image, scheduled_date = :scheduled_date, updated_at = NOW()
                WHERE id = :id";
        $stmt = self::$db->prepare($sql);
        $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);
        $stmt->bindParam(':title', $this->title);
        $stmt->bindParam(':slug', $slug);
        $stmt->bindParam(':content', $this->content);
        $stmt->bindParam(':excerpt', $this->excerpt);
        $stmt->bindParam(':meta_description', $this->metaDescription);
        $stmt->bindParam(':category_id', $this->categoryId);
        $stmt->bindParam(':featured_image', $this->featuredImage);
        $stmt->bindParam(':scheduled_date', $this->scheduledDate);

        if ($stmt->execute()) {
            $this->updateTags($tagIds);
            return true;
        }
        return false;
    }

    private function addTagToArticle($articleId, $tagId){
        $insertTagQuery = self::$db->prepare("INSERT INTO article_tags (article_id, tag_id) VALUES (:article_id, :tag_id)");
        $insertTagQuery->bindParam(':article_id', $articleId);
        $insertTagQuery->bindParam(':tag_id', $tagId);
        $insertTagQuery->execute();
    }

    private function updateTags($tagIds){
        $deleteTagsQuery = self::$db->prepare("DELETE FROM article_tags WHERE article_id = :article_id");
        $deleteTagsQuery->bindParam(':article_id', $this->id, PDO::PARAM_INT);
        $deleteTagsQuery->execute();

        foreach ($tagIds as $tagId) {
            $this->addTagToArticle($this->id, $tagId);
        }
    }

    private function generateSlug($title){
        return strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));
    }

    private function generateUniqueSlug($slug){
        $counter = 1;
        $uniqueSlug = $slug . '-' . $counter;

        while ($this->slugExists($uniqueSlug)) {
            $counter++;
            $uniqueSlug = $slug . '-' . $counter;
        }
        return $uniqueSlug;
    }

    private function slugExists($slug){
        $query = self::$db->prepare("SELECT COUNT(*) FROM articles WHERE slug = :slug");
        $query->bindParam(':slug', $slug);
        $query->execute();

        return $query->fetchColumn() > 0;
    }

    public function delete($articleId) { // delete An article 
        $sql = "DELETE FROM articles WHERE id = :id";
        $stmt = self::$db->prepare($sql);
        $stmt->bindParam(':id', $articleId, PDO::PARAM_INT);

        return $stmt->execute();
    }

    public function fetchAll(){ // Restore Any article 
        $sql = "SELECT * FROM articles";
        $stmt = self::$db->query($sql);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getCategories(){
        $sql = "SELECT id, name FROM categories";
        $stmt = self::$db->query($sql);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getTags(){
        $sql = "SELECT id, name FROM tags";
        $stmt = self::$db->query($sql);

        return $stmt->fetchAll(PDO::FETCH_ASSOC); 
    }
}
