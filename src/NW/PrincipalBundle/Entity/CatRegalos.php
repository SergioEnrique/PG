<?php

namespace NW\PrincipalBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CatRegalos
 */
class CatRegalos
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $categoriaName;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $mesaregalos;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->mesaregalos = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set categoriaName
     *
     * @param string $categoriaName
     * @return CatRegalos
     */
    public function setCategoriaName($categoriaName)
    {
        $this->categoriaName = $categoriaName;

        return $this;
    }

    /**
     * Get categoriaName
     *
     * @return string 
     */
    public function getCategoriaName()
    {
        return $this->categoriaName;
    }

    /**
     * Add mesaregalos
     *
     * @param \NW\PrincipalBundle\Entity\MesaRegalos $mesaregalos
     * @return CatRegalos
     */
    public function addMesaregalo(\NW\PrincipalBundle\Entity\MesaRegalos $mesaregalos)
    {
        $this->mesaregalos[] = $mesaregalos;

        return $this;
    }

    /**
     * Remove mesaregalos
     *
     * @param \NW\PrincipalBundle\Entity\MesaRegalos $mesaregalos
     */
    public function removeMesaregalo(\NW\PrincipalBundle\Entity\MesaRegalos $mesaregalos)
    {
        $this->mesaregalos->removeElement($mesaregalos);
    }

    /**
     * Get mesaregalos
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getMesaregalos()
    {
        return $this->mesaregalos;
    }
}
