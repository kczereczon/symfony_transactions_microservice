<?php

namespace App\Controller;

use App\Repository\TransactionRepository;
use App\Services\PaginatorService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class TransactionController extends AbstractController
{
    #[Route('/transaction', name: 'app_transaction')]
    public function index(
        PaginatorService $paginatorService,
        TransactionRepository $transactionRepository,
        Request $request
    ): JsonResponse {
        return $paginatorService->paginate($transactionRepository, $request);
    }
}
