<?php

namespace incentive\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * JobTitleUser
 *
 * @ORM\Table(name="job_title_user")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 */
class JobTitleUser
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
     * @ORM\ManyToOne(targetEntity="CmsUser")
     * @ORM\JoinColumn(name="staff_id", referencedColumnName="id", nullable=true)
     */
    private $staff;


    /**
     * @ORM\ManyToOne(targetEntity="JobTitle")
     * @ORM\JoinColumn(name="job_id", referencedColumnName="id", nullable=true)
     */
    private $jobTitle;


    /**
     * @ORM\ManyToOne(targetEntity="Plan")
     * @ORM\JoinColumn(name="plan", referencedColumnName="id", nullable=true)
     */
    private $plan;


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
     * Set createdDate
     *
     * @param \DateTime $createdDate
     *
     * @return JobTitleUser
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
     * @return JobTitleUser
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
     * Set staff
     *
     * @param \incentive\AppBundle\Entity\CmsUser $staff
     *
     * @return JobTitleUser
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

    /**
     * Set jobTitle
     *
     * @param \incentive\AppBundle\Entity\JobTitle $jobTitle
     *
     * @return JobTitleUser
     */
    public function setJobTitle(\incentive\AppBundle\Entity\JobTitle $jobTitle = null)
    {
        $this->jobTitle = $jobTitle;

        return $this;
    }

    /**
     * Get jobTitle
     *
     * @return \incentive\AppBundle\Entity\JobTitle
     */
    public function getJobTitle()
    {
        return $this->jobTitle;
    }

    /**
     * Set plan
     *
     * @param \incentive\AppBundle\Entity\Plan $plan
     *
     * @return JobTitleUser
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
}
