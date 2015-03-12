<?php

namespace Oro\Bundle\BtsBundle\Tests\Functional\Controller;

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

    public function testIndex()
    {
        $this->client->request('GET', $this->getUrl('oro_bts_issue_index'));
        $result = $this->client->getResponse();
        $this->assertHtmlResponseStatusCodeEquals($result, 200);
    }

    public function testCreate()
    {
        $crawler = $this->client->request('GET', $this->getUrl('oro_bts_issue_create'));

        $type = $this->em
            ->getRepository('OroBundleBtsBundle:IssueType')
            ->findOneByName(IssueType::TASK);

        $priority = $this->em
            ->getRepository('OroBundleBtsBundle:IssuePriority')
            ->findOneByName(IssuePriority::MAJOR);

        /** @var Form $form */
        $form = $crawler->selectButton('Save and Close')->form();
        $form['oro_btsbundle_issue_form[type]']        = (string)$type->getId();
        $form['oro_btsbundle_issue_form[priority]']    = (string)$priority->getId();
        $form['oro_btsbundle_issue_form[summary]']     = 'Issue_summary';
        $form['oro_btsbundle_issue_form[description]'] = 'Issue_description';
        $form['oro_btsbundle_issue_form[owner]']       = '1';
        $this->client->followRedirects(true);
        $crawler = $this->client->submit($form);

        $result = $this->client->getResponse();
        $this->assertHtmlResponseStatusCodeEquals($result, 200);
        $this->assertContains('Issue saved', $crawler->html());
    }

    /**
     * @depend testCreate
     * @return int
     */
    public function testUpdate()
    {
        $response = $this->client->requestGrid(
            'issue_grid',
            array(
                'issue_grid[_filter][summary][value]' => 'Issue_summary',
            )
        );

        $result = $this->getJsonResponseContent($response, 200);
        $result = reset($result['data']);

        $id = $result['id'];
        $crawler = $this->client->request(
            'GET',
            $this->getUrl('oro_bts_issue_update', array('id' => $id))
        );

        /** @var Form $form */
        $form = $crawler->selectButton('Save and Close')->form();
        $form['oro_btsbundle_issue_form[summary]']     = 'Issue_summary_updated';
        $form['oro_btsbundle_issue_form[description]'] = 'Issue_description_updated';

        $this->client->followRedirects(true);
        $crawler = $this->client->submit($form);

        $result = $this->client->getResponse();
        $this->assertHtmlResponseStatusCodeEquals($result, 200);
        $this->assertContains("Issue saved", $crawler->html());

        return $id;
    }

    /**
     * @depends testUpdate
     * @param int $id
     */
    public function testView($id)
    {
        $crawler = $this->client->request(
            'GET',
            $this->getUrl('oro_bts_issue_view', array('id' => $id))
        );

        $result = $this->client->getResponse();
        $this->assertHtmlResponseStatusCodeEquals($result, 200);
        $this->assertRegExp("/Oro-". $id."\s:\sIssue_summary_updated/", $crawler->html());
    }

    /**
     * @depends testUpdate
     * @param int $id
     */
    public function testDelete($id)
    {
        //Transit Issue to 'closed' state
        $this->closeIssue($id);

        //delete Issue
        $this->client->request(
            'DELETE',
            $this->getUrl('oro_bts_api_delete_issue', array('id' => $id))
        );

        $result = $this->client->getResponse();
        $this->assertEmptyResponseStatusCodeEquals($result, 204);

        $this->client->request(
            'GET',
            $this->getUrl('oro_bts_issue_view', array('id' => $id))
        );

        $result = $this->client->getResponse();
        $this->assertHtmlResponseStatusCodeEquals($result, 404);
    }

    public function testSubtask()
    {
        /*Create story*/
        $crawler = $this->client->request('GET', $this->getUrl('oro_bts_issue_create'));

        $type     = $this->em->getRepository('OroBundleBtsBundle:IssueType')->findOneByName(IssueType::STORY);
        $priority = $this->em->getRepository('OroBundleBtsBundle:IssuePriority')->findOneByName(IssuePriority::MAJOR);

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
        /*Create subtask*/
        $crawler = $this->client->request('GET', $this->getUrl('oro_bts_issue_add_subtask', array('id' => $id)));
        /** @var Form $form */
        $form = $crawler->selectButton('Save and Close')->form();
        $form['oro_btsbundle_issue_form[priority]']    = (string)$priority->getId();
        $form['oro_btsbundle_issue_form[summary]']     = 'Subtask_summary';
        $form['oro_btsbundle_issue_form[description]'] = 'Subtask_description';
        $form['oro_btsbundle_issue_form[owner]']       = '1';
        $this->client->followRedirects(true);
        $crawler = $this->client->submit($form);

        $result = $this->client->getResponse();
        $this->assertHtmlResponseStatusCodeEquals($result, 200);
        $this->assertContains('Issue saved', $crawler->html());

        /*Get subtask id*/
        $crawler = $this->client->request('GET', $this->getUrl('oro_bts_issue_view', array('id' => $id)));

        $response = $this->client->requestGrid(
            'issue_grid',
            array(
                'issue_grid[_filter][summary][value]' => 'Subtask_summary',
            )
        );
        $result = $this->getJsonResponseContent($response, 200);
        $result = reset($result['data']);
        $subtaskId = $result['id'];
        /*Get story view page*/
        $crawler = $this->client->request('GET', $this->getUrl('oro_bts_issue_view', array('id' => $id)));

        $result = $this->client->getResponse();
        $this->assertHtmlResponseStatusCodeEquals($result, 200);
        $this->assertRegExp("/<h4 class=\"scrollspy-title\">Subtasks<\/h4>/", $crawler->html());
        $this->assertRegExp("/Oro-". $subtaskId."/", $crawler->html());
        /*Delete subtask*/
        $this->closeIssue($subtaskId);

        $this->client->request('DELETE', $this->getUrl('oro_bts_api_delete_issue', array('id' => $subtaskId)));
        $result = $this->client->getResponse();
        $this->assertEmptyResponseStatusCodeEquals($result, 204);

        $this->client->request('GET', $this->getUrl('oro_bts_issue_view', array('id' => $subtaskId)));
        $result = $this->client->getResponse();
        $this->assertHtmlResponseStatusCodeEquals($result, 404);

        /*Delete story*/
        $this->closeIssue($id);

        $this->client->request('DELETE', $this->getUrl('oro_bts_api_delete_issue', array('id' => $id)));
        $result = $this->client->getResponse();
        $this->assertEmptyResponseStatusCodeEquals($result, 204);

        $this->client->request('GET', $this->getUrl('oro_bts_issue_view', array('id' => $id)));
        $result = $this->client->getResponse();
        $this->assertHtmlResponseStatusCodeEquals($result, 404);
    }

    /**
     * Transit Issue to CLOSED step
     * @param  int $id
     */
    protected function closeIssue($id)
    {
        $issue = $this->em
            ->getRepository('OroBundleBtsBundle:Issue')
            ->find($id);

        $resolution = $this->em
            ->getRepository('OroBundleBtsBundle:IssueResolution')
            ->findOneByName(IssueResolution::FIXED);

        $workflowItem = $this->workflowManager->getWorkflowItemByEntity($issue);
        $workflowItem->getData()->set('issue_resolution', $resolution);

        $this->workflowManager->transit($workflowItem, IssueWorkflowStep::CLOSE_TRANSITION);
    }
}
