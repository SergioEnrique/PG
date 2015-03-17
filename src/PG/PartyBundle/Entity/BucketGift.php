<?php

namespace PG\PartyBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * BucketGift
 */
class BucketGift
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var integer
     */
    private $usuarioId;

    /**
     * @var \DateTime
     */
    private $fecha;

    /**
     * @var string
     */
    private $titulo;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $mesaregalos;

    /**
     * @var \NW\UserBundle\Entity\User
     */
    private $user;

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
     * Set usuarioId
     *
     * @param integer $usuarioId
     * @return BucketGift
     */
    public function setUsuarioId($usuarioId)
    {
        $this->usuarioId = $usuarioId;

        return $this;
    }

    /**
     * Get usuarioId
     *
     * @return integer 
     */
    public function getUsuarioId()
    {
        return $this->usuarioId;
    }

    /**
     * Set fecha
     *
     * @param \DateTime $fecha
     * @return BucketGift
     */
    public function setFecha($fecha)
    {
        $this->fecha = $fecha;

        return $this;
    }

    /**
     * Get fecha
     *
     * @return \DateTime 
     */
    public function getFecha()
    {
        return $this->fecha;
    }

    /**
     * Set titulo
     *
     * @param string $titulo
     * @return BucketGift
     */
    public function setTitulo($titulo)
    {
        $this->titulo = $titulo;

        return $this;
    }

    /**
     * Get titulo
     *
     * @return string 
     */
    public function getTitulo()
    {
        return $this->titulo;
    }

    /**
     * Add mesaregalos
     *
     * @param \NW\PrincipalBundle\Entity\MesaRegalos $mesaregalos
     * @return BucketGift
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

    /**
     * Set user
     *
     * @param \NW\UserBundle\Entity\User $user
     * @return BucketGift
     */
    public function setUser(\NW\UserBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \NW\UserBundle\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
    }
}
