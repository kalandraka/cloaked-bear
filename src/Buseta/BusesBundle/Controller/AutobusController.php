<?php

namespace Buseta\BusesBundle\Controller;

use Buseta\BusesBundle\Entity\ArchivoAdjunto;
use Buseta\BusesBundle\Form\Filter\AutobusFilter;
use Buseta\BusesBundle\Form\Model\ArchivoAdjuntoModel;
use Buseta\BusesBundle\Form\Model\AutobusBasicoModel;
use Buseta\BusesBundle\Form\Model\AutobusFilterModel;
use Buseta\BusesBundle\Entity\Autobus;
use Buseta\BusesBundle\Form\Type\ArchivoAdjuntoType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;
/**
 * Autobus controller.
 *
 * @Route("/autobus")
 * @Breadcrumb(title="Inicio", routeName="core_homepage")
 * @Breadcrumb(title="Módulo de Vehículos", routeName="busetabuses_dashboard")
 */
class AutobusController extends Controller
{
    /**
     * Lists all Autobuses entities.
     *
     * @Route("/", name="busetabuses_autobus")
     * @Breadcrumb(title="Listado de Autobuses", routeName="busetabuses_autobus")
     */
    public function indexAction(Request $request)
    {
        $filter = new AutobusFilterModel();

        $form = $this->createForm(new AutobusFilter(), $filter, array(
            'action' => $this->generateUrl('busetabuses_autobus'),
        ));

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $entities = $this->get('doctrine.orm.entity_manager')
                ->getRepository('BusetaBusesBundle:Autobus')->filter($filter);
        } else {
            $entities = $this->get('doctrine.orm.entity_manager')
                ->getRepository('BusetaBusesBundle:Autobus')->filter();
        }

        $paginator = $this->get('knp_paginator');
        $entities = $paginator->paginate(
            $entities,
            $request->query->get('page', 1),
            10
        );

        return $this->render('BusetaBusesBundle:Autobus:index.html.twig', array(
            'entities'      => $entities,
            'filter_form'   => $form->createView(),
        ));
    }

    /**
     * Creates a new Autobus entity.
     *
     * @Route("/create", name="busetabuses_autobus_create", options={"expose":true})
     * @Method({"POST"})
     * @Breadcrumb(title="Crear Nuevo Autobus", routeName="busetabuses_autobus_new")
     */
    public function createAction(Request $request)
    {
        $basicosModel = new AutobusBasicoModel();
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
                $form = $this->createEditForm(new AutobusBasicoModel($entity));
                $renderView = $this->renderView('@BusetaBuses/Autobus/form_template.html.twig', array(
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
                    'message' => $trans->trans('messages.create.error.%key%', array('key' => 'Datos Basicos de Autobus'), 'BusetaBusesBundle')
                ), 500);
            }
        }

        $renderView = $this->renderView('@BusetaBuses/Autobus/form_template.html.twig', array(
            'form'     => $form->createView(),
        ));

        return new JsonResponse(array('view' => $renderView));
    }

    /**
     * Creates a form to create a Autobus entity.
     *
     * @param AutobusBasicoModel $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(AutobusBasicoModel $entity)
    {
        $form = $this->createForm('buses_autobus_basico', $entity, array(
            'action' => $this->generateUrl('busetabuses_autobus_create'),
            'method' => 'POST',
        ));

        return $form;
    }

    /**
     * Displays a form to create a new Autobus entity.
     *
     * @Route("/new", name="busetabuses_autobus_new")
     * @Method("GET")
     * @Breadcrumb(title="Crear Nuevo Autobus", routeName="busetabuses_autobus_new")
     */
    public function newAction()
    {
        $form   = $this->createCreateForm(new AutobusBasicoModel(new Autobus()));

        return $this->render('BusetaBusesBundle:Autobus:new.html.twig', array(
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Autobus entity.
     *
     * @Route("/{id}/show", name="busetabuses_autobus_show")
     * @Method("GET")
     * @Breadcrumb(title="Ver Datos de Autobus", routeName="busetabuses_autobus_show", routeParameters={"id"})
     */
    public function showAction(Autobus $autobus)
    {
        $deleteForm = $this->createDeleteForm($autobus->getId());

        return $this->render('BusetaBusesBundle:Autobus:show.html.twig', array(
            'entity'      => $autobus,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Tercero entity.
     *
     * @param Autobus $autobus
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Route("/{id}/edit", name="busetabuses_autobus_edit")
     * @Method("GET")
     * @Breadcrumb(title="Modificar Autobus", routeName="busetabuses_autobus_edit", routeParameters={"id"})
     */
    public function editAction(Autobus $autobus)
    {
        $model = new AutobusBasicoModel($autobus);
        $editForm = $this->createEditForm($model);

        return $this->render('BusetaBusesBundle:Autobus:edit.html.twig', array(
            'entity'      => $autobus,
            'edit_form'   => $editForm->createView(),
        ));
    }

    /**
     * Creates a form to edit a Autobus entity.
     *
     * @param AutobusBasicoModel $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(AutobusBasicoModel $entity)
    {
        $form = $this->createForm('buses_autobus_basico', $entity, array(
            'action' => $this->generateUrl('busetabuses_autobus_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        return $form;
    }

    /**
     * Edits an existing Autobus entity.
     *
     * @Route("/{id}/update", name="busetabuses_autobus_update", options={"expose": true})
     * @Method({"POST", "PUT"})
     * @Breadcrumb(title="Modificar Autobus", routeName="busetabuses_autobus_edit", routeParameters={"id"})
     */
    public function updateAction(Request $request, Autobus $autobus)
    {
        $autobusModel = new AutobusBasicoModel($autobus);
        $editForm = $this->createEditForm($autobusModel);

        $editForm->handleRequest($request);
        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em     = $this->get('doctrine.orm.entity_manager');
            $trans  = $this->get('translator');
            $logger = $this->get('logger');

            try {
                $autobus->setModelDatas($autobusModel);
                $em->flush();

                $editForm = $this->createEditForm(new AutobusBasicoModel($autobus));
                $renderView = $this->renderView('@BusetaBuses/Autobus/form_template.html.twig', array(
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
                    'message' => $trans->trans('messages.update.error.%key%', array('key' => 'Autobus Basico'), 'BusetaBodegaBundle')
                ), 500);
            }
        }

        $renderView = $this->renderView('@BusetaBuses/Autobus/form_template.html.twig', array(
            'form'     => $editForm->createView(),
        ));

        return new JsonResponse(array('view' => $renderView));
    }

    /**
     * Deletes a Autobus entity.
     *
     * @Route("/{id}/delete", name="busetabuses_autobus_delete", options={"expose": true})
     * @Method({"DELETE", "GET", "POST"})
     */
    public function deleteAction(Autobus $autobus, Request $request)
    {
        $trans = $this->get('translator');
        $deleteForm = $this->createDeleteForm($autobus->getId());

        $deleteForm->handleRequest($request);
        if($deleteForm->isSubmitted() && $deleteForm->isValid()) {
            try {
                $em = $this->get('doctrine.orm.entity_manager');

                $em->remove($autobus);
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
                $message = $trans->trans('messages.delete.error.%key%', array('key' => 'Autobus'), 'BusetaBodegaBundle');
                $this->get('logger')->addCritical(sprintf($message.' Detalles: %s', $e->getMessage()));

                if($request->isXmlHttpRequest()) {
                    return new JsonResponse(array(
                        'message' => $message,
                    ), 500);
                }
            }
        }

        $renderView =  $this->renderView('@BusetaBuses/Autobus/delete_modal.html.twig', array(
            'entity' => $autobus,
            'form' => $deleteForm->createView(),
        ));

        if($request->isXmlHttpRequest()) {
            return new JsonResponse(array('view' => $renderView));
        }
        return $this->redirect($this->generateUrl('busetabuses_autobus'));
    }

    /**
     * Creates a form to delete a Autobus entity by id.
     *
     * @param mixed $id The entity id
     *º
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('busetabuses_autobus_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->getForm()
            ;
    }

    /**
     * @param Request $request
     * @param Autobus $autobus
     *
     * @return JsonResponse
     *
     * @Route("/{id}/archivo-adjunto", name="busetabuses_autobus_archivoadjunto", options={"expose": true})
     * @Method({"GET", "POST"})
     */
    public function newArchivoAdjunto(Request $request, Autobus $autobus)
    {
        $model = new ArchivoAdjuntoModel();
        $model->setAutobus($autobus);
        $form = $this->createArchivoAdjuntoForm($model);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->get('doctrine.orm.entity_manager');

            try {
                $archivoAdjunto = $model->getEntityData();
                $autobus->addArchivosAdjunto($archivoAdjunto);

                $em->persist($autobus);
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

        $view = $this->renderView('@BusetaBuses/Autobus/form_template_archivoadjunto.html.twig', array(
            'form'   => $form->createView(),
            'entity' => $autobus,
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
            'action' => $this->generateUrl('busetabuses_autobus_archivoadjunto', array('id' => $model->getAutobus()->getId()))
        ));

        return $form;
    }

    /**
     * @param Request $request
     * @param ArchivoAdjunto $archivoAdjunto
     * @param Autobus $autobus
     *
     * @return JsonResponse
     *
     * @Route("/{id}/archivo-adjunto/{archivo}/delete", name="busetabuses_autobus_archivoadjunto_delete",
     *     options={"expose": true})
     * @Method("DELETE")
     * @ParamConverter("autobus")
     * @ParamConverter("archivoAdjunto", options={"mapping": {"archivo": "id"}})
     */
    public function deleteArchivoAdjuntoAction(Request $request, Autobus $autobus, ArchivoAdjunto $archivoAdjunto)
    {
        if ($archivoAdjunto->getAutobus()->getId() !== $autobus->getId()) {
            new JsonResponse(array('message' => 'El Archivo Adjunto no se corresponde con el Autobus activo.'), 500);
        }

        $em = $this->get('doctrine.orm.entity_manager');
        $logger = $this->get('logger');
        try {
            $em->remove($archivoAdjunto);
            $em->flush();

            return new JsonResponse(array('message' => 'Se ha eliminado el Archivo Adjunto satisfactoriamente.'), 202);
        } catch (\Exception $e) {
            $logger->addCritical(sprintf(
                'Ha ocurrido un error eliminando el Archivo Adjunto con id: %d. Detalles: %s',
                $archivoAdjunto->getId(),
                $e->getMessage()
            ));

            return new JsonResponse(array('message' => 'Ha ocurrido un error eliminando el Archivo Adjunto.'), 202);
        }
    }
}
