# Elasticsearch

![license](https://img.shields.io/badge/license-MIT-brightGreen.svg)
[![build](https://travis-ci.org/originphp/elasticsearch.svg?branch=master)](https://travis-ci.org/originphp/elasticsearch)
[![coverage](https://coveralls.io/repos/github/originphp/elasticsearch/badge.svg?branch=master)](https://coveralls.io/github/originphp/elasticsearch?branch=master)

If you need to improve performance of your application by moving search queries to Elasticsearch or implement more advanced searching, OriginPHP makes it ultra simple to implement Elasticsearch in your web application.

## Installation

To install this package

```linux
$ composer require originphp/elasticsearch
```

> To use the Elasticsearch features you will need to install Elasticsearch 7.0 or greater.

## Configuration

Here I will cover the configuration of Elasticsearch, the installation instructions for Elasticsearch with Docker are at the bottom of this guide.

### Connection

First you need to configure the Elasticsearch connection. In your configuration file `config/application.php` add the following (or create a separate file and adjust the bootstrap);

```php
use Origin\Elasticsearch\Elasticsearch;

Elasticsearch::config('default', [
    'host' => 'elasticsearch', // or 127.0.0.1 if not using the docker version
    'port' => 9200,
    'timeout' => 400,
    'https' => false
]);
```

### Model

Add the Elasticsearch `Concern` to your `Model`.

```php
use Elasticsearch\Model\Concern\ElasticSearch;

class MyModel extends ApplicationModel
{
    use ElasticSearch;
}
```

If you want to use a different connection, then you will need to set the `elasticSearchConnection` property on in your `Model`.

```php
protected $elasticSearchConnection = 'some-other-connection';
```

Whenever you create or delete a record the Elasticsearch index will be updated in the background, providing you do not disable callbacks.

> The `Model::updateAll` and `Model::deleteAll` do not trigger callbacks and therefore wont update or delete records from the index. You should loop through each record and update or delete so that the index can be kept in sync.

### Searching

To carry out a search use the model, which will have new methods from the `Concern`.

To search using keywords or a [query string](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-query-string-query.html) on columns in your index

```php
$results = $this->Post->search('Top Frameworks 2019');
$results = $this->Post->search('"Top Frameworks 2019"');
$results = $this->Post->search('title:how to');
$results = $this->Post->search('+framework +php -draft');
```

If you want to carry out a custom search using [Elasticsearch query DSL](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-bool-query.html) (Domain Specific Language), you can do like this

```php
$query = [
    'query' => [
        'multi_match' => [
            'query' => 'php framework',
            'fields' => ['title','body']
            ]
        ]
    ];
$results = $this->Post->search($query);
```

### Customizing Search

If you want to customize the search function or use custom searches from within your model you can do it like this:

```php
public function searchTitleAndBody(string $query)
{
    return $this->search([
        'query' => [
            'multi_match' => [
                'query' => $query,
                'fields' => ['title','body']
                ]
            ]
    ]);
}
```

### Mapping

By default, OriginPHP dynamically maps each column to Elasticsearch, all columns for the `Model` will be stored in the index unless you tell it otherwise.

To manually map columns for the indexes, in your model call the index method for each column that you want to index. You can optionally pass an options array which takes settings from Elasticsearch [mapping types](https://www.elastic.co/guide/en/elasticsearch/reference/current/mapping-types.html).

```php
use Elasticsearch\Model\Concern\ElasticSearch;

class Article extends ApplicationModel
{
    use Elasticsearch;
    
    protected function initialize(array $config) : void
    {
        // Set the columns to index
        $this->index('title',['type'=>'keyword','analyzer'=>'english']);
        $this->index('body'); // this will dynamically map

        // To set index settings
        $this->indexSettings(['number_of_shards' => 1]);
    }
}
```

You will then need to run the `elasticsearch:reindex` command telling which models recreate the indexes on with new settings, the data will then be added back to the new indexes.

```linux
$ bin/console elasticsearch:reindex Post Comment
```

> The only time indexes are created with your settings is when this command is run, if not Elasticsearch creates the index if it does not exist with default settings.

## Installing Elasticsearch Docker Image

Add the following to your `docker-compose.yml` file.

```yml
  elasticsearch:
    image: docker.elastic.co/elasticsearch/elasticsearch:7.3.0
    container_name: elasticsearch
    environment:
      - discovery.type=single-node
    volumes:
      - es-data:/usr/share/elasticsearch/data
    ports:
      - 9200:9200
```

And under the `volumes:` node add

```yml
    es-data:
      driver: local
```

The next time you run `docker-compose up` the Elasticsearch container will created.

For information on how to install on Ubuntu server see this [article](https://linuxize.com/post/how-to-install-elasticsearch-on-ubuntu-18-04/).

## Testing

When testing your application (or this plugin) you will need to start an Elasticsearch instance, you can do this with a nice oneliner from the command line

```
$ docker run -d -p 9200:9200 -p 9300:9300 -e "discovery.type=single-node" docker.elastic.co/elasticsearch/elasticsearch:7.3.0
```