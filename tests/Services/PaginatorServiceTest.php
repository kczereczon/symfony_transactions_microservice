<?php

namespace App\Tests\Services;

use App\Services\PaginatorService;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping\Entity;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RequestContext;

class PaginatorServiceTest extends TestCase
{

    private function urlGeneratorFactory(): UrlGeneratorInterface {
        $requestContext = $this->createMock(RequestContext::class);

        return new class($requestContext) implements UrlGeneratorInterface {

            public function __construct(public RequestContext $requestContext)
            {
            }

            public function generate(string $name, array $parameters = [], int $referenceType = 1): string
            {
                return '/' . $name . '?'. http_build_query($parameters);
            }

            public function setContext(RequestContext $context): void
            {
            }

            public function getContext(): RequestContext
            {
                return $this->requestContext;
            }
        };
    }

    public function testPaginateEmptyItems()
    {
        $paginatorService = new PaginatorService(
            $this->urlGeneratorFactory()
        );

        $repositoryMock = $this->createMock(EntityRepository::class);
        $criteria = ['filter1' => 1, 'filter2' => 2];
        $repositoryMock->expects($this->once())->method('count')->with($criteria)->willReturn(0);
        $repositoryMock->expects($this->once())->method('findBy')->with($criteria)->willReturn([]);
        $request = new Request(['page' => 1, 'limit' => '25'], [], ['_route' => 'app_test']);

        $this->assertEquals(new JsonResponse([
            'items' => [],
            'total' => 0,
            'page' => 1,
            'pages' => 0,
            'next' => '/app_test?page=1&limit=25',
            'previous' => '/app_test?page=1&limit=25',
            'limit' => 25
        ]),  $paginatorService->paginate($repositoryMock, $request, $criteria));
    }

    public function testPaginateWhenItemsAreNotEmpty()
    {
        $paginatorService = new PaginatorService(
            $this->urlGeneratorFactory()
        );

        $items = [];
        foreach (range(0, 24) as $i) {
            $items[$i] = new \stdClass();
        }

        $repositoryMock = $this->createMock(EntityRepository::class);
        $criteria = ['filter1' => 1, 'filter2' => 2];
        $repositoryMock->expects($this->once())->method('count')->with($criteria)->willReturn(100);
        $repositoryMock->expects($this->once())->method('findBy')->with($criteria)->willReturn($items);
        $request = new Request(['page' => 1, 'limit' => '25'], [], ['_route' => 'app_test']);

        $this->assertEquals(new JsonResponse([
            'items' => $items,
            'total' => 100,
            'page' => 1,
            'pages' => 4,
            'next' => '/app_test?page=2&limit=25',
            'previous' => '/app_test?page=1&limit=25',
            'limit' => 25
        ]),  $paginatorService->paginate($repositoryMock, $request, $criteria));
    }

    public function testPaginateChangingPagesHasAffectOnLinks()
    {
        $paginatorService = new PaginatorService(
            $this->urlGeneratorFactory()
        );

        $items = [];
        foreach (range(0, 24) as $i) {
            $items[$i] = new \stdClass();
        }

        $repositoryMock = $this->createMock(EntityRepository::class);
        $criteria = ['filter1' => 1, 'filter2' => 2];
        $repositoryMock->expects($this->once())->method('count')->with($criteria)->willReturn(100);
        $repositoryMock->expects($this->once())->method('findBy')->with($criteria)->willReturn($items);
        $request = new Request(['page' => 3, 'limit' => '25'], [], ['_route' => 'app_test']);

        $this->assertEquals(new JsonResponse([
            'items' => $items,
            'total' => 100,
            'page' => 3,
            'pages' => 4,
            'next' => '/app_test?page=4&limit=25',
            'previous' => '/app_test?page=2&limit=25',
            'limit' => 25
        ]),  $paginatorService->paginate($repositoryMock, $request, $criteria));
    }
}
