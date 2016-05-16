<?php

namespace Buseta\BodegaBundle\Controller;

use Buseta\BodegaBundle\Entity\Albaran;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Buseta\BodegaBundle\Entity\AlbaranLinea;

use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\BrowserKit\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Doctrine\ORM\EntityRepository;

/**
 * Class AlbaranLineaController
 * @package Buseta\BodegaBundle\Controller
 *
 * @Route("/albaran_linea")
 */
class AlbaranLineaController extends Controller
{
    /**
     * @param Albaran $albaran
     * @return Response
     *
     * @Route("/list/{albaran}", name="albaran_lineas_list", methods={"GET"}, options={"expose":true})
     * @ParamConverter("albaran", options={"mapping":{"albaran":"id"}})
     */
    public function listAction(Albaran $albaran, Request $request)
    {
        $entities = $this->get('doctrine.orm.entity_manager')
            ->getRepository('BusetaBodegaBundle:AlbaranLinea')
            ->findAllByAlbaranId($albaran->getId());

        $entities = $this->get('knp_paginator')
            ->paginate(
                $entities,
                $request->query->get('page', 1),
                10
            );

        return $this->render('@BusetaBodega/Albaran/Linea/list_template.html.twig', array(
            'entities' => $entities,
            'albaran' => $albaran,
        ));
    }

    /**
     * @param Albaran $albaran
     * @param Request $request
     *
     * @throws \Exception
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Route("/new/{albaran}", name="albaran_lineas_new_modal", options={"expose":true})
     * @Method({"GET","POST"})
     * @ParamConverter("albaran", options={"mapping":{"albaran":"id"}})
     */
    public function newModalAction(Albaran $albaran, Request $request)
    {
        $trans = $this->get('translator');
        $handler = $this->get('buseta_albaran.linea.handler');
        $handler->bindData($albaran);

        $handler->setRequest($request);

        if($handler->handle()) {
            $renderView = $this->renderView('@BusetaBodega/Albaran/Linea/modal_form.html.twig', array(
                'form' => $handler->getForm()->createView(),
            ));

            return new JsonResponse(array(
                'view' => $renderView,
                'message' => $trans->trans('messages.create.success', array(), 'BusetaBodegaBundle')
            ), 201);
        }

        if($handler->getError()) {
            $renderView = $this->renderView('@BusetaBodega/Albaran/Linea/modal_form.html.twig', array(
                'form' => $handler->getForm()->createView(),
            ));

            return new JsonResponse(array(
                'view' => $renderView,
                'message' => $trans->trans('messages.create.error.%key%', array('key' => 'Albarán'), 'BusetaBodegaBundle')
            ), 500);
        }

        $renderView = $this->renderView('@BusetaBodega/Albaran/Linea/modal_form.html.twig', array(
            'form' => $handler->getForm()->createView(),
        ));

        return new JsonResponse(array('view' => $renderView));
    }

    /**
     * @param Albaran $albaran
     * @param Request $request
     * @return JsonResponse
     * @throws \Exception
     *
     * @Route("/{id}/edit/{albaran}", name="albaran_lineas_edit_modal", options={"expose":true})
     * @Method({"GET","PUT"})
     * @ParamConverter("albaran", options={"mapping":{"albaran":"id"}})
     */
    public function editModalAction(AlbaranLinea $albaranLinea, Albaran $albaran, Request $request)
    {
        $trans = $this->get('translator');
        $handler = $this->get('buseta_albaran.linea.handler');
        $handler->bindData($albaran, $albaranLinea);

        $handler->setRequest($request);

        if($handler->handle()) {
            $renderView = $this->renderView('@BusetaBodega/Albaran/Linea/modal_form.html.twig', array(
                'form' => $handler->getForm()->createView(),
            ));

            return new JsonResponse(array(
                'view' => $renderView,
                'message' => $trans->trans('messages.create.success', array(), 'BusetaBodegaBundle')
            ), 201);
        }

        if($handler->getError()) {

            $renderView = $this->renderView('@BusetaBodega/Albaran/Linea/modal_form.html.twig', array(
                'form' => $handler->getForm()->createView(),
            ));

            return new JsonResponse(array(
                'view' => $renderView,
                'message' => $trans->trans('messages.create.error.%key%', array('key' => 'Línea'), 'BusetaBodegaBundle')
            ), 500);
        }

        $form = $handler->getForm();
        $id = $albaranLinea->getProducto()->getId();
        $form->add('producto', 'entity', array(
            'class' => 'BusetaBodegaBundle:Producto',
            'placeholder' => '---Filtrar por código o nombre---',
            'required' => true,
            'attr' => array(
                'class' => 'form-control',
            ),
            'query_builder' => function (EntityRepository $repository) use ($id) {
                $qb = $repository->createQueryBuilder('p');
                $qb->Where($qb->expr()->eq(':id', 'p.id'))
                    ->setParameter('id', $id);

                return $qb;
            },
        ));
        $renderView = $this->renderView(
            '@BusetaBodega/Albaran/Linea/modal_form.html.twig',
            array(
                'form' => $form->createView(),
            )
        );

        return new JsonResponse(array('view' => $renderView));
    }

    /**
     * @param Request $request
     *
     * @Route("/create", name="albaran_linea_compra_create")
     * @Method("POST")
     */
    public function create_compraAction(Request $request)
    {
        $entity = new AlbaranLinea();
        $form = $this->createCreateCompraForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();
        }
    }

    /**
     * Finds and displays a AlbaranLinea entity.
     *
     * @Route("/{id}/show", name="albaran_linea_show")
     * @Method("GET")
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BusetaBodegaBundle:AlbaranLinea')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AlbaranLinea entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('@BusetaBodega/Albaran/show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing AlbaranLinea entity.
     *
     * @Route("/{id}/edit", name="albaran_linea_edit")
     * @Method("GET")
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BusetaBodegaBundle:AlbaranLinea')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AlbaranLinea entity.');
        }

        //$linea = $this->createForm(new PedidoCompraLineaType());

        $editForm = $this->createEditForm($entity);

        $em = $this->getDoctrine()->getManager();

        return $this->render('BusetaBodegaBundle:AlbaranLinea:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            //'linea'       => $linea->createView(),
        ));
    }

    /**
     * Creates a form to edit a PedidoCompra entity.
     *
     * @param PedidoCompra $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(AlbaranLinea $entity)
    {
        $form = $this->createForm('bodega_albaran_type', $entity, array(
            'action' => $this->generateUrl('albaran_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        return $form;
    }

    /**
     * Edits an existing AlbaranLinea entity.
     *
     * @Route("/{id}/update", name="albaran_linea_update")
     * @Method({"POST", "PUT"})
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BusetaBodegaBundle:AlbaranLinea')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AlbaranLinea entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('pedidocompra_show', array('id' => $id)));
        }

        $em = $this->getDoctrine()->getManager();
        $productos = $em->getRepository('BusetaBodegaBundle:Producto')->findAll();

        $json = array();

        foreach ($productos as $p) {
            $json[$p->getId()] = array(
                'nombre' => $p->getNombre(),
                'precio_salida' => $p->getPrecioSalida(),
            );
        }

        return $this->render('BusetaBodegaBundle:AlbaranLinea:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'json'   => json_encode($json),
        ));
    }

    /**
     * Deletes a AlbaranLinea entity.
     * @Route("/{id}/delete", name="albaran_lineas_delete", options={"expose": true})
     * @Method({"DELETE", "GET"})
     */
    public function deleteAction(Request $request, AlbaranLinea $albaranLinea)
    {
        $form = $this->createDeleteForm($albaranLinea->getId());
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            try {
                $em->remove($albaranLinea);
                $em->flush();

                $message = $this->get('translator')->trans('messages.delete.success', array(), 'BusetaBodegaBundle');
                if($request->isXmlHttpRequest()) {
                    return new JsonResponse(array('message' => $message), 202);
                }
            } catch (\Exception $e) {
                $message = $this->get('translator')->trans('messages.delete.error.%key%', array('key' => 'Linea Albaran'), 'BusetaBodegaBundle');
                if($request->isXmlHttpRequest()) {
                    return new JsonResponse(array('message' => $message), 500);
                }
            }
        }

        if ($request->isXmlHttpRequest()) {
            $view = $this->renderView('@BusetaBodega/Albaran/Linea/delete_modal.html.twig', array(
                'entity'    => $albaranLinea,
                'form'      => $form->createView(),
            ));

            return new JsonResponse(array(
                'view' => $view
            ), 200);
        }

        return $this->redirect($this->generateUrl('linea'));
    }

    /**
     * Creates a form to delete a AlbaranLinea entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('albaran_lineas_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->getForm()
            ;
    }
}
