<?php

namespace incentive\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * BranchDraft
 *
 * @ORM\Table(name="branch_draft")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 */
class BranchDraft
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
     * @ORM\ManyToOne(targetEntity="PlanBranch")
     * @ORM\JoinColumn(name="plan_branch", referencedColumnName="id", nullable=true)
     */
    private $planBranch;

    /**
     * @ORM\ManyToOne(targetEntity="Plan")
     * @ORM\JoinColumn(name="plan_id", referencedColumnName="id", nullable=true)
     */
    private $plan;

    /**
     * @var string
     *
     * @ORM\Column(name="row_number", type="string", length=2, nullable=true)
     */
    private $rowNumber;

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
     * Set rowNumber
     *
     * @param string $rowNumber
     *
     * @return BranchDraft
     */
    public function setRowNumber($rowNumber)
    {
        $this->rowNumber = $rowNumber;

        return $this;
    }

    /**
     * Get rowNumber
     *
     * @return string
     */
    public function getRowNumber()
    {
        return $this->rowNumber;
    }

    /**
     * Set createdDate
     *
     * @param \DateTime $createdDate
     *
     * @return BranchDraft
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
     * @return BranchDraft
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
     * @return BranchDraft
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
     * Set planBranch
     *
     * @param \incentive\AppBundle\Entity\PlanBranch $planBranch
     *
     * @return BranchDraft
     */
    public function setPlanBranch(\incentive\AppBundle\Entity\PlanBranch $planBranch = null)
    {
        $this->planBranch = $planBranch;

        return $this;
    }

    /**
     * Get planBranch
     *
     * @return \incentive\AppBundle\Entity\PlanBranch
     */
    public function getPlanBranch()
    {
        return $this->planBranch;
    }
}
