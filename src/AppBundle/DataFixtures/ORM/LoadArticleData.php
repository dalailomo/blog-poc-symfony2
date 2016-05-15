<?php

namespace AppBundle\DataFixtures\ORM;


use AppBundle\Entity\Comment;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\Article;

class LoadArticleData implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {

        $article = new Article();
        $article->setTitle('Article title');
        $article->setBody('Hello world!');
        $article->setAuthor('Mike Patton');

        $manager->persist($article);

        $comment = new Comment();
        $comment->setAuthor('Ronnie James Dio');
        $comment->setBody('Hey! I am Ron!');
        $comment->setArticle($article);

        $manager->persist($comment);
        $manager->flush();
    }
}