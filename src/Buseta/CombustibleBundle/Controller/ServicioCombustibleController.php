<?php

namespace Buseta\CombustibleBundle\Controller;

use Buseta\BodegaBundle\Entity\BitacoraAlmacen;
use Buseta\BodegaBundle\Event\BitacoraEvents;
use Buseta\BodegaBundle\Event\LegacyBitacoraEvent;
use Buseta\BodegaBundle\Extras\FuncionesExtras;
use Buseta\CombustibleBundle\Entity\ServicioCombustible;
use Buseta\CombustibleBundle\Form\Filter\ServicioCombustibleFilter;
use Buseta\CombustibleBundle\Form\Model\ServicioCombustibleFilterModel;
use Buseta\CombustibleBundle\Form\Model\ServicioCombustibleModel;
use Buseta\CombustibleBundle\Form\Type\ServicioCombustibleType;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Response;
use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;

/**
 * ServicioCombustible controller.
 *
 * @Route("/servicioCombustible")
 *
 * @Breadcrumb(title="Inicio", routeName="core_homepage")
 * @Breadcrumb(title="Módulo de Combustible", routeName="servicioCombustible")
 */
class ServicioCombustibleController extends Controller
{
    /**
     * Lists all ServicioCombustible entities.
     *
     * @Route("/", name="servicioCombustible")
     *
     * @Method("GET")
     *
     * @Breadcrumb(title="Servicios de Combustibles", routeName="servicioCombustible")
     */
    public function indexAction(Request $request)
    {
        $filter = new ServicioCombustibleFilterModel();

        $form = $this->createForm(
            new ServicioCombustibleFilter(),
            $filter,
            array(
                'action' => $this->generateUrl('servicioCombustible'),
            )
        );

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entities = $this->get('doctrine.orm.entity_manager')
                ->getRepository('BusetaCombustibleBundle:ServicioCombustible')->filter($filter);
        } else {
            $entities = $this->get('doctrine.orm.entity_manager')
                ->getRepository('BusetaCombustibleBundle:ServicioCombustible')->filter();
        }

        $paginator = $this->get('knp_paginator');
        $entities = $paginator->paginate(
            $entities,
            $request->query->get('page', 1),
            10
        );

        return $this->render(
            'BusetaCombustibleBundle:ServicioCombustible:index.html.twig',
            array(
                'entities' => $entities,
                'filter_form' => $form->createView(),
            )
        );
    }

    /**
     * Displays a form to create a new ServicioCombustible entity.
     *
     * @Route("/new", name="servicioCombustible_new", methods={"GET"}, options={"expose":true})
     *
     * @Breadcrumb(title="Crear Nuevo Servicio de Combustible", routeName="servicioCombustible_new")
     */
    public function newAction()
    {
        $entity = new ServicioCombustibleModel();
        $form = $this->createCreateForm($entity);
        return $this->render(
            'BusetaCombustibleBundle:ServicioCombustible:new.html.twig',
            array(
                'entity' => $entity,
                'form' => $form->createView()
            )
        );
    }

    /**
     * Creates a form to create a ServicioCombustible entity.
     *
     * @param ServicioCombustibleModel $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(ServicioCombustibleModel $entity)
    {
        $form = $this->createForm(
            'combustible_servicio_combustible',
            $entity,
            array(
                'action' => $this->generateUrl('servicioCombustible_create'),
                'method' => 'POST',
            )
        );

        return $form;
    }

    /**
     * Creates a new ServicioCombustible entity.
     *
     * @param Request $request
     *
     * @return RedirectResponse|Response
     *
     * @Route("/create", name="servicioCombustible_create")
     * @Method({"POST"})
     *
     * @Breadcrumb(title="Crear Nuevo Servicio de Combustible", routeName="servicioCombustible_create")
     */
    public function createAction(Request $request)
    {
        $entityModel = new ServicioCombustibleModel();
        $form = $this->createCreateForm($entityModel);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $servicioCombustibleManager = $this->get('buseta.combustible.servicio_combustible.manager');

            if ($servicioCombustible = $servicioCombustibleManager->create($entityModel)) {
                $this->get('session')->getFlashBag()->add(
                    'success',
                    'Se ha creado el Servicio de Combustible satisfactoriamente.'
                );

                return $this->redirect(
                    $this->generateUrl('servicioCombustible_show', array('id' => $servicioCombustible->getId()))
                );
            } else {
                $this->get('session')->getFlashBag()->add(
                    'danger',
                    'Ha ocurrido un error al intentar crear Servicio Combustible'
                );
            }
        }

        return $this->render(
            'BusetaCombustibleBundle:ServicioCombustible:new.html.twig',
            array(
                'entity' => $entityModel,
                'form' => $form->createView(),
            )
        );
    }

    /**
     * Finds and displays a ServicioCombustible entity.
     *
     * @Route("/{id}/show", name="servicioCombustible_show")
     * @Method("GET")
     *
     * @Breadcrumb(title="Ver Datos de Servicio de Combustible", routeName="servicioCombustible_show", routeParameters={"id"})
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BusetaCombustibleBundle:ServicioCombustible')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ServicioCombustible entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render(
            'BusetaCombustibleBundle:ServicioCombustible:show.html.twig',
            array(
                'entity' => $entity,
                'delete_form' => $deleteForm->createView(),
            )
        );
    }

    /**
     * Deletes a ServicioCombustible entity.
     *
     * @Route("/{id}/delete", name="servicioCombustible_delete", options={"expose": true})
     * @Method({"DELETE", "GET", "POST"})
     */
    public function deleteAction(ServicioCombustible $servicioCombustible, Request $request)
    {
        $trans = $this->get('translator');
        $deleteForm = $this->createDeleteForm($servicioCombustible->getId());

        $deleteForm->handleRequest($request);
        if ($deleteForm->isSubmitted() && $deleteForm->isValid()) {
            try {
                $em = $this->get('doctrine.orm.entity_manager');

                $em->remove($servicioCombustible);
                $em->flush();

                $message = $trans->trans('messages.delete.success', array(), 'BusetaCombustibleBundle');

                if ($request->isXmlHttpRequest()) {
                    return new JsonResponse(
                        array(
                            'message' => $message,
                        ), 202
                    );
                } else {
                    $this->get('session')->getFlashBag()->add('success', $message);
                }
            } catch (\Exception $e) {
                $message = $trans->trans(
                    'messages.delete.error.%key%',
                    array('key' => 'ServicioCombustible'),
                    'BusetaBodegaBundle'
                );
                $this->get('logger')->addCritical(sprintf($message.' Detalles: %s', $e->getMessage()));

                if ($request->isXmlHttpRequest()) {
                    return new JsonResponse(
                        array(
                            'message' => $message,
                        ), 500
                    );
                }
            }
        }

        $renderView = $this->renderView(
            '@BusetaBuses/ServicioCombustible/delete_modal.html.twig',
            array(
                'entity' => $servicioCombustible,
                'form' => $deleteForm->createView(),
            )
        );

        if ($request->isXmlHttpRequest()) {
            return new JsonResponse(array('view' => $renderView));
        }

        return $this->redirect($this->generateUrl('servicioCombustible'));
    }

    /**
     * Creates a form to delete a ServicioCombustible entity by id.
     *
     * @param mixed $id The entity id
     *
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('servicioCombustible_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * Displays a form to edit an existing Tercero entity.
     *
     * @param ServicioCombustible $servicioCombustible
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Route("/{id}/edit", name="servicioCombustible_edit")
     * @Method("GET")
     * @Breadcrumb(title="Modificar Servicio de Combustible", routeName="servicioCombustible_edit", routeParameters={"id"})
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BusetaCombustibleBundle:ServicioCombustible')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ServicioCombustible entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render(
            'BusetaCombustibleBundle:ServicioCombustible:edit.html.twig',
            array(
                'entity' => $entity,
                'edit_form' => $editForm->createView(),
                'delete_form' => $deleteForm->createView(),
            )
        );
    }

    /**
     * Creates a form to edit a Autobus entity.
     *
     * @param ServicioCombustible $servicioCombustible The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(ServicioCombustible $entity)
    {
        $form = $this->createForm(
            new ServicioCombustibleType(),
            $entity,
            array(
                'action' => $this->generateUrl('servicioCombustible_update', array('id' => $entity->getId())),
                'method' => 'PUT',
            )
        );

        return $form;
    }

    /**
     * Edits an existing ServicioCombustible entity.
     *
     * @Route("/{id}/update", name="servicioCombustible_update", options={"expose": true})
     * @Method({"POST", "PUT"})
     * @Breadcrumb(title="Modificar Servicios de Combustibles", routeName="servicioCombustible_update", routeParameters={"id"})
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BusetaCombustibleBundle:ServicioCombustible')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ServicioCombustible entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('servicioCombustible_show', array('id' => $id)));
        }

        return $this->render(
            'BusetaCombustibleBundle:ServicioCombustible:edit.html.twig',
            array(
                'entity' => $entity,
                'edit_form' => $editForm->createView(),
                'delete_form' => $deleteForm->createView(),
            )
        );
    }

    /**
     * Find Chofer and Bus when change select Boleta.
     *
     * @param Request $request
     *
     * @return JsonResponse
     *
     * @Route("/find_chofer_bus", name="chofer_bus_ajax", options={"expose": true})
     * @Method({"GET"})
     */
    public function findChoferBusAction(Request $request)
    {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            return new Response('Acceso Denegado', 403);
        }

        if (!$request->isXmlHttpRequest()) {
            return new Response('No es una petición Ajax', 500);
        }

        $em = $this->get('doctrine.orm.entity_manager');
        $chofer = $em->getRepository('BusetaBusesBundle:Chofer')->findOneByCedula(
            $request->query->get('cedula_chofer')
        );
        $autobus = $em->getRepository('BusetaBusesBundle:Vehiculo')->findOneByNumero(
            $request->query->get('numero_bus')
        );
        $chofer_id = "";
        $bus_id = "";
        if ($chofer != null) {
            $chofer_id = $chofer->getId();
        }
        if ($autobus != null) {
            $bus_id = $autobus->getId();
        }

        return new JsonResponse(
            array(
                'chofer' => $chofer_id,
                'autobus' => $bus_id,
            )
        );
    }

}
