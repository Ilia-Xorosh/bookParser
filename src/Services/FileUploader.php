<?php
namespace App\Services;

use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Constraints as Assert;


class FileUploader
{
   private string $imagesDirectory = 'public/images/';
    public function upload($url)
    {
        $urlConstraint = new Assert\Url();
        $urlConstraint->message = 'Invalid URL address';
        $validator = Validation::createValidator();
        $errors = $validator->validate($url, $urlConstraint);
        if (count($errors) > 0 || $url == '') return '';
        $stringFile = file_get_contents($url);
        $fileName = $this->imagesDirectory . md5(uniqid()) . '.jpg';
        file_put_contents($fileName, $stringFile);
        return $fileName;
    }
}