<?php

namespace PlaygroundCore\Mapper;

use PlaygroundCore\Mapper\AbstractMapper;

class Website extends AbstractMapper
{
    /**
    * getEntityRepository : recupere l'entite website
    *
    * @return PlaygroundCore\Entity\Website $website
    */
    public function getEntityRepository()
    {
        if (null === $this->er) {
            $this->er = $this->em->getRepository('PlaygroundCore\Entity\Website');
        }

        return $this->er;
    }
}
