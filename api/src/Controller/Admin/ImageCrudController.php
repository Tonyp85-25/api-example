<?php

namespace App\Controller\Admin;

use App\Entity\Image;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\UrlField;

class ImageCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Image::class;
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideWhenCreating(),
            ImageField::new('file')->setBasePath('/images')->setUploadDir('public/images')->setUploadedFileNamePattern('[randomhash].[extension]')->setRequired(false),
            UrlField::new('url')->hideWhenCreating()
        ];
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {

        $entityInstance->setUrl($entityInstance->getFile());
        $entityManager->persist($entityInstance);
    }
}
