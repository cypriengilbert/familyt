<?php

namespace WishBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * wish
 *
 * @ORM\Table(name="wish")
 * @ORM\Entity(repositoryClass="WishBundle\Repository\wishRepository")
 */
class wish
{
    /**
     * @var string
     *
     * @ORM\Column(name="id", type="string")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="UUID")
     */
    private $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=255)
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="url", type="string", length=255, nullable=true)
     */
    private $url;

    /**
     * @var bool
     *
     * @ORM\Column(name="status", type="boolean")
     */
    private $status;

     /**
     * @var bool
     *
     * @ORM\Column(name="isCouple", type="boolean")
     */
     private $isCouple;

        /**
     * @var bool
     *
     * @ORM\Column(name="isFamily", type="boolean")
     */
     private $isFamily;

    /**
    * @ORM\OneToMany(targetEntity="WishBundle\Entity\Comment", mappedBy="wish")
     * @ORM\OrderBy({"date" = "DESC"})
    */
    protected $comments;
    
    /**
    * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User",cascade={"persist"})
    * @ORM\JoinColumn(nullable=false)
    */
    private $author;

        /**
    * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User",cascade={"persist"})
    * @ORM\JoinColumn(nullable=true)
    */
    private $co_author;

    /**
    * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User")
    * @ORM\JoinColumn(nullable=true)
    */
    private $buyer;
  
    /**
    * @ORM\ManyToOne(targetEntity="EventBundle\Entity\Event",cascade={"persist"})
    * @ORM\JoinColumn(nullable=true)
    */
    private $event;
    



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
     * Set date
     *
     * @param \DateTime $date
     *
     * @return wish
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
     * Set description
     *
     * @param string $description
     *
     * @return wish
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
     * Set url
     *
     * @param string $url
     *
     * @return wish
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get url
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set status
     *
     * @param boolean $status
     *
     * @return wish
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return bool
     */
    public function getStatus()
    {
        return $this->status;
    }
    public function __construct()
    {
        $this->comments = new ArrayCollection();
    }

    /**
     * Add comment
     *
     * @param \WishBundle\Entity\Comment $comment
     *
     * @return wish
     */
    public function addComment(\WishBundle\Entity\Comment $comment)
    {
        $this->comments[] = $comment;

        return $this;
    }

    /**
     * Remove comment
     *
     * @param \WishBundle\Entity\Comment $comment
     */
    public function removeComment(\WishBundle\Entity\Comment $comment)
    {
        $this->comments->removeElement($comment);
    }

    /**
     * Get comments
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * Set author
     *
     * @param \UserBundle\Entity\User $author
     *
     * @return wish
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
     * Set buyer
     *
     * @param \UserBundle\Entity\User $buyer
     *
     * @return wish
     */
    public function setBuyer(\UserBundle\Entity\User $buyer = null)
    {
        $this->buyer = $buyer;

        return $this;
    }

    /**
     * Get buyer
     *
     * @return \UserBundle\Entity\User
     */
    public function getBuyer()
    {
        return $this->buyer;
    }

    /**
     * Set isCouple
     *
     * @param boolean $isCouple
     *
     * @return wish
     */
    public function setIsCouple($isCouple)
    {
        $this->isCouple = $isCouple;

        return $this;
    }

    /**
     * Get isCouple
     *
     * @return boolean
     */
    public function getIsCouple()
    {
        return $this->isCouple;
    }

    /**
     * Set isFamily
     *
     * @param boolean $isFamily
     *
     * @return wish
     */
    public function setIsFamily($isFamily)
    {
        $this->isFamily = $isFamily;

        return $this;
    }

    /**
     * Get isFamily
     *
     * @return boolean
     */
    public function getIsFamily()
    {
        return $this->isFamily;
    }

    /**
     * Set coAuthor
     *
     * @param \UserBundle\Entity\User $coAuthor
     *
     * @return wish
     */
    public function setCoAuthor(\UserBundle\Entity\User $coAuthor)
    {
        $this->co_author = $coAuthor;

        return $this;
    }

    /**
     * Get coAuthor
     *
     * @return \UserBundle\Entity\User
     */
    public function getCoAuthor()
    {
        return $this->co_author;
    }

    /**
     * Set event
     *
     * @param \EventBundle\Entity\Event $event
     *
     * @return wish
     */
    public function setEvent(\EventBundle\Entity\Event $event = null)
    {
        $this->event = $event;

        return $this;
    }

    /**
     * Get event
     *
     * @return \EventBundle\Entity\Event
     */
    public function getEvent()
    {
        return $this->event;
    }
}
