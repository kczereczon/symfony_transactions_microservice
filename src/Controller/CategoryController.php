<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use App\Services\PaginatorService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CategoryController extends AbstractController
{
    #[Route('/category', name: 'app_category')]
    public function index(
        PaginatorService $paginatorService,
        CategoryRepository $categoryRepository,
        Request $request
    ): Response {
        return $paginatorService->paginate($categoryRepository, $request);
    }
}
