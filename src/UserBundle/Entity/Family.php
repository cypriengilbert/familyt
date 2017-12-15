<?php

namespace UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Family
 *
 * @ORM\Table(name="family")
 * @ORM\Entity(repositoryClass="UserBundle\Repository\FamilyRepository")
 */
class Family
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
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
    * @ORM\ManyToMany(targetEntity="UserBundle\Entity\User", mappedBy="families")
    */
    private $members;


    /**
    * @ORM\ManyToOne(
    *      targetEntity="UserBundle\Entity\Family")
    * 
    */
    protected $family_father;

      /**
    * @ORM\ManyToOne(
    *      targetEntity="UserBundle\Entity\Family")
    * 
    */
    protected $family_mother;

    /**
    * @ORM\ManyToOne(
    *      targetEntity="UserBundle\Entity\User")
    * 
    */
    protected $admin;

        /**
    * @ORM\ManyToOne(
    *      targetEntity="UserBundle\Entity\User")
    * 
    */
    protected $father;

        /**
    * @ORM\ManyToOne(
    *      targetEntity="UserBundle\Entity\User")
    * 
    */
    protected $mother;
    
    

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
     * Set name
     *
     * @param string $name
     *
     * @return Family
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->members = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add member
     *
     * @param \UserBundle\Entity\User $member
     *
     * @return Family
     */
    public function addMember(\UserBundle\Entity\User $member)
    {
        $this->members[] = $member;

        return $this;
    }

    /**
     * Remove member
     *
     * @param \UserBundle\Entity\User $member
     */
    public function removeMember(\UserBundle\Entity\User $member)
    {
        $this->members->removeElement($member);
    }

    /**
     * Get members
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getMembers()
    {
        return $this->members;
    }

    /**
     * Set familyFather
     *
     * @param \UserBundle\Entity\Family $familyFather
     *
     * @return Family
     */
    public function setFamilyFather(\UserBundle\Entity\Family $familyFather = null)
    {
        $this->family_father = $familyFather;

        return $this;
    }

    /**
     * Get familyFather
     *
     * @return \UserBundle\Entity\Family
     */
    public function getFamilyFather()
    {
        return $this->family_father;
    }

    /**
     * Set familyMother
     *
     * @param \UserBundle\Entity\Family $familyMother
     *
     * @return Family
     */
    public function setFamilyMother(\UserBundle\Entity\Family $familyMother = null)
    {
        $this->family_mother = $familyMother;

        return $this;
    }

    /**
     * Get familyMother
     *
     * @return \UserBundle\Entity\Family
     */
    public function getFamilyMother()
    {
        return $this->family_mother;
    }

    /**
     * Set admin
     *
     * @param \UserBundle\Entity\User $admin
     *
     * @return Family
     */
    public function setAdmin(\UserBundle\Entity\User $admin = null)
    {
        $this->admin = $admin;

        return $this;
    }

    /**
     * Get admin
     *
     * @return \UserBundle\Entity\User
     */
    public function getAdmin()
    {
        return $this->admin;
    }

    /**
     * Set father
     *
     * @param \UserBundle\Entity\User $father
     *
     * @return Family
     */
    public function setFather(\UserBundle\Entity\User $father = null)
    {
        $this->father = $father;

        return $this;
    }

    /**
     * Get father
     *
     * @return \UserBundle\Entity\User
     */
    public function getFather()
    {
        return $this->father;
    }

    /**
     * Set mother
     *
     * @param \UserBundle\Entity\User $mother
     *
     * @return Family
     */
    public function setMother(\UserBundle\Entity\User $mother = null)
    {
        $this->mother = $mother;

        return $this;
    }

    /**
     * Get mother
     *
     * @return \UserBundle\Entity\User
     */
    public function getMother()
    {
        return $this->mother;
    }
}
