<?php

namespace HatueySoft\MenuBundle\Controller;


use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;
use HatueySoft\MenuBundle\Form\Type\MenuNodeType;
use HatueySoft\MenuBundle\Model\MenuNode;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class MenuController
 * @package HatueySoft\MenuBundle\Controller
 *
 * @Route("/menu")
 * @Breadcrumb(title="Inicio", routeName="core_homepage")
 * @Breadcrumb(title="Módulo Seguridad", routeName="security_usuario")
 * @Breadcrumb(title="Listado de Menus")
 */
class MenuController extends Controller
{
    /**
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Route("/", name="hatueysoft_menu")
     * @Method("GET")
     */
    public function indexAction()
    {
        $menuManager = $this->get('hatuey_soft.menu.manager');
        if (!$menuManager->configFileExist()) {
            $this->get('session')->getFlashBag()->add('danger', sprintf('No existe el archivo de configuración "%s"
            y no es posible crearlo debido a problemas de permisos. Compruebe que exista el archivo y tenga
            permisos 775 con propietario y grupo correctos.', $menuManager->getMenuConf()));
        } elseif (!$menuManager->isReadable()) {
            $this->get('session')->getFlashBag()->add('danger', sprintf('No es posible leer el archivo de configuración "%s".
             Compruebe que tenga permisos 775 con propietario y grupo correctos.', $menuManager->getMenuConf()));
        } elseif (!$menuManager->isWritable()) {
            $this->get('session')->getFlashBag()->add('danger', sprintf('No es posible escribir el archivo de configuración "%s".
             Compruebe que tenga permisos 775 con propietario y grupo correctos.', $menuManager->getMenuConf()));
        }

        return $this->render('@HatueySoftMenu/Menu/index.html.twig');
    }

    /**
     * @param Request $request
     * @return JsonResponse
     *
     * @Route("/{id}.{_format}", name="hatueysoft_menu_getmenu", options={"expose": true})
     * @Method("GET")
     */
    public function getMenuAction(Request $request, $id)
    {
        $menuManager = $this->get('hatuey_soft.menu.manager');

        $menu = $menuManager->findTreeNode($id);
        $render = $this->childrensToArray($menu);

        return new JsonResponse($render);
    }

    /**
     * @param Request $request
     * @param $id
     * @return JsonResponse
     *
     * @Route("/{id}/parent.{_format}", name="hatueysoft_menu_getparent", options={"expose": true})
     * @Method("GET")
     */
    public function getParentMenuAction(Request $request, $id)
    {
        $menuManager = $this->get('hatuey_soft.menu.manager');

        $menu = $menuManager->findParentNode($id);
        $render = $this->childrensToArray($menu);

        return new JsonResponse($render);
    }

    /**
     * @param MenuNode $menu
     * @return array
     */
    private function childrensToArray(MenuNode $menu = null)
    {
        $render = array();
        if ($menu !== null && !$menu->getChildrens()->isEmpty()) {
            $childrens = $menu->getChildrens();

            foreach ($childrens as $child) {
                /** @var \HatueySoft\MenuBundle\Model\MenuNode $child */
                $attr = array(
                    'id' => $child->getId(),
                );

                $childAttributes = $child->getAttributesToArray();
                if (array_key_exists('icon', $childAttributes)) {
                    $attr['data-icon'] = $childAttributes['icon'];
                }

                $render[] = array(
                    'text' => $child->getLabel(),
                    'type' => $child->getType(),
                    'attr' => $attr,
                );
            }
        }

        return $render;
    }

    /**
     * @param Request $request
     *
     * @return Response
     *
     * @Route("/{parent}/new", name="hatueysoft_menu_new", options={"expose": true})
     * @Method("GET")
     */
    public function newAction(Request $request, $parent)
    {
        $form = $this->createNewForm(new MenuNode(), $parent);

        return $this->render('@HatueySoftMenu/Menu/new.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * @param Request $request
     * @param $parent
     *
     * @return Response
     *
     * @Route("/{parent}/create", name="hatueysoft_menu_create", options={"expose": true})
     * @Method("POST")
     */
    public function createAction(Request $request, $parent)
    {
        $menuManager = $this->get('hatuey_soft.menu.manager');
        $menuNode = new MenuNode();
        $form = $this->createNewForm($menuNode, $parent);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $menuManager->insertNode($menuNode, $parent);

                return new Response('Se ha insertado el menú de forma satisfactoria.', 201);
            } catch (\Exception $e) {
                $this->get('logger')->critical(sprintf('Ha ocurrido un error insertando un nuevo nodo en el menú. Detalles: %s', $e->getMessage()));

                return new Response('Ha ocurrido un error insertando un nuevo nodo en el menú.', 500);
            }
        }

        return $this->render('@HatueySoftMenu/Menu/new.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * @param MenuNode $menuNode
     * @return \Symfony\Component\Form\Form
     */
    private function createNewForm(MenuNode $menuNode, $parent)
    {
        $form = $this->createForm('hatueysoft_menu_node_type', $menuNode, array(
            'parent' => $parent,
            'method' => 'POST',
            'action' => $this->generateUrl('hatueysoft_menu_create', array('parent' => $parent)),
        ));

        return $form;
    }

    /**
     * @param Request $request
     * @param $id
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     *
     * @Route("/{id}/show", name="hatueysoft_menu_show", options={"expose": true})
     * @Method("GET")
     */
    public function showAction(Request $request, $id)
    {
        $menuManager = $this->get('hatuey_soft.menu.manager');

        $menu = $menuManager->findTreeNode($id);
        if ($menu === null) {
            $split = explode('_', $id);
            $id = implode('_', array_slice($split, 0, count($split) - 1));
            $menu = $menuManager->findTreeNode($id);
        }

        if ($menu->getType() === 'item') {
            $menu = $menuManager->findParentNode($id);
        }

        return new JsonResponse(array(
            'view' => $this->renderView('@HatueySoftMenu/Menu/show.html.twig', array(
                'node' => $menu,
            )),
            'id' => $menu->getId(),
        ));
    }

    /**
     * @param Request $request
     * @param $id
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Route("/{id}/edit", name="hatueysoft_menu_edit", options={"expose": true})
     * @Method("GET")
     */
    public function editAction(Request $request, $id)
    {
        $menuManager = $this->get('hatuey_soft.menu.manager');

        $menu = $menuManager->findTreeNode($id);
        $form = $this->createEditForm($menu);

        return $this->render('@HatueySoftMenu/Menu/edit.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * @param Request $request
     * @param $id
     *
     * @return Response
     *
     * @Route("/{id}/update", name="hatueysoft_menu_update", options={"expose": true})
     * @Method("PUT")
     */
    public function updateAction(Request $request, $id)
    {
        $menuManager = $this->get('hatuey_soft.menu.manager');

        $menu = $menuManager->findTreeNode($id);
        $form = $this->createEditForm($menu);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $menuManager->updateNode($menu);

                return new Response('Se han actualizado los datos del menú de forma satisfactoria.', 202);
            } catch (\Exception $e) {
                $this->get('logger')->critical(sprintf('Ha ocurrido un error actualizando nodo en el menú. Detalles: %s', $e->getMessage()));

                return new Response('Ha ocurrido un error actualizando nodo en el menú.', 500);
            }
        }

        return $this->render('@HatueySoftMenu/Menu/edit.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * @param MenuNode $menuNode
     * @return \Symfony\Component\Form\Form
     */
    private function createEditForm(MenuNode $menuNode)
    {
        $form = $this->createForm('hatueysoft_menu_node_type', $menuNode, array(
            'method' => 'PUT',
            'action' => $this->generateUrl('hatueysoft_menu_update', array('id' => $menuNode->getId())),
        ));

        return $form;
    }

    /**
     * Deletes a menu node.
     *
     * @Route("/{id}/delete", name="hatueysoft_menu_delete", options={"expose": true})
     * @Method({"DELETE", "GET", "POST"})
     */
    public function deleteAction(Request $request, $id)
    {
        $trans = $this->get('translator');
        $menuManager = $this->get('hatuey_soft.menu.manager');

        $menuNode = $menuManager->findTreeNode($id);
        $deleteForm = $this->createDeleteForm($id);

        $deleteForm->handleRequest($request);
        if($deleteForm->isSubmitted() && $deleteForm->isValid()) {
            try {
                $menuManager->removeNode($menuNode);

                return new Response('Se han eliminado los datos del menú de forma satisfactoria.', 202);
            } catch (\Exception $e) {
                $this->get('logger')->critical(sprintf('Ha ocurrido un error eliminando nodo en el menú. Detalles: %s', $e->getMessage()));

                return new Response('Ha ocurrido un error eliminando nodo en el menú.', 500);
            }
        }

        return $this->render('@HatueySoftMenu/Menu/delete_modal.html.twig', array(
            'node' => $menuNode,
            'form' => $deleteForm->createView(),
        ));
    }

    /**
     * Creates a form to delete a ConfiguracionCombustible entity by id.
     *
     * @param mixed $id The entity id
     *
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('hatueysoft_menu_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->getForm()
            ;
    }
}
