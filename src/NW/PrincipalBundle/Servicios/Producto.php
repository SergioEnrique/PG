<?php
// src/AG/PrincipalBundle/Servicios/Producto.php
namespace AG\PrincipalBundle\Servicios;

class Producto
{
    protected $em; // Doctrine Entity Manager
    protected $router; // Para gererar rutas url

    public function __construct($router, $em)
    {
    	$this->em = $em;
    	$this->router = $router;
    }

	public function getSlug()
    {
        return "nadanada";
    }
 
}