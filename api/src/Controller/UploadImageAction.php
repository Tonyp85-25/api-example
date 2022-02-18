<?php
namespace App\Controller;

use ApiPlatform\Core\Validator\ValidatorInterface;
use ApiPlatform\Core\Validator\Exception\ValidationException;
use App\Entity\Image;
use App\Form\ImageType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;

class UploadImageAction
{
    public function __construct(private FormFactoryInterface $formFactory, private EntityManagerInterface $entityManager,
      private ValidatorInterface $validator)
    {
        
    }

    public function __invoke(Request $request)
    {
        //create a new image instance
            $image =new Image();
        //validate the form
        $form= $this->formFactory->create(ImageType::class, $image);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
             //persist the new image entity
             $this->entityManager->persist($image);
             $this->entityManager->flush();
             //the api does not have to return image binaries
             $image->setFile(null);
             return $image;
        }
 //uploading done for us by VichUpload
        throw new ValidationException(
            $this->validator->validate($image)
        );
       
    }
}