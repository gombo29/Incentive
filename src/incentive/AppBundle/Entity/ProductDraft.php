<?php

namespace incentive\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ProductDraft
 *
 * @ORM\Table(name="product_draft")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 */
class ProductDraft
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
     * @ORM\ManyToOne(targetEntity="PlanProduct")
     * @ORM\JoinColumn(name="plan_product", referencedColumnName="id", nullable=true)
     */
    private $planProduct;

    /**
     * @ORM\ManyToOne(targetEntity="Plan")
     * @ORM\JoinColumn(name="plan_id", referencedColumnName="id", nullable=true)
     */
    private $plan;

    /**
     * @var string
     *
     * @ORM\Column(name="col_number", type="string", length=2, nullable=true)
     */
    private $colNumber;

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
     * Set colNumber
     *
     * @param string $colNumber
     *
     * @return ProductDraft
     */
    public function setColNumber($colNumber)
    {
        $this->colNumber = $colNumber;

        return $this;
    }

    /**
     * Get colNumber
     *
     * @return string
     */
    public function getColNumber()
    {
        return $this->colNumber;
    }

    /**
     * Set createdDate
     *
     * @param \DateTime $createdDate
     *
     * @return ProductDraft
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
     * @return ProductDraft
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
     * @return ProductDraft
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
     * Set planProduct
     *
     * @param \incentive\AppBundle\Entity\PlanProduct $planProduct
     *
     * @return ProductDraft
     */
    public function setPlanProduct(\incentive\AppBundle\Entity\PlanProduct $planProduct = null)
    {
        $this->planProduct = $planProduct;

        return $this;
    }

    /**
     * Get planProduct
     *
     * @return \incentive\AppBundle\Entity\PlanProduct
     */
    public function getPlanProduct()
    {
        return $this->planProduct;
    }
}
