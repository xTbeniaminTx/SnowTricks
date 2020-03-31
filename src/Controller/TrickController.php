<?php

namespace App\Controller;

use App\Entity\Trick;
use App\Form\TrickType;
use App\Repository\TrickRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TrickController extends AbstractController
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
     * @param ObjectManager $manager
     * @return Response
     */
    public function create(Request $request)
    {
        $trick = new Trick();

        $form = $this->createForm(TrickType::class, $trick);

        $form->handleRequest($request);

        if ($form->isSubmitted() and $form->isValid()) {
            $manager = $this->getDoctrine()->getManager();
//            $trick->setAuthor($this->getUser());
            $manager->persist($trick);
            $manager->flush();

            return $this->redirectToRoute('tricks_show', [
                'id' => $trick->getId()
            ]);
        }

        return $this->render('trick/new.html.twig', [
            'form' => $form->createView()
        ]);
    }


    /**
     * View one trick
     *
     * @Route("/tricks/{id}", name="tricks_show")
     *
     * @param Trick $trick
     * @return Response
     */
    public function show(Trick $trick)
    {
        return $this->render('trick/show.html.twig', [
            'trick' => $trick
        ]);
    }


}
