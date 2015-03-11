<?php

namespace Oro\Bundle\BtsBundle\Tests\Functional\Entity\Repository;

use Oro\Bundle\TestFrameworkBundle\Test\WebTestCase;

class IssueTypeRepositoryTest extends WebTestCase
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

    public function testLoadTypesWithoutSubtask()
    {
        $types = $this->em
            ->getRepository('OroBundleBtsBundle:IssueType')
            ->loadTypesWithoutSubtask();

        $this->assertCount(3, $types);
    }
}
