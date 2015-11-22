<?php

namespace XDU\ModalEntityBundle\Form\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Doctrine\Common\Persistence\ManagerRegistry;

class EntityModalFormSubscriber implements EventSubscriberInterface
{

    protected $registry;

    protected $entity_class = null;

    protected $entity_repository = null;

    protected $entity_label = null;


    public function __construct(ManagerRegistry $registry, $entity_label, $entity_repository, $entity_class)
    {
        $this->registry = $registry;
        $this->data_class = $entity_class;
        $this->entity_repository = $entity_repository;
        $this->entity_label = $entity_label;
    }

    public static function getSubscribedEvents()
    {
        return array(
            FormEvents::PRE_SET_DATA => 'preSetData',
            FormEvents::POST_SET_DATA => 'postSetData',
            FormEvents::PRE_SUBMIT => 'preSubmit',
            FormEvents::SUBMIT => 'submit'
        );
    }

    public function preSetData(FormEvent $event)
    {
        $data = $event->getData();

        if (is_object($data)) {
            $name = array();
            foreach ($this->entity_label as $label) {
                $func = 'get'.ucfirst($label);
                $name[] = $data->$func();
            }
            $tab = array(
                'id' => $data->getId(),
                'name' => implode(' ', $name),
                'entity' => $data
            );
            $event->setData($tab);
        }
    }

    public function postSetData(FormEvent $event)
    {
        $form = $event->getForm();
        $data = $event->getData();

        if (is_array($data)) {
            $form->get('id')->setData($data['id']);
            $form->get('name')->setData($data['name']);
        }
    }

    public function preSubmit(FormEvent $event)
    {
        $form = $event->getForm();
        $data = $event->getData();

        if (empty($data['id'])) {
            $form->setData(null);
        } else {
            $page = $this->registry->getRepository($this->entity_repository)->find($data['id']);
            $form->setData($page);
        }
    }

    public function submit(FormEvent $event)
    {
        $data = $event->getData();

        if (empty($data['entity'])) {
            $event->setData(null);
        } else {
            $event->setData($data['entity']);
        }
    }

}