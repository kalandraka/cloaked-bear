<?php

namespace Buseta\BusesBundle\Controller;

use Buseta\BusesBundle\Entity\Chofer;
use Buseta\BusesBundle\Form\Filter\ChoferFilter;
use Buseta\BusesBundle\Form\Model\ChoferFilterModel;
use Buseta\BusesBundle\Form\Type\ChoferType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Response;
use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;
/**
 * Chofer controller.
 *
 * @Route("/chofer")
 * @Breadcrumb(title="Inicio", routeName="core_homepage")
 * @Breadcrumb(title="Módulo de Vehículos", routeName="busetabuses_dashboard")
 */
class ChoferController extends Controller
{
    /**
     * Lists all Chofer entities.
     *
     * @Route("/", name="chofer")
     * @Method("GET")
     * @Breadcrumb(title="Listado de Choferes", routeName="chofer")
     */
    public function indexAction(Request $request)
    {
        $filter = new ChoferFilterModel();

        $form = $this->createForm(new ChoferFilter(), $filter, array(
            'action' => $this->generateUrl('chofer'),
        ));

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $entities = $this->get('doctrine.orm.entity_manager')
                ->getRepository('BusetaBusesBundle:Chofer')->filter($filter);
        } else {
            $entities = $this->get('doctrine.orm.entity_manager')
                ->getRepository('BusetaBusesBundle:Chofer')->filter();
        }

        $paginator = $this->get('knp_paginator');
        $entities = $paginator->paginate(
            $entities,
            $request->query->get('page', 1),
            10
        );

        return $this->render('BusetaBusesBundle:Chofer:index.html.twig', array(
            'entities'      => $entities,
            'filter_form'   => $form->createView(),
        ));
    }

    /**
     * Displays a form to create a new Chofer entity.
     *
     * @Route("/new", name="chofer_new", methods={"GET"}, options={"expose":true})
     * @Breadcrumb(title="Crear Nuevo Chofer", routeName="chofer_new")
     */
    public function newAction()
    {
        $entity = new Chofer();
        $form   = $this->createCreateForm($entity);

        return $this->render('BusetaBusesBundle:Chofer:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a Chofer entity.
     *
     * @param Chofer $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Chofer $entity)
    {
        $form = $this->createForm('buses_chofer', $entity, array(
            'action' => $this->generateUrl('chofer_create'),
            'method' => 'POST',
        ));

        return $form;
    }

    /**
     * Creates a new Chofer entity.
     *
     * @Route("/create", name="chofer_create", methods={"POST"}, options={"expose":true})
     * @Breadcrumb(title="Crear Nuevo Chofer", routeName="chofer_create")
     */
    public function createAction(Request $request)
    {
        $entity = new Chofer();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('chofer_show', array('id' => $entity->getId())));
        }

        return $this->render('BusetaBusesBundle:Chofer:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Chofer entity.
     *
     * @Route("/{id}/show", name="chofer_show")
     * @Method("GET")
     * @Breadcrumb(title="Ver Datos de Chofer", routeName="chofer_show", routeParameters={"id"})
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BusetaBusesBundle:Chofer')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Chofer entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('BusetaBusesBundle:Chofer:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Chofer entity.
     *
     * @Route("/{id}/delete", name="chofer_delete", options={"expose": true})
     * @Method({"DELETE", "GET", "POST"})
     */
    public function deleteAction(Chofer $chofer, Request $request)
    {
        $trans = $this->get('translator');
        $deleteForm = $this->createDeleteForm($chofer->getId());

        $deleteForm->handleRequest($request);
        if($deleteForm->isSubmitted() && $deleteForm->isValid()) {
            try {
                $em = $this->get('doctrine.orm.entity_manager');

                $em->remove($chofer);
                $em->flush();

                $message = $trans->trans('messages.delete.success', array(), 'BusetaBusesBundle');

                if($request->isXmlHttpRequest()) {
                    return new JsonResponse(array(
                        'message' => $message,
                    ), 202);
                }
                else {
                    $this->get('session')->getFlashBag()->add('success', $message);
                }
            } catch (\Exception $e) {
                $message = $trans->trans('messages.delete.error.%key%', array('key' => 'Chofer'), 'BusetaBodegaBundle');
                $this->get('logger')->addCritical(sprintf($message.' Detalles: %s', $e->getMessage()));

                if($request->isXmlHttpRequest()) {
                    return new JsonResponse(array(
                        'message' => $message,
                    ), 500);
                }
            }
        }

        $renderView =  $this->renderView('@BusetaBuses/Chofer/delete_modal.html.twig', array(
            'entity' => $chofer,
            'form' => $deleteForm->createView(),
        ));

        if($request->isXmlHttpRequest()) {
            return new JsonResponse(array('view' => $renderView));
        }
        return $this->redirect($this->generateUrl('chofer'));
    }

    /**
     * Creates a form to delete a Chofer entity by id.
     *
     * @param mixed $id The entity id
     *
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('chofer_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->getForm()
            ;
    }

    /**
     * Displays a form to edit an existing Tercero entity.
     *
     * @param Chofer $chofer
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Route("/{id}/edit", name="chofer_edit")
     * @Method("GET")
     * @Breadcrumb(title="Modificar Chofer", routeName="chofer_edit", routeParameters={"id"})
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BusetaBusesBundle:Chofer')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Chofer entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('BusetaBusesBundle:Chofer:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Creates a form to edit a Autobus entity.
     *
     * @param Chofer $chofer The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Chofer $entity)
    {
        $form = $this->createForm(new ChoferType(), $entity, array(
            'action' => $this->generateUrl('chofer_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        return $form;
    }

    /**
     * Edits an existing Chofer entity.
     *
     * @Route("/{id}/update", name="chofer_update", options={"expose": true})
     * @Method({"POST", "PUT"})
     * @Breadcrumb(title="Modificar Chofer", routeName="chofer_update", routeParameters={"id"})
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BusetaBusesBundle:Chofer')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Chofer entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('chofer_show', array('id' => $id)));
        }

        return $this->render('BusetaBusesBundle:Chofer:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
}
