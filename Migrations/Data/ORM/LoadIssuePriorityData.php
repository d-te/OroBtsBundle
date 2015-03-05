<?php

namespace Oro\Bundle\BtsBundle\Migrations\Data\ORM;

use Doctrine\Common\Persistence\ObjectManager;

use Oro\Bundle\TranslationBundle\DataFixtures\AbstractTranslatableEntityFixture;

use Oro\Bundle\BtsBundle\Entity\IssuePriority;

class LoadIssuePriorityData extends AbstractTranslatableEntityFixture
{
    const PREFIX = 'issue.priority';

    /**
     * @var array
     */
    protected $items = array(
        array('order' => 10, 'name' => IssuePriority::TRIVIAL),
        array('order' => 20, 'name' => IssuePriority::MINOR),
        array('order' => 30, 'name' => IssuePriority::MAJOR),
        array('order' => 40, 'name' => IssuePriority::CRITICAL),
        array('order' => 50, 'name' => IssuePriority::BLOCKER),
    );

    /**
     * Load statuses with translation to DB
     *
     * @param ObjectManager $objectManager
     */
    protected function loadEntities(ObjectManager $objectManager)
    {
        $repository = $objectManager->getRepository('OroBundleBtsBundle:IssuePriority');

        $locales = $this->getTranslationLocales();

        foreach ($locales as $locale) {
            foreach ($this->items as $item) {
                $type = $repository->findOneBy(array('name' => $item['name']));

                if (!$type) {
                    $type = new IssuePriority();
                    $type->setName($item['name']);
                    $type->setOrder($item['order']);
                }

                $label = $this->translate($item['name'], static::PREFIX, $locale);

                $type->setLocale($locale)->setLabel($label);

                $objectManager->persist($type);
            }

            $objectManager->flush();
        }
    }
}
