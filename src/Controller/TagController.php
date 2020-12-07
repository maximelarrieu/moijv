<?php

namespace App\Controller;

use App\Entity\Tag;
use App\Repository\GameRepository;
use App\Repository\TagRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TagController extends AbstractController
{
    /**
     * @Route("/tags", name="select_tags")
     */
    public function selectTags(Request $request, TagRepository $tagRepository): Response
    {
        $q = $request->get('q');
        $tags = array_map(function($tag) {
            return [
                'id' => $tag->getId(),
                'text' => $tag->getName()
            ];
        }, $tagRepository->findLike($q));
        return $this->json($tags);
    }

    /**
     * @Route("/tag/{slug}/{page}", name="tag_games")
     */
    public function index(GameRepository $gameRepository, Tag $tag, PaginatorInterface $paginator, $page = 1)
    {
        $games = $gameRepository->getLatestPaginatedGamesByTag($tag, $paginator, $page);
        return $this->render('tag/index.html.twig', [
            'tag' => $tag,
            'games' => $games
        ]);
    }
}
