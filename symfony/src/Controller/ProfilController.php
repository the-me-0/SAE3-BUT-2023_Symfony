<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;
use App\Form\UserType;
use App\Form\PasswordUpdateType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

// Controller for the modification of the information of the connected user
class ProfilController extends AbstractController
{
    #[Route('/profil', name: 'app_profil')]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
// ------------------- FORM CREATION -------------------
        $user = $this->getUser();
        $user_form = $this->createForm(UserType::class, $user);
        $user_form->handleRequest($request);
        // validation of the form
        if ($user_form->isSubmitted() && $user_form->isValid()) {
            $entityManager->flush();
            $this->addFlash(
                'notice',
                'Votre profil à bien été modifié !'
            );
            return $this->redirectToRoute('app_profil', [
            ]);
        }
        return $this->render('profil/index.html.twig', [
            'controller_name' => 'ProfileController',
            'user_form' => $user_form->createView(),
        ]);
    }

    #[Route('/profil/passwordedit', name: 'app_password_edit')]
    public function passwordEdit(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
// ------------------- FORM CREATION -------------------
        $pwd_form= $this->createForm(PasswordUpdateType::class,);
        $pwd_form->handleRequest($request);
        if ($pwd_form->isSubmitted() && $pwd_form->isValid()) {
            $user = new User(); $user = $this->getUser();
            // check if the password the same as the one in the database and if not throw an error to the user
            if(!($userPasswordHasher->isPasswordValid($user, $pwd_form->get('old_password')->getData()))) {
                $this->addFlash('notice', 'Votre mot de passe actuel est incorrect !');
                return $this->redirectToRoute('app_password_edit', [
                ]);
            }
            // if there are the same modify the password of the user
            else{
                $user->setPassword($userPasswordHasher->hashPassword($user, $pwd_form->get('new_password')->getData()));
                $entityManager->flush();
                $this->addFlash('notice', 'Votre mot de passe à bien été modifié !');
                return $this->redirectToRoute('app_profil', [
                ]);
            }
        }
        return $this->render('profil/passwordEdit.html.twig', [
            'controller_name' => 'ProfileController',
            'pwd_form' => $pwd_form->createView(),
        ]);
    }
}