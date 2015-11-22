<?php

namespace EntityModalBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class EntityModalController extends Controller
{

    protected function getChildrenNodes(&$nodes, $parentId, $list, $classes, $parentFunction, $entityId)
    {
        foreach ($list as $item) {
            if ($entityId == $item->getId() or !in_array(get_class($item), $classes)) {
                continue;
            }

            $itemParentId = is_null($item->$parentFunction()) ? null : $item->$parentFunction()->getId();
            if ($itemParentId === $parentId) {
                $nodes[] = $item;
                $this->getChildrenNodes($nodes, $item->getId(), $list, $classes, $parentFunction, $entityId);
            }
        }
    }

    public function modalAction(Request $request)
    {

        // Setting the sort parameters
        $sort = array();
        foreach (explode(',', $request->get('sort')) as $sort_item) {
            $sort[$sort_item] = 'ASC';
        }

        // fetching entities from database
        $list = $this->getDoctrine()->getRepository($request->get('repository'))->findBy(
            array(),
            $sort
        );

        // Setting the allowed classes in tree
        $classes = explode(',', $request->get('classes'));

        // Setting the function to check the parent
        $parentFunction = 'get'.ucfirst($request->get('parent'));

        // Initialize nodes array
        $nodes = array();

        // Ordering nodes to get a tree
        $this->getChildrenNodes($nodes, null, $list, $classes, $parentFunction, $request->get('entity'));

        // rendering the tree
        return $this->render('EntityModalBundle:EntityModal:entity_modal.html.twig', array(
            'nodes' => $nodes,
            'current' => $request->get('current')
        ));
    }

}
