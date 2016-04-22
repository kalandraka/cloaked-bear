<?php

namespace Buseta\BodegaBundle\Controller;

use Buseta\BodegaBundle\Entity\PrecioProducto;
use Buseta\BodegaBundle\Form\Filter\ProductoFilter;
use Buseta\BodegaBundle\Form\Model\ProductoFilterModel;
use Buseta\BodegaBundle\Form\Model\ProductoModel;
use Buseta\BodegaBundle\Form\Type\PrecioProductoType;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Buseta\BodegaBundle\Entity\Producto;
use Buseta\BodegaBundle\Form\Type\ProductoType;
use Buseta\BodegaBundle\Extras\FuncionesExtras;

use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Response;
use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;
/**
 * Producto controller.
 *
 * @Route("/producto")
 * @Breadcrumb(title="Inicio", routeName="core_homepage")
 * @Breadcrumb(title="Módulo de Bodegas", routeName="bodega_principal")
 */
class ProductoController extends Controller
{
    /**
     * Updated automatically el filtrado de busqueda de productos.
     */
    public function productoFilterAction(Request $request)
    {
        if (!$request->isXmlHttpRequest()) {
            return new \Symfony\Component\HttpFoundation\Response('No es una petición Ajax', 500);
        }

        $json = array(
            'id' => $busqueda,
        );

        /*$em = $this->getDoctrine()->getManager();
        $producto = $em->getRepository('BusetaBodegaBundle:Producto')->filterProducto($busqueda);

        if($producto != null) {
            $json = array(
                'id' => $producto->getId(),
            );
        }
        else {
            $json = array(
                'id' => null,
            );
        }*/

        return new \Symfony\Component\HttpFoundation\Response(json_encode($json), 200);
    }

    public function productoBitacoraAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $producto = $em->getRepository('BusetaBodegaBundle:Producto')->find($id);

        $bitacora = $em->getRepository('BusetaBodegaBundle:BitacoraAlmacen')->findBy(
            array(
                'producto' => $producto,
            )
        );

        $paginator = $this->get('knp_paginator');
        $bitacora = $paginator->paginate(
            $bitacora,
            $this->get('request')->query->get('page', 1),
            10,
            array('pageParameterName' => 'page')
        );

        return $this->render('BusetaBodegaBundle:Producto:bitacora.html.twig', array(
            'bitacora' => $bitacora,
            'producto' => $producto,
        ));
    }

    /**
     * Updated automatically select All when change select Producto.
     */
    public function select_productos_allAction(Request $request)
    {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            return new \Symfony\Component\HttpFoundation\Response('Acceso Denegado', 403);
        }

        if (!$request->isXmlHttpRequest()) {
            return new \Symfony\Component\HttpFoundation\Response('No es una petición Ajax', 500);
        }

        $em = $this->getDoctrine()->getManager();
        $producto = $em->getRepository('BusetaBodegaBundle:Producto')->findOneBy(array(
            'id' => $request->query->get('producto_id'),
        ));

        $impuesto = $em->getRepository('BusetaTallerBundle:Impuesto')->findOneBy(array(
            'id' => $request->query->get('impuesto_id'),
        ));

        $cantidad_pedido = $request->query->get('cantidad_pedido');
        foreach ($producto->getPrecioProducto() as $precios) {
            if ($precios->getActivo()) {
                $precioSalida = ($precios->getPrecio());
            }
        }

        if (isset($precioSalida)) {
            $precio_unitario = $precioSalida;
        } else {
            $precio_unitario = 0;
        }

        $porciento_descuento = $request->query->get('porciento_descuento');

        $funcionesExtras = new FuncionesExtras();
        $importeLinea = $funcionesExtras->ImporteLinea($impuesto, $cantidad_pedido, $precio_unitario, $porciento_descuento);

        $json = array(
            'id' => $producto->getId(),
            'precio' => $precio_unitario,
            'importeLinea' => $importeLinea,
            'porciento_descuento' => $porciento_descuento,
        );

        return new \Symfony\Component\HttpFoundation\Response(json_encode($json), 200);
    }

    /**
     * Updated automatically select Subgrupos when change select Grupos.
     */
    public function select_grupo_subgrupoAction(Request $request)
    {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            return new \Symfony\Component\HttpFoundation\Response('Acceso Denegado', 403);
        }

        if (!$request->isXmlHttpRequest()) {
            return new \Symfony\Component\HttpFoundation\Response('No es una petición Ajax', 500);
        }

        $em = $this->getDoctrine()->getManager();
        $subgrupos = $em->getRepository('BusetaNomencladorBundle:Subgrupo')->findBy(array(
            'grupo' => $request->query->get('grupo_id'),
        ));

        $json = array();
        foreach ($subgrupos as $subgrupo) {
            $json[] = array(
                'id' => $subgrupo->getId(),
                'valor' => $subgrupo->getValor(),
            );
        }

        return new \Symfony\Component\HttpFoundation\Response(json_encode($json), 200);
    }

    /**
     * Lists all Producto entities.
     * @Route("/", name="producto")
     * @Method("GET")
     * @Breadcrumb(title="Listado de Productos", routeName="producto")
     */
    public function indexAction(Request $request)
    {
        $filter = new ProductoFilterModel();

        $form = $this->createForm(new ProductoFilter(), $filter, array(
            'action' => $this->generateUrl('producto'),
        ));

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entities = $this->get('doctrine.orm.entity_manager')
                ->getRepository('BusetaBodegaBundle:Producto')->filter($filter);
        } else {
            $entities = $this->get('doctrine.orm.entity_manager')
                ->getRepository('BusetaBodegaBundle:Producto')->filter();
        }

        $paginator = $this->get('knp_paginator');
        $entities = $paginator->paginate(
            $entities,
            $request->query->get('page', 1),
            10
        );

        return $this->render('BusetaBodegaBundle:Producto:index.html.twig', array(
            'entities' => $entities,
            'filter_form' => $form->createView(),
        ));
    }

    /**
     * Creates a new Producto entity.
     *
     * @Route("/create", name="productos_producto_create", methods={"POST"}, options={"expose":true})
     * @Security("is_granted('create', 'Buseta\\BodegaBundle\\Entity\\Producto')")
     * @Breadcrumb(title="Crear Nuevo Producto", routeName="productos_producto_create")
     */
    public function createAction(Request $request)
    {
        //nuevo modelo vacio de producto
        $productoModel = new ProductoModel();
        //creacion del formulario para esto
        $form = $this->createCreateForm($productoModel);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->get('doctrine.orm.entity_manager');
            $trans = $this->get('translator');
            $logger = $this->get('logger');

            try {
                $entity = $productoModel->getEntityData();


                $em->persist($entity);
                $em->flush();

                // Creando nuevamente el formulario con los datos actualizados de la entidad
                $form = $this->createEditForm(new ProductoModel($entity));
                $renderView = $this->renderView('@BusetaBodega/Producto/form_template.html.twig', array(
                    'form' => $form->createView(),
                ));

                return new JsonResponse(array(
                    'view' => $renderView,
                    'message' => $trans->trans('messages.create.success', array(), 'BusetaBodegaBundle')
                ), 201);
            } catch (\Exception $e) {
                $logger->addCritical(sprintf(
                    $trans->trans('', array(), 'BusetaBodegaBundle') . '. Detalles: %s',
                    $e->getMessage()
                ));

                return new JsonResponse(array(
                    'message' => $trans->trans('messages.create.error.%key%', array('key' => 'Producto'), 'BusetaBodegaBundle')
                ), 500);
            }
        }

        $renderView = $this->renderView('@BusetaBodega/Producto/form_template.html.twig', array(
            'form' => $form->createView(),
        ));

        return new JsonResponse(array('view' => $renderView));
    }

    /**
     * Creates a form to create a Producto entity.
     *
     * @param ProductoModel $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(ProductoModel $entity)
    {
        $form = $this->createForm('bodega_producto', $entity, array(
            'action' => $this->generateUrl('productos_producto_create'),
            'method' => 'POST',
        ));

        return $form;
    }

    /**
     * Displays a form to create a new Producto entity.
     *
     * @Route("/new", name="productos_producto_new", methods={"GET"}, options={"expose":true})
     * @Security("is_granted('create', 'Buseta\\BodegaBundle\\Entity\\Producto')")
     * @Breadcrumb(title="Crear Nuevo Producto", routeName="productos_producto_new")
     */
    public function newAction()
    {
        $form = $this->createCreateForm(new ProductoModel());

        return $this->render('BusetaBodegaBundle:Producto:new.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Producto entity.
     * @Route("/{id}/show", name="producto_show", methods={"GET"}, options={"expose":true})
     * @Security("is_granted('show', producto)")
     * @Breadcrumb(title="Ver Datos de Producto", routeName="producto_show", routeParameters={"id"})
     */
    public function showAction(Producto $producto)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BusetaBodegaBundle:Producto')->find($producto);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Producto entity.');
        }

        $deleteForm = $this->createDeleteForm($producto);

        return $this->render('BusetaBodegaBundle:Producto:show.html.twig', array(
            'entity' => $entity,
            'delete_form' => $deleteForm->createView(),));
    }

    /**
     * Displays a form to edit an existing Producto entity.
     *
     * @Route("/{id}/edit", name="productos_producto_edit", methods={"GET"}, options={"expose":true})
     * @Security("is_granted('edit', producto)")
     * @Breadcrumb(title="Modificar Producto", routeName="productos_producto_edit", routeParameters={"id"})
     */
    public function editAction(Producto $producto)
    {
        $model = new ProductoModel($producto);
        $editForm = $this->createEditForm($model);
        $deleteForm = $this->createDeleteForm($producto->getId());

        return $this->render('BusetaBodegaBundle:Producto:edit.html.twig', array(
            'entity' => $producto,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Creates a form to edit a Producto entity.
     *
     * @param ProductoModel $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(ProductoModel $entity)
    {
        $form = $this->createForm('bodega_producto', $entity, array(
            'action' => $this->generateUrl('productos_producto_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        return $form;
    }

    /**
     * Edits an existing Producto entity.
     *
     * @Route("/{id}/update", name="productos_producto_update", methods={"POST","PUT"}, options={"expose":true})
     * @Breadcrumb(title="Modificar Producto", routeName="productos_producto_update", routeParameters={"id"})
     */
    public function updateAction(Request $request, Producto $producto)
    {
        $productoModel = new ProductoModel($producto);
        $editForm = $this->createEditForm($productoModel);

        $editForm->handleRequest($request);
        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->get('doctrine.orm.entity_manager');
            $trans = $this->get('translator');
            $logger = $this->get('logger');

            try {
                $producto->setModelData($productoModel);
                $em->flush();

                $renderView = $this->renderView('@BusetaBodega/Producto/form_template.html.twig', array(
                    'form' => $editForm->createView(),
                ));

                return new JsonResponse(array(
                    'view' => $renderView,
                    'message' => $trans->trans('messages.update.success', array(), 'BusetaBodegaBundle')
                ), 202);
            } catch (\Exception $e) {
                $logger->addCritical(sprintf(
                    $trans->trans('messages.update.success', array(), 'BusetaBodegaBundle') . '. Detalles: %s',
                    $e->getMessage()
                ));

                return new JsonResponse(array(
                    'message' => $trans->trans('messages.update.error.%key%', array('key' => 'Producto'), 'BusetaBodegaBundle')
                ), 500);
            }
        }

        $renderView = $this->renderView('@BusetaBodega/Producto/form_template.html.twig', array(
            'form' => $editForm->createView(),
        ));

        return new JsonResponse(array('view' => $renderView));
    }

    /**
     * Deletes a Producto entity.
     *
     * @Route("/{id}/delete", name="producto_delete")
     * @Method({"DELETE", "GET"})
     */
    public function deleteAction(Producto $producto, Request $request)
    {
        $trans = $this->get('translator');
        $deleteForm = $this->createDeleteForm($producto->getId());

        $deleteForm->handleRequest($request);
        if ($deleteForm->isSubmitted() && $deleteForm->isValid()) {
            try {
                $em = $this->get('doctrine.orm.entity_manager');

                $em->remove($producto);
                $em->flush();

                $message = $trans->trans('messages.delete.success', array(), 'BusetaTallerBundle');

                if ($request->isXmlHttpRequest()) {
                    return new JsonResponse(array(
                        'message' => $message,
                    ), 202);
                } else {
                    $this->get('session')->getFlashBag()->add('success', $message);
                }
            } catch (\Exception $e) {
                $message = $trans->trans('messages.delete.error.%key%', array('key' => 'Producto'), 'BusetaTallerBundle');
                $this->get('logger')->addCritical(sprintf($message . ' Detalles: %s', $e->getMessage()));

                if ($request->isXmlHttpRequest()) {
                    return new JsonResponse(array(
                        'message' => $message,
                    ), 500);
                }
            }
        }

        $renderView = $this->renderView('@BusetaBodega/Producto/delete_modal.html.twig', array(
            'entity' => $producto,
            'form' => $deleteForm->createView(),
        ));

        if ($request->isXmlHttpRequest()) {
            return new JsonResponse(array('view' => $renderView));
        }
        return $this->redirect($this->generateUrl('producto'));

    }

    /**
     * Creates a form to delete a Producto entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('producto_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * @param Producto $producto
     *
     * @Route("/{id}/data.{format}", name="productos_get_product_data", requirements={"format": "json|txt"}, defaults={"format":"json"}, options={"expose": true})
     * @Method({"GET"})
     */
    public function getProductDataAction($id)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $qb = $em->getRepository('BusetaBodegaBundle:Producto')
            ->createQueryBuilder('producto');

        /** @var \Buseta\BodegaBundle\Entity\Producto $producto */
        $producto = $qb->select('producto,uom,categoriaProducto')
            ->leftJoin('producto.uom', 'uom')
            ->leftJoin('producto.categoriaProducto', 'categoriaProducto')
            ->where($qb->expr()->eq('producto', ':id'))
            ->setParameter('id', $id)
            ->getQuery()
            ->getSingleResult();

        if (!$producto) {
            return new JsonResponse('No existe el producto con id: ' . $id, 404);
        }

        $data = array(
            'id' => $producto->getId(),
            'nombre' => $producto->getNombre(),
            'codigo' => $producto->getCodigo(),
        );

        // Select UOM
        if ($producto->getUom()) {
            $data['uom'] = array(
                'id' => $producto->getUom()->getId(),
                'value' => $producto->getUom()->getValor(),
            );
        }

//        $precio = $em->getRepository('BusetaBodegaBundle:Producto')->getPrecioActivo($id);
//        if ($precio) {
//            $data['precio'] = array(
//                'id'        => $precio->getId(),
//                'precio'    => $precio->getPrecio(),
//            );
//        }

        $costos = $em->getRepository('BusetaBodegaBundle:Producto')->getCostoActivo($id);
        if ($costos) {
            foreach ($costos as $costo) {
                /** @var \Buseta\BodegaBundle\Entity\CostoProducto $costo */
                $data['costos'][$costo->getId()] = array(
                    'costo' => $costo->getCosto(),
                    'codigo' => $costo->getCodigoAlternativo(),
                );

                if ($costo->getProveedor()) {
                    $proveedor = $costo->getProveedor();
                    $data['costos'][$costo->getId()]['proveedor'] = array(
                        'id' => $proveedor->getId(),
                        'nombre' => $proveedor->__toString(),
                    );
                }
            }
        }

        return new JsonResponse($data);
    }

    /**
     * @Route("/{id}/has_serial", name="productos_has_serial", options={"expose": true})
     * @Method("GET")
     *
     * @param Producto $producto
     * @return JsonResponse
     */
    public function hasSerialAction(Producto $producto)
    {
        $serial = $producto->getTieneNroSerie();
        $response = array("valor" => $serial);
        return new JsonResponse($response);
    }
}
