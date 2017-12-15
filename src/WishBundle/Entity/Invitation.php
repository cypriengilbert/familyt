<?php

namespace WishBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Invitation
 *
 * @ORM\Table(name="invitation")
 * @ORM\Entity(repositoryClass="WishBundle\Repository\InvitationRepository")
 */
class Invitation
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
    * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User",cascade={"persist"})
    * @ORM\JoinColumn(nullable=false)
    */
    private $user;

        /**
    * @ORM\ManyToOne(targetEntity="EventBundle\Entity\Event",cascade={"persist"})
    * @ORM\JoinColumn(nullable=false)
    */
    private $event;

    /**
     * @var bool
     *
     * @ORM\Column(name="isFamily", type="boolean")
     */
     private $isFamily;

    /**
     * @var string
     *
     * @ORM\Column(name="shortner", type="string", length=255)
     */
    private $shortner;


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
     * Set user
     *
     * @param string $user
     *
     * @return Invitation
     */
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return string
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set isFamily
     *
     * @param string $isFamily
     *
     * @return Invitation
     */
    public function setIsFamily($isFamily)
    {
        $this->isFamily = $isFamily;

        return $this;
    }

    /**
     * Get isFamily
     *
     * @return string
     */
    public function getIsFamily()
    {
        return $this->isFamily;
    }

    /**
     * Set shortner
     *
     * @param string $shortner
     *
     * @return Invitation
     */
    public function setShortner($shortner)
    {
        $this->shortner = $shortner;

        return $this;
    }

    /**
     * Get shortner
     *
     * @return string
     */
    public function getShortner()
    {
        return $this->shortner;
    }

    /**
     * Set event
     *
     * @param \EventBundle\Entity\Event $event
     *
     * @return Invitation
     */
    public function setEvent(\EventBundle\Entity\Event $event)
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
