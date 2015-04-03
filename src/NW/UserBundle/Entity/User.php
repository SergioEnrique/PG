<?php
// src/NW/UserBundle/Entity/usuario.php
namespace NW\UserBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;

/**
 * User
 */
class User extends BaseUser
{
    public function __construct()
    {
        parent::__construct();
        // your own logic
        // Con esto le asigno un rol por default a un nuevo usuario:
        $this->roles = array('ROLE_USER');
        //$this->novias = new ArrayCollection();
    }
    /**
     * @var integer
     */
    protected $id;

    /**
     * @var float
     */
    private $saldo;

    /**
     * @var string
     */
    private $facebookId;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $solicitudesRetiro;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $cosasRegaladas;


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
     * Set saldo
     *
     * @param float $saldo
     * @return User
     */
    public function setSaldo($saldo)
    {
        $this->saldo = $saldo;

        return $this;
    }

    /**
     * Get saldo
     *
     * @return float 
     */
    public function getSaldo()
    {
        return $this->saldo;
    }

    /**
     * Set facebookId
     *
     * @param string $facebookId
     * @return User
     */
    public function setFacebookId($facebookId)
    {
        $this->facebookId = $facebookId;

        return $this;
    }

    /**
     * Get facebookId
     *
     * @return string 
     */
    public function getFacebookId()
    {
        return $this->facebookId;
    }

    /**
     * Add solicitudesRetiro
     *
     * @param \NW\PrincipalBundle\Entity\SolicitudRetiro $solicitudesRetiro
     * @return User
     */
    public function addSolicitudesRetiro(\NW\PrincipalBundle\Entity\SolicitudRetiro $solicitudesRetiro)
    {
        $this->solicitudesRetiro[] = $solicitudesRetiro;

        return $this;
    }

    /**
     * Remove solicitudesRetiro
     *
     * @param \NW\PrincipalBundle\Entity\SolicitudRetiro $solicitudesRetiro
     */
    public function removeSolicitudesRetiro(\NW\PrincipalBundle\Entity\SolicitudRetiro $solicitudesRetiro)
    {
        $this->solicitudesRetiro->removeElement($solicitudesRetiro);
    }

    /**
     * Get solicitudesRetiro
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getSolicitudesRetiro()
    {
        return $this->solicitudesRetiro;
    }

    /**
     * Add cosasRegaladas
     *
     * @param \NW\PrincipalBundle\Entity\cosasRegaladas $cosasRegaladas
     * @return User
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
     * @var \Doctrine\Common\Collections\Collection
     */
    private $bucketGift;


    /**
     * Add bucketGift
     *
     * @param \PG\PartyBundle\Entity\BucketGift $bucketGift
     * @return User
     */
    public function addBucketGift(\PG\PartyBundle\Entity\BucketGift $bucketGift)
    {
        $this->bucketGift[] = $bucketGift;

        return $this;
    }

    /**
     * Remove bucketGift
     *
     * @param \PG\PartyBundle\Entity\BucketGift $bucketGift
     */
    public function removeBucketGift(\PG\PartyBundle\Entity\BucketGift $bucketGift)
    {
        $this->bucketGift->removeElement($bucketGift);
    }

    /**
     * Get bucketGift
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getBucketGift()
    {
        return $this->bucketGift;
    }
    /**
     * @var string
     */
    private $moneda;


    /**
     * Set moneda
     *
     * @param string $moneda
     * @return User
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
    /**
     * @var string
     */
    private $nombre;

    /**
     * @var string
     */
    private $apellidos;


    /**
     * Set nombre
     *
     * @param string $nombre
     * @return User
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;

        return $this;
    }

    /**
     * Get nombre
     *
     * @return string 
     */
    public function getNombre()
    {
        return $this->nombre;
    }

    /**
     * Set apellidos
     *
     * @param string $apellidos
     * @return User
     */
    public function setApellidos($apellidos)
    {
        $this->apellidos = $apellidos;

        return $this;
    }

    /**
     * Get apellidos
     *
     * @return string 
     */
    public function getApellidos()
    {
        return $this->apellidos;
    }

    public function getSaldoFormateado()
    {
        return number_format($this->saldo, 2, '.', ',');
    }
}
