<?php

namespace PlaygroundCore\Mapper;

use PlaygroundCore\Mapper\AbstractMapper;

class Locale extends AbstractMapper
{
    /**
    * getEntityRepository : recupere l'entite locale
    *
    * @return PlaygroundCore\Entity\Locale $locale
    */
    public function getEntityRepository()
    {
        if (null === $this->er) {
            $this->er = $this->em->getRepository('PlaygroundCore\Entity\Locale');
        }

        return $this->er;
    }
}
