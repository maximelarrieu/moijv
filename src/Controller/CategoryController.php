<?php

namespace App\Controller;

use App\Entity\Category;
use App\Repository\GameRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CategoryController extends AbstractController
{
    /**
     * @Route("/category/{slug}/{page}", name="games_by_category")
     */
    public function index(Category $category, GameRepository $gameRepository, PaginatorInterface $paginator, $page = 1): Response
    {
        return $this->render('category/games_by_category.html.twig', [
            'category' => $category,
            'games' => $gameRepository->getLatestPaginatedGamesByCategory($category, $paginator, $page)
        ]);
    }
}
