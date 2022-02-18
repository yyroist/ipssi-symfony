<?php

namespace App\Repository;

use App\Entity\Panier;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\Query\Parameter;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @method Panier|null find($id, $lockMode = null, $lockVersion = null)
 * @method Panier|null findOneBy(array $criteria, array $orderBy = null)
 * @method Panier[]    findAll()
 * @method Panier[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PanierRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Panier::class);
    }

    /**
     * Récupération du dernier panier non payé pour un utilisateur donné.
     *
     * @param User|UserInterface $user
     * @return Panier|null
     * @throws NonUniqueResultException
     */
    public function findLastNonPaid(User|UserInterface $user): ?Panier
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.etat = :etat')
            ->andWhere('p.utilisateur = :user')
            ->setParameters(new ArrayCollection([
                new Parameter('etat', false),
                new Parameter('user', $user)
            ]))
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * @return Panier[] Récupération de la liste des paniers non payés.
     */
    public function findNonPaid(): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.etat = :etat')
            ->setParameter('etat', false)
            ->orderBy('p.id', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * Récupération de la liste des paniers payés pour un utilisateur donné.
     *
     * @param User|UserInterface $user
     * @return Panier[]
     */
    public function findUserPaid(User|UserInterface $user): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.etat = :etat')
            ->andWhere('p.utilisateur = :user')
            ->setParameters(new ArrayCollection([
                new Parameter('etat', true),
                new Parameter('user', $user),
            ]))
            ->orderBy('p.id', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }
}
