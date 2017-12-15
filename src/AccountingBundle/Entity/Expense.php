<?php

namespace AccountingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Expense
 *
 * @ORM\Table(name="expense")
 * @ORM\Entity(repositoryClass="AccountingBundle\Repository\ExpenseRepository")
 */
class Expense
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="string")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="UUID")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=255, nullable=true)
     */
    private $description;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;

    /**
     * @var float
     *
     * @ORM\Column(name="amount", type="float")
     */
    private $amount;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=255)
     */
    private $type;

    /**
    * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User")
    * @ORM\JoinColumn(nullable=false)
    */
    private $author;

    /**
     * @ORM\ManyToMany(targetEntity="UserBundle\Entity\User", inversedBy="expenses")
     * @ORM\JoinTable(name="expense_contributor")
     */
     private $contributors;

     /**
   * @ORM\OneToOne(targetEntity="WishBundle\Entity\wish", cascade={"persist"})
   */
    private $wish;
  

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
     * Set description
     *
     * @param string $description
     *
     * @return Expense
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     *
     * @return Expense
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set amount
     *
     * @param float $amount
     *
     * @return Expense
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * Get amount
     *
     * @return float
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Set type
     *
     * @param string $type
     *
     * @return Expense
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->contributors = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set author
     *
     * @param \UserBundle\Entity\User $author
     *
     * @return Expense
     */
    public function setAuthor(\UserBundle\Entity\User $author)
    {
        $this->author = $author;

        return $this;
    }

    /**
     * Get author
     *
     * @return \UserBundle\Entity\User
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * Add contributor
     *
     * @param \UserBundle\Entity\User $contributor
     *
     * @return Expense
     */
    public function addContributor(\UserBundle\Entity\User $contributor)
    {
        $this->contributors[] = $contributor;

        return $this;
    }

    /**
     * Remove contributor
     *
     * @param \UserBundle\Entity\User $contributor
     */
    public function removeContributor(\UserBundle\Entity\User $contributor)
    {
        $this->contributors->removeElement($contributor);
    }

    /**
     * Get contributors
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getContributors()
    {
        return $this->contributors;
    }

    /**
     * Set wish
     *
     * @param \WishBundle\Entity\wish $wish
     *
     * @return Expense
     */
    public function setWish(\WishBundle\Entity\wish $wish = null)
    {
        $this->wish = $wish;

        return $this;
    }

    /**
     * Get wish
     *
     * @return \WishBundle\Entity\wish
     */
    public function getWish()
    {
        return $this->wish;
    }
}
