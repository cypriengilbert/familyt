<?php

namespace NotifBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Authorization
 *
 * @ORM\Table(name="authorization")
 * @ORM\Entity(repositoryClass="NotifBundle\Repository\AuthorizationRepository")
 */
class Authorization
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
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=255)
     */
    private $type;

    /**
     * @var bool
     *
     * @ORM\Column(name="isAuthorized", type="boolean")
     */
    private $isAuthorized;

    /**
    * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User", inversedBy="authorizations")
    * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
    */
    private $owner;

    /**
     * @var string
     *
     * @ORM\Column(name="linked_entity", type="string", length=255)
     */
     private $linked_entity;


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
     * Set type
     *
     * @param string $type
     *
     * @return Authorization
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
     * Set isAuthorized
     *
     * @param boolean $isAuthorized
     *
     * @return Authorization
     */
    public function setIsAuthorized($isAuthorized)
    {
        $this->isAuthorized = $isAuthorized;

        return $this;
    }

    /**
     * Get isAuthorized
     *
     * @return bool
     */
    public function getIsAuthorized()
    {
        return $this->isAuthorized;
    }

    /**
     * Set owner
     *
     * @param \UserBundle\Entity\User $owner
     *
     * @return Authorization
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
     * Set linkedEntity
     *
     * @param string $linkedEntity
     *
     * @return Authorization
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
