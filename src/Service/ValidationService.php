<?php


namespace App\Service;

use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ValidationService
{


    public function __construct(private ValidatorInterface $validator){}

    public function validateur($contraintes)
    {
        $cont = array();
        $input = array();
        foreach ($contraintes as $key => $value) {
            if (array_key_exists("length", $value))
                $cont[$key] = array(new Assert\Length(['max' => $value["length"]]));

            if (array_key_exists("type", $value)) {
                if ($value["type"] == "int") {
                    $cont[$key][1] = new Assert\Type("integer");
                } else if ($value["type"] == "string") {
                    $cont[$key][1] = new Assert\Type("string");
                }
            }
            $input[$key] = $value["val"];
        }
        $constraints = new Assert\Collection($cont);
        $violations = $this->validator->validate($input, $constraints);
        if (count($violations) > 0) {
            $accessor = PropertyAccess::createPropertyAccessor();
            $errorMessages = array();
            foreach ($violations as $violation) {
                $accessor->setValue(
                    $errorMessages,
                    $violation->getPropertyPath(),
                    $violation->getMessage()
                );
                $errorMessages[] = $violation->getMessage();
            }
            return $errorMessages;
        } else {
            return true;
        }
    }
}