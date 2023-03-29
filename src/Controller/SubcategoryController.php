<?php

namespace App\Controller;

use App\Repository\SubCategoryRepository;
use App\Services\PaginatorService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SubcategoryController extends AbstractController
{
    #[Route('/subcategory', name: 'app_subcategory')]
    public function index(
        PaginatorService $paginatorService,
        SubCategoryRepository $subCategoryRepository,
        Request $request
    ): Response {
        return $paginatorService->paginate($subCategoryRepository, $request);
    }
}
