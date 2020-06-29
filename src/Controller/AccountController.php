<?php

namespace App\Controller;

use App\Entity\PasswordUpdate;
use App\Entity\User;
use App\Event\UserRegisterEvent;
use App\Form\AccountType;
use App\Form\ForgotPassType;
use App\Form\NewPassType;
use App\Form\PasswordUpdateType;
use App\Form\RegistrationType;
use App\Mailer\Mailer;
use App\Repository\UserRepository;
use App\Service\TokenGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class AccountController extends BaseController
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
     * @Route("/forgot-password", name="account_forgot-password")
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param Mailer $mailer
     * @param Session $httpSession
     * @return Response
     */
    public function forgotPassword(
        Request $request,
        EntityManagerInterface $entityManager,
        Mailer $mailer
    ): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('account_index');
        }

        $form = $this->createForm(ForgotPassType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $email = $form->get('email')->getData();

            if ($user = $entityManager->getRepository(User::class)->findOneBy(['email' => $email])) {
                $mailer->sendPasswordEmail($user->getConfirmationToken(), $user);

                $this->addFlash('success',
                    "Un email avec un lien pour réinitialiser votre mot de passe vient 
                    de vous être envoyé, vous pouvez quitter la page!");

                return $this->redirectToRoute('account_login');
            }
        }

        return $this->render('account/forgot-password.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("new-password/{token}", name="account_confirm_token")
     * @param Request $request
     * @param UserRepository $userRepository
     * @param UserPasswordEncoderInterface $encoder
     * @param TokenGenerator $tokenGenerator
     * @param string $token
     * @return Response
     */
    public function newPassword(
        Request $request,
        UserRepository $userRepository,
        UserPasswordEncoderInterface $encoder,
        TokenGenerator $tokenGenerator,
        string $token
    ): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('account_index');
        }

        if (!$user = $userRepository->findOneBy(['confirmationToken' => $token])) {

            $this->addFlash('danger', "Token invalide, veuillez ressayer!");

            return $this->redirectToRoute('account_forgot-password');
        }

        $form = $this->createForm(NewPassType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user->setConfirmationToken($tokenGenerator->getRandomSecureToken(30));

            $userRepository->upgradePassword(
                $user,
                $encoder->encodePassword($user, $form->get('newPassword')->getData())
            );

            $this->addFlash('success',
                "Mot de passe modifié avec succès !");

            return $this->redirectToRoute('account_login');
        }

        return $this->render('account/new-password.html.twig', ['form' => $form->createView()]);
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
     * @param EventDispatcherInterface $eventDispatcher
     * @param TokenGenerator $tokenGenerator
     * @return Response
     */
    public function register(Request $request, EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder,
                             EventDispatcherInterface $eventDispatcher, TokenGenerator $tokenGenerator)
    {
        $user = new User();

        $form = $this->createForm(RegistrationType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /** @var UploadedFile $uploadedFile */

            $uploadedFile = $form->get('uploadPicture')->getData();

            if ($uploadedFile) {
                $destination = $this->getParameter('kernel.project_dir') . '/public/uploads/users/pictures';
                $originalFileWhitoutExt = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
                $newFileName = $originalFileWhitoutExt . '-' . uniqid() . '.' . $uploadedFile->guessExtension();

                $uploadedFile->move($destination, $newFileName);

                $user->setPicture($newFileName);
            }

            $password = $encoder->encodePassword($user, $user->getPassword());
            $user->setPassword($password);
            $user->setConfirmationToken($tokenGenerator->getRandomSecureToken(30));

            $manager->persist($user);
            $manager->flush();

            $userRegisterEvent = new UserRegisterEvent($user);
            $eventDispatcher->dispatch($userRegisterEvent, UserRegisterEvent::NAME);

            $this->addFlash('success', "Votre compte a bien été créé! Vous pouvez maintenant vous connecter!");

            return $this->redirectToRoute('account_login');
        }

        return $this->render('account/registration.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * Allow to change your profile information
     *
     * @Route("/account/profile", name="account_profile")
     * @IsGranted("ROLE_USER")
     *
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    public function profile(Request $request, EntityManagerInterface $manager)
    {
        $user = $this->getUser();

        $form = $this->createForm(AccountType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($user);
            $manager->flush();

            $this->addFlash(
                'success',
                "Les données du profil ont été enregistrée avec succès!"
            );
        }

        return $this->render('account/profile.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * Allow to change the password
     *
     * @Route("/account/password-update", name="account_password")
     * @IsGranted("ROLE_USER")
     *
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @param UserPasswordEncoderInterface $encoder
     * @return Response
     */
    public function updatePassword(Request $request, EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder)
    {
        $user = $this->getUser();

        $passwordUpdate = new PasswordUpdate();

        $form = $this->createForm(PasswordUpdateType::class, $passwordUpdate);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // verify that the oldPassword is the same as the user password in the db
            if (!password_verify($passwordUpdate->getOldPassword(), $user->getPassword())) {
                // manage the error
                $form->get('oldPassword')->addError(new FormError(
                    "Le mot de passe que vous avez tapé n'est pas votre mot de passe actuel!"
                ));
            } else {
                $newPassword = $passwordUpdate->getNewPassword();
                $password = $encoder->encodePassword($user, $newPassword);

                $user->setPassword($password);

                $manager->persist($user);
                $manager->flush();

                $this->addFlash(
                    'success',
                    "Votre mot de passe a été modifié!"
                );

                return $this->redirectToRoute('homepage');
            }
        }

        return $this->render('account/password.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * Alow to see the user profile
     *
     * @Route("/account", name="account_index")
     * @IsGranted("ROLE_USER")
     *
     * @return Response
     */
    public function myAccount()
    {
        return $this->render('user/index.html.twig', [
            'user' => $this->getUser()
        ]);
    }

    /**
     *
     * @Route("/confirm/{token}", name="security_confirm")
     *
     * @param string $token
     * @param UserRepository $userRepository
     * @param UserPasswordEncoderInterface $encoder
     * @return Response
     */
    public function confirmToken(string $token, UserRepository $userRepository, UserPasswordEncoderInterface $encoder)
    {
        $user = $userRepository->findOneBy([
            'confirmationToken' => $token
        ]);

        if (null !== $user) {
            $defaultPassword = 123456;
            $password = $encoder->encodePassword($user, $defaultPassword);
            $user->setPassword($password);
        }

        return $this->render('account/confirmation.html.twig', [
            'user' => $user
        ]);
    }
}
