<?php

namespace Oro\Bundle\BtsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Oro\Bundle\BtsBundle\Entity\Issue;
use Oro\Bundle\BtsBundle\Entity\IssuePriority;
use Oro\Bundle\BtsBundle\Entity\IssueType;
use Oro\Bundle\SecurityBundle\Annotation\Acl;
use Oro\Bundle\SecurityBundle\Annotation\AclAncestor;
use Oro\Bundle\UserBundle\Entity\User;

class IssueController extends Controller
{
    /**
     * @Route(
     *      "/{_format}",
     *      name="oro_bts_issue_index",
     *      requirements={"_format"="html|json"},
     *      defaults={"_format" = "html"}
     * )
     * @AclAncestor("oro_bts_view")
     * @Template
     */
    public function indexAction()
    {
        return [
            'entity_class' => $this->container->getParameter('oro_bts.issue.entity.class')
        ];
    }

    /**
     * @Route("/create/{owner}", defaults={"owner" = null}, name="oro_bts_issue_create")
     * @Template("OroBundleBtsBundle:Issue:update.html.twig")
     * @Acl(id="oro_bts_issue_create", type="entity", permission="CREATE", class="OroBundleBtsBundle:Issue")
     */
    public function createAction(User $owner = null)
    {
        $entity = $this->createEntity(IssueType::TASK, $owner);

        return $this->update($entity);
    }

    /**
     * @Route("/{id}/subtask/add", name="oro_bts_issue_add_subtask")
     * @Template("OroBundleBtsBundle:Issue:update.html.twig")
     * @Acl(id="oro_bts_issue_add_subtask", type="entity", permission="EDIT", class="OroBundleBtsBundle:Issue")
     *
     * @param Issue $issue
     */
    public function addSubtaskAction(Issue $parent)
    {
        if (!$parent->getModel()->isStory()) {
            return $this->redirect($this->generateUrl('oro_bts_issue_view', array('id' => $parent->getId())));
        }

        $entity = $this->createEntity(IssueType::SUBTASK, null, $parent);

        return $this->update($entity);
    }

    /**
     * @Route("/info/{id}", name="oro_bts_issue_info", requirements={"id"="\d+"})
     * @Template()
     * @AclAncestor("oro_bts_view")
     *
     * @param Issue $issue
     */
    public function infoAction(Issue $issue)
    {
        return array(
            'entity'  => $issue
        );
    }

    /**
     * @Route("/view/{id}", name="oro_bts_issue_view")
     * @Template
     * @Acl(id="oro_bts_issue_view", type="entity", permission="VIEW", class="OroBundleBtsBundle:Issue")
     *
     * @param Issue $issue
     */
    public function viewAction(Issue $entity)
    {
        return array(
            'entity' => $entity,
        );
    }

    /**
     * @Route("/update/{id}", name="oro_bts_issue_update")
     * @Template("OroBundleBtsBundle:Issue:update.html.twig")
     * @Acl(id="oro_bts_issue_update", type="entity", permission="EDIT", class="OroBundleBtsBundle:Issue")
     *
     * @param Issue $issue
     */
    public function updateAction(Issue $entity)
    {
        return $this->update($entity);
    }

    /**
     *  Update handler
     *
     * @param Issue $entity
     */
    protected function update(Issue $entity = null)
    {
        $saved = false;
        if ($this->get('oro_bts.form.handler.issue')->process($entity)) {
            if (!$this->getRequest()->get('_widgetContainer')) {
                $this->get('session')->getFlashBag()->add(
                    'success',
                    $this->get('translator')->trans('oro.bts.issue.form.saved.message')
                );

                return $this->get('oro_ui.router')->redirectAfterSave(
                    array(
                        'route' => 'oro_bts_issue_update',
                        'parameters' => array('id' => $entity->getId()),
                    ),
                    array(
                        'route' => 'oro_bts_issue_view',
                        'parameters' => array('id' => $entity->getId()),
                    )
                );
            }
            $saved = true;
        }

        return array(
            'entity'     => $entity,
            'saved'      => $saved,
            'form'       => $this->get('oro_bts.form.handler.issue')->getForm()->createView(),
        );
    }

    /**
     * @Route("/status/chart/{widget}", name="oro_bts_issue_statuses_chart", requirements={"widget"="[\w-]+"})
     * @Template("OroBundleBtsBundle:Dashboard:statuses_chart.html.twig")
     *
     * @param $widget
     * @return array $widgetAttr
     */
    public function chartAction($widget)
    {
        $data = $this->getDoctrine()->getRepository('OroBundleBtsBundle:Issue')->loadIssuesGroupedByStatus();

        $widgetAttr = $this->get('oro_dashboard.widget_configs')->getWidgetAttributesForTwig($widget);
        $widgetAttr['chartView'] = $this->get('oro_chart.view_builder')
            ->setArrayData($data)
            ->setOptions(
                array(
                    'name' => 'bar_chart',
                    'data_schema' => array(
                        'label' => array('field_name' => 'label'),
                        'value' => array(
                            'field_name' => 'cnt',
                        )
                    )
                )
            )
            ->getView();

        return $widgetAttr;
    }

    /**
     * @Route("/user/{userId}", name="oro_bts_issue_user_issues", requirements={"userId"="\d+"})
     * @AclAncestor("oro_bts_issue_view")
     * @Template()
     */
    public function userIssuesAction($userId)
    {
        return ['userId' => $userId];
    }

    /**
     * Create new Issue entity
     *
     * @param  string $typeName
     * @param  User $owner
     * @return Issue
     */
    protected function createEntity($typeName = IssueType::TASK, User $owner = null, Issue $parent = null)
    {
        $entity = new Issue();

        if (null === $owner) {
            $user = $this->container->get("security.context")->getToken()->getUser();
            $entity->setOwner($user);
        } else {
            $entity->setOwner($owner);
        }

        if (null !== $parent) {
            $entity->setParent($parent);
            $entity->setOrganization($parent->getOrganization());
        }

        $type = $this->getDoctrine()
            ->getRepository('OroBundleBtsBundle:IssueType')
            ->findOneByName($typeName);
        $entity->setType($type);

        $priority = $this->getDoctrine()
            ->getRepository('OroBundleBtsBundle:IssuePriority')
            ->findOneByName(IssuePriority::MAJOR);
        $entity->setPriority($priority);

        return $entity;
    }
}
