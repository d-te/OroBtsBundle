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
     * @Route("/create", defaults={"owner" = null}, name="oro_bts_issue_create")
     * @Template("OroBundleBtsBundle:Issue:update.html.twig")
     * @Acl(id="oro_bts_issue_create", type="entity", permission="CREATE", class="OroBundleBtsBundle:Issue")
     */
    public function createAction()
    {
        return $this->update();
    }

    /**
     * @Route("/{id}/subtask/add", name="oro_bts_issue_add_subtask")
     * @Template("OroBundleBtsBundle:Issue:update.html.twig")
     * @Acl(id="oro_bts_issue_add_subtask", type="entity", permission="EDIT", class="OroBundleBtsBundle:Issue")
     *
     * @param Issue $issue
     */
    public function addSubtaskAction(Issue $entity)
    {
        if (!$entity->getModel()->isStory()) {
            return $this->redirect($this->generateUrl('oro_bts_issue_view', array('id' => $entity->getId())));
        }

        $user = $this->container->get("security.context")->getToken()->getUser();

        $issue = new Issue();
        $issue->setParent($entity);
        $issue->setReporter($user);
        $issue->setOwner($user);
        $issue->setOrganization($entity->getOrganization());

        $type = $this->getDoctrine()
            ->getRepository('OroBundleBtsBundle:IssueType')
            ->findOneByName(IssueType::SUBTASK);
        $issue->setType($type);

        $priority = $this->getDoctrine()
            ->getRepository('OroBundleBtsBundle:IssuePriority')
            ->findOneByName(IssuePriority::MAJOR);
        $issue->setPriority($priority);

        return $this->update($issue);
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
        if (!$entity) {
            $entity = new Issue();

            $user = $this->container->get("security.context")->getToken()->getUser();
            $entity->setOwner($user);

            $type = $this->getDoctrine()
                ->getRepository('OroBundleBtsBundle:IssueType')
                ->findOneByName(IssueType::TASK);
            $entity->setType($type);

            $priority = $this->getDoctrine()
                ->getRepository('OroBundleBtsBundle:IssuePriority')
                ->findOneByName(IssuePriority::MAJOR);
            $entity->setPriority($priority);
        }

        return $this->get('oro_form.model.update_handler')->handleUpdate(
            $entity,
            $this->get('oro_bts.form.issue'),
            function (Issue $entity) {
                return array(
                    'route' => 'oro_bts_issue_update',
                    'parameters' => array('id' => $entity->getId())
                );
            },
            function (Issue $entity) {
                return array(
                    'route' => 'oro_bts_issue_view',
                    'parameters' => array('id' => $entity->getId())
                );
            },
            $this->get('translator')->trans('oro.bts.issue.form.saved.message'),
            $this->get('oro_bts.form.handler.issue')
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
}
