<?php

namespace App\Controller\Admin;

use App\Entity\Question;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class QuestionCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Question::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setSearchFields(['id', 'question', 'answers', 'text'])
            ->setPaginatorPageSize(50);
    }

    public function configureFields(string $pageName): iterable
    {
        $hall = AssociationField::new('hall');
        $question = TextField::new('question');
        $text = TextField::new('text');
        $answers = TextareaField::new('answers');
        $answersHtml = TextareaField::new('answersHtml', 'answers')->renderAsHtml();

        if (Crud::PAGE_INDEX === $pageName) {
            return [$hall, $question, $text, $answersHtml];
        } elseif (Crud::PAGE_DETAIL === $pageName) {
            return [$hall, $question, $text, $answers];
        } elseif (Crud::PAGE_NEW === $pageName) {
            return [$hall, $question, $text, $answers];
        } elseif (Crud::PAGE_EDIT === $pageName) {
            return [$hall, $question, $text, $answers];
        }
    }
}
