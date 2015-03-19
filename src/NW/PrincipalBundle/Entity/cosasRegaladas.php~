<?php

namespace NW\PrincipalBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * cosasRegaladas
 */
class cosasRegaladas
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
     * @var integer
     */
    private $regaloId;

    /**
     * @var string
     */
    private $regaladorName;

    /**
     * @var string
     */
    private $regaladorMail;

    /**
     * @var integer
     */
    private $cantidad;

    /**
     * @var float
     */
    private $amount;

    /**
     * @var \NW\UserBundle\Entity\User
     */
    private $user;

    /**
     * @var \NW\PrincipalBundle\Entity\MesaRegalos
     */
    private $regalo;


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
     * @return cosasRegaladas
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
     * Set regaloId
     *
     * @param integer $regaloId
     * @return cosasRegaladas
     */
    public function setRegaloId($regaloId)
    {
        $this->regaloId = $regaloId;

        return $this;
    }

    /**
     * Get regaloId
     *
     * @return integer 
     */
    public function getRegaloId()
    {
        return $this->regaloId;
    }

    /**
     * Set regaladorName
     *
     * @param string $regaladorName
     * @return cosasRegaladas
     */
    public function setRegaladorName($regaladorName)
    {
        $this->regaladorName = $regaladorName;

        return $this;
    }

    /**
     * Get regaladorName
     *
     * @return string 
     */
    public function getRegaladorName()
    {
        return $this->regaladorName;
    }

    /**
     * Set regaladorMail
     *
     * @param string $regaladorMail
     * @return cosasRegaladas
     */
    public function setRegaladorMail($regaladorMail)
    {
        $this->regaladorMail = $regaladorMail;

        return $this;
    }

    /**
     * Get regaladorMail
     *
     * @return string 
     */
    public function getRegaladorMail()
    {
        return $this->regaladorMail;
    }

    /**
     * Set cantidad
     *
     * @param integer $cantidad
     * @return cosasRegaladas
     */
    public function setCantidad($cantidad)
    {
        $this->cantidad = $cantidad;

        return $this;
    }

    /**
     * Get cantidad
     *
     * @return integer 
     */
    public function getCantidad()
    {
        return $this->cantidad;
    }

    /**
     * Set amount
     *
     * @param float $amount
     * @return cosasRegaladas
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * Get amount
     *
     * @return float 
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Set user
     *
     * @param \NW\UserBundle\Entity\User $user
     * @return cosasRegaladas
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

    /**
     * Set regalo
     *
     * @param \NW\PrincipalBundle\Entity\MesaRegalos $regalo
     * @return cosasRegaladas
     */
    public function setRegalo(\NW\PrincipalBundle\Entity\MesaRegalos $regalo = null)
    {
        $this->regalo = $regalo;

        return $this;
    }

    /**
     * Get regalo
     *
     * @return \NW\PrincipalBundle\Entity\MesaRegalos 
     */
    public function getRegalo()
    {
        return $this->regalo;
    }
}
