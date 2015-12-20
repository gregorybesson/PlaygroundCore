<?php

namespace PlaygroundCore\Mapper;

use PlaygroundCore\Mapper\AbstractMapper;

class Formgen extends AbstractMapper
{
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
