<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use AppBundle\Entity\Firm;

/**
 * Category
 *
 * @ORM\Table(name="category")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CategoryRepository")
 */
class Category
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     * @Assert\NotBlank()
     */
    private $name;

    /**
     * @ORM\ManyToOne(targetEntity="Category", inversedBy="child_categories")
     * @ORM\JoinColumn(name="parent_category_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $parentCategory;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Category", mappedBy="parentCategory")
     */
    private $childCategories;
    
    /**
     * @var ArrayCollection
     * @ORM\ManyToMany(targetEntity="Firm", mappedBy="categories")
     */
    private $firms;


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->childCategories = new \Doctrine\Common\Collections\ArrayCollection();
        $this->firms = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set name
     *
     * @param string $name
     *
     * @return Category
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
     * Set parentCategory
     *
     * @param \AppBundle\Entity\Category $parentCategory
     *
     * @return Category
     */
    public function setParentCategory(\AppBundle\Entity\Category $parentCategory = null)
    {
        $this->parentCategory = $parentCategory;

        return $this;
    }

    /**
     * Get parentCategory
     *
     * @return \AppBundle\Entity\Category
     */
    public function getParentCategory()
    {
        return $this->parentCategory;
    }

    /**
     * Add childCategory
     *
     * @param \AppBundle\Entity\Category $childCategory
     *
     * @return Category
     */
    public function addChildCategory(\AppBundle\Entity\Category $childCategory)
    {
        $this->childCategories[] = $childCategory;

        return $this;
    }

    /**
     * Remove childCategory
     *
     * @param \AppBundle\Entity\Category $childCategory
     */
    public function removeChildCategory(\AppBundle\Entity\Category $childCategory)
    {
        $this->childCategories->removeElement($childCategory);
    }

    /**
     * Get childCategories
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getChildCategories()
    {
        return $this->childCategories;
    }

    /**
     * Add firm
     *
     * @param \AppBundle\Entity\Firm $firm
     *
     * @return Category
     */
    public function addFirm(\AppBundle\Entity\Firm $firm)
    {
        $this->firms[] = $firm;

        return $this;
    }

    /**
     * Remove firm
     *
     * @param \AppBundle\Entity\Firm $firm
     */
    public function removeFirm(\AppBundle\Entity\Firm $firm)
    {
        $this->firms->removeElement($firm);
    }

    /**
     * Get firms
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getFirms()
    {
        return $this->firms;
    }
}
