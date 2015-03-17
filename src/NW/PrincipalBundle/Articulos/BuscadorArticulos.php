<?php
// src/NW/PrincipalBundle/Articulos/BuscadorArticulos.php
namespace NW\PrincipalBundle\Articulos;

class BuscadorArticulos
{
    protected $em; // Doctrine Entity Manager
    protected $router; // Para gererar rutas url

    public function __construct($router, $em)
    {
    	$this->em = $em;
    	$this->router = $router;
    }

	public function generarLink($categoria, $otra, $proveedor)
	{
		$link = $this->router->generate('nw_principal_articulos'); // Link que se regresará

        if ($proveedor)
        {
            $proveedor = $this->quitarAcentos($proveedor);
            $link = $this->router->generate('nw_principal_proveedor_busqueda', array('buscar' => $proveedor));
        }
		else if ($otra)
		{
			$otra = $this->quitarAcentos($otra);
            $link.= 'busqueda/'.$otra;
		}
		else if($categoria)
		{
			$catEntity = $this->em->getRepository('NWPrincipalBundle:Categorias'); // Entidad de Categorias
            $catObj = $catEntity->find($categoria); // Categoria recuperada como objeto
            $catName = $catObj->getNombre(); // Nombre de la categoria recuperada
            $catName = $this->quitarAcentos($catName); // Sin acentos

            $link.= $catName;
		}

		return $link;
	}

    public function busquedaResultados($formData)
    {
        if ($formData["proveedor"])
        {
            return $this->proveedoresPorCoincidencia($formData["proveedor"]);
        }
        else if ($formData["otro"])
        {
            return $this->articulosPorCoincidencia($formData["otro"]);
        }
        else if($formData["categorias"])
        {
            return $this->articulosPorCategoria($formData["categorias"]);
        }

        return false;
    }

	public function articulosPorCoincidencia($palabra)
	{
		// Obtener repositorio
        $articulosEntity = $this->em->getRepository('NWPrincipalBundle:Articulos'); // Entidad de Artículos
         
        $query = $articulosEntity->createQueryBuilder('a')
            ->where('a.nombre LIKE :buscar')
            ->orWhere('a.descripcion LIKE :buscar')
            ->orderBy('a.nombre', 'ASC')
            ->setParameter('buscar', '%'.$palabra.'%')
            ->getQuery();
         
        $resultados = $query->getResult();

        return $this->articulosToArray($resultados);
	}

	public function articulosPorCategoria($categoria)
	{
		// Obteniendo entidades de categorías y artículos
        $CategoriasEntidad = $this->em->getRepository('NWPrincipalBundle:Categorias');
        $ArticulosEntidad = $this->em->getRepository('NWPrincipalBundle:Articulos');

        // Obteniendo la categoría por nombre
        $catObjN = $CategoriasEntidad->findOneBy(array('nombre' => $categoria));

        // Obteniendo la categoría por ID
        $catObjI = $CategoriasEntidad->findOneBy(array('id' => $categoria));

        // ¿Existe la categoría?
        if(is_object($catObjN))
        {
            $resultados = $ArticulosEntidad->findBy(array('categoriaId' => $catObjN->getId()));
        }
        else if(is_object($catObjI))
        {
            $resultados = $ArticulosEntidad->findBy(array('categoriaId' => $catObjI->getId()));
        }
        else
        {
            $resultados = false;
        }

        return $this->articulosToArray($resultados);
	}

    public function proveedoresPorCoincidencia($palabra)
    {
        // Obtener repositorio
        $proveedoresEntity = $this->em->getRepository('NWUserBundle:registroproveedores'); // Entidad de Proveedores
         
        $query = $proveedoresEntity->createQueryBuilder('a')
            ->where('a.nombreComercial LIKE :buscar')
            ->orderBy('a.nombreComercial', 'ASC')
            ->setParameter('buscar', '%'.$palabra.'%')
            ->getQuery();
         
        $resultados = $query->getResult();

        return $this->proveedoresToArray($resultados);
    }

    public function proveedorPorNombreComercial($proveedorName)
    {
        // Obteniendo entidad
        $proveedoresEntidad = $this->em->getRepository('NWUserBundle:registroproveedores');

        // Obteniendo el objeto proveedor
        $proveedorObj = $proveedoresEntidad->findOneBy(array('nombreComercial' => $proveedorName));

        // ¿Existe el proveedor?
        if(is_object($proveedorObj))
        {
            return $proveedorObj->getValues();

        }
        else
        {
            return false;
        }

    }

	public function articulosToArray($resultados)
	{
		// Si existen resultados que mostrar, éstos convierten en array sus contenidos desde objetos
        if($resultados)
        {
            // Convirtiendo los resultados en arrays
            foreach($resultados as $index=>$value)
            {
                $objetoenArray=$resultados[$index]->getValues();
                $resultados[$index]=$objetoenArray;

                foreach($resultados[$index]['fotos'] as $indice=>$valor)
                {
                    $objeto2enArray=$resultados[$index]['fotos'][$indice]->getValues(false);
                    $resultados[$index]['fotos'][$indice]=$objeto2enArray;
                    $resultados[$index]['articulo'] = true;
                }
            }
        }

        return $resultados;
	}

    public function proveedoresToArray($resultados)
    {
        // Si existen resultados que mostrar, éstos convierten en array sus contenidos desde objetos
        if($resultados)
        {
            // Convirtiendo los resultados en arrays
            foreach($resultados as $index=>$value)
            {
                $objetoenArray=$resultados[$index]->getValues();
                $resultados[$index]=$objetoenArray;
                $resultados[$index]['url'] = $this->quitarAcentos($objetoenArray['nombreComercial']);
                $resultados[$index]['articulo'] = false;
            }
        }

        return $resultados;
    }

	private function quitarAcentos($palabra)
	{
        $no_permitidas = array ('á','é','í','ó','ú','Á','É','Í','Ó','Ú','ñ','Ñ');
        $si_permitidas = array ('a','e','i','o','u','A','E','I','O','U','n','N');

        $palabra = str_replace($no_permitidas, $si_permitidas, $palabra);

		return $palabra;
	}
 
}

