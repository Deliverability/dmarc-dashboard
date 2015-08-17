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
 *     indexes = {
 *         @ORM\Index(name="reportId_idx",             columns={"reportId"}),
 *         @ORM\Index(name="authResultDkimResult_idx", columns={"authResultDkimResult"}),
 *         @ORM\Index(name="authResultSpfResult_idx",  columns={"authResultSpfResult"}),
 *     },
 * )
 */
class ReportRecord
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
    private $reportId;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     */
    private $sourceIp;

    /**
     * @ORM\Column(type="integer")
     */
    private $messageCount;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     */
    private $policyEvaluatedDisposition;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     */
    private $policyEvaluatedDkim;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     */
    private $policyEvaluatedSpf;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     */
    private $identifierEnvelopeFrom = "";

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     */
    private $identifierHeaderFrom = "";

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     */
    private $authResultDkimDomain = "";

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     */
    private $authResultDkimResult = "";

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     */
    private $authResultSpfDomain = "";

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     */
    private $authResultSpfResult = "";

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
     * Set reportId
     *
     * @param integer $reportId
     * @return ReportRecord
     */
    public function setReportId($reportId)
    {
        $this->reportId = $reportId;

        return $this;
    }

    /**
     * Get reportId
     *
     * @return integer 
     */
    public function getReportId()
    {
        return $this->reportId;
    }

    /**
     * Set sourceIp
     *
     * @param string $sourceIp
     * @return ReportRecord
     */
    public function setSourceIp($sourceIp)
    {
        $this->sourceIp = $sourceIp;

        return $this;
    }

    /**
     * Get sourceIp
     *
     * @return string 
     */
    public function getSourceIp()
    {
        return $this->sourceIp;
    }

    /**
     * Set messageCount
     *
     * @param integer $messageCount
     * @return ReportRecord
     */
    public function setMessageCount($messageCount)
    {
        $this->messageCount = $messageCount;

        return $this;
    }

    /**
     * Get messageCount
     *
     * @return integer 
     */
    public function getMessageCount()
    {
        return $this->messageCount;
    }

    /**
     * Set policyEvaluatedDisposition
     *
     * @param string $policyEvaluatedDisposition
     * @return ReportRecord
     */
    public function setPolicyEvaluatedDisposition($policyEvaluatedDisposition)
    {
        $this->policyEvaluatedDisposition = $policyEvaluatedDisposition;

        return $this;
    }

    /**
     * Get policyEvaluatedDisposition
     *
     * @return string 
     */
    public function getPolicyEvaluatedDisposition()
    {
        return $this->policyEvaluatedDisposition;
    }

    /**
     * Set policyEvaluatedDkim
     *
     * @param string $policyEvaluatedDkim
     * @return ReportRecord
     */
    public function setPolicyEvaluatedDkim($policyEvaluatedDkim)
    {
        $this->policyEvaluatedDkim = $policyEvaluatedDkim;

        return $this;
    }

    /**
     * Get policyEvaluatedDkim
     *
     * @return string 
     */
    public function getPolicyEvaluatedDkim()
    {
        return $this->policyEvaluatedDkim;
    }

    /**
     * Set policyEvaluatedSpf
     *
     * @param string $policyEvaluatedSpf
     * @return ReportRecord
     */
    public function setPolicyEvaluatedSpf($policyEvaluatedSpf)
    {
        $this->policyEvaluatedSpf = $policyEvaluatedSpf;

        return $this;
    }

    /**
     * Get policyEvaluatedSpf
     *
     * @return string 
     */
    public function getPolicyEvaluatedSpf()
    {
        return $this->policyEvaluatedSpf;
    }

    /**
     * Set identifierHeaderFrom
     *
     * @param string $identifierHeaderFrom
     * @return ReportRecord
     */
    public function setIdentifierHeaderFrom($identifierHeaderFrom)
    {
        $this->identifierHeaderFrom = $identifierHeaderFrom;

        return $this;
    }

    /**
     * Get identifierHeaderFrom
     *
     * @return string 
     */
    public function getIdentifierHeaderFrom()
    {
        return $this->identifierHeaderFrom;
    }

    /**
     * Set authResultDkimDomain
     *
     * @param string $authResultDkimDomain
     * @return ReportRecord
     */
    public function setAuthResultDkimDomain($authResultDkimDomain)
    {
        $this->authResultDkimDomain = $authResultDkimDomain;

        return $this;
    }

    /**
     * Get authResultDkimDomain
     *
     * @return string 
     */
    public function getAuthResultDkimDomain()
    {
        return $this->authResultDkimDomain;
    }

    /**
     * Set authResultDkimResult
     *
     * @param string $authResultDkimResult
     * @return ReportRecord
     */
    public function setAuthResultDkimResult($authResultDkimResult)
    {
        $this->authResultDkimResult = $authResultDkimResult;

        return $this;
    }

    /**
     * Get authResultDkimResult
     *
     * @return string 
     */
    public function getAuthResultDkimResult()
    {
        return $this->authResultDkimResult;
    }

    /**
     * Set authResultSpfDomain
     *
     * @param string $authResultSpfDomain
     * @return ReportRecord
     */
    public function setAuthResultSpfDomain($authResultSpfDomain)
    {
        $this->authResultSpfDomain = $authResultSpfDomain;

        return $this;
    }

    /**
     * Get authResultSpfDomain
     *
     * @return string 
     */
    public function getAuthResultSpfDomain()
    {
        return $this->authResultSpfDomain;
    }

    /**
     * Set authResultSpfResult
     *
     * @param string $authResultSpfResult
     * @return ReportRecord
     */
    public function setAuthResultSpfResult($authResultSpfResult)
    {
        $this->authResultSpfResult = $authResultSpfResult;

        return $this;
    }

    /**
     * Get authResultSpfResult
     *
     * @return string 
     */
    public function getAuthResultSpfResult()
    {
        return $this->authResultSpfResult;
    }

    /**
     * Set datetimeCreated
     *
     * @param \DateTime $datetimeCreated
     * @return ReportRecord
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
     * @return ReportRecord
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
     * Set identifierEnvelopeFrom
     *
     * @param string $identifierEnvelopeFrom
     * @return ReportRecord
     */
    public function setIdentifierEnvelopeFrom($identifierEnvelopeFrom)
    {
        $this->identifierEnvelopeFrom = $identifierEnvelopeFrom;

        return $this;
    }

    /**
     * Get identifierEnvelopeFrom
     *
     * @return string 
     */
    public function getIdentifierEnvelopeFrom()
    {
        return $this->identifierEnvelopeFrom;
    }
}
