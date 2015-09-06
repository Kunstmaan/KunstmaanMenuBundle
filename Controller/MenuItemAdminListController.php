<?php

namespace Kunstmaan\MenuBundle\Controller;

use Kunstmaan\AdminListBundle\AdminList\Configurator\AbstractAdminListConfigurator;
use Kunstmaan\AdminListBundle\AdminList\ItemAction\SimpleItemAction;
use Kunstmaan\MenuBundle\AdminList\MenuItemAdminListConfigurator;
use Kunstmaan\AdminListBundle\Controller\AdminListController;
use Kunstmaan\AdminListBundle\AdminList\Configurator\AdminListConfiguratorInterface;
use Kunstmaan\MenuBundle\Form\MenuItemAdminType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class MenuItemAdminListController extends AdminListController
{
    /**
     * @var AdminListConfiguratorInterface
     */
    private $configurator;

    /**
     * @param Request $request
     * @param int $menuid
     * @param int $entityId
     * @return AbstractAdminListConfigurator
     */
    public function getAdminListConfigurator(Request $request, $menuid, $entityId = null)
    {
        if (!isset($this->configurator)) {
            $menu = $this->getDoctrine()->getManager()->getRepository('KunstmaanMenuBundle:Menu')->find($menuid);
            $rootNode = $this->get('kunstmaan_node.domain_configuration')->getRootNode();
            $this->configurator = new MenuItemAdminListConfigurator($this->getEntityManager(), null, $menu);
            $this->configurator->setAdminType(new MenuItemAdminType($request->getLocale(), $menu, $entityId, $rootNode));
        }

        return $this->configurator;
    }

    /**
     * The index action
     *
     * @Route("/{menuid}/items", name="kunstmaanmenubundle_admin_menuitem")
     */
    public function indexAction(Request $request, $menuid)
    {
        $configurator = $this->getAdminListConfigurator($request, $menuid);

        $itemRoute = function ($item) use ($menuid) {
            return array(
                'path'   => 'kunstmaanmenubundle_admin_menuitem_move_up',
                'params' => array(
                    'menuid' => $menuid,
                    'item' => $item->getId()
                )
            );
        };
        $configurator->addItemAction(new SimpleItemAction($itemRoute, 'arrow-up', 'Move up'));

        $itemRoute = function ($item) use ($menuid) {
            return array(
                'path'   => 'kunstmaanmenubundle_admin_menuitem_move_down',
                'params' => array(
                    'menuid' => $menuid,
                    'item' => $item->getId()
                )
            );
        };
        $configurator->addItemAction(new SimpleItemAction($itemRoute, 'arrow-down', 'Move down'));

        return parent::doIndexAction($configurator, $request);
    }

    /**
     * The add action
     *
     * @Route("/{menuid}/items/add", name="kunstmaanmenubundle_admin_menuitem_add")
     * @Method({"GET", "POST"})
     * @return array
     */
    public function addAction(Request $request, $menuid)
    {
        return parent::doAddAction($this->getAdminListConfigurator($request, $menuid), null, $request);
    }

    /**
     * The edit action
     *
     * @param int $id
     *
     * @Route("{menuid}/items/{id}/edit", requirements={"id" = "\d+"}, name="kunstmaanmenubundle_admin_menuitem_edit")
     * @Method({"GET", "POST"})
     *
     * @return array
     */
    public function editAction(Request $request, $menuid, $id)
    {
        return parent::doEditAction($this->getAdminListConfigurator($request, $menuid, $id), $id, $request);
    }

    /**
     * The delete action
     *
     * @param int $id
     *
     * @Route("{menuid}/items/{id}/delete", requirements={"id" = "\d+"}, name="kunstmaanmenubundle_admin_menuitem_delete")
     * @Method({"GET", "POST"})
     *
     * @return array
     */
    public function deleteAction(Request $request, $menuid, $id)
    {
        return parent::doDeleteAction($this->getAdminListConfigurator($request, $menuid), $id, $request);
    }

    /**
     * Move an item up in the list.
     *
     * @Route("{menuid}/items/{item}/move-up", name="kunstmaanmenubundle_admin_menuitem_move_up")
     * @Method({"GET"})
     * @return RedirectResponse
     */
    public function moveUpAction(Request $request, $menuid, $item)
    {
        $em = $this->getEntityManager();
        $repo = $em->getRepository('KunstmaanMenuBundle:MenuItem');
        $item = $repo->find($item);

        if ($item) {
            $repo->moveUp($item);
        }

        return new RedirectResponse($this->generateUrl('kunstmaanmenubundle_admin_menuitem', array('menuid' => $menuid)));
    }

    /**
     * Move an item down in the list.
     *
     * @Route("{menuid}/items/{item}/move-down", name="kunstmaanmenubundle_admin_menuitem_move_down")
     * @Method({"GET"})
     * @return RedirectResponse
     */
    public function moveDownAction(Request $request, $menuid, $item)
    {
        $em = $this->getEntityManager();
        $repo = $em->getRepository('KunstmaanMenuBundle:MenuItem');
        $item = $repo->find($item);

        if ($item) {
            $repo->moveDown($item);
        }

        return new RedirectResponse($this->generateUrl('kunstmaanmenubundle_admin_menuitem', array('menuid' => $menuid)));
    }
}
