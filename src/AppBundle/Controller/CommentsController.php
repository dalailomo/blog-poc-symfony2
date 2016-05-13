<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Article;
use AppBundle\Entity\Comment;
use AppBundle\Form\CommentType;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CommentsController extends FOSRestController
{
    /**
     * @param Comment $comment
     * @internal param $id
     *
     * @return Comment
     */
    public function getCommentAction(Comment $comment)
    {
        return $comment;
    }

    /**
     * @param Article $article
     * @internal param $id
     * @param Request $request
     *
     * @return View
     */
    public function postArticlesCommentAction(Article $article, Request $request)
    {
        $comment = new Comment();
        $comment->setArticle($article);

        $form = $this->createForm(
            new CommentType(),
            $comment,
            array(
                'method' => $request->getMethod()
            )
        );

        $form->handleRequest($request);

        $errors = $this->get('validator')->validate($comment);

        if (count($errors) > 0) {
            return new View(
                $errors,
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        $manager = $this->getDoctrine()->getManager();
        $manager->persist($comment);
        $manager->flush();

        return new View($comment, Response::HTTP_OK);
    }
}
