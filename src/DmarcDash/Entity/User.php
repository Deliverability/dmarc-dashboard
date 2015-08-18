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
use FOS\UserBundle\Entity\User as BaseUser;



/**
 * @ORM\Entity
 * @ORM\Table(
 *      indexes = {
 *              @ORM\Index(name="imapEnabled_idx", columns={"imapEnabled"}),
 *      },
 * )
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $imapEnabled;

    /**
     * @ORM\Column(type="string")
     */
    private $imapHost;

    /**
     * @ORM\Column(type="string")
     */
    private $imapPort;

    /**
     * @ORM\Column(type="string")
     */
    private $imapProtocol;

    /**
     * @ORM\Column(type="string")
     */
    private $imapUsername;

    /**
     * @ORM\Column(type="string")
     */
    private $imapPassword;

    public function __construct()
    {
        parent::__construct();
        // your own logic
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
     * Set imapEnabled
     *
     * @param integer $imapEnabled
     * @return User
     */
    public function setImapEnabled($imapEnabled)
    {
        $this->imapEnabled = $imapEnabled;

        return $this;
    }

    /**
     * Get imapEnabled
     *
     * @return integer 
     */
    public function getImapEnabled()
    {
        return $this->imapEnabled;
    }

    /**
     * Set imapHost
     *
     * @param string $imapHost
     * @return User
     */
    public function setImapHost($imapHost)
    {
        $this->imapHost = $imapHost;

        return $this;
    }

    /**
     * Get imapHost
     *
     * @return string 
     */
    public function getImapHost()
    {
        return $this->imapHost;
    }

    /**
     * Set imapPort
     *
     * @param string $imapPort
     * @return User
     */
    public function setImapPort($imapPort)
    {
        $this->imapPort = $imapPort;

        return $this;
    }

    /**
     * Get imapPort
     *
     * @return string 
     */
    public function getImapPort()
    {
        return $this->imapPort;
    }

    /**
     * Set imapProtocol
     *
     * @param string $imapProtocol
     * @return User
     */
    public function setImapProtocol($imapProtocol)
    {
        $this->imapProtocol = $imapProtocol;

        return $this;
    }

    /**
     * Get imapProtocol
     *
     * @return string 
     */
    public function getImapProtocol()
    {
        return $this->imapProtocol;
    }

    /**
     * Set imapUsername
     *
     * @param string $imapUsername
     * @return User
     */
    public function setImapUsername($imapUsername)
    {
        $this->imapUsername = $imapUsername;

        return $this;
    }

    /**
     * Get imapUsername
     *
     * @return string 
     */
    public function getImapUsername()
    {
        return $this->imapUsername;
    }

    /**
     * Set imapPassword
     *
     * @param string $imapPassword
     * @return User
     */
    public function setImapPassword($imapPassword)
    {
        $this->imapPassword = $imapPassword;

        return $this;
    }

    /**
     * Get imapPassword
     *
     * @return string 
     */
    public function getImapPassword()
    {
        return $this->imapPassword;
    }
}
