<?php

namespace App\Repository;

use App\Entity\RondaJugador;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<RondaJugador>
 *
 * @method RondaJugador|null find($id, $lockMode = null, $lockVersion = null)
 * @method RondaJugador|null findOneBy(array $criteria, array $orderBy = null)
 * @method RondaJugador[]    findAll()
 * @method RondaJugador[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RondaJugadorRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RondaJugador::class);
    }

    public function add(RondaJugador $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(RondaJugador $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    //metodo para obtener la lista de ganadores de la ronda
    public function listaGanadores(int $ronda, string $ganador)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $query = $qb->select('x')
                    ->from('App:RondaJugador', 'x')
                    ->leftJoin('x.ronda', 'y')
                    ->where('y.id = :ronda')
                    ->setParameter('ronda', $ronda)
                    ->andWhere('x.apuesta = :apuesta')
                    ->setParameter('apuesta', $ganador)
                    ->getQuery()
                    ->getResult();
        return $query;
    }

    //    /**
    //     * @return RondaJugador[] Returns an array of RondaJugador objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('r.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?RondaJugador
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
