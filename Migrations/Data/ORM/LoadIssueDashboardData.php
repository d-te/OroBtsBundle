<?php

namespace Oro\Bundle\BtsBundle\Migrations\Data\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

use Oro\Bundle\DashboardBundle\Migrations\Data\ORM\AbstractDashboardFixture;

class LoadIssueDashboardData extends AbstractDashboardFixture implements DependentFixtureInterface
{
    /**
     * {@inheritdoc}
     */
    public function getDependencies()
    {
        return array(
            'Oro\Bundle\UserBundle\Migrations\Data\ORM\LoadAdminUserData',
            'Oro\Bundle\BtsBundle\Migrations\Data\ORM\LoadIssuePriorityData',
            'Oro\Bundle\BtsBundle\Migrations\Data\ORM\LoadIssueTypeData',
            'Oro\Bundle\BtsBundle\Migrations\Data\ORM\LoadIssueResolutionData',
        );
    }

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $main = $this->findAdminDashboardModel($manager, 'main');

        if ($main) {
            $main->addWidget(
                $this->createWidgetModel('issues_activities', [0, 40])
            );

            $main->addWidget(
                $this->createWidgetModel('issues_statuses_chart', [0, 50])
            );

            $manager->flush();
        }
    }
}
