<?php

namespace Oro\Bundle\BtsBundle\Form\Type;

use Doctrine\ORM\EntityRepository;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Oro\Bundle\BtsBundle\Entity\Issue;
use Oro\Bundle\BtsBundle\Entity\Repository\IssueTypeRepository;

class IssueType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('summary', 'text', array(
                'label' => 'oro.bts.issue.form.summary.label'
            ))
            ->add('description', 'oro_rich_text', array(
                'required' => false,
                'label'    => 'oro.bts.issue.form.description.label'
            ))
            ->add('priority', 'entity', array(
                'class'    => 'Oro\Bundle\BtsBundle\Entity\IssuePriority',
                'property' => 'label',
                'label'    => 'oro.bts.issue.form.priority.label'
            ))->add('owner', 'oro_user_select', array(
                'required' => true,
                'label'    => 'oro.bts.issue.form.assignee.label',
            ))->add('tags', 'oro_tag_select',  array(
                'label' => 'oro.tag.entity_plural_label'
            )
        );


        $formModifier = function(FormEvent $event) {
            $issue = $event->getData();
            $form  = $event->getForm();

            if ($issue instanceof Issue) {
                if (!$issue->getModel()->isSubtask() && !$issue->getModel()->isStory()) {
                    $form->add('type', 'entity', array(
                        'class'         => 'Oro\Bundle\BtsBundle\Entity\IssueType',
                        'property'      => 'label',
                        'label'         => 'oro.bts.issue.form.type.label',
                        'query_builder' => function (IssueTypeRepository $em) {
                            return $em->loadTypesWithoutSubtaskQueryBuilder();
                        },
                    ));
                }
            }
        };

        $builder->addEventListener(FormEvents::PRE_SET_DATA, $formModifier);
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => 'Oro\Bundle\BtsBundle\Entity\Issue',
            )
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'oro_btsbundle_issue';
    }
}
