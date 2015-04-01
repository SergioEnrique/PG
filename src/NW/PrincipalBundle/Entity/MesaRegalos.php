<?php

namespace NW\PrincipalBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MesaRegalos
 */
class MesaRegalos
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var integer
     */
    private $eventoId;

    /**
     * @var string
     */
    private $regalo;

    /**
     * @var float
     */
    private $precioTotal;

    /**
     * @var integer
     */
    private $cantidad;

    /**
     * @var integer
     */
    private $horcruxes;

    /**
     * @var integer
     */
    private $horcruxesPagados;

    /**
     * @var string
     */
    private $descripcion;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $cosasRegaladas;

    /**
     * @var \PG\PartyBundle\Entity\BucketGift
     */
    private $bucketGift;

    /**
     * @var \NW\PrincipalBundle\Entity\CatRegalos
     */
    private $catregalos;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->cosasRegaladas = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set eventoId
     *
     * @param integer $eventoId
     * @return MesaRegalos
     */
    public function setEventoId($eventoId)
    {
        $this->eventoId = $eventoId;

        return $this;
    }

    /**
     * Get eventoId
     *
     * @return integer 
     */
    public function getEventoId()
    {
        return $this->eventoId;
    }

    /**
     * Set regalo
     *
     * @param string $regalo
     * @return MesaRegalos
     */
    public function setRegalo($regalo)
    {
        $this->regalo = $regalo;

        return $this;
    }

    /**
     * Get regalo
     *
     * @return string 
     */
    public function getRegalo()
    {
        return $this->regalo;
    }

    /**
     * Set precioTotal
     *
     * @param float $precioTotal
     * @return MesaRegalos
     */
    public function setPrecioTotal($precioTotal)
    {
        $this->precioTotal = $precioTotal;

        return $this;
    }

    /**
     * Get precioTotal
     *
     * @return float 
     */
    public function getPrecioTotal()
    {
        return $this->precioTotal;
    }

    /**
     * Set cantidad
     *
     * @param integer $cantidad
     * @return MesaRegalos
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
     * Set horcruxes
     *
     * @param integer $horcruxes
     * @return MesaRegalos
     */
    public function setHorcruxes($horcruxes)
    {
        $this->horcruxes = $horcruxes;

        return $this;
    }

    /**
     * Get horcruxes
     *
     * @return integer 
     */
    public function getHorcruxes()
    {
        return $this->horcruxes;
    }

    /**
     * Set horcruxesPagados
     *
     * @param integer $horcruxesPagados
     * @return MesaRegalos
     */
    public function setHorcruxesPagados($horcruxesPagados)
    {
        $this->horcruxesPagados = $horcruxesPagados;

        return $this;
    }

    /**
     * Get horcruxesPagados
     *
     * @return integer 
     */
    public function getHorcruxesPagados()
    {
        return $this->horcruxesPagados;
    }

    /**
     * Set descripcion
     *
     * @param string $descripcion
     * @return MesaRegalos
     */
    public function setDescripcion($descripcion)
    {
        $this->descripcion = $descripcion;

        return $this;
    }

    /**
     * Get descripcion
     *
     * @return string 
     */
    public function getDescripcion()
    {
        return $this->descripcion;
    }

    /**
     * Add cosasRegaladas
     *
     * @param \NW\PrincipalBundle\Entity\cosasRegaladas $cosasRegaladas
     * @return MesaRegalos
     */
    public function addCosasRegalada(\NW\PrincipalBundle\Entity\cosasRegaladas $cosasRegaladas)
    {
        $this->cosasRegaladas[] = $cosasRegaladas;

        return $this;
    }

    /**
     * Remove cosasRegaladas
     *
     * @param \NW\PrincipalBundle\Entity\cosasRegaladas $cosasRegaladas
     */
    public function removeCosasRegalada(\NW\PrincipalBundle\Entity\cosasRegaladas $cosasRegaladas)
    {
        $this->cosasRegaladas->removeElement($cosasRegaladas);
    }

    /**
     * Get cosasRegaladas
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCosasRegaladas()
    {
        return $this->cosasRegaladas;
    }

    /**
     * Set bucketGift
     *
     * @param \PG\PartyBundle\Entity\BucketGift $bucketGift
     * @return MesaRegalos
     */
    public function setBucketGift(\PG\PartyBundle\Entity\BucketGift $bucketGift = null)
    {
        $this->bucketGift = $bucketGift;

        return $this;
    }

    /**
     * Get bucketGift
     *
     * @return \PG\PartyBundle\Entity\BucketGift 
     */
    public function getBucketGift()
    {
        return $this->bucketGift;
    }

    /**
     * Set catregalos
     *
     * @param \NW\PrincipalBundle\Entity\CatRegalos $catregalos
     * @return MesaRegalos
     */
    public function setCatregalos(\NW\PrincipalBundle\Entity\CatRegalos $catregalos = null)
    {
        $this->catregalos = $catregalos;

        return $this;
    }

    /**
     * Get catregalos
     *
     * @return \NW\PrincipalBundle\Entity\CatRegalos 
     */
    public function getCatregalos()
    {
        return $this->catregalos;
    }
    /**
     * @var string
     */
    private $moneda;


    /**
     * Set moneda
     *
     * @param string $moneda
     * @return MesaRegalos
     */
    public function setMoneda($moneda)
    {
        $this->moneda = $moneda;

        return $this;
    }

    /**
     * Get moneda
     *
     * @return string 
     */
    public function getMoneda()
    {
        return $this->moneda;
    }

    public function getPrecioTotalFormateado()
    {
        return number_format($this->getPrecioTotal(), 2, '.', ',');
    }

    public function getPrecioParteFormateado()
    {
        return number_format($this->getPrecioTotal()/$this->getHorcruxes(), 2, '.', ',');
    }
}
