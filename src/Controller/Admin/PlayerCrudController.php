<?php

namespace App\Controller\Admin;

use App\Entity\Player;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class PlayerCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Player::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setSearchFields(['id', 'nom', 'telephone', 'email', 'ville'])
            ->setPaginatorPageSize(50);
    }

    public function configureActions(Actions $actions): Actions
    {
        $actionTirage = Action::new("Tirage au sort")
            ->createAsGlobalAction()
            ->linkToRoute('rand_player')
        ;

        return $actions
            ->add(Crud::PAGE_INDEX, $actionTirage)
            ->disable('new', 'edit');
    }

    public function configureFields(string $pageName): iterable
    {
        $name = TextField::new('nomComplet', 'Nom');
        $firstConnection = DateTimeField::new('firstConnection', 'Première connexion');
        $lastConnection = DateTimeField::new('lastConnection', 'Dernière connexion');
        $lastRandom = DateTimeField::new('lastRandom');
        $email = TextField::new('email');
        $fbshare = BooleanField::new('fbshare', 'facebook')->renderAsSwitch(false);
        $token = TextField::new('token');
        $answers = AssociationField::new('answers', 'Réponses');
        $id = IntegerField::new('id', 'ID');
        $tel = TextField::new('telephone');
        $ville = TextField::new('ville');
        $random = BooleanField::new('random')->renderAsSwitch(false);

        if (Crud::PAGE_INDEX === $pageName) {
            return [$id, $name, $ville, $answers, $firstConnection, $lastConnection, $random, $fbshare];
        } elseif (Crud::PAGE_DETAIL === $pageName) {
            return [$id, $name, $email, $tel, $ville, $firstConnection, $lastConnection, $lastRandom, $token, $fbshare, $answers];
        } elseif (Crud::PAGE_NEW === $pageName) {
            return [$name, $firstConnection, $lastConnection, $lastRandom, $email, $fbshare, $token, $answers];
        } elseif (Crud::PAGE_EDIT === $pageName) {
            return [$name, $firstConnection, $lastConnection, $lastRandom, $email, $fbshare, $token, $answers];
        }
    }
}
