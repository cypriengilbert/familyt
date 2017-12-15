<?php

namespace InvitationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Invit
 *
 * @ORM\Table(name="invit")
 * @ORM\Entity(repositoryClass="InvitationBundle\Repository\InvitRepository")
 */
class Invit
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
    * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User",cascade={"persist"})
    * @ORM\JoinColumn(nullable=false)
    */
    private $sender;

    /**
     * @var string
     *
     * @ORM\Column(name="receiver", type="string", length=255)
     */
    private $receiver;

    /**
    * @ORM\ManyToOne(targetEntity="UserBundle\Entity\Family",cascade={"persist"})
    * @ORM\JoinColumn(nullable=false)
    */
    private $family;


    /**
     * @var bool
     *
     * @ORM\Column(name="isAccepted", type="boolean")
     */
     private $isAccepted;


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
     * Set sender
     *
     * @param string $sender
     *
     * @return Invit
     */
    public function setSender($sender)
    {
        $this->sender = $sender;

        return $this;
    }

    /**
     * Get sender
     *
     * @return string
     */
    public function getSender()
    {
        return $this->sender;
    }

    /**
     * Set receiver
     *
     * @param string $receiver
     *
     * @return Invit
     */
    public function setReceiver($receiver)
    {
        $this->receiver = $receiver;

        return $this;
    }

    /**
     * Get receiver
     *
     * @return string
     */
    public function getReceiver()
    {
        return $this->receiver;
    }

    /**
     * Set family
     *
     * @param string $family
     *
     * @return Invit
     */
    public function setFamily($family)
    {
        $this->family = $family;

        return $this;
    }

    /**
     * Get family
     *
     * @return string
     */
    public function getFamily()
    {
        return $this->family;
    }

    /**
     * Set isAccepted
     *
     * @param boolean $isAccepted
     *
     * @return Invit
     */
    public function setIsAccepted($isAccepted)
    {
        $this->isAccepted = $isAccepted;

        return $this;
    }

    /**
     * Get isAccepted
     *
     * @return boolean
     */
    public function getIsAccepted()
    {
        return $this->isAccepted;
    }
}
