<?php

namespace App\Services;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class PaginatorService
{
    public function __construct(public UrlGeneratorInterface $urlGenerator)
    {
    }

    public function paginate(EntityRepository $repository, Request $request, array $criteria = []): mixed {
        $page = $request->query->get('page', 1);
        $limit = $request->query->get('limit', 25);
        $items = $repository->findBy(criteria: $criteria, limit: $limit, offset: $limit * ($page - 1));
        $total = $repository->count(criteria: $criteria);
        $pages = floor($total / $limit);

        return new JsonResponse([
            'items' => $items,
            'total' => $total,
            'page' => $page,
            'pages' => $pages,
            'next' => $this->urlGenerator->generate(
                $request->attributes->get('_route'),
                ['page' => ($page + 1 > $pages) ? $page : $page + 1, 'limit' => $limit]
            ),
            'previous' => $this->urlGenerator->generate(
                $request->attributes->get('_route'),
                ['page' => ($page - 1 <= 0) ? 1 : $page - 1, 'limit' => $limit]
            ),
            'limit' => (int)$limit
        ]);
    }
}