<?php

namespace App\Controller;

use App\Repository\TransactionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class TransactionController extends AbstractController
{
    #[Route('/transaction', name: 'app_transaction')]
    public function index(TransactionRepository $transactionRepository, Request $request): JsonResponse
    {
        $page = $request->query->get('page', 1);
        $limit = $request->query->get('limit', 25);
        $transactions = $transactionRepository->findBy(criteria: [], limit: $limit, offset: $limit * ($page - 1));
        $total = $transactionRepository->count(criteria: []);
        $pages = (int)($total / $limit);

        return $this->json([
            'transactions' => $transactions,
            'total' => $total,
            'page' => $page,
            'pages' => $pages,
            'next' => $this->generateUrl(
                'app_transaction',
                ['page' => ($page + 1 > $pages) ? $page : $page + 1, 'limit' => $limit]
            ),
            'previous' => $this->generateUrl(
                'app_transaction',
                ['page' => ($page - 1 <= 0) ? 1 : $page - 1, 'limit' => $limit]
            )
        ]);
    }
}
