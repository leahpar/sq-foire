<?php

namespace App\Controller\Admin;

use App\Entity\Pub;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class PubCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Pub::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setSearchFields(['id', 'name', 'image', 'link'])
            ->setPaginatorPageSize(20);
    }

    public function configureFields(string $pageName): iterable
    {
        $name = TextField::new('name');
        $halls = ArrayField::new('halls');
        $imageFile = Field::new('imageFile');
        $link = BooleanField::new('link');
        $exclu = BooleanField::new('exclu');
        $image = ImageField::new('image');

        if (Crud::PAGE_INDEX === $pageName) {
            return [$name, $halls, $image, $exclu, $link];
        } elseif (Crud::PAGE_DETAIL === $pageName) {
            return [$name, $halls, $image, $exclu];
        } elseif (Crud::PAGE_NEW === $pageName) {
            return [$name, $halls, $imageFile, $link, $exclu];
        } elseif (Crud::PAGE_EDIT === $pageName) {
            return [$name, $halls, $imageFile, $link, $exclu];
        }
    }
}
