<?php

namespace App\Controller\Admin;

use App\Entity\Answer;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class AnswerCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Answer::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setSearchFields(['id', 'answer'])
            ->setPaginatorPageSize(50);
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->disable('new', 'edit', 'show', 'delete', 'search');
    }

    public function configureFields(string $pageName): iterable
    {
        $answer = TextField::new('answer');
        $date = DateTimeField::new('date');
        $good = BooleanField::new('good');
        $player = AssociationField::new('player');
        $question = AssociationField::new('question');
        $id = IntegerField::new('id', 'ID');
        $questionHall = TextareaField::new('question.hall');

        if (Crud::PAGE_INDEX === $pageName) {
            return [$player, $question, $questionHall, $good, $answer, $date];
        } elseif (Crud::PAGE_DETAIL === $pageName) {
            return [$id, $answer, $date, $good, $player, $question];
        } elseif (Crud::PAGE_NEW === $pageName) {
            return [$answer, $date, $good, $player, $question];
        } elseif (Crud::PAGE_EDIT === $pageName) {
            return [$answer, $date, $good, $player, $question];
        }
    }
}
