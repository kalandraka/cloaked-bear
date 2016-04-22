<?php

namespace Buseta\BodegaBundle\Controller;

use Buseta\BodegaBundle\BusetaBodegaDocumentStatus;
use Buseta\BodegaBundle\Entity\Albaran;
use Buseta\BodegaBundle\Entity\AlbaranLinea;
use Buseta\BodegaBundle\Entity\NecesidadMaterialLinea;
use Buseta\BodegaBundle\Entity\PedidoCompra;
use Buseta\BodegaBundle\Entity\PedidoCompraLinea;
use Buseta\BodegaBundle\Form\Filter\NecesidadMaterialFilter;
use Buseta\BodegaBundle\Form\Model\NecesidadMaterialFilterModel;
use Buseta\BodegaBundle\Form\Model\NecesidadMaterialModel;
use Buseta\BodegaBundle\Form\Type\NecesidadMaterialLineaType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
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
        $manager = $this->get('buseta.bodega.necesidadmaterial.manager');
        if (true === $result = $manager->procesar($necesidadMaterial)) {
            $this->get('session')->getFlashBag()->add(
                'success',
                'Se ha procesado la Necesidad Material de forma correcta.'
            );

            return $this->redirect($this->generateUrl('necesidadmaterial_show', array('id' => $necesidadMaterial->getId())));
        } else {
            $this->get('session')->getFlashBag()->add(
                'danger',
                'Ha ocurrido un error al procesar la Necesidad Material.'
            );

            return $this->redirect($this->generateUrl('necesidadmaterial_show', array('id' => $necesidadMaterial->getId())));
        }
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
        {
            $manager = $this->get('buseta.bodega.necesidadmaterial.manager');
            if (true === $result = $manager->completar($necesidadMaterial)) {
                $this->get('session')->getFlashBag()->add(
                    'success',
                    'Se ha completado la Necesidad Material de forma correcta.'
                );

                return $this->redirect($this->generateUrl('necesidadmaterial_show', array('id' => $necesidadMaterial->getId())));
            } else {
                $this->get('session')->getFlashBag()->add(
                    'danger',
                    sprintf('Ha ocurrido un error al completar la Necesidad Material.')
                );

                return $this->redirect($this->generateUrl('necesidadmaterial_show', array('id' => $necesidadMaterial->getId())));
            }
        }
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
        $necesidadMaterialModel = new NecesidadMaterialModel();
        $form = $this->createCreateForm($necesidadMaterialModel);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $trans  = $this->get('translator');
            $logger = $this->get('logger');
            $necesidadMaterialManager = $this->get('buseta.bodega.necesidadmaterial.manager');

            if ($necesidadMaterial = $necesidadMaterialManager->crear($necesidadMaterialModel)) {
                // Creando nuevamente el formulario con los datos actualizados de la entidad
                $form = $this->createEditForm(new NecesidadMaterialModel($necesidadMaterial));
                $renderView = $this->renderView('@BusetaBodega/NecesidadMaterial/form_template.html.twig', array(
                    'form'   => $form->createView(),
                ));

                return new JsonResponse(array(
                    'view' => $renderView,
                    'message' => $trans->trans('messages.create.success', array(), 'BusetaBodegaBundle')
                ), 201);
            } else {
                return new JsonResponse(array(
                    'message' => $trans->trans('messages.create.error.%key%', array('key' => 'Necesidad Material'), 'BusetaBodegaBundle')
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
     *
     * @Breadcrumb(title="Crear Nueva Necesidad de Material", routeName="necesidadmaterial_new")
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

        return $this->render('@BusetaBodega/NecesidadMaterial/new.html.twig', array(
            'form'   => $form->createView(),
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
     *
     * @Breadcrumb(title="Modificar Necesidad Material", routeName="necesidadmaterial_edit", routeParameters={"id"})
     */
    public function editAction(NecesidadMaterial $necesidadmaterial)
    {
        if ($necesidadmaterial->getEstadoDocumento() !== BusetaBodegaDocumentStatus::DOCUMENT_STATUS_DRAFT) {
            throw $this->createAccessDeniedException(
                'No se puede modificar la Necesidad Material, pues ya ha sido Procesada.'
            );
        }

        $editForm = $this->createEditForm(new NecesidadMaterialModel($necesidadmaterial));
        $deleteForm = $this->createDeleteForm($necesidadmaterial->getId());

        return $this->render('BusetaBodegaBundle:NecesidadMaterial:edit.html.twig', array(
                'entity' => $necesidadmaterial,
                'edit_form' => $editForm->createView(),
                'delete_form' => $deleteForm->createView(),
            )
        );
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

    /**
     * @param Request $request
     * @param Tercero $vehiculo
     *
     * @return JsonResponse
     *
     * @Route("/select_proveedor_moneda", name="necesidadMaterial_ajax_proveedor_moneda", options={"expose": true})
     * @Method({"GET"})
     */
    public function selectProveedorMonedaAction(Request $request) {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY'))
            return new \Symfony\Component\HttpFoundation\Response('Acceso Denegado', 403);

        if (!$request->isXmlHttpRequest())
            return new \Symfony\Component\HttpFoundation\Response('No es una petición Ajax', 500);

        $em = $this->getDoctrine()->getManager();
        $tercero = $em->getRepository('BusetaBodegaBundle:Tercero')->find($request->query->get('tercero_id'));
        $moneda = $tercero->getProveedor()->getMoneda();

        $json = array(
            'id' => $moneda->getId(),
            'valor' => $moneda->getValor(),
            );


        return new \Symfony\Component\HttpFoundation\Response(json_encode($json), 200);
    }
}
