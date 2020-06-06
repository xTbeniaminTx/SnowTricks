<?php

namespace App\Controller;

use App\Entity\Image;
use App\Entity\Trick;
use App\Form\TrickType;
use App\Repository\TrickRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TrickController extends BaseController
{
    /**
     * @Route("/tricks", name="tricks_index")
     * @param TrickRepository $trickRepository
     * @return Response
     */
    public function index(TrickRepository $trickRepository)
    {
        $tricks = $trickRepository->findAll();

        return $this->render('trick/index.html.twig', [
            'tricks' => $tricks

        ]);
    }

    /**
     * Create a trick
     *
     * @Route("/tricks/new", name="triks_create")
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    public function create(Request $request, EntityManagerInterface $manager) //https://stackoverflow.com/questions/58954082/symfony-4-no-such-service-exists-for-objectmanager-after-composer-update
    {
        $trick = new Trick();

        $form = $this->createForm(TrickType::class, $trick);

        $form->handleRequest($request);


        if ($form->isSubmitted() and $form->isValid()) {

            $trick->setAuthor($this->getUser());

            $manager->persist($trick);
            $manager->flush();

            $this->addFlash(
                'success',
                "Le trick <strong>{$trick->getTitle()}</strong> a bien été enregistré!"
            );

            return $this->redirectToRoute('tricks_show', [
                'id' => $trick->getId()
            ]);
        }

        return $this->render('trick/new.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * Allow to edit a trick
     *
     * @Route("/tricks/{id}/edit", name="tricks_edit")
     * @param Trick $trick
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    public function edit(Trick $trick, Request $request, EntityManagerInterface $manager)
    {
        $form = $this->createForm(TrickType::class, $trick);

        $form->handleRequest($request);

        if ($form->isSubmitted() and $form->isValid()) {

            $trick->setAuthor($this->getUser());


            $manager->persist($trick);
            $manager->flush();

            $this->addFlash(
                'success',
                "Les modifications du trick <strong>{$trick->getTitle()}</strong> ont bien été enregistré!"
            );

            return $this->redirectToRoute('tricks_show', [
                'id' => $trick->getId()
            ]);
        }

        return $this->render('trick/edit.html.twig', [
            'form' => $form->createView(),
            'trick' => $trick
        ]);
    }


    /**
     * View one trick
     *
     * @Route("/tricks/{id}", name="tricks_show")
     *
     * @param Trick $trick
     * @param UserRepository $userRepository
     * @return Response
     */
    public function show(Trick $trick, UserRepository $userRepository)
    {
        return $this->render('trick/show.html.twig', [
            'trick' => $trick,
        ]);
    }

    /**
     * permet la suppresion d une annonce
     *
     * @Route("/tricks/{id}/delete", name="tricks_delete")
     * @Security("is_granted('ROLE_USER') and user == trick.getAuthor()", message="Vous n'etes pas le proprietaire de cette trick!")
     *
     * @param Trick $trick
     * @param EntityManagerInterface $manager
     */
    public function delete(Trick $trick, EntityManagerInterface $manager)
    {
        $manager->remove($trick);
        $manager->flush();

        $this->addFlash(
            'success',
            "Le trick <strong>{$trick->getTitle()}</strong> a bien été supprimé!"
        );

        return $this->redirectToRoute("tricks_index");
    }


}
