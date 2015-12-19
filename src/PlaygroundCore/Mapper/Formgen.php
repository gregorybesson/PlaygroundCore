<?php

namespace PlaygroundCore\Mapper;

use Doctrine\ORM\EntityManager;
use PlaygroundCore\Options\ModuleOptions;

class Formgen
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $em;

    /**
     * @var \Doctrine\ORM\EntityRepository
     */
    protected $er;

    /**
     * @var \PlaygroundCore\Options\ModuleOptions
     */
    protected $options;


    /**
    * __construct
    * @param Doctrine\ORM\EntityManager $em
    * @param PlaygroundCore\Options\ModuleOptions $options
    *
    */
    public function __construct(EntityManager $em, ModuleOptions $options)
    {
        $this->em      = $em;
        $this->options = $options;
    }

    /**
    * findById : recupere l'entite en fonction de son id
    * @param int $id id de la formgen
    *
    * @return PlaygroundCore\Entity\Formgen $formgen
    */
    public function findById($id)
    {
        return $this->getEntityRepository()->find($id);
    }

    /**
    * findBy : recupere des entites en fonction de filtre
    * @param array $array tableau de filtre
    *
    * @return collection $formgens collection de PlaygroundCore\Entity\Formgen
    */
    public function findBy($filter, $order = null, $limit = null, $offset = null)
    {
        return $this->getEntityRepository()->findBy($filter, $order, $limit, $offset);
    }

    /**
    * insert : insert en base une entité formgen
    * @param PlaygroundCore\Entity\Formgen $formgen formgen
    *
    * @return PlaygroundCore\Entity\Formgen $formgen
    */
    public function insert($entity)
    {
        return $this->persist($entity);
    }

    /**
    * insert : met a jour en base une entité formgen
    * @param PlaygroundCore\Entity\Formgen $formgen formgen
    *
    * @return PlaygroundCore\Entity\Formgen $formgen
    */
    public function update($entity)
    {
        return $this->persist($entity);
    }

    /**
    * insert : met a jour en base une entité formgen et persiste en base
    * @param PlaygroundCore\Entity\Formgen $entity formgen
    *
    * @return PlaygroundCore\Entity\Formgen $formgen
    */
    protected function persist($entity)
    {
        $this->em->persist($entity);
        $this->em->flush();

        return $entity;
    }

    /**
    * findAll : recupere toutes les entites
    *
    * @return collection $formgen collection de PlaygroundCore\Entity\Formgen
    */
    public function findAll()
    {
        return $this->getEntityRepository()->findAll();
    }

     /**
    * remove : supprimer une entite formgen
    * @param PlaygroundCore\Entity\Formgen $formgen Formgen
    *
    */
    public function remove($entity)
    {
        $this->em->remove($entity);
        $this->em->flush();
    }

    /**
    * getEntityRepository : recupere l'entite formgen
    *
    * @return PlaygroundCore\Entity\Formgen $formgen
    */
    public function getEntityRepository()
    {
        if (null === $this->er) {
            $this->er = $this->em->getRepository('PlaygroundCore\Entity\Formgen');
        }

        return $this->er;
    }
}
