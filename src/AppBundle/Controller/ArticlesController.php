<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Article;
use AppBundle\Entity\Comment;
use AppBundle\Form\ArticleScoreType;
use AppBundle\Form\ArticleType;
use AppBundle\Form\CommentType;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ArticlesController extends FOSRestController
{
    /**
     * @param Article $article
     * @internal param $id
     *
     * @return Article
     */
    public function getArticleAction(Article $article)
    {
        return $article;
    }

    private function treatAndValidateRequest(Article $article, Request $request)
    {
        $form = $this->createForm(
            new ArticleType(),
            $article,
            array(
                'method' => $request->getMethod()
            )
        );

        $form->handleRequest($request);

        $errors = $this->get('validator')->validate($article);
        return $errors;
    }

    private function persistAndFlush(Article $article)
    {
        $manager = $this->getDoctrine()->getManager();
        $manager->persist($article);
        $manager->flush();
    }

    /**
     * @return array
     */
    public function getArticlesAction()
    {
        $articles = $this
            ->getDoctrine()
            ->getRepository('AppBundle:Article')
            ->findAll();

        return $articles;
    }

    /**
     * @param Request $request
     *
     * @return View
     */
    public function postArticlesAction(Request $request)
    {
        $article = new Article();
        $errors = $this->treatAndValidateRequest($article, $request);

        if (count($errors) > 0) {
            return new View(
                $errors,
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        $this->persistAndFlush($article);

        return new View($article, Response::HTTP_CREATED);
    }

    /**
     * @param Article $article
     * @internal param $id
     * @param Request $request
     *
     * @return View
     */
    public function putArticleAction(Article $article, Request $request)
    {
        $errors = $this->treatAndValidateRequest($article, $request);

        if (count($errors) > 0) {
            return new View(
                $errors,
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        $this->persistAndFlush($article);

        return new View($article, Response::HTTP_OK);
    }

    /**
     * @param Article $article
     * @internal param $id
     *
     * @return View
     */
    public function deleteArticleAction(Article $article)
    {
        $manager = $this->getDoctrine()->getManager();
        $manager->remove($article);
        $manager->flush();

        return new View($article, Response::HTTP_OK);
    }

    /**
     * @param Article $article
     * @param Request $request
     *
     * @return View
     */
    public function putArticleRateAction(Article $article, Request $request)
    {
        $form = $this->createForm(
            new ArticleScoreType(),
            $article,
            array(
                'method' => $request->getMethod()
            )
        );

        $form->handleRequest($request);

        $errors = $this->get('validator')->validate($article);

        if (count($errors) > 0) {
            return new View(
                $errors,
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        $this->persistAndFlush($article);

        return new View($article, Response::HTTP_OK);
    }

}
