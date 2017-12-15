<?php

namespace NotifBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Notification
 *
 * @ORM\Table(name="notification")
 * @ORM\Entity(repositoryClass="NotifBundle\Repository\NotificationRepository")
 */
class Notification
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
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=255)
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(name="origin", type="string", length=255)
     */
    private $origin;

    /**
     * @var string
     *
     * @ORM\Column(name="linked_entity", type="string", length=255)
     */
     private $linked_entity;

    /**
    * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User", inversedBy="notifications")
    * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
    */
    private $owner;

    /**
     * @var bool
     *
     * @ORM\Column(name="isSaw", type="boolean", nullable=false)
     */
     private $isSaw;




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
     * @return Notification
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
     * Set type
     *
     * @param string $type
     *
     * @return Notification
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
     * Set origin
     *
     * @param string $origin
     *
     * @return Notification
     */
    public function setOrigin($origin)
    {
        $this->origin = $origin;

        return $this;
    }

    /**
     * Get origin
     *
     * @return string
     */
    public function getOrigin()
    {
        return $this->origin;
    }

    /**
     * Set owner
     *
     * @param \UserBundle\Entity\User $owner
     *
     * @return Notification
     */
    public function setOwner(\UserBundle\Entity\User $owner)
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * Get owner
     *
     * @return \UserBundle\Entity\User
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * Set isSaw
     *
     * @param boolean $isSaw
     *
     * @return Notification
     */
    public function setIsSaw($isSaw)
    {
        $this->isSaw = $isSaw;

        return $this;
    }

    /**
     * Get isSaw
     *
     * @return boolean
     */
    public function getIsSaw()
    {
        return $this->isSaw;
    }

    /**
     * Set linkedEntity
     *
     * @param string $linkedEntity
     *
     * @return Notification
     */
    public function setLinkedEntity($linkedEntity)
    {
        $this->linked_entity = $linkedEntity;

        return $this;
    }

    /**
     * Get linkedEntity
     *
     * @return string
     */
    public function getLinkedEntity()
    {
        return $this->linked_entity;
    }
}
