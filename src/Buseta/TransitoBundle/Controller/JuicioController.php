<?php

namespace Buseta\TransitoBundle\Controller;

use Buseta\TransitoBundle\Entity\Accidente;
use Buseta\TransitoBundle\Entity\Juicio;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Buseta\TransitoBundle\Form\Type\JuicioType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\BrowserKit\Response;
use Symfony\Component\HttpFoundation\JsonResponse;


/**
 * Class JuicioController
 * @package Buseta\TransitoBundle\Controller
 *
 * @Route("/juicio")
 */
class JuicioController extends Controller
{
    /**
     * Creates a new Juicio entity.
     *
     * @Route("/create/{accidente}", name="juicio_create")
     * @ParamConverter("accidente", options={"mapping":{"accidente":"id"}})
     *
     * @Method("POST")
     */
    public function createAction(Accidente $accidente, Request $request)
    {
        $entity = new Juicio();
        $entity->setAccidente($accidente);
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->get('doctrine.orm.entity_manager');
            $trans = $this->get('translator');

            $em->persist($entity);
            $em->persist($accidente);
            $em->flush();

            return new JsonResponse(array(
                'message' => $trans->trans('messages.create.success', array(), 'BusetaTransitoBundle'),
            ), 201);
        }

        $renderView = $this->renderView('BusetaTransitoBundle:Juicio:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));

        return new JsonResponse(array('view' => $renderView));
    }

    /**
     * Creates a form to create a Juicio entity.
     *
     * @param Juicio $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Juicio $entity)
    {
        $form = $this->createForm(new JuicioType(), $entity, array(
            'action' => $this->generateUrl('juicio_create', array('accidente' => $entity->getAccidente()->getId())),
            'method' => 'POST',
        ));

        return $form;
    }

    /**
     * Displays a form to create a new Juicio entity.
     *
     * @Route("/new/{accidente}", name="juicio_new", methods={"GET", "POST"}, options={"expose":true})
     * @ParamConverter("accidente", options={"mapping":{"accidente":"id"}})
     */
    public function newAction(Accidente $accidente)
    {
        $entity = new Juicio();
        $entity->setAccidente($accidente);
        $form   = $this->createCreateForm($entity);

        $renderView = $this->renderView('BusetaTransitoBundle:Juicio:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));

        return new JsonResponse(array('view' => $renderView));
    }

    /**
     * Displays a form to edit an existing Juicio entity.
     *
     * @Route("/{id}/edit", name="juicio_edit", options={"expose":true})
     *
     * @Method("GET")
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BusetaTransitoBundle:Juicio')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Juicio entity.');
        }

        $editForm = $this->createEditForm($entity);

        $renderView = $this->renderView('BusetaTransitoBundle:Juicio:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
        ));

        return new JsonResponse(array('view' => $renderView), 202);
    }

    /**
     * Creates a form to edit a Juicio entity.
     *
     * @param Juicio $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Juicio $entity)
    {
        $form = $this->createForm(new JuicioType(), $entity, array(
            'action' => $this->generateUrl('juicio_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        return $form;
    }

    /**
     * Edits an existing Juicio entity.
     *
     * @Route("/{id}", name="juicio_update")
     *
     * @Method("PUT")
     * @Template("BusetaTransitoBundle:Juicio:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BusetaTransitoBundle:Juicio')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Juicio entity.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('juicio_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
        );
    }
}
