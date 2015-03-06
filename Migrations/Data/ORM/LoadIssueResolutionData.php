<?php

namespace Oro\Bundle\BtsBundle\Migrations\Data\ORM;

use Doctrine\Common\Persistence\ObjectManager;

use Oro\Bundle\TranslationBundle\DataFixtures\AbstractTranslatableEntityFixture;

use Oro\Bundle\BtsBundle\Entity\IssueResolution;

/**
 * @codeCoverageIgnore
 */
class LoadIssueResolutionData extends AbstractTranslatableEntityFixture
{
    const PREFIX = 'issue.resolution';

    /**
     * @var array
     */
    protected $items = array(
        array('order' => 10, 'name' => IssueResolution::FIXED),
        array('order' => 20, 'name' => IssueResolution::DUPLICATE),
        array('order' => 30, 'name' => IssueResolution::WONT_FIX),
        array('order' => 40, 'name' => IssueResolution::INCOMPLETE),
        array('order' => 50, 'name' => IssueResolution::CANT_REPRODUCE),
        array('order' => 60, 'name' => IssueResolution::WORK_AS_DESIGNED),
    );

    /**
     * Load statuses with translation to DB
     *
     * @param ObjectManager $objectManager
     */
    protected function loadEntities(ObjectManager $objectManager)
    {
        $repository = $objectManager->getRepository('OroBundleBtsBundle:IssueResolution');

        $locales = $this->getTranslationLocales();

        foreach ($locales as $locale) {
            foreach ($this->items as $item) {
                $type = $repository->findOneBy(array('name' => $item['name']));

                if (!$type) {
                    $type = new IssueResolution();
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
