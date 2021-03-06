<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Build
 *
 * @ORM\Table(name="building")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\BuildingRepository")
 */
class Building
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
     * @ORM\Column(name="streetName", type="string", length=255)
     * @Assert\NotBlank()
     */
    private $streetName;

    /**
     * @var int
     *
     * @ORM\Column(name="buildingNumber", type="integer")
     * @Assert\NotBlank()
     */
    private $buildingNumber;

    /**
     * @var float
     *
     * @ORM\Column(name="coordinateX", type="float")
     * @Assert\NotBlank()
     */
    private $coordinateX;

    /**
     * @var float
     *
     * @ORM\Column(name="coordinateY", type="float")
     * @Assert\NotBlank()
     */
    private $coordinateY;

   /**
    * @var ArrayCollection
    * @ORM\OneToMany(targetEntity="Firm", mappedBy="categories")
    */
    private $firms;

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
     * Set streetName
     *
     * @param string $streetName
     *
     * @return Building
     */
    public function setStreetName($streetName)
    {
        $this->streetName = $streetName;

        return $this;
    }

    /**
     * Get streetName
     *
     * @return string
     */
    public function getStreetName()
    {
        return $this->streetName;
    }

    /**
     * Set buildingNumber
     *
     * @param integer $buildingNumber
     *
     * @return Building
     */
    public function setBuildingNumber($buildingNumber)
    {
        $this->buildingNumber = $buildingNumber;

        return $this;
    }

    /**
     * Get buildingNumber
     *
     * @return int
     */
    public function getBuildingNumber()
    {
        return $this->buildingNumber;
    }

    /**
     * Set coordinateX
     *
     * @param float $coordinateX
     *
     * @return Building
     */
    public function setCoordinateX($coordinateX)
    {
        $this->coordinateX = $coordinateX;

        return $this;
    }

    /**
     * Get coordinateX
     *
     * @return float
     */
    public function getCoordinateX()
    {
        return $this->coordinateX;
    }

    /**
     * Set coordinateY
     *
     * @param float $coordinateY
     *
     * @return Building
     */
    public function setCoordinateY($coordinateY)
    {
        $this->coordinateY = $coordinateY;

        return $this;
    }

    /**
     * Get coordinateY
     *
     * @return float
     */
    public function getCoordinateY()
    {
        return $this->coordinateY;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->firms = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add firm
     *
     * @param \AppBundle\Entity\Firm $firm
     *
     * @return Building
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
