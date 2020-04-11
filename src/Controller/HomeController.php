<?php


namespace App\Controller;



use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;



class HomeController extends BaseController
{
    /**
     * @Route("/", name="homepage")
     *
     * @return Response
     */
    public function home()
    {

        return $this->render(
            'home.html.twig'
        );

    }

}