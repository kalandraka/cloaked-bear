<?php

namespace Buseta\BodegaBundle\Controller;

use Buseta\BodegaBundle\Entity\Albaran;
use Buseta\BodegaBundle\Entity\AlbaranLinea;
use Buseta\BodegaBundle\Entity\NecesidadMaterialLinea;
use Buseta\BodegaBundle\Entity\PedidoCompra;
use Buseta\BodegaBundle\Entity\PedidoCompraLinea;
use Buseta\BodegaBundle\Form\Filter\NecesidadMaterialFilter;
use Buseta\BodegaBundle\Form\Model\NecesidadMaterialFilterModel;
use Buseta\BodegaBundle\Form\Model\NecesidadMaterialModel;
use Buseta\BodegaBundle\Form\Type\NecesidadMaterialLineaType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Buseta\BodegaBundle\Entity\NecesidadMaterial;

use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Security\Core\Util\ClassUtils;
use Symfony\Component\Validator\ConstraintViolation;
use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;
/**
 * NecesidadMaterial controller.
 *
 * @Route("/necesidadmaterial")
 * @Breadcrumb(title="Inicio", routeName="core_homepage")
 * @Breadcrumb(title="Módulo de Bodegas", routeName="bodega_principal")
 */
class NecesidadMaterialController extends Controller
{
    /**
     * Lists all NecesidadMaterial entities.
     *
     * @Route("/", name="necesidadmaterial")
     * @Method("GET")
     * @Breadcrumb(title="Listado de Necesidad de Materiales", routeName="necesidadmaterial")
     */
    public function indexAction(Request $request)
    {
        $filter = new NecesidadMaterialFilterModel();

        $form = $this->createForm(new NecesidadMaterialFilter(), $filter, array(
            'action' => $this->generateUrl('necesidadmaterial'),
        ));

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $entities = $this->get('doctrine.orm.entity_manager')
                ->getRepository('BusetaBodegaBundle:NecesidadMaterial')->filter($filter);
        } else {
            $entities = $this->get('doctrine.orm.entity_manager')
                ->getRepository('BusetaBodegaBundle:NecesidadMaterial')->filter();
        }

        $paginator = $this->get('knp_paginator');
        $entities = $paginator->paginate(
            $entities,
            $request->query->get('page', 1),
            10
        );

        return $this->render('BusetaBodegaBundle:NecesidadMaterial:index.html.twig', array(
            'entities'      => $entities,
            'filter_form'   => $form->createView(),
        ));
    }

    /**
     * @param $necesidadMaterial
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @Route("/{id}/procesarNecesidad", name="procesarNecesidad")
     * @Method("GET")
     */
    public function procesarNecesidadAction(NecesidadMaterial $necesidadMaterial)
    {
        $validator  = $this->get('validator');
        $session    = $this->get('session');
        if (($errors = $validator->validate($necesidadMaterial, 'on_complete')) && count($errors) > 0) {
            foreach ($errors as $e) {
                /** @var ConstraintViolation $e */
                $session->getFlashBag()->add('danger', $e->getMessage());
            }

            return $this->redirect($this->generateUrl('necesidadmaterial_show', array('id' => $necesidadMaterial->getId())));
        }

        $em = $this->getDoctrine()->getManager();

        //Cambia el estado de Borrador a Procesado
        $necesidadMaterial->setEstadoDocumento('PR');

        try {
            $em->persist($necesidadMaterial);
            $em->flush();
        } catch (\Exception $e) {
            $this->get('logger')->addCritical(sprintf('Ha ocurrido un error actualizando el estado del documento. Detalles: %s', $e->getMessage()));
            $this->get('session')->getFlashBag()->add('danger', 'Ha ocurrido un error actualizando el estado del documento.');
        }

        return $this->redirect($this->generateUrl('necesidadmaterial'));
    }

    /**
     * @param $necesidadMaterial
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @Route("/{id}/completarNecesidad", name="completarNecesidad")
     * @Method("GET")
     */
    public function completarNecesidadAction(NecesidadMaterial $necesidadMaterial)
    {
        $em         = $this->getDoctrine()->getManager();
        $logger     = $this->get('logger');
        $session    = $this->get('session');
        $error      = false;

        //Cambia el estado de Procesado a Completado
        $necesidadMaterial->setEstadoDocumento('CO');
        try {
            $em->persist($necesidadMaterial);
            $em->flush();
        } catch (\Exception $e) {
            $logger->addCritical(sprintf('Ha ocurrido un error actualizando el estado del documento. Detalles: %s', $e->getMessage()));
            $session->getFlashBag()->add('danger', 'Ha ocurrido un error actualizando el estado del documento.');

            $error = true;
        }

        if (!$error) {
            $fecha =  new \DateTime();

            $almacen = $em->getRepository('BusetaBodegaBundle:Bodega')->find($necesidadMaterial->getAlmacen());
            $tercero = $em->getRepository('BusetaBodegaBundle:Tercero')->find($necesidadMaterial->getTercero());

            //Registro los datos del nuevo PedidoCompra que se crear al procesar la NecesidadMaterial

            $sequenceManager = $this->get('hatuey_soft.sequence.manager');
            $pedidoCompra = new PedidoCompra();
            if (($sequenceManager->getNextValue('orden_entrada_seq')) != null) {
                $pedidoCompra->setNumeroDocumento($sequenceManager->getNextValue('registro_compra_seq'));

            }  else {
                $pedidoCompra->setNumeroDocumento($necesidadMaterial->getNumeroDocumento());

            }
            $pedidoCompra->setTercero($tercero);
            $pedidoCompra->setFechaPedido($necesidadMaterial->getFechaPedido());
            $pedidoCompra->setAlmacen($almacen);
            $pedidoCompra->setMoneda($necesidadMaterial->getMoneda());
            $pedidoCompra->setFormaPago($necesidadMaterial->getFormaPago());
            $pedidoCompra->setCondicionesPago($necesidadMaterial->getCondicionesPago());
            $pedidoCompra->setEstadoDocumento('BO');
            $pedidoCompra->setImporteTotal($necesidadMaterial->getImporteTotal());
            $pedidoCompra->setImporteTotalLineas($necesidadMaterial->getImporteTotalLineas());
            $pedidoCompra->setImpuesto($necesidadMaterial->getImpuesto());
            $pedidoCompra->setDescuento($necesidadMaterial->getDescuento());
            $pedidoCompra->setImporteImpuesto($necesidadMaterial->getImporteImpuesto());
            $pedidoCompra->setImporteDescuento($necesidadMaterial->getImporteDescuento());
            $pedidoCompra->setImporteCompra($necesidadMaterial->getImporteCompra());
            $pedidoCompra->setObservaciones($necesidadMaterial->getObservaciones());
            $pedidoCompra->setCreated(new \DateTime());

            $em->persist($pedidoCompra);
            $em->flush();

            //Registro los datos de las líneas del PedidoCompra
            foreach ($necesidadMaterial->getNecesidadMaterialLineas() as $linea) {
                $registroCompraLinea = new PedidoCompraLinea();
                $registroCompraLinea->setPedidoCompra($pedidoCompra);
                $registroCompraLinea->setLinea($linea->getLinea());
                $registroCompraLinea->setProducto($linea->getProducto());
                $registroCompraLinea->setCantidadPedido($linea->getCantidadPedido());
                $registroCompraLinea->setUom($linea->getUom());
                $registroCompraLinea->setPrecioUnitario($linea->getPrecioUnitario());
                $registroCompraLinea->setImpuesto($linea->getImpuesto());
                $registroCompraLinea->setMoneda($linea->getMoneda());
                $registroCompraLinea->setPorcientoDescuento($linea->getPorcientoDescuento());
                $registroCompraLinea->setImporteLinea($linea->getImporteLinea());

                $em->persist($registroCompraLinea);
                $em->flush();
            }

            $necesidadMaterial->setEstadoDocumento('CO');
            $em->persist($necesidadMaterial);
            $em->flush();

            return $this->redirect($this->generateUrl('necesidadmaterial'));
        }

        return $this->redirect($this->generateUrl('necesidadmaterial'));
    }

    /**
     * Creates a new NecesidadMaterial entity.
     *
     * @Route("/create", name="necesidadmaterial_create", options={"expose": true})
     * @Method("POST")
     * @Breadcrumb(title="Crear Nueva Necesidad de Materiales", routeName="necesidadmaterial_create")
     */
    public function createAction(Request $request)
    {
        $necesidadmaterialModel = new NecesidadMaterialModel();
        $form = $this->createCreateForm($necesidadmaterialModel);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em     = $this->get('doctrine.orm.entity_manager');
            $trans  = $this->get('translator');
            $logger = $this->get('logger');

            try {
                $entity = $necesidadmaterialModel->getEntityData();

                $em->persist($entity);
                $em->flush();

                // Creando nuevamente el formulario con los datos actualizados de la entidad
                $form = $this->createEditForm(new NecesidadMaterialModel($entity));
                $renderView = $this->renderView('@BusetaBodega/NecesidadMaterial/form_template.html.twig', array(
                    'form'   => $form->createView(),
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
                    'message' => $trans->trans('messages.create.error.%key%', array('key' => 'Registro de Compra'), 'BusetaBodegaBundle')
                ), 500);
            }
        }

        $renderView = $this->renderView('@BusetaBodega/NecesidadMaterial/form_template.html.twig', array(
            'form'     => $form->createView(),
        ));

        return new JsonResponse(array('view' => $renderView));
    }

    /**
     * Creates a form to create a NecesidadMaterial entity.
     *
     * @param NecesidadMaterialModel $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(NecesidadMaterialModel $entity)
    {
        $form = $this->createForm('bodega_necesidad_material', $entity, array(
            'action' => $this->generateUrl('necesidadmaterial_create'),
            'method' => 'POST',
        ));

        return $form;
    }

    /**
     * Displays a form to create a new NecesidadMaterial entity.
     *
     * @Route("/new", name="necesidadmaterial_new")
     * @Method("GET")
     * @Breadcrumb(title="Crear Nueva Necesidad de Materiales", routeName="necesidadmaterial_new")
     */
    public function newAction()
    {

        $form   = $this->createCreateForm(new NecesidadMaterialModel());
        $em = $this->get('doctrine.orm.entity_manager');
        $productos = $em->getRepository('BusetaBodegaBundle:Producto')
            ->createQueryBuilder('p')
            ->select('p,c')
            ->innerJoin('p.costoProducto', 'c')
            ->getQuery()
            ->getResult();

        $json = array();
        $costoSalida = 0;

        foreach ($productos as $p) {
            foreach ($p->getCostoProducto() as $costo) {
                if ($costo->getActivo()) {
                    $costoSalida = ($costo->getCosto());
                }
            }

            $json[$p->getId()] = array(
                'nombre' => $p->getNombre(),
                'precio_salida' => $costoSalida,
            );
        }

        return $this->render('@BusetaBodega/NecesidadMaterial/new.html.twig', array(
            'form'   => $form->createView(),
            'json'   => json_encode($json),
        ));
    }

    /**
     * Finds and displays a NecesidadMaterial entity.
     *
     * @Route("/{id}/show", name="necesidadmaterial_show")
     * @Method("GET")
     * @Breadcrumb(title="Ver Datos de Necesidad Material", routeName="necesidadmaterial_show", routeParameters={"id"})
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BusetaBodegaBundle:NecesidadMaterial')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find NecesidadMaterial entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('BusetaBodegaBundle:NecesidadMaterial:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),        ));
    }

    /**
     * Displays a form to edit an existing NecesidadMaterial entity.
     *
     * @Route("/{id}/edit", name="necesidadmaterial_edit", options={"expose": true})
     * @Method("GET")
     * @Breadcrumb(title="Modificar Necesidad Material", routeName="necesidadmaterial_edit", routeParameters={"id"})
     */
    public function editAction(NecesidadMaterial $necesidadmaterial)
    {
        $editForm = $this->createEditForm(new NecesidadMaterialModel($necesidadmaterial));
        $deleteForm = $this->createDeleteForm($necesidadmaterial->getId());

        return $this->render('BusetaBodegaBundle:NecesidadMaterial:edit.html.twig', array(
            'entity'        => $necesidadmaterial,
            'edit_form'     => $editForm->createView(),
            'delete_form'   => $deleteForm->createView(),
        ));
    }

    /**
     * Creates a form to edit a NecesidadMaterial entity.
     *
     * @param NecesidadMaterialModel $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(NecesidadMaterialModel $entity)
    {
        $form = $this->createForm('bodega_necesidad_material', $entity, array(
            'action' => $this->generateUrl('necesidadmaterial_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        return $form;
    }

    /**
     * Edits an existing NecesidadMaterial entity.
     *
     * @Route("/{id}/update", name="necesidadmaterial_update", options={"expose": true})
     * @Method({"POST", "PUT"})
     * @Breadcrumb(title="Modificar Necesidad de Material", routeName="necesidadmaterial_update", routeParameters={"id"})
     */
    public function updateAction(Request $request, NecesidadMaterial $necesidadmaterial)
    {
        $necesidadmaterialModel = new NecesidadMaterialModel($necesidadmaterial);
        $editForm = $this->createEditForm($necesidadmaterialModel);

        $editForm->handleRequest($request);
        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em     = $this->get('doctrine.orm.entity_manager');
            $trans  = $this->get('translator');
            $logger = $this->get('logger');

            try {
                $necesidadmaterial->setModelData($necesidadmaterialModel);
                $em->flush();

                $editForm = $this->createEditForm(new NecesidadMaterialModel($necesidadmaterial));
                $renderView = $this->renderView('@BusetaBodega/NecesidadMaterial/form_template.html.twig', array(
                    'form'     => $editForm->createView(),
                ));

                return new JsonResponse(array(
                    'view' => $renderView,
                    'message' => $trans->trans('messages.update.success', array(), 'BusetaBodegaBundle')
                ), 202);
            } catch (\Exception $e) {
                $logger->addCritical(sprintf(
                    $trans->trans('messages.update.success', array(), 'BusetaBodegaBundle'). '. Detalles: %s',
                    $e->getMessage()
                ));

                new JsonResponse(array(
                    'message' => $trans->trans('messages.update.error.%key%', array('key' => 'Registro de Compra'), 'BusetaBodegaBundle')
                ), 500);
            }
        }

        $renderView = $this->renderView('@BusetaBodega/NecesidadMaterial/form_template.html.twig', array(
            'form'     => $editForm->createView(),
        ));

        return new JsonResponse(array('view' => $renderView));
    }

    /**
     * Deletes a NecesidadMaterial entity.
     *
     * @Route("/{id}/delete", name="necesidadmaterial_delete", options={"expose": true})
     * @Method({"DELETE", "GET", "POST"})
     */
    public function deleteAction(NecesidadMaterial $necesidadmaterial, Request $request)
    {
        $trans = $this->get('translator');
        $deleteForm = $this->createDeleteForm($necesidadmaterial->getId());

        $deleteForm->handleRequest($request);
        if($deleteForm->isSubmitted() && $deleteForm->isValid()) {
            try {
                $em = $this->get('doctrine.orm.entity_manager');

                $em->remove($necesidadmaterial);
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
                $message = $trans->trans('messages.delete.error.%key%', array('key' => 'Pedido Compra'), 'BusetaTallerBundle');
                $this->get('logger')->addCritical(sprintf($message.' Detalles: %s', $e->getMessage()));

                if($request->isXmlHttpRequest()) {
                    return new JsonResponse(array(
                        'message' => $message,
                    ), 500);
                }
            }
        }

        $renderView =  $this->renderView('@BusetaBodega/NecesidadMaterial/delete_modal.html.twig', array(
            'entity' => $necesidadmaterial,
            'form' => $deleteForm->createView(),
        ));

        if($request->isXmlHttpRequest()) {
            return new JsonResponse(array('view' => $renderView));
        }
        return $this->redirect($this->generateUrl('necesidadmaterial'));
    }

    /**
     * Creates a form to delete a NecesidadMaterial entity by id.
     *
     * @param mixed $id The entity id
     *º
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('necesidadmaterial_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->getForm()
            ;
    }
}
