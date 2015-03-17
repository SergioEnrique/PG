<?php

namespace NW\PrincipalBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SolicitudRetiro
 */
class SolicitudRetiro
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
     * @var string
     */
    private $cuentaPaypal;

    /**
     * @var \DateTime
     */
    private $fecha;

    /**
     * @var float
     */
    private $amount;

    /**
     * @var boolean
     */
    private $realizado;

    /**
     * @var \NW\UserBundle\Entity\User
     */
    private $usuario;


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
     * @return SolicitudRetiro
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
     * Set cuentaPaypal
     *
     * @param string $cuentaPaypal
     * @return SolicitudRetiro
     */
    public function setCuentaPaypal($cuentaPaypal)
    {
        $this->cuentaPaypal = $cuentaPaypal;

        return $this;
    }

    /**
     * Get cuentaPaypal
     *
     * @return string 
     */
    public function getCuentaPaypal()
    {
        return $this->cuentaPaypal;
    }

    /**
     * Set fecha
     *
     * @param \DateTime $fecha
     * @return SolicitudRetiro
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
     * Set amount
     *
     * @param float $amount
     * @return SolicitudRetiro
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
     * Set realizado
     *
     * @param boolean $realizado
     * @return SolicitudRetiro
     */
    public function setRealizado($realizado)
    {
        $this->realizado = $realizado;

        return $this;
    }

    /**
     * Get realizado
     *
     * @return boolean 
     */
    public function getRealizado()
    {
        return $this->realizado;
    }

    /**
     * Set usuario
     *
     * @param \NW\UserBundle\Entity\User $usuario
     * @return SolicitudRetiro
     */
    public function setUsuario(\NW\UserBundle\Entity\User $usuario = null)
    {
        $this->usuario = $usuario;

        return $this;
    }

    /**
     * Get usuario
     *
     * @return \NW\UserBundle\Entity\User 
     */
    public function getUsuario()
    {
        return $this->usuario;
    }
}
