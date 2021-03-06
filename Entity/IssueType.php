<?php

namespace Oro\Bundle\BtsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Translatable\Translatable;

use JMS\Serializer\Annotation as JMS;

use Oro\Bundle\EntityConfigBundle\Metadata\Annotation\Config;
use Oro\Bundle\EntityConfigBundle\Metadata\Annotation\ConfigField;

/**
 * IssueType
 *
 * @ORM\Table(name="oro_bts_type")
 * @ORM\Entity(repositoryClass="Oro\Bundle\BtsBundle\Entity\Repository\IssueTypeRepository")
 * @Gedmo\TranslationEntity(class="Oro\Bundle\BtsBundle\Entity\IssueTypeTranslation")
 * @Config(
 *      defaultValues={
 *          "entity"={
 *              "icon"="icon-twitter"
 *          }
 *      }
 * )
 * @JMS\ExclusionPolicy("ALL")
 */
class IssueType implements Translatable
{
    const BUG     = 'bug';
    const TASK    = 'task';
    const STORY   = 'story';
    const SUBTASK = 'subtask';

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
     * @ConfigField(
     *  defaultValues={
     *      "importexport"={
     *          "identity"=true
     *      }
     *  }
     * )
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="label", type="string", length=255, nullable=false)
     * @Gedmo\Translatable
     */
    private $label;

    /**
     * @var integer
     *
     * @ORM\Column(name="`order`", type="integer", nullable=false)
     */
    private $order;

    /**
     * @Gedmo\Locale
     */
    protected $locale;

    /**
     * Set name
     *
     * @param string $name
     * @return IssueType
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set label
     *
     * @param string $label
     * @return IssueType
     */
    public function setLabel($label)
    {
        $this->label = $label;

        return $this;
    }

    /**
     * Get label
     *
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * Set order
     *
     * @param integer $order
     * @return IssueType
     */
    public function setOrder($order)
    {
        $this->order = $order;

        return $this;
    }

    /**
     * Get order
     *
     * @return integer
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set locale
     *
     * @param string $locale
     * @return IssueType
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;
        return $this;
    }
    /**
     * Returns locale code
     *
     * @return string
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->getLabel();
    }
}
