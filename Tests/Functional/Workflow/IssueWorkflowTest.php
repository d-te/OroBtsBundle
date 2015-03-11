<?php

namespace Oro\Bundle\BtsBundle\Tests\Functional\Workflow;

use Symfony\Component\DomCrawler\Form;

use Oro\Bundle\BtsBundle\Entity\IssuePriority;
use Oro\Bundle\BtsBundle\Entity\IssueType;
use Oro\Bundle\BtsBundle\Entity\IssueResolution;
use Oro\Bundle\BtsBundle\Entity\IssueWorkflowStep;

use Oro\Bundle\TestFrameworkBundle\Test\WebTestCase;
use Oro\Bundle\WorkflowBundle\Model\WorkflowManager;

/**
 * @outputBuffering enabled
 * @dbIsolation
 */
class IssueControllerTest extends WebTestCase
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * @var WorkflowManager
     */
    protected $workflowManager;

    protected function setUp()
    {
        $this->initClient(
            array(),
            array_merge($this->generateBasicAuthHeader(), array('HTTP_X-CSRF-Header' => 1))
        );

        $this->em              = static::$kernel->getContainer()->get('doctrine')->getManager();
        $this->workflowManager = static::$kernel->getContainer()->get('oro_workflow.manager');
    }

    public function testWorkflow()
    {
        /*Create story*/
        $crawler = $this->client->request('GET', $this->getUrl('oro_bts_issue_create'));

        $type = $this->em
            ->getRepository('OroBundleBtsBundle:IssueType')
            ->findOneByName(IssueType::STORY);

        $priority = $this->em
            ->getRepository('OroBundleBtsBundle:IssuePriority')
            ->findOneByName(IssuePriority::MAJOR);

        /** @var Form $form */
        $form = $crawler->selectButton('Save and Close')->form();
        $form['oro_btsbundle_issue_form[type]']        = (string)$type->getId();
        $form['oro_btsbundle_issue_form[priority]']    = (string)$priority->getId();
        $form['oro_btsbundle_issue_form[summary]']     = 'Story_summary';
        $form['oro_btsbundle_issue_form[description]'] = 'Story_description';
        $form['oro_btsbundle_issue_form[owner]']       = '1';
        $this->client->followRedirects(true);
        $crawler = $this->client->submit($form);

        $result = $this->client->getResponse();
        $this->assertHtmlResponseStatusCodeEquals($result, 200);
        $this->assertContains('Issue saved', $crawler->html());

        /*Get story id*/
        $response = $this->client->requestGrid(
            'issue_grid',
            array(
                'issue_grid[_filter][summary][value]' => 'Story_summary',
            )
        );

        $result = $this->getJsonResponseContent($response, 200);
        $result = reset($result['data']);

        $id = $result['id'];
        /*Get Issue entity*/
        $issue = $this->em
            ->getRepository('OroBundleBtsBundle:Issue')
            ->find($id);

        $resolution = $this->em
            ->getRepository('OroBundleBtsBundle:IssueResolution')
            ->findOneByName(IssueResolution::FIXED);

        /*start workflow test*/
        $workflowItem = $this->workflowManager->getWorkflowItemByEntity($issue);
        $this->assertEquals(IssueWorkflowStep::OPENED, $workflowItem->getCurrentStep()->getName());
        $this->assertCount(3, $this->workflowManager->getTransitionsByWorkflowItem($workflowItem));

        $this->workflowManager->transit($workflowItem, IssueWorkflowStep::START_TRANSITION);
        $this->assertEquals(IssueWorkflowStep::IN_PROGRESS, $workflowItem->getCurrentStep()->getName());
        $this->assertCount(3, $this->workflowManager->getTransitionsByWorkflowItem($workflowItem));

        $workflowItem = $this->workflowManager->getWorkflowItemByEntity($issue);
        $workflowItem->getData()->set('issue_resolution', $resolution);
        $this->workflowManager->transit($workflowItem, IssueWorkflowStep::RESOLVE_TRANSITION);
        $this->assertEquals(IssueWorkflowStep::RESOLVED, $workflowItem->getCurrentStep()->getName());
        $this->assertCount(2, $this->workflowManager->getTransitionsByWorkflowItem($workflowItem));

        $workflowItem = $this->workflowManager->getWorkflowItemByEntity($issue);
        $workflowItem->getData()->set('issue_resolution', $resolution);
        $this->workflowManager->transit($workflowItem, IssueWorkflowStep::CLOSE_TRANSITION);
        $this->assertEquals(IssueWorkflowStep::CLOSED, $workflowItem->getCurrentStep()->getName());
        $this->assertCount(1, $this->workflowManager->getTransitionsByWorkflowItem($workflowItem));

        $workflowItem = $this->workflowManager->getWorkflowItemByEntity($issue);
        $workflowItem->getData()->set('issue_resolution', $resolution);
        $this->workflowManager->transit($workflowItem, IssueWorkflowStep::REOPEN_TRANSITION);
        $this->assertEquals(IssueWorkflowStep::OPENED, $workflowItem->getCurrentStep()->getName());
        $this->assertCount(3, $this->workflowManager->getTransitionsByWorkflowItem($workflowItem));
    }
}
