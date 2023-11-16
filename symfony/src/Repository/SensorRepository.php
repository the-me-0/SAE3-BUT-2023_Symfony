<?php

namespace App\Repository;

use App\Entity\Sensor;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Sensor>
 *
 * @method Sensor|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sensor|null findOneBy(array $criteria, array $orderBy = null)
 * @method Sensor[]    findAll()
 * @method Sensor[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SensorRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sensor::class);
    }

    public function save(Sensor $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Sensor $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

   public function findSensorsNotConnected(): array
   {
        // $dql = "SELECT s.id, s.num, IDENTITY(s.room) AS room, s.description FROM App\Entity\Sensor s WHERE s.enabled = 0";

        // $query = $this->getEntityManager()->createQuery($dql);
        // return $query->execute();
        return $this->createQueryBuilder('s')
           ->andWhere('s.enabled = 0')
           ->orderBy('s.id', 'ASC')
           ->setMaxResults(20)
           ->getQuery()
           ->getResult()
       ;
   }

   public function getByTag($value): ?Sensor
   {
       return $this->createQueryBuilder('s')
           ->andWhere('s.tag = :val')
           ->setParameter('val', $value)
           ->getQuery()
           ->getOneOrNullResult()
       ;
   }

   public function findByRoom($value): ?Sensor
   {
       return $this->createQueryBuilder('s')
           ->andWhere('s.room = :val')
           ->setParameter('val', $value)
           ->getQuery()
           ->getOneOrNullResult()
       ;
   }
}
