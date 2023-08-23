<?php

namespace App\Controller\Admin;

use App\Entity\Hall;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class HallCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Hall::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setSearchFields(['id', 'name', 'tri'])
            ->setPaginatorPageSize(50);
    }

    public function configureFields(string $pageName): iterable
    {
        $tri = IntegerField::new('tri');
        $name = TextField::new('name');
        $questions = AssociationField::new('questions');
        $hasExclu = BooleanField::new('hasExclu', 'exclu');

        if (Crud::PAGE_INDEX === $pageName) {
            return [$tri, $name, $questions, $hasExclu];
        } elseif (Crud::PAGE_DETAIL === $pageName) {
            return [$name, $tri, $questions, $hasExclu];
        } elseif (Crud::PAGE_NEW === $pageName) {
            return [$tri, $name];
        } elseif (Crud::PAGE_EDIT === $pageName) {
            return [$tri, $name];
        }
    }
}
