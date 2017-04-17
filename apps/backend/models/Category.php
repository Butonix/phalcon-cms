<?php
namespace Multiple\Backend\Models;

use Multiple\Backend\Models\Behaviors\NestedSet;
use Phalcon\Mvc\Model;

class Category extends Model
{
    public function initialize()
    {
        $this->setSource("categories");

        $this->addBehavior(new NestedSet([
            'rootAttribute'  => 'root',
            'leftAttribute'  => 'lft',
            'rightAttribute' => 'rgt',
            'levelAttribute' => 'level',
            'hasManyRoots'   => true
        ]));

        $this->addBehavior(
            new Model\Behavior\Timestampable(
                [
                    "beforeCreate" => [
                        "field"  => "created_at",
                        "format" => "Y-m-d H:i:s",
                    ]
                ]
            )
        );
    }

    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var int
     */
    protected $root;

    /**
     * @var int
     */
    protected $lft;

    /**
     * @var int
     */
    protected $rgt;

    /**
     * @var int
     */
    protected $level;
    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return int
     */
    public function getRoot()
    {
        return $this->root;
    }

    /**
     * @return int
     */
    public function getLft()
    {
        return $this->lft;
    }

    /**
     * @return int
     */
    public function getRgt()
    {
        return $this->rgt;
    }

    /**
     * @return int
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * @param int $id
     */
    public function setId(int $id)
    {
        $this->id = $id;
    }

    /**
     * @param string $name
     */
    public function setName(string $name)
    {
        $this->name = $name;
    }

    /**
     * @param int $root
     */
    public function setRoot(int $root)
    {
        $this->root = $root;
    }

    /**
     * @param int $lft
     */
    public function setLft(int $lft)
    {
        $this->lft = $lft;
    }

    /**
     * @param int $rgt
     */
    public function setRgt(int $rgt)
    {
        $this->rgt = $rgt;
    }

    /**
     * @param int $level
     */
    public function setLevel(int $level)
    {
        $this->level = $level;
    }

    /**
     * @param $id
     * @return bool|string
     */
    public function deleteCategory($id) {

        $category = $this->findFirst($id);
        if($category){
            if($category->deleteNode()){
                return true;
            } else {
               return false;
            }
        } else {
            return 'id doesn\'t exists';
        }
    }

    /**
     * Create category
     * @param $post_data
     * @return mixed
     */
    public function createCategory($post_data)
    {
        $nestedSet = new Category();
        if ($post_data->name && $post_data->id) {

            $root = $this->findFirst($post_data->id);
            $nestedSet->name = $post_data->name;

            return $nestedSet->appendTo($root);
        } else if ($post_data->name) {

            $nestedSet->name = $post_data->name;
            return $nestedSet->saveNode();
        }
    }

    /**
     * @param $post_data
     * @return bool
     */
    public function updateCategory($post_data)
    {
            $nestedSet = self::findFirst($post_data->id);

        if ($post_data->name && $post_data->parent_id) {

            $root = $this->findFirst($post_data->parent_id);
            $nestedSet->name = $post_data->name;
                return  $nestedSet->moveAsFirst($root);


        } else if ($post_data->name) {

            $nestedSet->findFirst($post_data->id);
            $nestedSet->name = $post_data->name;
                return $nestedSet->saveNode();
        }

    }

    /**
     * return all categories for options in crawler controller missionAction
     */
    public static function allCategoriesForOption()
    {
        $categoriesList = Category::find();
        $categoryData = [];
        foreach ($categoriesList as $category) {
            $categoryData[] = ['value' => $category->id, 'label' => $category->name];
        }
      return $categoryData;
    }

    /**
     * @param $id
     * @return Model
     */
    public function findCategory($id)
    {
        return $this->findFirst($id);
    }
}