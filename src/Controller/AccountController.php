<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class AccountController extends AbstractController
{
    /**
     * View and manage the login form
     *
     * @Route("/login", name="account_login")
     *
     * @param AuthenticationUtils $utils
     * @return Response
     */
    public function login(AuthenticationUtils $utils)
    {
        $error = $utils->getLastAuthenticationError();
        $lastusername = $utils->getLastUsername();

        return $this->render('account/login.html.twig', [
            'hasError' => $error !== null,
            'lastusername' => $lastusername
        ]);
    }

    /**
     * Allows to disconnect
     *
     * @Route("/logout", name="account_logout")
     *
     * @return void
     */
    public function logout()
    {
        //nada Symfony take care!
    }

    /**
     * Show the register form
     *
     * @Route("/register", name="account_register")
     *
     * @return Response
     */
    public function register()
    {
        $user = new User();

        $form = $this->createForm(RegistrationType::class, $user);

        return $this->render('account/registration.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
