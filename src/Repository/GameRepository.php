<?php

namespace App\Repository;

use App\Entity\Category;
use App\Entity\Game;
use App\Entity\Tag;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;

/**
 * @method Game|null find($id, $lockMode = null, $lockVersion = null)
 * @method Game|null findOneBy(array $criteria, array $orderBy = null)
 * @method Game[]    findAll()
 * @method Game[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GameRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Game::class);
    }

    public function getLatestPaginatedGames(PaginatorInterface $paginator, $page = 1)
    {
        // SELECT g.* FROM App\Entity\Game as g ORDER BY g.date_add DESC
        $query = $this->createQueryBuilder('g') // SELECT game as g
            ->orderBy('g.date_add', 'DESC') // ORDER BY g.date_add DESC
            ->join('g.tags', 't')
            ->addSelect('t')
            ->join('g.user', 'u')
            ->addSelect('u')
            ->getQuery();

        return $paginator->paginate($query, $page, 9);
    }

    public function getLatestPaginatedGamesByCategory(Category $category, PaginatorInterface $paginator, $page = 1)
    {
        // SELECT g.* FROM App\Entity\Game as g WHERE g.category = :category ORDER BY g.date_add DESC
        $query = $this->createQueryBuilder('g') // SELECT game as g
            ->where('g.category = :category')
            ->join('g.tags', 't')
            ->addSelect('t')
            ->join('g.user', 'u')
            ->addSelect('u')
            ->setParameter('category', $category)
            ->orderBy('g.date_add', 'DESC') // ORDER BY g.date_add DESC
            ->getQuery();

        return $paginator->paginate($query, $page, 9);
    }

    public function getLatestPaginatedGamesByTag(Tag $tag, PaginatorInterface $paginator, $page = 1)
    {
        // SELECT g.* FROM App\Entity\Game as g WHERE g.category = :category ORDER BY g.date_add DESC
        $query = $this->createQueryBuilder('g') // SELECT game as g
            ->where(':tag MEMBER OF g.tags')
            ->join('g.tags', 't')
            ->addSelect('t')
            ->setParameter('tag', $tag)
            ->join('g.user', 'u')
            ->addSelect('u')
            ->orderBy('g.date_add', 'DESC') // ORDER BY g.date_add DESC
            ->getQuery();

        return $paginator->paginate($query, $page, 9);
    }

    // /**
    //  * @return Game[] Returns an array of Game objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('g.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Game
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
