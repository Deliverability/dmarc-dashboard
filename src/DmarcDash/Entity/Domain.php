<?php



/**
 * Namespace definition
 */
namespace DmarcDash\Entity;



/**
 * Namespace shortcuts
 */
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;



/**
 * @ORM\Entity
 * @ORM\Table(
 *      uniqueConstraints = {
 *              @ORM\UniqueConstraint(
 *                      name    = "domainName_unique",
 *                      columns = {"domainName"},
 *              ),
 *      },
 *      indexes = {
 *              @ORM\Index(name="userId_idx", columns={"userId"}),
 *      },
 * )
 * @UniqueEntity(
 *     fields    = {
 *         "domainName",
 *     },
 *     errorPath = "domainName",
 *     message   = "Duplicate domain names are not supported.",
 * )
 */
class Domain
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $userId;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     * @Assert\Regex("/^[-a-z0-9]+(\.[-a-z0-9]+)*$/")
     */
    private $domainName;

    /**
     * @ORM\Column(
     *  type    = "datetime",
     *  options = {"comment": "In UTC"},
     * )
     */
    private $datetimeCreated;

    /**
     * @ORM\Column(
     *  type    = "datetime",
     *  options = {"comment": "In UTC"},
     * )
     */
    private $datetimeUpdated;

    /**
     * Contructor
     */
    public function __construct()
    {
        $tzUtc                 = new \DateTimeZone("UTC");
        $this->datetimeCreated = new \DateTime(NULL, $tzUtc);
        $this->datetimeUpdated = new \DateTime(NULL, $tzUtc);
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
     * Set domainName
     *
     * @param string $domainName
     * @return Domain
     */
    public function setDomainName($domainName)
    {
        $this->domainName = $domainName;

        return $this;
    }

    /**
     * Get domainName
     *
     * @return string 
     */
    public function getDomainName()
    {
        return $this->domainName;
    }

    /**
     * Set datetimeCreated
     *
     * @param \DateTime $datetimeCreated
     * @return Domain
     */
    public function setDatetimeCreated($datetimeCreated)
    {
        $this->datetimeCreated = $datetimeCreated;

        return $this;
    }

    /**
     * Get datetimeCreated
     *
     * @return \DateTime 
     */
    public function getDatetimeCreated()
    {
        return $this->datetimeCreated;
    }

    /**
     * Set datetimeUpdated
     *
     * @param \DateTime $datetimeUpdated
     * @return Domain
     */
    public function setDatetimeUpdated($datetimeUpdated)
    {
        $this->datetimeUpdated = $datetimeUpdated;

        return $this;
    }

    /**
     * Get datetimeUpdated
     *
     * @return \DateTime 
     */
    public function getDatetimeUpdated()
    {
        return $this->datetimeUpdated;
    }

    /**
     * Set userId
     *
     * @param integer $userId
     * @return Domain
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * Get userId
     *
     * @return integer 
     */
    public function getUserId()
    {
        return $this->userId;
    }
}
