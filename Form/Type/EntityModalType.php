<?php

namespace XDU\ModalEntityBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\DependencyInjection\Container;
use Doctrine\Common\Persistence\ManagerRegistry;

use EntityModalBundle\Form\EventListener\EntityModalFormSubscriber;

class EntityModalType extends AbstractType
{

    protected $registry;

    protected $router;

    public function __construct(Container $container, ManagerRegistry $registry)
    {
        $this->registry = $registry;
        $this->router = $container->get('router');
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {

        $view->vars['modal_uri'] = $this->router->generate('xdu_modal_entity_tree', array(
            'repository' => $options['entity_repository'],
            'labels' => implode(',', $options['entity_label']),
            'classes' => implode(',', $options['entity_classes']),
            'parent' => $options['entity_parent'],
            'sort' => implode(',', $options['entity_sort']),
            'current' => $form->get('id')->getData(),
            'entity' => $form->getParent()->getData()->getId()
        ));

    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder->add('id', 'hidden', array(
            'mapped' => false,
            'required' => false
        ));

        $builder->add('name', 'text', array(
            'attr' => array('class' => 'form-control'),
            'label' => false,
            'mapped' => false,
            'required' => false
        ));

        $builder->addEventSubscriber(new EntityModalFormSubscriber(
            $this->registry,
            $options['entity_label'],
            $options['entity_repository'],
            $options['entity_classes']
        ));

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'cascade_validation' => false,
        ));

        $resolver->setRequired(array(
            'entity_label',
            'entity_repository',
            'entity_classes',
            'entity_parent',
            'entity_sort'
        ));
    }

    public function getName()
    {
        return 'entity_modal';
    }
}