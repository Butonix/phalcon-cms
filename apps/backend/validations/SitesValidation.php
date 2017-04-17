<?php
namespace Multiple\Backend\Validations;

use Phalcon\Validation;
use Phalcon\Validation\Validator\PresenceOf;

class SitesValidation extends MainValidation
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

        $this->add(
            "country",
            new PresenceOf(
                [
                    "message" => "The country is required",
                ]
            )
        );

        $this->add(
            "continent",
            new PresenceOf(
                [
                    "message" => "The continent is required",
                ]
            )
        );

    }
}