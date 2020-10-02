<?php

namespace App\Service;

use Elasticsearch\ClientBuilder;

class ElasticSearchService
{
    private $client;

    public function __construct()
    {
        $this->client = ClientBuilder::create()
            ->setHosts(['elasticsearch.dev.wrenkitchens.com'])
            ->build();
    }

    public function connection()
    {
        return $this->client;
    }
}
