<?php

namespace Buseta\BodegaBundle\Controller;

use Buseta\BodegaBundle\BusetaBodegaDocumentStatus;
use Buseta\BodegaBundle\Entity\BitacoraAlmacen;
use Buseta\BodegaBundle\Entity\Repository\BitacoraAlmacenRepository;
use Buseta\BodegaBundle\Form\Filter\MovimientoFilter;
use Buseta\BodegaBundle\Form\Model\MovimientoFilterModel;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Buseta\BodegaBundle\Entity\MovimientosProductos;
use Buseta\BodegaBundle\Form\Type\MovimientosProductosType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Buseta\BodegaBundle\Entity\Movimiento;
use Buseta\BodegaBundle\Form\Type\MovimientoType;
use Buseta\BodegaBundle\Extras\FuncionesExtras;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;

/**
 * Movimiento controller.
 * @Breadcrumb(title="Inicio", routeName="core_homepage")
 * @Breadcrumb(title="Módulo de Bodegas", routeName="bodega_principal")
 */
class MovimientoController extends Controller
{

    public function procesarMovimientoAction(Movimiento $movimiento)
    {
        $manager = $this->get('buseta.bodega.movimiento.manager');

        if ($manager->procesar($movimiento)){
            $this->get('session')->getFlashBag()->add('success', 'Se ha procesado el Movimiento de forma correcta.');
            return $this->redirect( $this->generateUrl('movimiento_show', array( 'id' => $movimiento->getId())));
        }

        $this->get('session')->getFlashBag()->add('danger', 'Ha ocurrido un error al procesar el Movimento.');

        return $this->redirect( $this->generateUrl('movimiento_show', array( 'id' => $movimiento->getId())));

    }

    /**
     * Creates a new Movimiento entity.
     * @param Movimiento $entity
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function completarMovimientoAction(Movimiento $entity)
    {
        /*  @var  \Buseta\BodegaBundle\Entity\Movimiento  $entity */
        /*  @var  \Buseta\BodegaBundle\Entity\MovimientosProductos  $movimientoProducto */
        /*  @var  \Buseta\BodegaBundle\Entity\Producto  $producto*/
        /*  @var  \Buseta\BodegaBundle\Entity\Bodega  $bodega*/

        $em = $this->get('doctrine.orm.entity_manager');

        $fe = new FuncionesExtras();

        $almacenOrigen  = $entity->getAlmacenOrigen();

        $error=false;

        //Comparar la existencia de cantidad de productos disponibles en el almacen
        //a partir de la solicitud de movimiento de productos entre almacenes
        //ciclo a traves de todos las Movimientos de productos para verificar y validar
        //la existencia fisica en el almacen de Origen del producto
        foreach ($entity->getMovimientosProductos() as $movimientoProducto) {
            $producto = $movimientoProducto->getProducto();
            $cantidad = $movimientoProducto->getCantidad();
            $cantidadDisponible = $fe->comprobarCantProductoAlmacen($producto, $almacenOrigen, $cantidad, $em);

            //Comprobar la existencia del producto en la bodega seleccionada
            if ($cantidadDisponible === 'No existe') {
                $error=true;
                //Fallo de validacion, al no existir el producto en el almacen de origen
                //volver al menu de de crear nuevo Movimiento
                $movimientoProductoFormulario = $this->createForm(new MovimientosProductosType());
                $form   = $this->createCreateForm($entity);
                $form->addError(new FormError( sprintf( "El producto %s no existe en la bodega seleccionada", $producto->getNombre()) ));
                return $this->render('BusetaBodegaBundle:Movimiento:new.html.twig', array(
                    'entity' => $entity,
                    'movimientosProductos' => $movimientoProductoFormulario->createView(),
                    'form'   => $form->createView(),
                ));

            } elseif ($cantidadDisponible < 0) {
                $error=true;
                //Fallo de validacion, al no existir la cantidad solicitada del producto seleccionado en el almacen de origen
                //volver al menu de de crear nuevo Movimientos
                $movimientoProductoFormulario = $this->createForm(new MovimientosProductosType());
                $form   = $this->createCreateForm($entity);
                $form->addError(new FormError( sprintf("No existe en la bodega %s la cantidad de productos solicitados para el producto: %s", $almacenOrigen->getNombre(), $producto->getNombre()  )));
                return $this->render('BusetaBodegaBundle:Movimiento:new.html.twig', array(
                    'entity' => $entity,
                    'movimientosProductos' => $movimientoProductoFormulario->createView(),
                    'form'   => $form->createView(),
                ));

            }
            //si no hay problema con la existencia del producto en la bodega de origen
            //ni con la cantidad de ese producto entonces sigo al siguiente producto
        }

        //Si no hubo error en la validacion de las existencias de ninguna linea de $movimientoProducto
        if (!$error) {
            $manager = $this->get('buseta.bodega.movimiento.manager');
            if ($manager->completar($entity)){
                $this->get('session')->getFlashBag()->add('success', 'Se ha completado el movimiento de forma correcta.');
                return $this->redirect( $this->generateUrl('movimiento_show', array( 'id' => $entity->getId() ) ) );
            } else {
                $this->get('session')->getFlashBag()->add('danger',
                    sprintf('Ha ocurrido un error al completar el movimiento: %s', $result));
                return $this->redirect($this->generateUrl('movimiento_show', array('id' => $entity->getId())));
            }
        }
    }

    /**
     * Updated automatically select AlmacenDestino when change select AlmacenOrigen.
     */
    public function select_almacenOrigen_almacenDestinoAction(Request $request)
    {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            return new \Symfony\Component\HttpFoundation\Response('Acceso Denegado', 403);
        }

        if (!$request->isXmlHttpRequest()) {
            return new \Symfony\Component\HttpFoundation\Response('No es una petición Ajax', 500);
        }

        $em = $this->getDoctrine()->getManager();
//        $almacenDestino = $em->getRepository('BusetaBodegaBundle:Bodega')->findBy(array(
//            'id' => $request->query->get('almacenOrigen_id')
//        ));

        $almacenDestino = $em->getRepository('BusetaBodegaBundle:Bodega')->findAll();

        $json = array();
        foreach ($almacenDestino as $almacenDestino) {
            if ($almacenDestino->getId() != $request->query->get('almacenOrigen_id')) {
                $json[] = array(
                    'id' => $almacenDestino->getId(),
                    'valor' => $almacenDestino->getNombre(),
                );
            }
        }

        return new \Symfony\Component\HttpFoundation\Response(json_encode($json), 200);
    }

    /**
     * Lists all Movimiento entities.
     * @Route("/", name="movimiento")
     * @Breadcrumb(title="Listados de Movimientos de Productos", routeName="movimiento")
     */
    public function indexAction(Request $request)
    {
        $filter = new MovimientoFilterModel();

        $form = $this->createForm(new MovimientoFilter(), $filter, array(
            'action' => $this->generateUrl('movimiento'),
        ));

        $form->handleRequest($request);
        $em = $this->getDoctrine()->getManager();

        if($form->isSubmitted() && $form->isValid()) {
            $entities = $this->get('doctrine.orm.entity_manager')
                ->getRepository('BusetaBodegaBundle:Movimiento')->filter($filter);
        } else {
            $entities = $this->get('doctrine.orm.entity_manager')
                ->getRepository('BusetaBodegaBundle:Movimiento')->filter();
        }

        $paginator = $this->get('knp_paginator');
        $entities = $paginator->paginate(
            $entities,
            $request->query->get('page', 1),
            10
        );

        return $this->render('BusetaBodegaBundle:Movimiento:index.html.twig', array(
            'entities'      => $entities,
            'filter_form'   => $form->createView(),
        ));
    }

    /**
     * Creates a new Movimiento entity.
     * @Route("/create", name="movimiento_create", methods={"POST"}, options={"expose":true})
     * @Security("is_granted('create', 'Buseta\\BodegaBundle\\Entity\\Movimiento')")
     * @Breadcrumb(title="Crear Nuevo Movimiento de Productos", routeName="movimiento_create")
     */
    public function createAction(Request $request)
    {
        /*  @var  \Buseta\BodegaBundle\Entity\Movimiento $entity */
        /*  @var  \Buseta\BodegaBundle\Entity\MovimientosProductos $movimiento */
        /*  @var  \Buseta\BodegaBundle\Entity\MovimientosProductos $movimientoProducto */
        /*  @var  \Buseta\BodegaBundle\Entity\Producto $producto */
        /*  @var  \Buseta\BodegaBundle\Entity\Bodega $almacenOrigen */

        //hay que crear un nuevo movimiento
        $entity = new Movimiento();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $request = $this->get('request');
            $datos = $request->request->get('buseta_bodegabundle_movimiento');

            $idAlmacenOrigen = $datos['almacenOrigen'];
            $almacenOrigen = $em->getRepository('BusetaBodegaBundle:Bodega')->find($idAlmacenOrigen);
            $idAlmacenDestino = $datos['almacenDestino'];
            $almacenDestino = $em->getRepository('BusetaBodegaBundle:Bodega')->find($idAlmacenDestino);

            $movimientos = $datos['movimientos_productos'];
            $fe = new FuncionesExtras();

            $error = false;

            //Comparar la existencia de cantidad de productos disponibles en el almacen
            //a partir de la solicitud de movimiento de productos entre almacenes
            //ciclo a traves de todos las movimiento de productos para verificar y validar
            //la existencia fisica en el almacen de Origen del producto
            //Validacion del formulario
            foreach ($movimientos as $movimiento) {
                //recojo los datos
                $idProducto = $movimiento['producto'];
                $producto = $em->getRepository('BusetaBodegaBundle:Producto')->find($idProducto);
                $cantidadProducto = $movimiento['cantidad'];


                $cantidadDisponible = $fe->comprobarCantProductoAlmacen($producto, $almacenOrigen, $cantidadProducto,
                    $em);

                //Comprobar la existencia del producto en la bodega seleccionada
                if ($cantidadDisponible === 'No existe') {
                    //Volver al menu de de crear nuevo Movimiento
                    $movimientosProductos = $this->createForm(new MovimientosProductosType());

                    $form = $this->createCreateForm($entity);

                    $form->addError(new FormError(sprintf("El producto %s no existe en la bodega seleccionada",
                        $producto->getNombre())));

                    return $this->render('BusetaBodegaBundle:Movimiento:new.html.twig', array(
                        'entity' => $entity,
                        'movimientosProductos' => $movimientosProductos->createView(),
                        'form' => $form->createView(),
                    ));
                } //Si no existe la cantidad solicitada en el almacen del producto seleccionado
                elseif ($cantidadDisponible < 0) {
                    //Volver al menu de de crear nuevo Movimiento
                    $movimientosProductos = $this->createForm(new MovimientosProductosType());

                    $form = $this->createCreateForm($entity);

                    $form->addError(new FormError(sprintf("No existe en la bodega %s la cantidad de productos solicitados para el producto: %s",
                        $almacenOrigen->getNombre(), $producto->getNombre())));

                    return $this->render('BusetaBodegaBundle:Movimiento:new.html.twig', array(
                        'entity' => $entity,
                        'movimientosProductos' => $movimientosProductos->createView(),
                        'form' => $form->createView(),
                    ));
                }
            }

            //si no hubo error de validacion
            //Persistimos los movimientos
            //la entity es Movimiento

            if (!$error) {
                //entity es la entidad Movimiento
                $entity->setEstadoDocumento(BusetaBodegaDocumentStatus::DOCUMENT_STATUS_DRAFT);
                $entity->setAlmacenOrigen($almacenOrigen);
                $entity->setAlmacenDestino($almacenDestino);
                $entity->setFechaMovimiento($fechaMovimiento = new \DateTime());

                $em->persist($entity);
                $em->flush();
                //antes elcompletar era aqui
/*                $manager = $this->get('buseta.bodega.movimiento.manager');

                $result = $manager->completar($entity->getId());
                if ($result===true){
                    $this->get('session')->getFlashBag()->add('success', 'Se ha completado el Movimiento de forma correcta.');
                    return $this->redirect( $this->generateUrl('movimiento_show', array( 'id' => $entity->getId() ) ) );
                } else {
                    $this->get('session')->getFlashBag()->add('danger',  sprintf('Ha ocurrido un error al completar el Movimiento: %s',$result));
                    return $this->redirect( $this->generateUrl('movimiento_show', array( 'id' => $entity->getId() ) ) );
                }*/

                $this->get('session')->getFlashBag()->add('success',  'Se ha creado el movimiento correctamente');

                return $this->redirect( $this->generateUrl('movimiento_show', array( 'id' => $entity->getId() ) ) );
            }

        }

        return $this->render('BusetaBodegaBundle:Movimiento:new.html.twig', array(
            'entity' => $entity,
            'form' => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a Movimiento entity.
     *
     * @param Movimiento $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Movimiento $entity)
    {
        $form = $this->createForm(new MovimientoType(), $entity, array(
            'action' => $this->generateUrl('movimiento_create'),
            'method' => 'POST',
        ));

        //$form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Movimiento entity.
     * @Route("/new", name="movimiento_new", methods={"GET"}, options={"expose":true})
     * @Security("is_granted('create', 'Buseta\\BodegaBundle\\Entity\\Movimiento')")
     * @Breadcrumb(title="Crear Nuevo Movimiento de Productos", routeName="movimiento_new")
     */
    public function newAction()
    {
        $em = $this->getDoctrine()->getManager();

        $productos = $em->getRepository('BusetaBodegaBundle:Producto')->findAll();

        $paginator = $this->get('knp_paginator');

        $productos = $paginator->paginate(
            $productos,
            $this->get('request')->query->get('page', 1),
            10,
            array('pageParameterName' => 'page')
        );

        $entity = new Movimiento();

        $movimientosProductos = new MovimientosProductos();
        $movimientosProductos = $this->createForm(new MovimientosProductosType());

        $form   = $this->createCreateForm($entity);

        return $this->render('BusetaBodegaBundle:Movimiento:new.html.twig', array(
            'entity' => $entity,
            'movimientosProductos' => $movimientosProductos->createView(),
            'productos' => $productos,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Movimiento entity.
     * @Route("/{id}/show", name="movimiento_show", methods={"GET"}, options={"expose":true})
     * @Security("is_granted('show', movimiento)")
     * @Breadcrumb(title="Ver Datos de Movimiento de Productos", routeName="movimiento_show", routeParameters={"id"})
     */
    public function showAction(Movimiento $movimiento)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BusetaBodegaBundle:Movimiento')->find($movimiento->getId());

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Movimiento entity.');
        }

        $deleteForm = $this->createDeleteForm($movimiento->getId());

        return $this->render('BusetaBodegaBundle:Movimiento:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Movimiento entity.
     * @Route("/{id}/edit", name="movimiento_edit", methods={"GET"}, options={"expose":true})
     * @Security("is_granted('edit', movimiento)")
     * @Breadcrumb(title="Modificar Movimiento de Productos", routeName="movimiento_edit", routeParameters={"id"})
     */
    public function editAction(Movimiento $movimiento)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BusetaBodegaBundle:Movimiento')->find($movimiento->getId());

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Movimiento entity.');
        }

        $movimientosProductos = $this->createForm(new MovimientosProductosType());

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($movimiento->getId());

        return $this->render('BusetaBodegaBundle:Movimiento:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'movimientosProductos'   => $movimientosProductos->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Creates a form to edit a Movimiento entity.
     *
     * @param Movimiento $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Movimiento $entity)
    {
        $form = $this->createForm('buseta_bodegabundle_movimiento', $entity, array(
            'action' => $this->generateUrl('movimiento_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        //$form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }

    /**
     * Edits an existing Movimiento entity.
     * @Route("/{id}/update", name="movimiento_update", methods={"POST","PUT"}, options={"expose":true})
     * @Breadcrumb(title="Modificar Movimiento de Productos", routeName="movimiento_update", routeParameters={"id"})
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BusetaBodegaBundle:Movimiento')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Movimiento entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->submit($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('movimiento_show', array('id' => $entity->getId())));
        }

        return $this->render('BusetaBodegaBundle:Movimiento:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Movimiento entity.
     *
     * /////@Route("/{id}/delete", name="movimiento_delete")
     * @Security("is_granted('delete', movimiento)")
     * /////@Method({"DELETE", "GET"})
     */
    public function deleteAction(Movimiento $movimiento, Request $request)
    {
        $trans = $this->get('translator');
        $deleteForm = $this->createDeleteForm($movimiento->getId());

        $deleteForm->handleRequest($request);
        if($deleteForm->isSubmitted() && $deleteForm->isValid()) {
            try {
                $em = $this->get('doctrine.orm.entity_manager');

                $em->remove($movimiento);
                $em->flush();

                $message = $trans->trans('messages.delete.success', array(), 'BusetaTallerBundle');

                if($request->isXmlHttpRequest()) {
                    return new JsonResponse(array(
                        'message' => $message,
                    ), 202);
                }
                else {
                    $this->get('session')->getFlashBag()->add('success', $message);
                }
            } catch (\Exception $e) {
                $message = $trans->trans('messages.delete.error.%key%', array('key' => 'Movimiento'), 'BusetaTallerBundle');
                $this->get('logger')->addCritical(sprintf($message.' Detalles: %s', $e->getMessage()));

                if($request->isXmlHttpRequest()) {
                    return new JsonResponse(array(
                        'message' => $message,
                    ), 500);
                }
            }
        }

        $renderView =  $this->renderView('@BusetaBodega/Movimiento/delete_modal.html.twig', array(
            'entity' => $movimiento,
            'form' => $deleteForm->createView(),
        ));

        if($request->isXmlHttpRequest()) {
            return new JsonResponse(array('view' => $renderView));
        }
        return $this->redirect($this->generateUrl('movimiento'));
    }

    /**
     * Creates a form to delete a Movimiento entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('movimiento_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->getForm()
            ;
    }
}
