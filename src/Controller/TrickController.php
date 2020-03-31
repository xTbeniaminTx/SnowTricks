<?php

namespace App\Controller;

use App\Repository\TrickRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
     * View one trick
     *
     * @Route("/tricks/{id}", name="tricks_show")
     *
     * @param $id
     * @param TrickRepository $trickRepository
     * @return Response
     */
    public function show($id, TrickRepository $trickRepository)
    {
        $trick = $trickRepository->findOneById($id);

        return $this->render('trick/show.html.twig', [
            'trick' => $trick
        ]);
    }
}
