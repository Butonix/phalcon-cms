<?php
namespace Multiple\Backend\Models;
use Phalcon\Mvc\Model;

class Crawler extends Model
{

    public function initialize()
    {
        $this->setSource("crawlers");

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
     * @var integer
     */
    public $id;
    /**
     * @var string
     */
    public $data_title;
    /**
     * @var string
     */
    public $data_description;
    /**
     * @var string
     */
    public $company_name;

    public function getId()
    {
        return $this->id;
    }


}