<?php
namespace Src\categories;

use Src\Crud\CRUD;

class Category {
    private $crud;

    public $id;
    public $name;

    public function __construct($db) {
        $this->crud = new CRUD($db, "categories"); 
    }


    public function create() {
        $this->crud->fields = [
            'name' => $this->name,
        ];
        return $this->crud->create();
    }

    public function read() {
        return $this->crud->read();
    }

    public function readOne() {
        $this->crud->id = $this->id;
        return $this->crud->readOne();
    }

    public function update() {
        $this->crud->fields = [
            'name' => $this->name,
        ];
        $this->crud->id = $this->id;
        return $this->crud->update();
    }

    public function delete() {
        $this->crud->id = $this->id;
        return $this->crud->delete();
    }
}

?>