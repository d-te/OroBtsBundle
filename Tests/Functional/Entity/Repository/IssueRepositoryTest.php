<?php

namespace Oro\Bundle\BtsBundle\Tests\Functional\Entity\Repository;

use Oro\Bundle\TestFrameworkBundle\Test\WebTestCase;

class IssueRepositoryTest extends WebTestCase
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    protected function setUp()
    {
        $this->initClient(
            array(),
            array_merge($this->generateBasicAuthHeader(), array('HTTP_X-CSRF-Header' => 1))
        );

        $this->em = static::$kernel->getContainer()->get('doctrine')->getManager();
    }

    public function testLoadIssuesGroupedByStatus()
    {
        $issues = $this->em
            ->getRepository('OroBundleBtsBundle:Issue')
            ->loadIssuesGroupedByStatus();

        $this->assertCount(0, $issues);
    }
}
