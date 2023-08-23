<?php

namespace App\Controller\Admin;

use App\Entity\Sms;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class SmsCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Sms::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setSearchFields(['id', 'message'])
            ->setPaginatorPageSize(20);
    }

    public function configureFields(string $pageName): iterable
    {
        $message = TextField::new('message');

        if (Crud::PAGE_INDEX === $pageName) {
            return [$message];
        } elseif (Crud::PAGE_DETAIL === $pageName) {
            return [$message];
        } elseif (Crud::PAGE_NEW === $pageName) {
            return [$message];
        } elseif (Crud::PAGE_EDIT === $pageName) {
            return [$message];
        }
    }
}
