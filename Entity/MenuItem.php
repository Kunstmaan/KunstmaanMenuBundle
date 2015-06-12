<?php

namespace Kunstmaan\MenuBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Kunstmaan\AdminBundle\Entity\AbstractEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * @ORM\Table(name="kuma_menu_item")
 * @ORM\Entity(repositoryClass="Kunstmaan\MenuBundle\Repository\MenuItemRepository")
 * @Gedmo\Tree(type="nested")
 * @Assert\Callback(methods={"validateEntity"})
 */
class MenuItem extends AbstractEntity
{
    const TYPE_PAGE_LINK = 'page_link';
    const TYPE_URL_LINK = 'url_link';

    /**
     * @var array
     */
    public static $types = array(
        self::TYPE_PAGE_LINK,
        self::TYPE_URL_LINK
    );

    /**
     * @var Menu
     *
     * @ORM\ManyToOne(targetEntity="Menu", inversedBy="items")
     * @ORM\JoinColumn(name="menu_id", referencedColumnName="id")
     * @Assert\NotNull()
     */
    private $menu;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=15, nullable=true)
     * @Assert\NotBlank()
     */
    private $type;

    /**
     * @var NodeTranslation
     *
     * @ORM\ManyToOne(targetEntity="Kunstmaan\NodeBundle\Entity\NodeTranslation")
     * @ORM\JoinColumn(name="node_translation_id", referencedColumnName="id")
     */
    private $nodeTranslation;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", nullable=true)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="url", type="string", nullable=true)
     */
    private $url;

    /**
     * @var boolean
     *
     * @ORM\Column(name="new_window", type="boolean", nullable=true)
     */
    private $newWindow;

    /**
     * @var integer
     *
     * @Gedmo\TreeLeft
     * @ORM\Column(name="lft", type="integer")
     */
    private $lft;

    /**
     * @var integer
     *
     * @Gedmo\TreeLevel
     * @ORM\Column(name="lvl", type="integer")
     */
    private $lvl;

    /**
     * @var integer
     *
     * @Gedmo\TreeRight
     * @ORM\Column(name="rgt", type="integer")
     */
    private $rgt;

    /**
     * @var MenuItem
     *
     * @Gedmo\TreeParent
     * @ORM\ManyToOne(targetEntity="MenuItem", inversedBy="children")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $parent;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="MenuItem", mappedBy="parent")
     * @ORM\OrderBy({"lft" = "ASC"})
     */
    private $children;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->children = new ArrayCollection();
    }

    /**
     * @return Menu
     */
    public function getMenu()
    {
        return $this->menu;
    }

    /**
     * @param Menu $menu
     *
     * @return MenuItem
     */
    public function setMenu($menu)
    {
        $this->menu = $menu;

        return $this;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     *
     * @return MenuItem
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return NodeTranslation
     */
    public function getNodeTranslation()
    {
        return $this->nodeTranslation;
    }

    /**
     * @param NodeTranslation $nodeTranslation
     *
     * @return MenuItem
     */
    public function setNodeTranslation($nodeTranslation)
    {
        $this->nodeTranslation = $nodeTranslation;

        return $this;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     *
     * @return MenuItem
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param string $url
     *
     * @return MenuItem
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isNewWindow()
    {
        return $this->newWindow;
    }

    /**
     * @param boolean $newWindow
     *
     * @return MenuItem
     */
    public function setNewWindow($newWindow)
    {
        $this->newWindow = $newWindow;

        return $this;
    }

    /**
     * @return int
     */
    public function getLft()
    {
        return $this->lft;
    }

    /**
     * @param int $lft
     *
     * @return MenuItem
     */
    public function setLft($lft)
    {
        $this->lft = $lft;

        return $this;
    }

    /**
     * @return int
     */
    public function getLvl()
    {
        return $this->lvl;
    }

    /**
     * @param int $lvl
     *
     * @return MenuItem
     */
    public function setLvl($lvl)
    {
        $this->lvl = $lvl;

        return $this;
    }

    /**
     * @return int
     */
    public function getRgt()
    {
        return $this->rgt;
    }

    /**
     * @param int $rgt
     *
     * @return MenuItem
     */
    public function setRgt($rgt)
    {
        $this->rgt = $rgt;

        return $this;
    }

    /**
     * @return MenuItem
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @param MenuItem $parent
     *
     * @return MenuItem
     */
    public function setParent($parent)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * @param ArrayCollection $children
     *
     * @return MenuItem
     */
    public function setChildren($children)
    {
        $this->children = $children;

        return $this;
    }

    /**
     * @return string
     */
    public function getDisplayTitle()
    {
        if ($this->getType() == self::TYPE_PAGE_LINK) {
            if (!is_null($this->getTitle())) {
                return $this->getTitle();
            } else {
                return $this->getNodeTranslation()->getTitle();
            }
        } else {
            return $this->getTitle();
        }
    }

    /**
     * @return string
     */
    public function getSelectTitle()
    {
        if ($this->getType() == self::TYPE_PAGE_LINK) {
            if (!is_null($this->getTitle())) {
                $title = $this->getTitle();
            } else {
                $title = $this->getNodeTranslation()->getTitle();
            }
        } else {
            $title = $this->getTitle();
        }

        $nrStripes = $this->getLvl() * 4;

        return str_repeat('-' , $nrStripes) . ' ' . $title;
    }

    /**
     * @return string
     */
    public function getDisplayUrl()
    {
        if ($this->getType() == self::TYPE_PAGE_LINK) {
            return $this->getNodeTranslation()->getUrl();
        } else {
            return $this->getUrl();
        }
    }

    /**
     * @return bool
     */
    public function isOnline()
    {
        if ($this->getType() == self::TYPE_URL_LINK) {
           return true;
        } elseif ($this->getNodeTranslation()->isOnline() && !$this->getNodeTranslation()->getNode()->isDeleted() && !$this->getNodeTranslation()->getNode()->getHiddenFromNav()) {
            return true;
        }

        return false;
    }

    /**
     * @param ExecutionContextInterface $context
     */
    public function validateEntity(ExecutionContextInterface $context)
    {
        if ($this->getType() == self::TYPE_PAGE_LINK && !$this->getNodeTranslation()) {
            $context->addViolationAt('nodeTranslation', 'Please select a page');
        } elseif ($this->getType() == self::TYPE_URL_LINK) {
            if (strlen($this->getTitle()) == 0) {
                $context->addViolationAt('title', 'Please set the link title');
            }
            if (strlen($this->getUrl()) == 0) {
                $context->addViolationAt('url', 'Please set the link URL');
            }
        }
    }
}
