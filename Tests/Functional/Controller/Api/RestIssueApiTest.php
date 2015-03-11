<?php

namespace Oro\Bundle\BtsBundle\Tests\Functional\Controller\API;

use Oro\Bundle\BtsBundle\Entity\IssuePriority;
use Oro\Bundle\BtsBundle\Entity\IssueResolution;
use Oro\Bundle\BtsBundle\Entity\IssueType;
use Oro\Bundle\BtsBundle\Entity\IssueWorkflowStep;

use Oro\Bundle\TestFrameworkBundle\Test\WebTestCase;
use Oro\Bundle\WorkflowBundle\Model\WorkflowManager;

/**
 * @outputBuffering enabled
 * @dbIsolation
 */
class RestIssueApiTest extends WebTestCase
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
        $this->initClient([], $this->generateWsseAuthHeader());

        $this->em              = static::$kernel->getContainer()->get('doctrine')->getManager();
        $this->workflowManager = static::$kernel->getContainer()->get('oro_workflow.manager');
    }

    /**
     * @return array
     */
    public function testCreate()
    {
        $type = $this->em
            ->getRepository('OroBundleBtsBundle:IssueType')
            ->findOneByName(IssueType::TASK);

        $priority = $this->em
            ->getRepository('OroBundleBtsBundle:IssuePriority')
            ->findOneByName(IssuePriority::MAJOR);

        $request = [
            'issue' => [
                'type'        => (string)$type->getId(),
                'priority'    => (string)$priority->getId(),
                'summary'     => 'Issue_summary',
                'description' => 'Issue_description',
                'owner'       => '1',
            ]
        ];
        $this->client->request('POST', $this->getUrl('oro_bts_api_post_issue'), $request);

        $issue = $this->getJsonResponseContent($this->client->getResponse(), 201);

        $this->assertArrayHasKey('id', $issue);
        $this->assertNotEmpty($issue['id']);

        return $request;
    }

    /**
     * @param $request
     *
     * @depends testCreate
     * @return array
     */
    public function testGet($request)
    {
        $this->client->request(
            'GET',
            $this->getUrl('oro_bts_api_get_issues')
        );

        $entities = $this->getJsonResponseContent($this->client->getResponse(), 200);

        $this->assertNotEmpty($entities);

        $summary     = $request['issue']['summary'];
        $requiredIssue = array_filter(
            $entities,
            function ($a) use ($summary) {
                return $a['summary'] == $summary;
            }
        );

        $this->assertNotEmpty($requiredIssue);
        $requiredIssue = reset($requiredIssue);

        $this->client->request('GET', $this->getUrl('oro_bts_api_get_issue', ['id' => $requiredIssue['id']]));

        $selectedIssue = $this->getJsonResponseContent($this->client->getResponse(), 200);

        $this->assertEquals($requiredIssue, $selectedIssue);

        return $selectedIssue;
    }

    /**
     * @param array $issue
     * @param array $request
     *
     * @depends testGet
     * @depends testCreate
     */
    public function testUpdate($issue, $request)
    {
        $request['issue']['summary'] .= "_Updated";

        $this->client->request('PUT', $this->getUrl('oro_bts_api_put_issue', ['id' => $issue['id']]), $request);
        $result = $this->client->getResponse();
        $this->assertEmptyResponseStatusCodeEquals($result, 204);

        $this->client->request('GET', $this->getUrl('oro_bts_api_get_issue', ['id' => $issue['id']]));

        $issue = $this->getJsonResponseContent($this->client->getResponse(), 200);
        $this->assertEquals($request['issue']['summary'], $issue['summary'], 'issue was not updated');
    }

    /**
     * @param $issue
     *
     * @depends testGet
     */
    public function testDelete($issue)
    {
        $entity = $this->em
            ->getRepository('OroBundleBtsBundle:Issue')
            ->find($issue['id']);

        $resolution = $this->em
            ->getRepository('OroBundleBtsBundle:IssueResolution')
            ->findOneByName(IssueResolution::FIXED);

        $workflowItem = $this->workflowManager->getWorkflowItemByEntity($entity);
        $workflowItem->getData()->set('issue_resolution', $resolution);

        $this->workflowManager->transit($workflowItem, IssueWorkflowStep::CLOSE_TRANSITION);

        $this->client->request('DELETE', $this->getUrl('oro_bts_api_delete_issue', ['id' => $issue['id']]));
        $result = $this->client->getResponse();
        $this->assertEmptyResponseStatusCodeEquals($result, 204);

        $this->client->request('GET', $this->getUrl('oro_bts_api_get_issue', ['id' => $issue['id']]));
        $result = $this->client->getResponse();
        $this->assertJsonResponseStatusCodeEquals($result, 404);
    }
}
