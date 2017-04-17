<?php
/**
 * Created by PhpStorm.
 * User: stefan
 * Date: 2/25/17
 * Time: 8:27 PM
 */

namespace Multiple\Backend\Controllers;


use Multiple\Backend\Models\Category;
use Multiple\Backend\Validations\CategoriesValidation;
use Phalcon\Mvc\Controller;


class CategoriesController extends Controller
{
    /**
     * Return all categories and render view
     */
    public function allCategoriesAction() {

        $categories = Category::find();
        $data = [];

        foreach ($categories as $n => $category) {
            if ($category->isRoot()) {

                $descendants = $category->descendants();

                if(count($descendants) > 0) {
                    foreach ($descendants as $descendant) {
                        $data[$category->name][] = ['primary_category_id'=>$category->id,'child_name'=>$descendant->name,'child_id'=>$descendant->id];

                    }
                } else {
                    $data[$category->name][] = ['primary_category_id'=>$category->id];
                }

            }
        }

        $this->view->setVar('data',$data);
    }

    /**
     * Create category with ajax. Encode to json categories var and set to view.
     * @return string
     */
    public function createCategoryAction(){

        if ($this->request->isAjax() || $this->request->isPost()) {
            $post_data = $this->request->getJsonRawBody();
            $validation = new CategoriesValidation();
            $errors = $validation->validate($post_data);
            $errors = $validation->returnArrayErrors($errors);

            if(!empty($errors)) {
                return json_encode(['error' => true, 'errors' => $errors, 'message' => 'Error in saving records' ]);
            } else {
                $category = new Category();
                if ($category->createCategory($post_data)) {
                    return json_encode(['error' => false, 'message' => 'Records saved']);
                } else {
                    return json_encode(['error' => true, 'message' => 'Error in saving records']);
                }
            }
        }
        $categories = Category::find();
        $this->view->setVar('categories', json_encode($categories));
    }

    /**
     * @param $id
     */
    public function editCategoryAction($id) {

        $categoryObj = new Category();
        $category = $categoryObj->findCategory($id);
        $parent = $category->parent();

        $selectedCategory = ['value' => $parent->id, 'label' => $parent->name];
        $allCategories = Category::allCategoriesForOption();
        $cat_arr = $categoryObj->findCategory($id)->toArray();
//        print_r($cat_arr);
//        die;
//        print_r($parent->name);
//        die;
        $this->view->setVars(['allCategories' => json_encode($allCategories), 'category_data'=> json_encode($cat_arr), 'parent_data' => json_encode($selectedCategory)]);
    }

    /**
     * @return string
     */
    public function updateCategoryAction() {

        if ($this->request->isAjax() || $this->request->isPost()) {
            $post_data = $this->request->getJsonRawBody();
            $validation = new CategoriesValidation();
            $errors = $validation->validate($post_data);
            $errors = $validation->returnArrayErrors($errors);

            if(!empty($errors)) {
                return json_encode(['error' => true, 'errors' => $errors, 'message' => 'Error in saving records' ]);
            }else{
                $categoryObj = new Category();
                if ($categoryObj->updateCategory($post_data)){
                    return json_encode(['error' => false, 'message' => 'Records saved']);
                } else {
                    return json_encode(['error' => true, 'message' => 'Error in saving records']);
                }
            }
        }
    }


    /**
     * Delete category or subcategory
     * @param $id
     */
    public function deleteAction($id){
        if(is_numeric($id) && isset($id)) {
            $category = new Category();
            if($category->deleteCategory($id)) {
                $this->flashSession->success("Successfully deleted category");
               $this->redirectAction();
            } else if($category->deleteCategory($id) === false) {
                $this->flashSession->warning("Error in deleting category");
                $this->redirectAction();
            } else {
                $this->flashSession->warning("id doesn't exists");
                // Forward to the index action
                $this->redirectAction();
            }
        } else {
            $this->flashSession->warning("id is not integer");
            // Forward to the index action
            $this->redirectAction();
        }
    }

    /**
     * Redirects to all categories page
     */
    private function redirectAction() {
         $this->dispatcher->forward(array(
            "controller" => "categories",
            "action" => "allCategories"
        ));
    }


}