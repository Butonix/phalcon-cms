<?php
/**
 * Created by PhpStorm.
 * User: nemanja94
 * Date: 2/25/17
 * Time: 6:29 PM
 */

namespace Multiple\Backend\Validations;

use Phalcon\Validation;

class MainValidation extends  Validation
{

    public function returnArrayErrors($errors){
        $data = [];
        foreach($errors as $error){
            $data[$error->getField()] = $error->getMessage();
        }
        return $data;
    }
}