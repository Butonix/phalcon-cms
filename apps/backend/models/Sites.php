<?php
/**
 * Created by PhpStorm.
 * User: stefan
 * Date: 2/16/17
 * Time: 2:17 PM
 */

namespace Multiple\Backend\Models;

use Phalcon\Mvc\Model;
use Phalcon\Mvc\Model\Behavior\Timestampable;

class Sites extends Model
{
    public function initialize()
    {
        $this->setSource("sites");

        $this->addBehavior(
            new Timestampable(
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
     * @var integer
     */
    protected $id;
    /**
     * @var string
     */
    protected $name;
    /**
     * @var string
     */
    protected $continent;
    /**
     * @var string
     */
    protected $country;
    /**
     * @var datetime
     */
//    protected $created_at;

    /**
     * @var string
     */
//    protected $modified;

    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getContinent(): string
    {
        return $this->continent;
    }

    /**
     * @return string
     */
    public function getCountry(): string
    {
        return $this->country;
    }

    /**
     * @return string
     */
//    public function getCreated()
//    {
//        return $this->created_at;
//    }

    /**
     * @return string
     */
//    public function getModified(): string
//    {
//        return $this->modified;
//    }

    /**
     * @param string $name
     */
    public function setName(string $name)
    {
        $this->name = $name;
    }

    /**
     * @param string $continent
     */
    public function setContinent(string $continent)
    {
        $this->continent = $continent;
    }

    /**
     * @param string $country
     */
    public function setCountry(string $country)
    {
        $this->country = $country;
    }

    /**
     * @param string $created
     */
//    public function setCreated(datetime $created_at)
//    {
//        $this->created = $created_at;
//    }

    /**
     * @param string $modified
     */
//    public function setModified(string $modified)
//    {
//        $this->modified = $modified;
//    }

    /**
     * @param $post_data
     * @return bool
     */
    public function createWebsite($post_data)
    {

        $this->name = $post_data->name;
        $this->continent = $post_data->continent;
        $this->country = $post_data->country;
        return $this->save();
    }

    public function getAllSites()
    {
        return $this->find([
            'order' => 'id'
        ]);
    }

    public function findSite($id)
    {
        return $this->findFirst($id);
    }

    public function updateSite($post_data)
    {

        $site = $this->findFirst($post_data->id);
        $site->setName($post_data->name);
        $site->setContinent($post_data->continent);
        $site->setCountry($post_data->country);
        return $site->save();
    }

    public function deleteSite($post_data)
    {

        $site = $this->findFirst($post_data->id);
        return $site->delete();
    }

    public static function findAllSitesForOption()
    {
        $sitesList = Sites::find();
        $sitesData = [];
        foreach ($sitesList as $site) {
            $sitesData[] = ['value' => $site->id, 'label' => $site->name];
        }
        return $sitesData;
    }

}