<?php

namespace App\Controller\Admin;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Controller\Admin\UserCrudController;
use App\Entity\User;

class AdminController extends AbstractDashboardController
{
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        return $this->redirect($adminUrlGenerator->setController(UserCrudController::class)->generateUrl());
    }

    public function __construct(
        private AdminUrlGenerator $adminUrlGenerator,
        private UserPasswordHasherInterface $passwordHasher
    )
    {
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new ()
            ->setTitle('User Management');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::section('Bloc');

        yield MenuItem::linkToRoute('Retour au site', 'fas fa-arrow-left', 'app_home');

        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');

        yield MenuItem::section('User');

        yield MenuItem::subMenu('Action', 'fas fa-bar')->setSubItems([
            MenuItem::linkToCrud('Ajouter un Utilisateur', 'fas fa-plus-circle', User::class)->setAction(Crud::PAGE_NEW),
            MenuItem::linkToCrud('Afficher les utilisateurs', 'fas fa-eye', User::class),
        ]);
    }
}
