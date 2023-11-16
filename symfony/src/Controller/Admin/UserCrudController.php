<?php

namespace App\Controller\Admin;

use App\Entity\User;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;


class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    // To pass on parameter the passwordhasherInterface
    public function __construct(private UserPasswordHasherInterface $passwordHasher)
    {
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('lastName', 'Nom'),
            TextField::new('username', 'Nom Utilisateur'),
            TextField::new('password', 'Mot de passe')->onlyOnForms(),
            ChoiceField::new('roles', 'Roles')->setChoices([
                'Admin' => 'ROLE_ADMIN',
                'Superviseur' => 'ROLE_SV',
                'Technicien' => 'ROLE_TECH',
                'Utilisateur' => 'ROLE_USER',
                'Edition (permission secondaire)' => 'ROLE_EDIT',
                'Voir tout (permission secondaire)' => 'ROLE_SEEALL'
            ])->allowMultipleChoices(),
            TextEditorField::new('description', 'Description'),
        ];
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $entityInstance->setPassword($this->passwordHasher->hashPassword($entityInstance, $entityInstance->getPassword()));
        parent::persistEntity($entityManager, $entityInstance);
    }
}
