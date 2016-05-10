<?php

namespace Buseta\TransitoBundle\Controller;

use Buseta\TransitoBundle\Entity\Accidente;
use Buseta\TransitoBundle\Entity\PenalAccidente;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Buseta\TransitoBundle\Form\Type\PenalAccidenteType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\BrowserKit\Response;
use Symfony\Component\HttpFoundation\JsonResponse;


/**
 * Class PenalAccidenteController
 * @package Buseta\TransitoBundle\Controller
 *
 * @Route("/penal_accidente")
 */
class PenalAccidenteController extends Controller
{
    /**
     * Creates a new PenalAccidente entity.
     *
     * @Route("/create/{accidente}", name="penal_accidente_create")
     * @ParamConverter("accidente", options={"mapping":{"accidente":"id"}})
     *
     * @Method("POST")
     */
    public function createAction(Accidente $accidente, Request $request)
    {
        $entity = new PenalAccidente();
        $entity->setAccidente($accidente);
        $accidente->setEstado('PENAL');
        $accidente->setParte('PENAL');
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

        $renderView = $this->renderView('BusetaTransitoBundle:PenalAccidente:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));

        return new JsonResponse(array('view' => $renderView));
    }

    /**
     * Creates a form to create a PenalAccidente entity.
     *
     * @param PenalAccidente $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(PenalAccidente $entity)
    {
        $form = $this->createForm(new PenalAccidenteType(), $entity, array(
            'action' => $this->generateUrl('penal_accidente_create', array('accidente' => $entity->getAccidente()->getId())),
            'method' => 'POST',
        ));

        return $form;
    }

    /**
     * Displays a form to create a new PenalAccidente entity.
     *
     * @Route("/new/{accidente}", name="penal_accidente_new", methods={"GET", "POST"}, options={"expose":true})
     * @ParamConverter("accidente", options={"mapping":{"accidente":"id"}})
     */
    public function newAction(Accidente $accidente)
    {
        $entity = new PenalAccidente();
        $entity->setAccidente($accidente);
        $form   = $this->createCreateForm($entity);

        $renderView = $this->renderView('BusetaTransitoBundle:PenalAccidente:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));

        return new JsonResponse(array('view' => $renderView));
    }

    /**
     * Displays a form to edit an existing PenalAccidente entity.
     *
     * @Route("/{id}/edit", name="penal_accidente_edit", options={"expose":true})
     *
     * @Method("GET")
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BusetaTransitoBundle:PenalAccidente')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find PenalAccidente entity.');
        }

        $editForm = $this->createEditForm($entity);

        $renderView = $this->renderView('BusetaTransitoBundle:PenalAccidente:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
        ));

        return new JsonResponse(array('view' => $renderView), 202);
    }

    /**
     * Creates a form to edit a PenalAccidente entity.
     *
     * @param PenalAccidente $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(PenalAccidente $entity)
    {
        $form = $this->createForm(new PenalAccidenteType(), $entity, array(
            'action' => $this->generateUrl('penal_accidente_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        return $form;
    }

    /**
     * Edits an existing PenalAccidente entity.
     *
     * @Route("/{id}", name="penal_accidente_update")
     *
     * @Method("PUT")
     * @Template("BusetaTransitoBundle:PenalAccidente:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BusetaTransitoBundle:PenalAccidente')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find PenalAccidente entity.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('penal_accidente_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
        );
    }
}
