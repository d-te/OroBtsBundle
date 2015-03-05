<?php

namespace Oro\Bundle\BtsBundle\Migrations\Data\ORM;

use Doctrine\Common\Persistence\ObjectManager;

use Oro\Bundle\TranslationBundle\DataFixtures\AbstractTranslatableEntityFixture;

use Oro\Bundle\BtsBundle\Entity\IssueType;

class LoadIssueTypeData extends AbstractTranslatableEntityFixture
{
    const PREFIX = 'issue.type';

    /**
     * @var array
     */
    protected $items = array(
        array('order' => 10, 'name' => IssueType::BUG),
        array('order' => 20, 'name' => IssueType::TASK),
        array('order' => 30, 'name' => IssueType::STORY),
        array('order' => 40, 'name' => IssueType::SUBTASK),
    );

    /**
     * Load statuses with translation to DB
     *
     * @param ObjectManager $objectManager
     */
    protected function loadEntities(ObjectManager $objectManager)
    {
        $repository = $objectManager->getRepository('OroBundleBtsBundle:IssueType');

        $locales = $this->getTranslationLocales();

        foreach ($locales as $locale) {
            foreach ($this->items as $item) {
                $type = $repository->findOneBy(array('name' => $item['name']));

                if (!$type) {
                    $type = new IssueType();
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
