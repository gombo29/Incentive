<?php

namespace incentive\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Plan
 *
 * @ORM\Table(name="plan_product")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 */
class PlanProduct
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Plan")
     * @ORM\JoinColumn(name="plan_id", referencedColumnName="id", nullable=true)
     */
    private $plan;

    /**
     * @ORM\ManyToOne(targetEntity="Product")
     * @ORM\JoinColumn(name="product_id", referencedColumnName="id", nullable=true)
     */
    private $product;

    /**
     * @var string
     *
     * @ORM\Column(name="formula", type="string", length=100, nullable=true)
     */
    private $formula;


    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_date", type="datetime", nullable=true)
     */
    private $createdDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_date", type="datetime", nullable=true)
     */
    private $updatedDate;


    /**
     * @ORM\PrePersist
     */
    public function onPrePersist()
    {
        $this->setCreatedDate(new \DateTime("now"));
    }

    /**
     * @ORM\PreUpdate
     */
    public function onPreUpdate()
    {
        $this->setUpdatedDate(new \DateTime("now"));
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
     * Set formula
     *
     * @param string $formula
     *
     * @return PlanProduct
     */
    public function setFormula($formula)
    {
        $this->formula = $formula;

        return $this;
    }

    /**
     * Get formula
     *
     * @return string
     */
    public function getFormula()
    {
        return $this->formula;
    }

    /**
     * Set createdDate
     *
     * @param \DateTime $createdDate
     *
     * @return PlanProduct
     */
    public function setCreatedDate($createdDate)
    {
        $this->createdDate = $createdDate;

        return $this;
    }

    /**
     * Get createdDate
     *
     * @return \DateTime
     */
    public function getCreatedDate()
    {
        return $this->createdDate;
    }

    /**
     * Set updatedDate
     *
     * @param \DateTime $updatedDate
     *
     * @return PlanProduct
     */
    public function setUpdatedDate($updatedDate)
    {
        $this->updatedDate = $updatedDate;

        return $this;
    }

    /**
     * Get updatedDate
     *
     * @return \DateTime
     */
    public function getUpdatedDate()
    {
        return $this->updatedDate;
    }

    /**
     * Set plan
     *
     * @param \incentive\AppBundle\Entity\Plan $plan
     *
     * @return PlanProduct
     */
    public function setPlan(\incentive\AppBundle\Entity\Plan $plan = null)
    {
        $this->plan = $plan;

        return $this;
    }

    /**
     * Get plan
     *
     * @return \incentive\AppBundle\Entity\Plan
     */
    public function getPlan()
    {
        return $this->plan;
    }

    /**
     * Set product
     *
     * @param \incentive\AppBundle\Entity\Product $product
     *
     * @return PlanProduct
     */
    public function setProduct(\incentive\AppBundle\Entity\Product $product = null)
    {
        $this->product = $product;

        return $this;
    }

    /**
     * Get product
     *
     * @return \incentive\AppBundle\Entity\Product
     */
    public function getProduct()
    {
        return $this->product;
    }
}
