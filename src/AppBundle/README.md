# Blog PoC using Symfony 2

This is a basic proof of concept of a REST api allowing us to write articles, rate articles, and comment articles.

## Requirements

- PHP >= 7.0.3

## Installation

We need to run the following commands:

```
$ composer install
$ php app/console doctrine:database:create
$ php app/console doctrine:migrations:migrate
```

## Development

We need to run the following commands to create the test database:

```
$ composer install
$ php app/console doctrine:database:create --env="test"
$ php app/console doctrine:migrations:migrate --env="test"
```

Then, go inside `app/` directory and run:

```
$ phpunit
```

Fixtures for tests are located on `AppBundle\DataFixtures\ORM\LoadArticleData`.

## Configuration

### `app/config/parameters.yml.dist`

Used to set the database configuration. Note that there are `database_name`, used for production db and `test_database_name`, used for fixture data.

### `app/phpunit.xml.dist`

We can use this configuration in our `phpunit.xml` configuration file:

```
<?xml version="1.0" encoding="UTF-8"?>

<!-- https://phpunit.de/manual/current/en/appendixes.configuration.html -->
<phpunit xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
         xsi:noNamespaceSchemaLocation="http://schema.phpunit.de/4.8/phpunit.xsd"
         backupGlobals="false"
         colors="true"
         bootstrap="autoload.php"
>
    <php>
        <ini name="error_reporting" value="-1" />
        <!--
            <server name="KERNEL_DIR" value="/path/to/your/app/" />
        -->
    </php>

    <testsuites>
        <testsuite name="IWTests">
            <directory>../src/AppBundle/Tests</directory>
        </testsuite>
    </testsuites>

    <filter>
        <whitelist>
            <directory>../src</directory>
            <exclude>
                <directory>../src/*Bundle/Resources</directory>
                <directory>../src/*Bundle/Tests</directory>
            </exclude>
        </whitelist>
    </filter>
</phpunit>

```

## Routes

|Name|Method|Scheme|Host|Path|
|:---|:---|:---|:---|:---|
|`api_articles_get_article`             |`GET`      |`ANY`      |`ANY`    |`/api/articles/{article}.{_format}`|
|`api_articles_get_articles`            |`GET`      |`ANY`      |`ANY`    |`/api/articles.{_format}`|
|`api_articles_post_articles`           |`POST`     |`ANY`      |`ANY`    |`/api/articles.{_format}`|
|`api_articles_put_article`             |`PUT`      |`ANY`      |`ANY`    |`/api/articles/{article}.{_format}`|
|`api_articles_delete_article`          |`DELETE`   |`ANY`      |`ANY`    |`/api/articles/{article}.{_format}`|
|`api_articles_put_article_rate`        |`PUT`      |`ANY`      |`ANY`    |`/api/articles/{article}/rate.{_format}`|
|`api_comments_get_comment`             |`GET`      |`ANY`      |`ANY`    |`/api/comments/{comment}.{_format}`|
|`api_comments_post_articles_comment`   |`POST`     |`ANY`      |`ANY`    |`/api/articles/{article}/comments.{_format}`|

## Usage example

I used `httpie` to test my calls. We will use that for the following examples.

### Getting a list of articles

```
$ http GET http://localhost:8000/app_dev.php/api/articles
```

> Note here that the method used is `GET`.

Will return all the articles

### Getting a single article

```
$ http GET http://localhost:8000/app_dev.php/api/articles/2
```

> Note here that the method used is `GET` and we pass the `id` at the end.

Will return the selected article

### Create new article

```
$ echo '
  {
      "title" : "My new title",
      "body" : "Hello new article",
      "author" : "Ronnie James Dio"
  }
  ' | http POST http://localhost:8000/app_dev.php/api/articles
```

> Note here that the method used is `POST`.

Will return the new article with the generated id.

### Modify existing article

```
$ echo '
  {
      "title" : "My modified title",
      "body" : "Hello modified article",
      "author" : "Ronnie James Dio"
  }
  ' | http PUT http://localhost:8000/app_dev.php/api/articles/2
```

> Note here that the method used is `PUT` and we pass the `id` at the end.

Will return the modified article with the new data.

### Deleting an article

```
$ http DELETE http://localhost:8000/app_dev.php/api/articles/2
```

> Note here that the method used is `DELETE` and we pass the `id` at the end.

Will return the deleted article.

### Rate an article

```
$ echo '
  {
      "score" : 4
  }
  ' | http PUT http://localhost:8000/app_dev.php/api/articles/2/rate
```

> Note here that the method used is `PUT` and we pass the `id` of the article.

Will return the rated article with the score.

### Get a list of comments belonging to an article

```
$ http GET http://localhost:8000/app_dev.php/api/articles/2/comments
```

> Note here that the method used is `GET` and we pass the `id` of the article.

Will return the comment and the article where the comment belongs.

### Post a new comment on an article

```
$ echo '
  {
      "body" : "I want to comment this",
      "author" : "John Rambo"
  }
  ' | http POST http://localhost:8000/app_dev.php/api/articles/3/comments
```

> Note here that the method used is `POST` and we pass the `id` of the article.

Will return the recently added comment with the generated id and the article where it belongs.
