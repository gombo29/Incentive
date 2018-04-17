<?php

namespace incentive\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Plan
 *
 * @ORM\Table(name="monthly_plan_staff")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 */
class MonthlyPlanStaff
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
     * @ORM\JoinColumn(name="plan", referencedColumnName="id", nullable=true)
     */
    private $plan;

    /**
     * @ORM\ManyToOne(targetEntity="Branch")
     * @ORM\JoinColumn(name="branch_id", referencedColumnName="id", nullable=true)
     */
    private $branch;

    /**
     * @ORM\ManyToOne(targetEntity="CmsUser")
     * @ORM\JoinColumn(name="user", referencedColumnName="id", nullable=true)
     */
    private $user;

    /**
     * @var int
     * @ORM\Column(name="timeFund", type="integer", nullable=true)
     */
    private $timeFund;

    /**
     * @var int
     * @ORM\Column(name="relaxTime", type="integer", nullable=true)
     */
    private $relaxTime;


    /**
     * @var int
     * @ORM\Column(name="valuePercent", type="integer", nullable=true)
     */
    private $valuePercent;


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
     * Set timeFund
     *
     * @param integer $timeFund
     *
     * @return MonthlyPlanStaff
     */
    public function setTimeFund($timeFund)
    {
        $this->timeFund = $timeFund;

        return $this;
    }

    /**
     * Get timeFund
     *
     * @return integer
     */
    public function getTimeFund()
    {
        return $this->timeFund;
    }

    /**
     * Set relaxTime
     *
     * @param integer $relaxTime
     *
     * @return MonthlyPlanStaff
     */
    public function setRelaxTime($relaxTime)
    {
        $this->relaxTime = $relaxTime;

        return $this;
    }

    /**
     * Get relaxTime
     *
     * @return integer
     */
    public function getRelaxTime()
    {
        return $this->relaxTime;
    }

    /**
     * Set valuePercent
     *
     * @param integer $valuePercent
     *
     * @return MonthlyPlanStaff
     */
    public function setValuePercent($valuePercent)
    {
        $this->valuePercent = $valuePercent;

        return $this;
    }

    /**
     * Get valuePercent
     *
     * @return integer
     */
    public function getValuePercent()
    {
        return $this->valuePercent;
    }

    /**
     * Set createdDate
     *
     * @param \DateTime $createdDate
     *
     * @return MonthlyPlanStaff
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
     * @return MonthlyPlanStaff
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
     * @return MonthlyPlanStaff
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
     * Set user
     *
     * @param \incentive\AppBundle\Entity\CmsUser $user
     *
     * @return MonthlyPlanStaff
     */
    public function setUser(\incentive\AppBundle\Entity\CmsUser $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \incentive\AppBundle\Entity\CmsUser
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set branch
     *
     * @param \incentive\AppBundle\Entity\Branch $branch
     *
     * @return MonthlyPlanStaff
     */
    public function setBranch(\incentive\AppBundle\Entity\Branch $branch = null)
    {
        $this->branch = $branch;

        return $this;
    }

    /**
     * Get branch
     *
     * @return \incentive\AppBundle\Entity\Branch
     */
    public function getBranch()
    {
        return $this->branch;
    }
}
