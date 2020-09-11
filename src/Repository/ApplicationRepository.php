<?php

namespace App\Repository;

use App\Entity\Application;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Application|null find($id, $lockMode = null, $lockVersion = null)
 * @method Application|null findOneBy(array $criteria, array $orderBy = null)
 * @method Application[]    findAll()
 * @method Application[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ApplicationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Application::class);
    }

    /**
     * @param $date
     * @param $icon
     * @return Application[]
     */
    public function findAllGreaterThanDate($date, $icon): array{
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            'SELECT d
            FROM App\Entity\Application d
            WHERE d.date > :date
            AND d.icon = :icon
            ORDER BY d.date ASC'
        )
            ->setParameter('date', $date)
            ->setParameter('icon', $icon);

        // returns an array of Product objects
        return $query->getResult();
    }

    /**
     * @param $icon
     * @param $store
     * @param $top
     * @return Application[]
     */
    public function findDuplicate($icon, $store, $top): array{
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            'SELECT i
            FROM App\Entity\Application i
            WHERE i.store = :store AND i.top = :top AND i.icon = :icon
            ORDER BY i.date DESC'
        )
            ->setParameter('icon', $icon)
            ->setParameter('store', $store)
            ->setParameter('top', $top);

        // returns an array of Product objects
        return $query->getResult();
    }

    /**
     * @param $date
     * @param $key
     * @return array
     */
    public function getAllInfoForThisDay($date, $key): array{
        $entityManager = $this->getEntityManager();
        /*WHERE (i.title LIKE :key OR i.description LIKE :key OR i.key_words LIKE :key)*/
        $query = $entityManager->createQuery(
            'SELECT i
            FROM App\Entity\Application i
            WHERE i.key_words LIKE :key
            AND i.date > :date
            ORDER BY i.top ASC'
        )
            ->setParameter('key', '%'.$key.'%')
            ->setParameter('date', $date);

        // returns an array of Product objects
        return $query->getResult();
    }

    // /**
    //  * @return Application[] Returns an array of Application objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Application
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
