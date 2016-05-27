<?php

namespace Buseta\BusesBundle\Controller;

use Buseta\BusesBundle\Entity\ArchivoAdjunto;
use Buseta\BusesBundle\Form\Filter\VehiculoFilter;
use Buseta\BusesBundle\Form\Model\ArchivoAdjuntoModel;
use Buseta\BusesBundle\Form\Model\VehiculoModel;
use Buseta\BusesBundle\Form\Model\VehiculoFilterModel;
use Buseta\BusesBundle\Entity\Vehiculo;

use Buseta\BusesBundle\Form\Type\ArchivoAdjuntoType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Response;
use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;
/**
 * Vehiculo controller.
 *
 * @Route("/vehiculo")
 * @Breadcrumb(title="Inicio", routeName="core_homepage")
 * @Breadcrumb(title="Módulo de Vehículos", routeName="busetabuses_dashboard")
 */
class VehiculoController extends Controller
{
    /**
     * Module Vehiculos entity.
     */
    public function principalAction()
    {
        return $this->render('BusetaBusesBundle:Default:principal.html.twig');
    }

    /**
     * Lists all Vehiculo entities.
     *
     * @Route("/", name="vehiculo")
     * @Method("GET")
     * @Breadcrumb(title="Listado de Vehículos", routeName="vehiculo")
     */
    public function indexAction(Request $request)
    {
        $filter = new VehiculoFilterModel();

        $form = $this->createForm(new VehiculoFilter(), $filter, array(
            'action' => $this->generateUrl('vehiculo'),
        ));

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $entities = $this->get('doctrine.orm.entity_manager')
                ->getRepository('BusetaBusesBundle:Vehiculo')->filter($filter);
        } else {
            $entities = $this->get('doctrine.orm.entity_manager')
                ->getRepository('BusetaBusesBundle:Vehiculo')->filter();
        }

        $paginator = $this->get('knp_paginator');
        $entities = $paginator->paginate(
            $entities,
            $request->query->get('page', 1),
            10
        );

        return $this->render('BusetaBusesBundle:Vehiculo:index.html.twig', array(
            'entities'      => $entities,
            'filter_form'   => $form->createView(),
        ));
    }

    /**
     * Creates a new Vehiculo entity.
     *
     * @Route("/create", name="vehiculo_create", methods={"POST"}, options={"expose":true})
     * @Breadcrumb(title="Crear Nuevo Vehículos", routeName="vehiculo_create")
     */
    public function createAction(Request $request)
    {
        $basicosModel = new VehiculoModel();
        $form = $this->createCreateForm($basicosModel);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em     = $this->get('doctrine.orm.entity_manager');
            $trans  = $this->get('translator');
            $logger = $this->get('logger');

            try {
                $entity = $basicosModel->getEntityData();
                $em->persist($entity);
                $em->flush();

                // Creando nuevamente el formulario con los datos actualizados de la entidad
                $form = $this->createEditForm(new VehiculoModel($entity));
                $renderView = $this->renderView('@BusetaBuses/Vehiculo/form_template.html.twig', array(
                    'form'   => $form->createView(),
                ));

                return new JsonResponse(array(
                    'view' => $renderView,
                    'message' => $trans->trans('messages.create.success', array(), 'BusetaBusesBundle')
                ), 201);
            } catch (\Exception $e) {
                $logger->addCritical(sprintf(
                    $trans->trans('', array(), 'BusetaBusesBundle') . '. Detalles: %s',
                    $e->getMessage()
                ));

                return new JsonResponse(array(
                    'message' => $trans->trans('messages.create.error.%key%', array('key' => 'Datos Basicos de Vehiculo'), 'BusetaBusesBundle')
                ), 500);
            }
        }

        $renderView = $this->renderView('@BusetaBuses/Vehiculo/form_template.html.twig', array(
            'form'     => $form->createView(),
        ));

        return new JsonResponse(array('view' => $renderView));
    }

    /**
     * Creates a form to create a Vehiculo entity.
     *
     * @param VehiculoModel $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(VehiculoModel $entity)
    {
        $form = $this->createForm('buses_vehiculo', $entity, array(
            'action' => $this->generateUrl('vehiculo_create'),
            'method' => 'POST',
        ));

        return $form;
    }

    /**
     * Displays a form to create a new Vehiculo entity.
     *
     * @Route("/new", name="vehiculo_new", methods={"GET"}, options={"expose":true})
     * @Breadcrumb(title="Crear Nuevo Vehículos", routeName="vehiculo_new")
     */
    public function newAction()
    {
        $form   = $this->createCreateForm(new VehiculoModel(new Vehiculo()));

        return $this->render('BusetaBusesBundle:Vehiculo:new.html.twig', array(
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Vehiculo entity.
     *
     * @Route("/{id}/show", name="vehiculo_show")
     * @Method("GET")
     * @Breadcrumb(title="Ver Datos de Vehículo", routeName="vehiculo_show", routeParameters={"id"})
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BusetaBusesBundle:Vehiculo')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Vehiculo entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('BusetaBusesBundle:Vehiculo:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Tercero entity.
     *
     * @param Vehiculo $vehiculo
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Route("/{id}/edit", name="vehiculo_edit")
     * @Method("GET")
     * @Breadcrumb(title="Modificar Vehículo", routeName="vehiculo_edit", routeParameters={"id"})
     */
    public function editAction(Vehiculo $vehiculo)
    {
        $model = new VehiculoModel($vehiculo);
        $editForm = $this->createEditForm($model);

        return $this->render('BusetaBusesBundle:Vehiculo:edit.html.twig', array(
            'entity'      => $vehiculo,
            'edit_form'   => $editForm->createView(),
        ));
    }

    /**
     * Creates a form to edit a Vehiculo entity.
     *
     * @param VehiculoModel $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(VehiculoModel $entity)
    {
        $form = $this->createForm('buses_vehiculo', $entity, array(
            'action' => $this->generateUrl('vehiculo_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        return $form;
    }

    /**
     * Edits an existing Vehiculo entity.
     *
     * @Route("/{id}/update", name="vehiculo_update", options={"expose": true})
     * @Method({"POST", "PUT"})
     * @Breadcrumb(title="Modificar Vehículo", routeName="vehiculo_update", routeParameters={"id"})
     */
    public function updateAction(Request $request, Vehiculo $vehiculo)
    {
        $vehiculoModel = new VehiculoModel($vehiculo);
        $editForm = $this->createEditForm($vehiculoModel);

        $editForm->handleRequest($request);
        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em     = $this->get('doctrine.orm.entity_manager');
            $trans  = $this->get('translator');
            $logger = $this->get('logger');

            try {
                $vehiculo->setModelData($vehiculoModel);
                $em->flush();

                $editForm = $this->createEditForm(new VehiculoModel($vehiculo));
                $renderView = $this->renderView('@BusetaBuses/Vehiculo/form_template.html.twig', array(
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
                    'message' => $trans->trans('messages.update.error.%key%', array('key' => 'Vehiculo Basico'), 'BusetaBodegaBundle')
                ), 500);
            }
        }

        $renderView = $this->renderView('@BusetaBuses/Vehiculo/form_template.html.twig', array(
            'form'     => $editForm->createView(),
        ));

        return new JsonResponse(array('view' => $renderView));
    }

    /**
     * Deletes a Vehiculo entity.
     *
     * @Route("/{id}/delete", name="vehiculo_delete", options={"expose": true})
     * @Method({"DELETE", "GET", "POST"})
     */
    public function deleteAction(Vehiculo $vehiculo, Request $request)
    {
        $trans = $this->get('translator');
        $deleteForm = $this->createDeleteForm($vehiculo->getId());

        $deleteForm->handleRequest($request);
        if($deleteForm->isSubmitted() && $deleteForm->isValid()) {
            try {
                $em = $this->get('doctrine.orm.entity_manager');

                $em->remove($vehiculo);
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
                $message = $trans->trans('messages.delete.error.%key%', array('key' => 'Vehiculo'), 'BusetaBodegaBundle');
                $this->get('logger')->addCritical(sprintf($message.' Detalles: %s', $e->getMessage()));

                if($request->isXmlHttpRequest()) {
                    return new JsonResponse(array(
                        'message' => $message,
                    ), 500);
                }
            }
        }

        $renderView =  $this->renderView('@BusetaBuses/Vehiculo/delete_modal.html.twig', array(
            'entity' => $vehiculo,
            'form' => $deleteForm->createView(),
        ));

        if($request->isXmlHttpRequest()) {
            return new JsonResponse(array('view' => $renderView));
        }
        return $this->redirect($this->generateUrl('vehiculo'));
    }

    /**
     * Creates a form to delete a Vehiculo entity by id.
     *
     * @param mixed $id The entity id
     *º
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('vehiculo_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->getForm()
            ;
    }

    /**
     * @param Request $request
     * @param Vehiculo $vehiculo
     *
     * @return JsonResponse
     *
     * @Route("/{id}/archivoAdjunto", name="vehiculo_archivoadjunto", options={"expose": true})
     * @Method({"GET", "POST"})
     */
    public function newArchivoAdjunto(Request $request, Vehiculo $vehiculo)
    {
        $model = new ArchivoAdjuntoModel();
        $model->setVehiculo($vehiculo);
        $form = $this->createArchivoAdjuntoForm($model);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->get('doctrine.orm.entity_manager');

            try {
                $archivoAdjunto = $model->getEntityData();
                $vehiculo->addArchivosAdjunto($archivoAdjunto);

                $em->persist($vehiculo);
                $em->flush();

                return new JsonResponse(array(
                    'message' => 'Se ha adicionado el archivo satisfatoriamente.'
                ), 201);
            } catch (\Exception $ex) {
                $this->get('logger')->addCritical(sprintf('Ha ocurrido un error adicionando el archivo. Detalles: %s', $ex->getMessage()));

                return new JsonResponse(array(
                    'message' => 'Ha ocurrido un error adicionando el archivo.'
                ), 500);
            }
        }

        $view = $this->renderView('@BusetaBuses/Vehiculo/form_template_archivoadjunto.html.twig', array(
            'form'   => $form->createView(),
            'entity' => $vehiculo,
        ));
        return new JsonResponse(array(
            'view' => $view
        ), 200);
    }

    /**
     * @param ArchivoAdjuntoModel $model
     * @return \Symfony\Component\Form\Form
     */
    private function createArchivoAdjuntoForm(ArchivoAdjuntoModel $model)
    {
        $form = $this->createForm(new ArchivoAdjuntoType(), $model, array(
            'method' => 'POST',
            'action' => $this->generateUrl('vehiculo_archivoadjunto', array('id' => $model->getVehiculo()->getId()))
        ));

        return $form;
    }

    /**
     * @param Request $request
     * @param ArchivoAdjunto $archivoAdjunto
     * @param Vehiculo $vehiculo
     *
     * @return JsonResponse
     *
     * @Route("/{id}/archivoAdjunto/{archivo}/delete", name="vehiculo_archivoadjunto_delete", options={"expose": true})
     * @Method("DELETE")
     * @ParamConverter("vehiculo")
     * @ParamConverter("archivoAdjunto", options={"mapping": {"archivo": "id"}})
     */
    public function deleteArchivoAdjuntoAction(Request $request, Vehiculo $vehiculo, ArchivoAdjunto $archivoAdjunto)
    {
        if ($archivoAdjunto->getVehiculo()->getId() !== $vehiculo->getId()) {
            new JsonResponse(array('message' => 'El Archivo Adjunto no se corresponde con el Vehiculo activo.'), 500);
        }

        $em = $this->get('doctrine.orm.entity_manager');
        $logger = $this->get('logger');
        try {
            $em->remove($archivoAdjunto);
            $em->flush();

            return new JsonResponse(array('message' => 'Se ha eliminado el Archivo Adjunto satisfactoriamente.'), 202);
        } catch (\Exception $e) {
            $logger->addCritical(sprintf('Ha ocurrido un error eliminando el Archivo Adjunto con id: %d. Detalles: %s', $archivoAdjunto->getId(), $e->getMessage()));
            return new JsonResponse(array('message' => 'Ha ocurrido un error eliminando el Archivo Adjunto.'), 202);
        }
    }

    /**
     * @param Request $request
     * @param Vehiculo $vehiculo
     *
     * @return JsonResponse
     *
     * @Route("/select_marca_modelo", name="vehiculo_ajax_marca_modelo", options={"expose": true})
     * @Method({"GET"})
     */
    public function selectMarcaModeloAction(Request $request) {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY'))
            return new \Symfony\Component\HttpFoundation\Response('Acceso Denegado', 403);

        if (!$request->isXmlHttpRequest())
            return new \Symfony\Component\HttpFoundation\Response('No es una petición Ajax', 500);

        $em = $this->getDoctrine()->getManager();

        $em = $this->getDoctrine()->getManager();
        $modelos = $em->getRepository('BusetaNomencladorBundle:Modelo')->findBy(array(
            'marca' => $request->query->get('marca_id')
        ));

        $json = array();
        foreach ($modelos as $modelo)
        {
            $json[] = array(
                'id' => $modelo->getId(),
                'valor' => $modelo->getValor(),
            );
        }

        return new \Symfony\Component\HttpFoundation\Response(json_encode($json), 200);
    }
}
