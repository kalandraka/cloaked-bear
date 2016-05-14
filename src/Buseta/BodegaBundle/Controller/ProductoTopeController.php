<?php

namespace Buseta\BodegaBundle\Controller;


use Buseta\BodegaBundle\Entity\Bodega;
use Buseta\BodegaBundle\Extras\FuncionesExtras;
use Buseta\BodegaBundle\Form\Filter\ProductoTopeFilter;
use Buseta\BodegaBundle\Form\Model\ProductoTopeFilterModel;
use Buseta\BodegaBundle\Entity\ProductoTope;
use Buseta\BodegaBundle\Form\Type\ProductoTopeType;
/*
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
//use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;*/


use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;

/**
 * ProductoTope controller.
 *
 * @Route("/productotope")
 * @Breadcrumb(title="Inicio", routeName="core_homepage")
 * @Breadcrumb(title="Módulo de Bodegas", routeName="bodega_principal")
 */
class ProductoTopeController extends Controller
{

    /**
     * Lists all ProductoTope entities.
     *
     * @Route("/", name="productotope")
     * @Method("GET")
     * @Breadcrumb(title="Productos con Tope", routeName="productotope")
     */
    public function indexAction(Request $request)
    {
        $filter = new ProductoTopeFilterModel();

        $form = $this->createForm(new ProductoTopeFilter(), $filter, array(
            'action' => $this->generateUrl('productotope'),
        ));

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entities = $this->get('doctrine.orm.entity_manager')
                ->getRepository('BusetaBodegaBundle:ProductoTope')->filter($filter);
        } else {
            $entities = $this->get('doctrine.orm.entity_manager')
                ->getRepository('BusetaBodegaBundle:ProductoTope')->filter();
        }

        $paginator = $this->get('knp_paginator');
        $entities = $paginator->paginate(
            $entities,
            $request->query->get('page', 1),
            10
        );

        return $this->render('BusetaBodegaBundle:ProductoTope:index.html.twig', array(
            'entities' => $entities,
            'filter_form' => $form->createView(),
        ));
    }

    /**
     * Creates a new ProductoTope entity.
     *
     * @Route("/create", name="productotope_create", options={"expose":true})
     * @Method("POST")
     * @Breadcrumb(title="Crear Nuevo Producto Tope", routeName="productotope_create")
     */
    public function createAction(Request $request)
    {
        $entity = new ProductoTope();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('productotope_show', array('id' => $entity->getId())));
        }

        return $this->render('BusetaBodegaBundle:ProductoTope:new.html.twig', array(
            'entity' => $entity,
            'form' => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a ProductoTope entity.
     *
     * @param ProductoTope $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(ProductoTope $entity)
    {
        $form = $this->createForm(new ProductoTopeType(), $entity, array(
            'action' => $this->generateUrl('productotope_create'),
            'method' => 'POST',
        ));

        //$form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new ProductoTope entity.
     *
     * @Route("/new", name="productotope_new", methods={"GET"}, options={"expose":true})
     * @Breadcrumb(title="Crear Nuevo Producto Tope", routeName="productotope_new")
     */
    public function newAction()
    {
        $entity = new ProductoTope();
        $form = $this->createCreateForm($entity);

        return $this->render('BusetaBodegaBundle:ProductoTope:new.html.twig', array(
            'entity' => $entity,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a ProductoTope entity.
     *
     * @Route("/{id}/show", name="productotope_show", methods={"GET"}, options={"expose":true})
     * @Breadcrumb(title="Ver Datos de Producto Tope", routeName="productotope_show", routeParameters={"id"})
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BusetaBodegaBundle:ProductoTope')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ProductoTope entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('BusetaBodegaBundle:ProductoTope:show.html.twig', array(
            'entity' => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing ProductoTope entity.
     *
     * @Route("/{id}/edit", name="productotope_edit", methods={"GET"}, options={"expose":true})
     * @Breadcrumb(title="Modificar Producto Tope", routeName="productotope_edit", routeParameters={"id"})
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BusetaBodegaBundle:ProductoTope')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ProductoTope entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        //var_dump('dsdd');die;

        return $this->render('BusetaBodegaBundle:ProductoTope:edit.html.twig', array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Creates a form to edit a ProductoTope entity.
     *
     * @param ProductoTope $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(ProductoTope $entity)
    {
        $form = $this->createForm(new ProductoTopeType(), $entity, array(
            'action' => $this->generateUrl('productotope', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));
        return $form;
    }

    /**
     * Edits an existing ProductoTope entity.
     *
     * @Route("/{id}/update", name="productotope_update", options={"expose":true})
     * @Method({"PUT", "POST"})
     * @Breadcrumb(title="Modificar Producto Tope", routeName="productotope_update", routeParameters={"id"})
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BusetaBodegaBundle:ProductoTope')->find($id);

        //var_dump('dddd-'.$entity->getMax());die;

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Producto Tope entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);

        $editForm->submit($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('productotope_show', array('id' => $id)));
        }

        return $this->render('BusetaBodegaBundle:ProductoTope:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a ProductoTope entity.
     *
     * @Route("/{id}/delete", name="productotope_delete")
     * @Method({"DELETE", "GET", "POST"})
     */
    public function deleteAction(ProductoTope $productoTope, Request $request)
    {

        $trans = $this->get('translator');
        $deleteForm = $this->createDeleteForm($productoTope->getId());

        $deleteForm->handleRequest($request);
        if ($deleteForm->isSubmitted() && $deleteForm->isValid()) {
            try {
                $em = $this->get('doctrine.orm.entity_manager');

                $em->remove($productoTope);
                $em->flush();

                $message = $trans->trans('messages.delete.success', array(), 'BusetaTallerBundle');

                if ($request->isXmlHttpRequest()) {
                    return new JsonResponse(array(
                        'message' => $message,
                    ), 202);
                } else {
                    $this->get('session')->getFlashBag()->add('success', $message);
                }
            } catch (\Exception $e) {
                $message = $trans->trans('messages.delete.error.%key%', array('key' => 'ProductoTope'),
                    'BusetaTallerBundle');
                $this->get('logger')->addCritical(sprintf($message . ' Detalles: %s', $e->getMessage()));

                if ($request->isXmlHttpRequest()) {
                    return new JsonResponse(array(
                        'message' => $message,
                    ), 500);
                }
            }
        }

        $renderView = $this->renderView('BusetaBodegaBundle:ProductoTope:delete_modal.html.twig', array(
            'entity' => $productoTope,
            'form' => $deleteForm->createView(),
        ));

        if ($request->isXmlHttpRequest()) {
            return new JsonResponse(array('view' => $renderView));
        }
        return $this->redirect($this->generateUrl('productotope'));
    }

    /**
     * Creates a form to delete a ProductoTope entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('productotope_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->getForm();
    }


    //A partir de aqui los metodos requeridos para el AJAX que llaman desde el formulario de Bodega

    /**
     * @param Bodega $bodega
     * @return Response
     *
     * /////@Route("/list/{bodega}", name="bodega_productotope_list", methods={"GET"}, options={"expose":true})
     * /////@ParamConverter("bodega", options={"mapping":{"bodega":"id"}})
     */
    public function listAction(Bodega $bodega, Request $request)
    {
        $entities = $this->get('doctrine.orm.entity_manager')
            ->getRepository('BusetaBodegaBundle:ProductoTope')
            ->findAllByBodegaId($bodega->getId());

        $entities = $this->get('knp_paginator')
            ->paginate(
                $entities,
                $request->query->get('page', 1),
                10
            );

        return $this->render('@BusetaBodega/Bodega/ProductoTope/list_template.html.twig', array(
            'entities' => $entities,
           // 'bodega' => $bodega,
        ));
    }

//     //se le pasa el almacen
//    public function listAction($id)
//    {
//        $em = $this->getDoctrine()->getManager();
//
//        $almacen = $em->getRepository('BusetaBodegaBundle:Bodega')->find($id);
//
//        $productotopeList = $em->getRepository('BusetaBodegaBundle:ProductoTope')->findBy(
//            array(
//                'almacen' => $almacen,
//            )
//        );
//
//        $productotopeArray = array();
//
//        /*  @var  \Buseta\BodegaBundle\Entity\ProductoTope  $pt*/
//        $fe = new FuncionesExtras();
//        foreach ($productotopeList as $pt) {
//            $producto = $pt->getProducto();
//            $productotopeElem['prodtope']=$pt;
//            $cantidadDisponible = $fe->obtenerCantidadProductosAlmancen($producto,$almacen,$em);
//            $productotopeElem['cantidad']=$cantidadDisponible;
//            $productotopeArray[]=$productotopeElem;
//        }
//
//        $productotopeArray = $this->get('knp_paginator')
//            ->paginate(
//                $productotopeArray,
//                $this->get('request')->query->get('page', 1),
//                10
//            );
//
//        return $this->render('@BusetaBodega/Bodega/ProductoTope/list_template.html.twig', array(
//            'entities' => $productotopeArray,
//            'almacen' => $almacen,
//        ));
//    }


    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * ///@Route("/new/modal/{reporte}", name="bodega_productotope_new_modal", methods={"GET","POST"}, options={"expose":true})
     * ///@ParamConverter("reporte", options={"mapping":{"reporte":"id"}})
     */
    public function newModalAction(Bodega $bodega, Request $request)
    {

        $trans = $this->get('translator');

        $handler = $this->get('buseta_bodega.productotope.handler');
        $handler->bindData($bodega);

        $handler->setRequest($request);
        if($handler->handle()) {
            $renderView = $this->renderView('@BusetaBodega/Bodega/ProductoTope/modal_form.html.twig', array(
                'form' => $handler->getForm()->createView(),
            ));

            return new JsonResponse(array(
                'view' => $renderView,
                'message' => $trans->trans('messages.create.success', array(), 'BusetaBodegaBundle')
            ), 201);
        }

        if($handler->getError()) {
            $renderView = $this->renderView('@BusetaBodega/Bodega/ProductoTope/modal_form.html.twig', array(
                'form' => $handler->getForm()->createView(),
            ));

            return new JsonResponse(array(
                'view' => $renderView,
                'message' => $trans->trans('messages.create.error.%key%', array('key' => 'Producto Tope'), 'BusetaBodegaBundle')
            ), 500);
        }

        $renderView = $this->renderView('@BusetaBodega/Bodega/ProductoTope/modal_form.html.twig', array(
            'form' => $handler->getForm()->createView(),
        ));

        return new JsonResponse(array('view' => $renderView));
    }


    public function editModalAction(ProductoTope $productoTope,  $bodega = 1 , Request $request)
    {
       // var_dump('---------');die;

        $bodega = $this->get('doctrine.orm.entity_manager')->getRepository('BusetaBodegaBundle:Bodega')->find($bodega);  ;

        $trans = $this->get('translator');
        $handler = $this->get('buseta_bodega.productotope.handler');

        $handler->bindData($bodega, $productoTope);
        $handler->setRequest($request);

        if($handler->handle()) {
            $renderView = $this->renderView('@BusetaBodega/Bodega/ProductoTope/modal_form.html.twig', array(
                'form' => $handler->getForm()->createView(),
            ));

            return new JsonResponse(array(
                'view' => $renderView,
                'message' => $trans->trans('messages.create.success', array(), 'BusetaBodegaBundle')
            ), 201);
        }

        if($handler->getError()) {

            $renderView = $this->renderView('@BusetaBodega/Bodega/ProductoTope/modal_form.html.twig', array(
                'form' => $handler->getForm()->createView(),
            ));

            return new JsonResponse(array(
                'view' => $renderView,
                'message' => $trans->trans('messages.create.error.%key%', array('key' => 'Producto Tope'), 'BusetaBodegaBundle')
            ), 500);
        }

        $renderView = $this->renderView('@BusetaBodega/Bodega/ProductoTope/modal_form.html.twig', array(
            'form' => $handler->getForm()->createView(),
        ));

        return new JsonResponse(array('view' => $renderView));
    }

    /**
     * Deletes a ProductoTope entity.
     * @Route("/{id}/delete", name="productotope_delete_modal", options={"expose": true})
     * @Method({"DELETE", "GET"})
     */
    public function deleteModalAction(Request $request, ProductoTope $productoTope)
    {
        $form = $this->createDeleteForm($productoTope->getId());
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            try {
                $em->remove($productoTope);
                $em->flush();

                $message = $this->get('translator')->trans('messages.delete.success', array(), 'BusetaBodegaBundle');
                if($request->isXmlHttpRequest()) {
                    return new JsonResponse(array('message' => $message), 202);
                }
            } catch (\Exception $e) {
                $message = $this->get('translator')->trans('messages.delete.error.%key%', array('key' => 'Producto Tope'), 'BusetaBodegaBundle');
                if($request->isXmlHttpRequest()) {
                    return new JsonResponse(array('message' => $message), 500);
                }
            }
        }

        if ($request->isXmlHttpRequest()) {
            $view = $this->renderView('@BusetaBodega/Bodega/ProductoTope/modal_delete.html.twig', array(
                'entity'    => $productoTope,
                'form'      => $form->createView(),
            ));

            return new JsonResponse(array(
                'view' => $view
            ), 200);
        }

        return $this->redirect($this->generateUrl('linea'));//?????
    }


}
