<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
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
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @param UserPasswordEncoderInterface $encoder
     * @return Response
     */
    public function register(Request $request, EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder)
    {
        $user = new User();

        $form = $this->createForm(RegistrationType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $password = $encoder->encodePassword($user, $user->getPassword());
            $user->setPassword($password);

            $manager->persist($user);
            $manager->flush();

            $this->addFlash(
                'success',
                "Votre compte a bien été créé! Vous pouvez maintenant vous connecter!"
            );

            return $this->redirectToRoute('account_login');
        }

        return $this->render('account/registration.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
