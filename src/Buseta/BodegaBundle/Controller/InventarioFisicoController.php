<?php

namespace Buseta\BodegaBundle\Controller;

use Buseta\BodegaBundle\BusetaBodegaDocumentStatus;
use Buseta\BodegaBundle\Entity\Albaran;
use Buseta\BodegaBundle\Entity\AlbaranLinea;
use Buseta\BodegaBundle\Entity\InventarioFisicoLinea;
use Buseta\BodegaBundle\Form\Filter\InventarioFisicoFilter;
use Buseta\BodegaBundle\Form\Model\InventarioFisicoFilterModel;
use Buseta\BodegaBundle\Form\Model\InventarioFisicoModel;
use Buseta\BodegaBundle\Form\Type\InventarioFisicoLineaType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Buseta\BodegaBundle\Entity\InventarioFisico;
use Buseta\BodegaBundle\Entity\BitacoraAlmacen;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Buseta\BodegaBundle\BusetaBodegaBitacoraEvents;
use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;

/**
 * InventarioFisico controller.
 *
 * @Route("/inventariofisico")
 * @Breadcrumb(title="Inicio", routeName="core_homepage")
 * @Breadcrumb(title="Módulo de Bodegas", routeName="bodega_principal")
 */
class InventarioFisicoController extends Controller
{
    /**
     * Lists all InventarioFisico entities.
     * @Route("/inventariofisico", name="inventario_fisico")
     * @Method("GET")
     * @Breadcrumb(title="Inventarios Físicos", routeName="inventario_fisico")
     */
    public function indexAction(Request $request)
    {
        $filter = new InventarioFisicoFilterModel();

        $form = $this->createForm(new InventarioFisicoFilter(), $filter, array(
            'action' => $this->generateUrl('inventariofisico'),
        ));

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $entities = $this->get('doctrine.orm.entity_manager')
                ->getRepository('BusetaBodegaBundle:InventarioFisico')->filter($filter);
        } else {
            $entities = $this->get('doctrine.orm.entity_manager')
                ->getRepository('BusetaBodegaBundle:InventarioFisico')->filter();
        }

        $paginator = $this->get('knp_paginator');
        $entities = $paginator->paginate(
            $entities,
            $request->query->get('page', 1),
            10
        );

        return $this->render('BusetaBodegaBundle:InventarioFisico:index.html.twig', array(
            'entities'      => $entities,
            'filter_form'   => $form->createView(),
        ));
    }

    public function procesarInventarioFisicoAction(InventarioFisico $inventarioFisico)
    {
        $manager = $this->get('buseta.bodega.inventariofisico.manager');
        if (true === $result = $manager->procesar($inventarioFisico)) {
            $this->get('session')->getFlashBag()->add(
                'success',
                'Se ha procesado el Inventario Físico de forma correcta.'
            );

            return $this->redirect(
                $this->generateUrl('inventariofisico_show', array('id' => $inventarioFisico->getId()))
            );
        } else {
            $this->get('session')->getFlashBag()->add(
                'danger',
                sprintf('Ha ocurrido un error al procesar el Inventario Físico.', $result)
            );

            return $this->redirect(
                $this->generateUrl('inventariofisico_show', array('id' => $inventarioFisico->getId()))
            );
        }
    }

    public function completarInventarioFisicoAction(InventarioFisico $inventarioFisico)
    {
        $manager = $this->get('buseta.bodega.inventariofisico.manager');
        if (true === $result = $manager->completar($inventarioFisico)) {
            $this->get('session')->getFlashBag()->add(
                'success',
                'Se ha completado el Inventario Físico de forma correcta.'
            );

            return $this->redirect(
                $this->generateUrl('inventariofisico_show', array('id' => $inventarioFisico->getId()))
            );
        } else {
            $this->get('session')->getFlashBag()->add(
                'danger',
                sprintf('Ha ocurrido un error al completar Inventario Fisico.', $result)
            );

            return $this->redirect(
                $this->generateUrl('inventariofisico_show', array('id' => $inventarioFisico->getId()))
            );
        }
    }

    /**
     * Creates a new InventarioFisico entity.
     *
     * @Route("/create", name="inventariofisicos_inventariofisico_create", methods={"POST"}, options={"expose":true})
     * @Security("is_granted('create', 'Buseta\\BodegaBundle\\Entity\\InventarioFisico')")
     * @Breadcrumb(title="Crear Nuevo Inventario Físico", routeName="inventariofisicos_inventariofisico_create")
     */
    public function createAction(Request $request)
    {
        $inventariofisicoModel = new InventarioFisicoModel();
        $form = $this->createCreateForm($inventariofisicoModel);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em     = $this->get('doctrine.orm.entity_manager');
            $trans  = $this->get('translator');
            $logger = $this->get('logger');

            try {
                $entity = $inventariofisicoModel->getEntityData();
                $entity->setEstado(BusetaBodegaDocumentStatus::DOCUMENT_STATUS_DRAFT);
                $em->persist($entity);
                $em->flush();

                // Creando nuevamente el formulario con los datos actualizados de la entidad
                $form = $this->createEditForm(new InventarioFisicoModel($entity));
                $renderView = $this->renderView('@BusetaBodega/InventarioFisico/form_template.html.twig', array(
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
                    'message' => $trans->trans('messages.create.error.%key%', array('key' => 'InventarioFisico de Compra'), 'BusetaBodegaBundle')
                ), 500);
            }
        }

        $renderView = $this->renderView('@BusetaBodega/InventarioFisico/form_template.html.twig', array(
            'form'     => $form->createView(),
        ));

        return new JsonResponse(array('view' => $renderView));
    }

    /**
     * Creates a form to create a InventarioFisico entity.
     *
     * @param InventarioFisicoModel $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(InventarioFisicoModel $entity)
    {
        $form = $this->createForm('bodega_inventariofisico_type', $entity, array(
            'action' => $this->generateUrl('inventariofisicos_inventariofisico_create'),
            'method' => 'POST',
        ));

        return $form;
    }

    /**
     * Displays a form to create a new InventarioFisico entity.
     *
     * @Route("/new", name="inventariofisicos_inventariofisico_new", methods={"GET"}, options={"expose":true})
     * @Security("is_granted('create', 'Buseta\\BodegaBundle\\Entity\\InventarioFisico')")
     * @Breadcrumb(title="Crear Nuevo Inventario Físico", routeName="inventariofisicos_inventariofisico_new")
     */
    public function newAction()
    {
        $form   = $this->createCreateForm(new InventarioFisicoModel());

        $em = $this->getDoctrine()->getManager();
        $productos = $em->getRepository('BusetaBodegaBundle:InventarioFisico')->findAll();

        return $this->render('@BusetaBodega/InventarioFisico/new.html.twig', array(
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a InventarioFisico entity.
     * @Route("/{id}/show", name="inventariofisico_show", methods={"GET"}, options={"expose":true})
     * @Security("is_granted('show', inventarioFisico)")
     * @Breadcrumb(title="Ver Datos de Inventario Físico", routeName="inventariofisico_show", routeParameters={"id"})
     */
    public function showAction(InventarioFisico $inventarioFisico)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BusetaBodegaBundle:InventarioFisico')->find($inventarioFisico->getId());

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find InventarioFisico entity.');
        }

        $deleteForm = $this->createDeleteForm($inventarioFisico->getId());

        return $this->render('BusetaBodegaBundle:InventarioFisico:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),        ));
    }

    /**
     * Displays a form to edit an existing InventarioFisico entity.
     *
     * @Route("/{id}/edit", name="inventariofisicos_inventariofisico_edit", methods={"GET"}, options={"expose":true})
     * @Security("is_granted('edit', inventariofisico)")
     * @Breadcrumb(title="Modificar Inventario Físico", routeName="inventariofisicos_inventariofisico_edit", routeParameters={"id"})
     */
    public function editAction(InventarioFisico $inventariofisico)
    {
        $editForm = $this->createEditForm(new InventarioFisicoModel($inventariofisico));
        $deleteForm = $this->createDeleteForm($inventariofisico->getId());

        return $this->render('BusetaBodegaBundle:InventarioFisico:edit.html.twig', array(
            'entity'        => $inventariofisico,
            'edit_form'     => $editForm->createView(),
            'delete_form'   => $deleteForm->createView(),
        ));
    }

    /**
     * Creates a form to edit a InventarioFisico entity.
     *
     * @param InventarioFisicoModel $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(InventarioFisicoModel $entity)
    {
        $form = $this->createForm('bodega_inventariofisico_type', $entity, array(
            'action' => $this->generateUrl('inventariofisicos_inventariofisico_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        return $form;
    }

    /**
     * Edits an existing InventarioFisico entity.
     *
     * @Route("/{id}/update", name="inventariofisicos_inventariofisico_update", methods={"POST","PUT"}, options={"expose":true})
     * @Breadcrumb(title="Modificar Inventario Físico", routeName="inventariofisicos_inventariofisico_update", routeParameters={"id"})
     */
    public function updateAction(Request $request, InventarioFisico $inventariofisico)
    {
        $inventariofisicoModel = new InventarioFisicoModel($inventariofisico);
        $editForm = $this->createEditForm($inventariofisicoModel);

        $editForm->handleRequest($request);
        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em     = $this->get('doctrine.orm.entity_manager');
            $trans  = $this->get('translator');
            $logger = $this->get('logger');

            try {
                $inventariofisico->setModelData($inventariofisicoModel);
                $em->flush();

                $renderView = $this->renderView('@BusetaBodega/InventarioFisico/form_template.html.twig', array(
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
                    'message' => $trans->trans('messages.update.error.%entidad%', array('entidad' => 'InventarioFisico de Compra'), 'BusetaBodegaBundle')
                ), 500);
            }
        }

        $renderView = $this->renderView('@BusetaBodega/InventarioFisico/form_template.html.twig', array(
            'form'     => $editForm->createView(),
        ));

        return new JsonResponse(array('view' => $renderView));
    }

    /**
     * Deletes a InventarioFisico entity.
     *
     * @Route("/{id}/delete", name="inventariofisico_delete")
     * @Security("is_granted('delete', inventariofisico)")
     * @Method({"DELETE", "GET"})
     */
    public function deleteAction(InventarioFisico $inventariofisico, Request $request)
    {
        $trans = $this->get('translator');
        $deleteForm = $this->createDeleteForm($inventariofisico->getId());

        $deleteForm->handleRequest($request);
        if($deleteForm->isSubmitted() && $deleteForm->isValid()) {
            try {
                $em = $this->get('doctrine.orm.entity_manager');

                $em->remove($inventariofisico);
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

        $renderView =  $this->renderView('@BusetaBodega/InventarioFisico/delete_modal.html.twig', array(
            'entity' => $inventariofisico,
            'form' => $deleteForm->createView(),
        ));

        if($request->isXmlHttpRequest()) {
            return new JsonResponse(array('view' => $renderView));
        }
        return $this->redirect($this->generateUrl('inventariofisico'));
    }

    /**
     * Creates a form to delete a InventarioFisico entity by id.
     *
     * @param mixed $id The entity id
     *º
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('inventariofisico_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
