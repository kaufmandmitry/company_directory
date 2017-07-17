<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Firm
 *
 * @ORM\Table(name="firm")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\FirmRepository")
 */
class Firm
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
     */
    private $name;

    /**
     * @var ArrayCollection
     *
     * @ORM\Column(name="phoneNumbers", type="array")
     */
    private $phoneNumbers;

    /**
     * @var Building
     *
     * @ORM\OneToOne(targetEntity="Building")
     * @ORM\JoinColumn(name="building_id", referencedColumnName="id")
     * @Assert\NotBlank()
     */
    private $building;

    /**
     * @var ArrayCollection
     * @ORM\Column(name="categories")
     * @ORM\ManyToMany(targetEntity="Category", inversedBy="firms")
     * @ORM\JoinTable(name="x_firm_category")
     */
    private $categories;

    /**
     * Get id
     *
     * @return int
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
     * @return Firm
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
     * Set phoneNumbers
     *
     * @param array $phoneNumbers
     *
     * @return Firm
     */
    public function setPhoneNumbers($phoneNumbers)
    {
        $this->phoneNumbers = $phoneNumbers;

        return $this;
    }

    /**
     * Get phoneNumbers
     *
     * @return array
     */
    public function getPhoneNumbers()
    {
        return $this->phoneNumbers;
    }

    /**
     * Add phoneNumber
     *
     * @param string $phoneNumber
     *
     * @return Firm
     */
    public function addPhoneNumber($phoneNumber)
    {
        $this->phoneNumbers[] = $phoneNumber;

        return $this;
    }

    /**
     * Set building
     *
     * @param Building $building
     *
     * @return Firm
     */
    public function setBuilding($building)
    {
        $this->building = $building;

        return $this;
    }

    /**
     * Get building
     *
     * @return Building
     */
    public function getBuilding()
    {
        return $this->building;
    }

    /**
     * Set categories
     *
     * @param array $categories
     *
     * @return Firm
     */
    public function setCategories($categories)
    {
        $this->categories = $categories;

        return $this;
    }

    /**
     * Get categories
     *
     * @return array
     */
    public function getCategories()
    {
        return $this->categories;
    }

    /**
     * Add category
     *
     * @param $categories
     *
     * @return Firm
     */
    public function addCategory($category)
    {
        $this->categories[] = $category;

        return $this;
    }
}

