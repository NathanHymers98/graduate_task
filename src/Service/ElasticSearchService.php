<?php

namespace App\Service;

use App\Entity\User;
use Elasticsearch\Client;
use Elasticsearch\ClientBuilder;

class ElasticSearchService
{
    /**
     * @var Client
     */
    private $client;

    /**
     * ElasticSearchService constructor.
     */
    public function __construct()
    {
        $this->client = ClientBuilder::create()
            ->setHosts(['elasticsearch.dev.wrenkitchens.com'])
            ->build();
    }

    /**
     * @return Client
     */
    public function connection()
    {
        return $this->client;
    }

    public function addToElastic(User $item)
    {
        $params = [
            'index' => 'my_users',
            'type' => 'users',
            'id' => $item->getId(),
            'body' => [
                'id' => $item->getId(),
                'Username' => $item->getUsername(),
                'Email' => $item->getEmail(),
            ],
        ];
        $this->client->index($params);
    }

    /**
     * @param $search
     *
     * @return null
     */
    public function getUsersFromElastic($search)
    {
        $params = [
            'index' => 'my_users',
            'type' => 'users',
            'body' => [
                'query' => [
                    'wildcard' => [
                        'Username' => "$search*",
                    ],
                ],
            ],
        ];

        $response = $this->client->search($params);
        $hits = count($response['hits']['hits']);
        $result = null;
        $i = 0;

        while ($i < $hits) {
            $result[$i] = $response['hits']['hits'][$i]['_source'];
            ++$i;
        }

        return $result;
    }

    public function deleteUsers()
    {
        $params = [
            'index' => 'my_users',
            'type' => 'users',
        ];

        $this->client->delete($params);
    }
}
