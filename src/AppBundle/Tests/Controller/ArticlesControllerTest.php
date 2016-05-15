<?php

namespace AppBundle\Tests\Controller;

use AppBundle\DataFixtures\ORM\LoadArticleData;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ArticlesControllerTest extends WebTestCase
{
    protected $client;

    public function setUp()
    {
        $loader = new Loader();
        $loader->addFixture(new LoadArticleData());

        $this->client = static::createClient();

        $executor = new ORMExecutor(
            $this->client->getContainer()->get('doctrine.orm.entity_manager'),
            new ORMPurger()
        );

        $executor->execute($loader->getFixtures());
    }

    private function getArticleList()
    {
        $this->client->request('GET', '/api/articles');
        return $this->client->getResponse();
    }

    private function getPostNewArticle()
    {
        $this->client->request(
            'POST',
            '/api/articles',
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json'
            ],
            json_encode([
                'title' => 'New article!',
                'author' => 'Roger Waters',
                'body' => 'A brand new body'
            ])
        );

        return $this->client->getResponse();
    }

    private function getPutArticle($id)
    {
        $this->client->request(
            'PUT',
            '/api/articles/'.$id,
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json'
            ],
            json_encode([
                'title' => 'Modified article title!',
                'author' => 'Roger Waters',
                'body' => 'A brand new body'
            ])
        );

        return $this->client->getResponse();
    }

    private function getDeleteArticle($id)
    {
        $this->client->request(
            'DELETE',
            '/api/articles/'.$id
        );

        return $this->client->getResponse();
    }

    public function testGetArticleList()
    {
        $response = $this->getArticleList();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('application/json', $response->headers->get('Content-Type'));
        $this->assertContains(
            '"title":"Article title","body":"Hello world!","author":"Mike Patton"',
            $response->getContent()
        );

        $this->assertContains(
            '"body":"Hey! I am Ron!","author":"Ronnie James Dio"',
            $response->getContent()
        );
    }

    public function testPostArticle()
    {
        $response = $this->getPostNewArticle();

        $this->assertEquals(201, $response->getStatusCode());
        $this->assertContains(
            '"title":"New article!","body":"A brand new body","author":"Roger Waters"',
            $response->getContent()
        );
    }

    public function testPutArticle()
    {
        $id = json_decode($this->getPostNewArticle()->getContent(), true)['id'];
        $response = $this->getPutArticle($id);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertContains(
            '"title":"Modified article title!","body":"A brand new body","author":"Roger Waters"',
            $response->getContent()
        );
    }

    public function testDeleteArticle()
    {
        $id = json_decode($this->getPostNewArticle()->getContent(), true)['id'];
        $response = $this->getDeleteArticle($id);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertContains(
            '"title":"New article!","body":"A brand new body","author":"Roger Waters"',
            $response->getContent()
        );
    }

    public function testRateArticle()
    {
        $id = json_decode($this->getPostNewArticle()->getContent(), true)['id'];

        $this->client->request(
            'PUT',
            '/api/articles/'.$id.'/rate',
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json'
            ],
            json_encode([
                'score' => '4'
            ])
        );
        $response = $this->client->getResponse();

        $this->assertContains(
            '"score":4',
            $response->getContent()
        );
    }
}
