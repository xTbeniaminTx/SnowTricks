<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Trick;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
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

    /**
     * Create a comment
     *
     * @Route("/tricks/{id}/comments/new", name="comments_create")
     * @param Trick $trick
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    public function create(Trick $trick, Request $request, EntityManagerInterface $manager)
    {
        $comment = new Comment();
        $author = $request->get('author');
        $content = $request->get('content');

        if ($author and $content) {

            $comment->setTrick($trick);
            $comment->setDate(new \DateTime('now'));
            $comment->setStatus('valid');
            $comment->setAuthor($author);
            $comment->setContent($content);

            $manager->persist($comment);
            $manager->flush();

            $this->addFlash(
                'success',
                "Le commentaire a bien été enregistré!"
            );
            return $this->redirectToRoute('tricks_show', [
                'id' => $trick->getId()
            ]);

        }

        $this->addFlash(
            'warning',
            "Le author et le commentaire ne peut etre vide!"
        );

        return $this->redirectToRoute('tricks_show', [
            'id' => $trick->getId()
        ]);
    }

}
