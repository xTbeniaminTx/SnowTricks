<?php


namespace App\Controller;


use App\Entity\Trick;
use App\Repository\TrickRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class HomeController extends BaseController
{
    /**
     * @Route("/", name="homepage")
     *
     * @param TrickRepository $trickRepository
     * @return Response
     */
    public function home(TrickRepository $trickRepository)
    {

        dd(getenv("APP_SECRETS"));
        $tricks = $trickRepository->findAll();
        return $this->render(
            'home.html.twig', [
                'tricks' => $tricks
            ]
        );

    }

}