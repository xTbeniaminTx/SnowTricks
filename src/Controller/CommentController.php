<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Trick;
use App\Entity\User;
use App\Repository\CommentRepository;
use App\Repository\TrickRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CommentController extends BaseController
{
    /**
     * @Route("/comment", name="comment")
     */
    public function loadMoreComments()
    {
        return $this->render('comment/index.html.twig', [
            'controller_name' => 'CommentController',
        ]);
    }

    /**
     * @Route("/trick/{id}/comments/load/{offset}", name="comments_load", defaults={"offset": 0})
     *
     * @param Trick $trick
     * @param CommentRepository $commentRepository
     * @param int $offset
     * @return Response
     */
    public function loadMore(Trick $trick, CommentRepository $commentRepository, int $offset)
    {
        $comments = $commentRepository->findBy(['trick' => $trick], ['id' => 'DESC'], 3, $offset);

        return $this->render('trick/_comments.html.twig', [
            'comments' => $comments,
            'trick' => $trick
        ]);

    }

}
