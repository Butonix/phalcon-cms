<?php
/**
 * Created by PhpStorm.
 * User: stefan
 * Date: 2/5/17
 * Time: 4:25 PM
 */
namespace Multiple\Backend\Controllers;
use Phalcon\Http\Request;
use Phalcon\Mvc\Controller;
use Phalcon\Mvc\View;
use Multiple\Backend\Models\Crawler as Crawler;
use Multiple\Backend\Models\Sites as Site;
use Phalcon\Paginator\Adapter\Model as PaginatorModel;
use Multiple\Backend\Validations\SitesValidation;
use Multiple\Backend\Models\Category;


class CrawlerController extends Controller
{
    /**
     * get html from websites, parse and save into db.
     *
     */
    public function dataAction()
    {
        $data = file_get_contents('http://www.prakse.rs/frontend/pretraga');
        //Pri parametar je tag. Moze da se pretrazuje po klasi ili id-ju elementa.
        $element_title = 'div[class=post_title_left]';
        $element_description = 'strong[class=ttl]';
        $title = $this->getTextBetweenTags($data, $element_title);
        $description = $this->getTextBetweenTags($data, $element_description);

        $import_data = [];
        $count_data = count($title);
        for ($i = 0; $i < $count_data; $i++) {
            $import_data[$i]['data_title'] = $title[$i];
            $import_data[$i]['data_description'] = $description[$i];
        }
        $crawler = new Crawler();

        foreach ($import_data as $data) {
            $save =  $crawler->save($data);
            $crawler->id = null;

        }
        if($save) {
           die("Saved records");
        }
    }

    /**
     * @param $string
     * @param $element
     * @return array
     */
    private function getTextBetweenTags($string, $element) {
        // Create DOM from string
        $html = str_get_html($string);

        $titles = array();
        // Find all tags
        foreach($html->find($element) as $k => $element) {
            $titles[] = $element->plaintext;
        }

        return $titles;
    }

    /**
     * @param $id
     */
    public function editWebsiteAction($id) {

        $siteObj = new Site();
        $site = $siteObj->findSite($id)->toArray();
        $this->view->setVar('site_data', json_encode($site));
    }

    /**
     * Create websites which will be crawled by interno bot
     *
     */
    public function createWebsiteAction(){

        if ($this->request->isAjax() || $this->request->isPost()) {
            $post_data = $this->request->getJsonRawBody();
            $validation = new SitesValidation();
            $errors = $validation->validate($post_data);
            $errors = $validation->returnArrayErrors($errors);

            if(!empty($errors)) {
                return json_encode(['error' => true, 'errors' => $errors, 'message' => 'Error in saving records' ]);
            } else {
                $site = new Site();
                if ($site->createWebsite($post_data)) {
                    return json_encode(['error' => false, 'message' => 'Records saved']);
                } else {
                    return json_encode(['error' => true, 'message' => 'Error in saving records']);
                }
            }
        }
    }

    /**
     * Update website data
     * @return string
     */
    public function updateWebsiteAction() {

        if ($this->request->isAjax()) {
            $post_data = $this->request->getJsonRawBody();
            $validation = new SitesValidation();
            $errors = $validation->validate($post_data);
            $errors = $validation->returnArrayErrors($errors);

            if(!empty($errors)) {
                return json_encode(['error' => true, 'errors' => $errors, 'message' => 'Error in saving records' ]);
            }else{
                $siteObj = new Site();
                if ($siteObj->updateSite($post_data)){
                    return json_encode(['error' => false, 'message' => 'Records saved']);
                } else {
                    return json_encode(['error' => true, 'message' => 'Error in saving records']);
                }
            }
        }
    }

    public function deleteWebsiteAction(){

        if ($this->request->isAjax() || $this->request->isPost()) {
            $post_data = $this->request->getJsonRawBody();
            $validation = new SitesValidation();
            $errors = $validation->validate($post_data);
            $errors = $validation->returnArrayErrors($errors);

            if(!empty($errors)) {
                return json_encode(['error' => true, 'errors' => $errors, 'message' => 'Error in deleting records' ]);
            }else{
                $siteObj = new Site();
                if ($siteObj->deleteSite($post_data)){
                    return json_encode(['error' => false, 'message' => 'Record deleted']);
                } else {
                    return json_encode(['error' => true, 'message' => 'Error in deleting records']);
                }
            }
        } else {
            die('aaa');
        }
    }

    /**
     * Return all websites and return response to json.
     * @return string
     */
    public function allWebsiteAction(){

        if ($this->request->isAjax()) {
            $this->view->setRenderLevel(View::LEVEL_NO_RENDER);

            $sites = new Site();

            $all = $sites->find(
                [
                    'order' => 'name'
                ]
            );
            foreach ($all as $site) {
                $data[] = $site->name;
            }
            if (empty($data)) {

                return json_encode(['error' => true, 'message' => 'There is no sites']);
            } else {

                return json_encode(['data' => $data]);
            }
        } else {

            $sites = new Site();

            $currentPage = (int) $_GET["page"];

            // The data set to paginate
            $data = $sites->getAllSites();

            // Create a Model paginator, show 10 rows by page starting from $currentPage
            $paginator = new PaginatorModel(
                [
                    "data"  => $data,
                    "limit" => 30,
                    "page"  => $currentPage,
                ]
            );

            // Get the paginated results
            $page = $paginator->getPaginate();

            $this->view->setVar('page', $page);
        }
    }

    /**
     * Getting all categories and all sites list and return to view with json.
     */
    public function missionAction(){
        if ($this->request->isAjax() || $this->request->isPost()) {
            var_dump($this->request->getJsonRawBody()); die;
        }
        $categoryData = Category::allCategoriesForOption();
        $sitesData = Site::findAllSitesForOption();

        $this->view->setVars(['categoryData' => json_encode($categoryData), 'sitesData' => json_encode($sitesData)]);
    }

}