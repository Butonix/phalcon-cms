<?php
/**
 * Created by PhpStorm.
 * User: milos
 * Date: 3/9/17
 * Time: 11:25 PM
 */

namespace Multiple\Backend\Validations;


use Phalcon\Validation\Validator\PresenceOf;

class CategoriesValidation extends MainValidation
{
    public function initialize()
    {
        $this->add(
            "name",
            new PresenceOf(
                [
                    "message" => "The name is required",
                ]
            )
        );
    }
}