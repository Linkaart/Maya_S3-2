<?php

namespace App\Repository;

use App\Entity\Produit;
use App\Entity\ProduitRecherche;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
 
/**
 * @extends ServiceEntityRepository<Produit>
 *
 * @method Produit|null find($id, $lockMode = null, $lockVersion = null)
 * @method Produit|null findOneBy(array $criteria, array $orderBy = null)
 * @method Produit[]    findAll()
 * @method Produit[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProduitRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Produit::class);
    }

         /**
     * @return Produit[]
     */
    public function findAllByCriteria(ProduitRecherche $produitRecherche): Array
    {
        // le "p" est un alias utilisé dans la requête
        $qb = $this->createQueryBuilder('p')
            ->orderBy('p.libelle', 'ASC');

        if ($produitRecherche->getLibelle()) {
            $qb->andWhere('p.libelle LIKE :libelle')
                ->setParameter('libelle', $produitRecherche->getLibelle().'%');
        }

        if ($produitRecherche->getPrixMini()) {
            $qb->andWhere('p.prix >= :prixMini')
                ->setParameter('prixMini', $produitRecherche->getPrixMini());
        }

        if ($produitRecherche->getPrixMaxi()) {
            $qb->andWhere('p.prix < :prixMaxi')
                ->setParameter('prixMaxi', $produitRecherche->getPrixMaxi());
        }

        $query = $qb->getQuery();
        return $query->execute();
    }

    /**
    * @return Produit[]
    */
   public function findAllOrderByLibelle(): array
   {
       $entityManager = $this->getEntityManager();
       $query = $entityManager->createQuery(
           'SELECT p
           FROM App\Entity\Produit p
           ORDER BY p.libelle ASC'
       );

       // retourne un tableau d'objets de type Produit
       return $query->getResult();
   }

    /**
     * @return Produit[]
     */
    public function findAllGreaterThanPrice($prix): array
    {
        $entityManager = $this->getEntityManager();

        // ce n'est pas du SQL mais du DQL : Doctrine Query Language
        // il s'agit en fait d'une requête classique mais qui référence l'objet au lieu de la table
        $query = $entityManager->createQuery(
            'SELECT p
            FROM App\Entity\Produit p
            WHERE p.prix > :prix
            ORDER BY p.prix ASC'
        )->setParameter('prix', $prix);

        // retourne un tableau d'objets de type Produit 
        return $query->getResult();
    }


    public function findAllGreaterThanPrice2($prix) : array
    {
        $conn = $this->getEntityManager()->getConnection();
    
        $sql = '
            SELECT * FROM produit p
            WHERE p.prix > :prix
            ORDER BY p.prix ASC
            ';
        $stmt = $conn->prepare($sql);
        $resultat = $stmt->executeQuery(['prix' => $prix]);
    
        return $resultat->fetchAllNumeric();
    }
    


//    /**
//     * @return Produit[] Returns an array of Produit objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Produit
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
