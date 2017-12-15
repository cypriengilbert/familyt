<?php

namespace WishBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Comment
 *
 * @ORM\Table(name="comment")
 * @ORM\Entity(repositoryClass="WishBundle\Repository\CommentRepository")
 */
class Comment
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
     * @ORM\Column(name="content", type="string", length=255)
     */
    private $content;

    /**
    * @ORM\ManyToOne(
    *      targetEntity="WishBundle\Entity\wish",
    *      inversedBy="comments"
    * )
    * @ORM\JoinColumn(onDelete="CASCADE")
    */
    protected $wish;

    /**
     * @var bool
     *
     * @ORM\Column(name="isEdited", type="boolean")
     */
     private $isEdited;
     


    /**
    * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User", inversedBy="author",cascade={"persist"})
    * @ORM\JoinColumn(nullable=true)
    */
    private $author;


    

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
     * @return Comment
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
     * Set content
     *
     * @param string $content
     *
     * @return Comment
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set wish
     *
     * @param \WishBundle\Entity\wish $wish
     *
     * @return Comment
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

    /**
     * Set author
     *
     * @param \UserBundle\Entity\User $author
     *
     * @return Comment
     */
    public function setAuthor(\UserBundle\Entity\User $author = null)
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
     * Set isEdited
     *
     * @param boolean $isEdited
     *
     * @return Comment
     */
    public function setIsEdited($isEdited)
    {
        $this->isEdited = $isEdited;

        return $this;
    }

    /**
     * Get isEdited
     *
     * @return boolean
     */
    public function getIsEdited()
    {
        return $this->isEdited;
    }
}
