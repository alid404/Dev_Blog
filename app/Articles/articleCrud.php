<?php
class Posts {
    private $article;

    public function __construct(){
        if(!isLoggedIn()){
            redirect('users/login');
        }
        $this->article = new Article();
    }

    public function index(){
        // Get articles
        $articles = $this->article->getArticles();

        $data = [
            'articles' => $articles
        ];

        $this->view('posts/index', $data);
    }

    public function add(){
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Sanitize POST array
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

            $data = [
                'title' => trim($_POST['title']),
                'slug' => strtolower(trim($_POST['slug'])),
                'content' => trim($_POST['content']),
                'excerpt' => trim($_POST['excerpt']),
                'meta_description' => trim($_POST['meta_description']),
                'category_id' => $_POST['category_id'],
                'featured_image' => trim($_POST['featured_image']),
                'status' => $_POST['status'],
                'author_id' => $_SESSION['user_id'],
                'scheduled_date' => $_POST['scheduled_date'],
                'title_err' => '',
                'content_err' => ''
            ];

            if (empty($data['title'])) {
                $data['title_err'] = 'Please enter a title';
            }
            if (empty($data['content'])) {
                $data['content_err'] = 'Please enter content';
            }

            // Make sure there are no errors
            if (empty($data['title_err']) && empty($data['content_err'])) {
                if ($this->article->addArticle($data)) {
                    flash('post_message', 'Article Added');
                    redirect('posts');
                } else {
                    die('Something went wrong');
                }
            } else {
                $this->view('posts/add', $data);
            }
        } else {
            $data = [
                'title' => '',
                'slug' => '',
                'content' => '',
                'excerpt' => '',
                'meta_description' => '',
                'category_id' => '',
                'featured_image' => '',
                'status' => '',
                'scheduled_date' => ''
            ];

            $this->view('posts/add', $data);
        }
    }

    public function edit($id){
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Sanitize POST array
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

            $data = [
                'id' => $id,
                'title' => trim($_POST['title']),
                'slug' => strtolower(trim($_POST['slug'])),
                'content' => trim($_POST['content']),
                'excerpt' => trim($_POST['excerpt']),
                'meta_description' => trim($_POST['meta_description']),
                'category_id' => $_POST['category_id'],
                'featured_image' => trim($_POST['featured_image']),
                'status' => $_POST['status'],
                'scheduled_date' => $_POST['scheduled_date'],
                'title_err' => '',
                'content_err' => ''
            ];

            // Validate data
            if (empty($data['title'])) {
                $data['title_err'] = 'Please enter a title';
            }
            if (empty($data['content'])) {
                $data['content_err'] = 'Please enter content';
            }

            // Make sure there are no errors
            if (empty($data['title_err']) && empty($data['content_err'])) {
                if ($this->article->updateArticle($data)) {
                    flash('post_message', 'Article Updated');
                    redirect('posts');
                } else {
                    die('Something went wrong');
                }
            } else {
                $this->view('posts/edit', $data);
            }
        } else {
            $article = $this->article->getArticleById($id);

            $data = [
                'id' => $id,
                'title' => $article->title,
                'slug' => $article->slug,
                'content' => $article->content,
                'excerpt' => $article->excerpt,
                'meta_description' => $article->meta_description,
                'category_id' => $article->category_id,
                'featured_image' => $article->featured_image,
                'status' => $article->status,
                'scheduled_date' => $article->scheduled_date
            ];

            $this->view('posts/edit', $data);
        }
    }

    public function show($id){
        $article = $this->article->getArticleById($id);

        $data = [
            'article' => $article
        ];

        $this->view('posts/show', $data);
    }

    public function delete($id){
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $article = $this->article->getArticleById($id);

            // Check if the logged-in user is the author
            if ($article->author_id != $_SESSION['user_id']) {
                redirect('posts');
            }

            if ($this->article->deleteArticle($id)) {
                flash('post_message', 'Article Removed');
                redirect('posts');
            } else {
                die('Something went wrong');
            }
        } else {
            redirect('posts');
        }
    }
}
