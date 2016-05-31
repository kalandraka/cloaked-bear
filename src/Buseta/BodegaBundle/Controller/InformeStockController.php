<?php

namespace Buseta\BodegaBundle\Controller;

use Buseta\BodegaBundle\Extras\FuncionesExtras;
use Buseta\BodegaBundle\Form\Filtro\BusquedaInformeStockType;
use Buseta\BodegaBundle\Form\Model\BitSerialFilterModel;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;
use Symfony\Component\HttpFoundation\JsonResponse;
use Buseta\BodegaBundle\Form\Filter\BitSerialFilter;

/**
 * Class InformeStockController
 *
 * @package Buseta\BodegaBundle\Controller
 *
 * @Breadcrumb(title="Inicio", routeName="core_homepage")
 * @Breadcrumb(title="Módulo de Bodegas", routeName="bodega_principal")
 *
 * @Route("/informe_stock")
 */
class InformeStockController extends Controller
{
    /**
     * Lists all Informe de Stock entities.
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Route("/", name="bodega_informe_stock")
     * @Method({"GET", "POST"})
     *
     * @Breadcrumb(title="Informe de Stock", routeName="bodega_informe_stock")
     */
    public function indexAction(Request $request)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $almacenesArray = null;
        $almacenesFinal = null;
        $informeStock = $this->createForm(new BusquedaInformeStockType());

        $informeStock->handleRequest($request);
        if ($informeStock->isSubmitted() && $informeStock->isValid()) {
            //Se obtienen todas las bitacoras que cumplieron con el filtro de búsqueda
            //$bitacoras = $em->getRepository('BusetaBodegaBundle:BitacoraAlmacen')->busquedaBitacoraAlmacen($informeStock);
            $almacenes = $em->getRepository('BusetaBodegaBundle:Bodega')->findAll();

            $funcionesExtras = new FuncionesExtras();
            $almacenesArray = $funcionesExtras->generarInformeStock($informeStock->getData(), $em);

            $almacenesFinal = array();
            $pos = 0;
            foreach ($almacenes as $almacen) {
                foreach ($almacenesArray as $almacenArray) {
                    if ($almacen->getId() == $almacenArray['almacen_id']) {
                        $almacenesFinal[$pos] = $almacen;
                        $pos++;
                        break;
                    }
                }
            }
        }

        return $this->render('BusetaBodegaBundle:InformeStock:index.html.twig', array(
            'entities' => $almacenesArray,
            'almacenes' => $almacenesFinal,
            'informeStock' => $informeStock->createView(),
        ));
    }

    /**
     * @param Request $request
     *
     * @throws \Exception
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/show/modal/seriales", name="seriales_modal_action", methods={"GET"}, options={"expose":true})
     */
    public function serialesModalAction(Request $request)
    {
        $filter = new BitSerialFilterModel();

        $em = $this->get('doctrine.orm.entity_manager');
        if($request->get('stock') != null) {
            $almacen = $em->getRepository('BusetaBodegaBundle:Bodega')->find($request->get('stock'));
            $filter->setAlmacen($almacen);

        }


        if($request->get('product') != null) {
            $producto = $em->getRepository('BusetaBodegaBundle:Producto')->find($request->get('product'));
            $filter->setProducto($producto);
        }

        $form = $this->createForm(new BitSerialFilter(), $filter, array(
            'action' => $this->generateUrl('seriales_modal_action', array(
                'stock' => $request->get('stock'),
                'product' => $request->get('product'),
            )),
        ));

        $fune = new FuncionesExtras();

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $producto = $filter->getProducto();
            $almacen = $filter->getAlmacen();

            $entities = $fune->getListaSerialesTeoricoEnAlmacen($producto,$almacen, $em);
        } else {
            $entities = $fune->getListaSerialesTeoricoEnAlmacen($producto,$almacen, $em);
            $entities = $fune->getListaSerialesEntitiesEnAlmacen($producto, $almacen, $em);
        }

        $paginator = $this->get('knp_paginator');
        $entities = $paginator->paginate(
            $entities,
            $request->query->get('page', 1),
            10
        );

        $renderView = $this->renderView('@BusetaBodega/InformeStock/modal_seriales.html.twig', array(
            'form' => $form->createView(),
            'entities'      => $entities
        ));

        return new JsonResponse(array('view' => $renderView));
    }
}
