<?php

namespace App\ElasticSearch;

use App\Entity\User;
use App\Service\ElasticSearchService;

class ElasticSearchUsers
{
    /**
     * @var ElasticSearchService
     */
    private $elasticSearch;

    public function __construct(ElasticSearchService $elasticSearch)
    {
        $this->elasticSearch = $elasticSearch;
    }

    public function addToElastic(User $item)
    {
        $client = $this->elasticSearch;
        $client = $client->connection();
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
        $client->index($params);
    }

    public function getUsersFromElastic($search)
    {
        $client = $this->elasticSearch->connection();

        $params = [
            'index' => 'my_users',
            'type' => 'users',
            'body' => [
                'query' => [
                    'match' => [
                        'Username' => $search,
                    ],
                ],
            ],
        ];

        $response = $client->search($params);
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
        $client = $this->elasticSearch->connection();

        $params = [
            'index' => 'my_users',
            'type' => 'users',

        ];

        $client->delete($params);
    }
}
