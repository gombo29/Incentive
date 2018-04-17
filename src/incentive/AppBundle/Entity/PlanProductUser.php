<?php

namespace incentive\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Plan
 *
 * @ORM\Table(name="plan_product_user")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 */
class PlanProductUser
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
     * @ORM\ManyToOne(targetEntity="CmsUser")
     * @ORM\JoinColumn(name="staff_id", referencedColumnName="id", nullable=true)
     */
    private $staff;

    /**
     * @var string
     *
     * @ORM\Column(name="value", type="string", length=100, nullable=true)
     */
    private $value;


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
     * Set value
     *
     * @param string $value
     *
     * @return PlanProductUser
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get value
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set createdDate
     *
     * @param \DateTime $createdDate
     *
     * @return PlanProductUser
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
     * @return PlanProductUser
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
     * @return PlanProductUser
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
     * @return PlanProductUser
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

    /**
     * Set staff
     *
     * @param \incentive\AppBundle\Entity\CmsUser $staff
     *
     * @return PlanProductUser
     */
    public function setStaff(\incentive\AppBundle\Entity\CmsUser $staff = null)
    {
        $this->staff = $staff;

        return $this;
    }

    /**
     * Get staff
     *
     * @return \incentive\AppBundle\Entity\CmsUser
     */
    public function getStaff()
    {
        return $this->staff;
    }
}
