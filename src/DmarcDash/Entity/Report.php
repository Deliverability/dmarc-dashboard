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
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use DmarcDash\ModelRepository\RequestPathModelRepository as ModelRepository;



/**
 * @ORM\Entity
 * @ORM\Table(
 *     uniqueConstraints = {
 *         @ORM\UniqueConstraint(
 *             name    = "domainId_reportDomain_submittedReportId_unique",
 *             columns = {
 *                 "domainId",
 *                 "reportDomain",
 *                 "submittedReportId",
 *             },
 *         ),
 *     },
 *     indexes = {
 *         @ORM\Index(name="timestampStart_idx", columns={"timestampStart"}),
 *         @ORM\Index(name="timestampEnd_idx",   columns={"timestampEnd"}),
 *     },
 * )
 */
class Report
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
    private $domainId;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     */
    private $reportDomain;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     */
    private $submittedReportId;

    /**
     * @ORM\Column(type="integer")
     */
    private $timestampStart;

    /**
     * @ORM\Column(type="integer")
     */
    private $timestampEnd;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     */
    private $policyPublishedDomain;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     */
    private $policyPublishedAdkim;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     */
    private $policyPublishedAspf;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     */
    private $policyPublishedP;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     */
    private $policyPublishedSp = "";


    /**
     * @ORM\Column(type="integer")
     * @Assert\NotBlank()
     */
    private $policyPublishedPct;

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
     * Additional validation
     *
     * @Assert\Callback
     */
    public function validate (ExecutionContextInterface $context)
    {
        /*
         * These checks were moved to ModelRepo class because they are needed there
         * for validation before data passes into database query in existsBy*() methods.
         */

        // Check for syntactic correctness of methodName
        if (!ModelRepository::isPathValid($this->getPath())) {
            $context->buildViolation('Syntax check failed')
                ->atPath('path')
                ->addViolation();
        }
    }

    /**
     * Constructor
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
     * Set domainId
     *
     * @param integer $domainId
     * @return Report
     */
    public function setDomainId($domainId)
    {
        $this->domainId = $domainId;

        return $this;
    }

    /**
     * Get domainId
     *
     * @return integer 
     */
    public function getDomainId()
    {
        return $this->domainId;
    }

    /**
     * Set reportDomain
     *
     * @param string $reportDomain
     * @return Report
     */
    public function setReportDomain($reportDomain)
    {
        $this->reportDomain = $reportDomain;

        return $this;
    }

    /**
     * Get reportDomain
     *
     * @return string 
     */
    public function getReportDomain()
    {
        return $this->reportDomain;
    }

    /**
     * Set submittedReportId
     *
     * @param string $submittedReportId
     * @return Report
     */
    public function setSubmittedReportId($submittedReportId)
    {
        $this->submittedReportId = $submittedReportId;

        return $this;
    }

    /**
     * Get submittedReportId
     *
     * @return string 
     */
    public function getSubmittedReportId()
    {
        return $this->submittedReportId;
    }

    /**
     * Set timestampStart
     *
     * @param integer $timestampStart
     * @return Report
     */
    public function setTimestampStart($timestampStart)
    {
        $this->timestampStart = $timestampStart;

        return $this;
    }

    /**
     * Get timestampStart
     *
     * @return integer 
     */
    public function getTimestampStart()
    {
        return $this->timestampStart;
    }

    /**
     * Set timestampEnd
     *
     * @param integer $timestampEnd
     * @return Report
     */
    public function setTimestampEnd($timestampEnd)
    {
        $this->timestampEnd = $timestampEnd;

        return $this;
    }

    /**
     * Get timestampEnd
     *
     * @return integer 
     */
    public function getTimestampEnd()
    {
        return $this->timestampEnd;
    }

    /**
     * Set policyPublishedDomain
     *
     * @param string $policyPublishedDomain
     * @return Report
     */
    public function setPolicyPublishedDomain($policyPublishedDomain)
    {
        $this->policyPublishedDomain = $policyPublishedDomain;

        return $this;
    }

    /**
     * Get policyPublishedDomain
     *
     * @return string 
     */
    public function getPolicyPublishedDomain()
    {
        return $this->policyPublishedDomain;
    }

    /**
     * Set policyPublishedAdkim
     *
     * @param string $policyPublishedAdkim
     * @return Report
     */
    public function setPolicyPublishedAdkim($policyPublishedAdkim)
    {
        $this->policyPublishedAdkim = $policyPublishedAdkim;

        return $this;
    }

    /**
     * Get policyPublishedAdkim
     *
     * @return string 
     */
    public function getPolicyPublishedAdkim()
    {
        return $this->policyPublishedAdkim;
    }

    /**
     * Set policyPublishedAspf
     *
     * @param string $policyPublishedAspf
     * @return Report
     */
    public function setPolicyPublishedAspf($policyPublishedAspf)
    {
        $this->policyPublishedAspf = $policyPublishedAspf;

        return $this;
    }

    /**
     * Get policyPublishedAspf
     *
     * @return string 
     */
    public function getPolicyPublishedAspf()
    {
        return $this->policyPublishedAspf;
    }

    /**
     * Set policyPublishedP
     *
     * @param string $policyPublishedP
     * @return Report
     */
    public function setPolicyPublishedP($policyPublishedP)
    {
        $this->policyPublishedP = $policyPublishedP;

        return $this;
    }

    /**
     * Get policyPublishedP
     *
     * @return string 
     */
    public function getPolicyPublishedP()
    {
        return $this->policyPublishedP;
    }

    /**
     * Set policyPublishedSp
     *
     * @param string $policyPublishedSp
     * @return Report
     */
    public function setPolicyPublishedSp($policyPublishedSp)
    {
        $this->policyPublishedSp = $policyPublishedSp;

        return $this;
    }

    /**
     * Get policyPublishedSp
     *
     * @return string 
     */
    public function getPolicyPublishedSp()
    {
        return $this->policyPublishedSp;
    }

    /**
     * Set policyPublishedPct
     *
     * @param integer $policyPublishedPct
     * @return Report
     */
    public function setPolicyPublishedPct($policyPublishedPct)
    {
        $this->policyPublishedPct = $policyPublishedPct;

        return $this;
    }

    /**
     * Get policyPublishedPct
     *
     * @return integer 
     */
    public function getPolicyPublishedPct()
    {
        return $this->policyPublishedPct;
    }

    /**
     * Set datetimeCreated
     *
     * @param \DateTime $datetimeCreated
     * @return Report
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
     * @return Report
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
}
