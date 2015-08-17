<?php



/**
 * Namespace definition
 */
namespace DmarcDash\ModelRepository;



/**
 * Namespace imports
 */
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormError;
use Teon\Symfony\Validation\Exception as ValidationException;



/**
 * Model repository
 */
class     ReportModelRepository
extends   AbstractModelRepository
{



    /**
     * Get reports for this user
     *
     * @return   array
     */
    public function getByUser ($User)
    {
        // Check for timeframe overlap - at END
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('r')
            ->from('DmarcDash:Report', 'r')
            ->leftJoin('DmarcDash:Domain', 'd', 'WITH', 'r.domainId = d.id')
            ->where('d.userId = :userId')
            ->setParameter("userId", $User->id)
            ->orderBy('r.timestampStart', 'DESC');
        $query = $qb->getQuery();
        $entities = $query->getResult();

        return $this->getModels($entities);
    }



    /**
     * Instantiate createForm, populate it with request data if applicable
     *
     * @param    \Symfony\Component\HttpFoundation\Request object or data array
     * @return   Model\DomainModel
     */
    public function getUploadForm ($requestOrDataArray, array $options=array())
    {
        $formBuilder = $this->Core->getFormFactory();
        $newEntity   = new $this->entityClass();
        $createForm  = $formBuilder->create(new \DmarcDash\Form\ReportUploadType(), null, $options);

        // Bind request data to form
        if (is_array($requestOrDataArray)) {
            $createForm->submit($requestOrDataArray);
        } else {
            $createForm->handleRequest($requestOrDataArray);
        }

        return $createForm;
    }



    /**
     * Validate form with embedded data - is it suitable for creation of new model?
     *
     * In reality this method should be called by controller. If it fails, false is returned.
     * When called from createFromForm(), it throws exception.
     *
     * @param    Form with bound data to validate
     * @param    Validation failure throws exception? (mostly used by createFromForm() method for final checkup)
     * @return   bool false | parsedReport object? todo
     */
    public function isUploadedFileValidForUser ($filePath, $User, $origFileName, $form, $throws=false)
    {
        // Skip generic validation - we are adding errors, thus we prevent looped processing
        //if (!parent::isFormValid($form, $throws)) {
        //    return $this->formValidationFailure($throws);
        //}

        // Get repositories
        $dRepo = $this->getModelRepository('Domain');
        $rRepo = $this->getModelRepository('Report');

        // Try to parse file
        try {
            $parsedReport = $rRepo->parseReportFile($filePath);
        } catch (\Exception $e) {
            $form->addError(new FormError("File parsing failed: filename=$origFileName, error=". $e->getMessage()));
            return $this->formValidationFailure($throws);
        }

        // Check if report domain is indeed owned by this user
        // Auth error is the same to prevent unauthorized domain-in-db-check.
        // There could still be possible time-analysis-based attack here, but let's keep our focus on functionality:)
        $reportForDomain = $parsedReport->policy_published->domain;
        $authError = new FormError("Domain is not in your profile: $reportForDomain");

        if (!$dRepo->existsByDomainName($reportForDomain)) {
            $form->addError($authError);
            return $this->formValidationFailure($throws);
        }
        $Domain = $dRepo->getByDomainName($reportForDomain);

        if (!$Domain->isOwnedBy($User)) {
            $form->addError($authError);
            return $this->formValidationFailure($throws);
        }

        // Check if this report is already uploaded
        $reportingDomain   = $parsedReport->report_metadata->org_name;
        $submittedReportId = $parsedReport->report_metadata->report_id;
        $reportStart       = $parsedReport->report_metadata->date_range->begin;
        $reportEnd         = $parsedReport->report_metadata->date_range->end;
        if ($rRepo->isAlreadyProcessed($Domain, $reportingDomain, $submittedReportId, $reportStart, $reportEnd)) {
            $form->addError(new FormError("This report has already been processed: for=$reportForDomain, from=$reportingDomain, id=$submittedReportId, start=$reportStart, end=$reportEnd"));
            return $this->formValidationFailure($throws);
        }


        // Valid - MUST RETURN PARSED REPORT TO AVOID DOUBLE PARSING
        return $parsedReport;
    }



    /**
     * Parses raw DMARC report file (either .xml, .zip or .gz)
     *
     * @param    Path to the file
     * @param    Validation failure throws exception? (mostly used by createFromForm() method for final checkup)
     * @return   bool false | parsedReport object? todo
     */
    public function parseReportFile ($reportFilePath)
    {
        return \Teon\Dmarc\Parser\AggregateReportParser::parseFile($reportFilePath, false);
    }



    /**
     * Check if this report has already been processed?
     *
     * @param    ...
     * @return   bool
     */
    public function isAlreadyProcessed (
        $Domain, $reportDomain, $submittedReportId, $reportStart, $reportEnd
    ) {
        // Check by submittedReportId
        $entities = $this->er->findBy(array(
            'domainId'          => $Domain->id,
            'reportDomain'      => $reportDomain,
            'submittedReportId' => $submittedReportId,
        ));
        if (count($entities) > 0) return true;

        // Check for timeframe overlap - at START
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('r')
            ->from('DmarcDash:Report', 'r')
            ->where   ("r.domainId     = :domainId")
            ->andWhere("r.reportDomain = :reportDomain")
            ->andWhere("r.timestampStart < :reportStart")
            ->andWhere("r.timestampEnd   > :reportStart")
            ->setParameter("domainId",     $Domain->id)
            ->setParameter("reportDomain", $reportDomain)
            ->setParameter("reportStart",  $reportStart);
        $query = $qb->getQuery();
        $entities = $query->getResult();
        if (count($entities) > 0) return true;

        // Check for timeframe overlap - at END
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('r')
            ->from('DmarcDash:Report', 'r')
            ->where   ("r.domainId     = :domainId")
            ->andWhere("r.reportDomain = :reportDomain")
            ->andWhere("r.timestampStart < :reportEnd")
            ->andWhere("r.timestampEnd   > :reportEnd")
            ->setParameter("domainId",     $Domain->id)
            ->setParameter("reportDomain", $reportDomain)
            ->setParameter("reportEnd",    $reportEnd);
        $query = $qb->getQuery();
        $entities = $query->getResult();
        if (count($entities) > 0) return true;

        // I guess this report has not been uploaded yet
        return false;
    }



    /**
     * Crete new report and reportrecords from parsedReport data structure
     *
     * @param    parsedReport object
     * @return   ReportModel
     */
    public function createReportFromParsedReport ($parsedReport)
    {
        // Start transaction
        $this->getEntityManager()->getConnection()->beginTransaction();
        try {

            // Get relevant data from database
            $reportForDomainName = $parsedReport->policy_published->domain;
            $Domain = $this->getModelRepository('Domain')->getByDomainName($reportForDomainName);
            $reportByDomainName  = $parsedReport->report_metadata->org_name;


            // Create report entity first
            $newReportEntity = new $this->entityClass();
            $newReportEntity->setDomainId         ($Domain->id);
            $newReportEntity->setReportDomain     ($parsedReport->report_metadata->org_name);
            $newReportEntity->setSubmittedReportId($parsedReport->report_metadata->report_id);
            $newReportEntity->setTimestampStart   ($parsedReport->report_metadata->date_range->begin);
            $newReportEntity->setTimestampEnd     ($parsedReport->report_metadata->date_range->end);

            $newReportEntity->setPolicyPublishedDomain($parsedReport->policy_published->domain);
            $newReportEntity->setPolicyPublishedAdkim ($parsedReport->policy_published->adkim);
            $newReportEntity->setPolicyPublishedAspf  ($parsedReport->policy_published->aspf);
            $newReportEntity->setPolicyPublishedP     ($parsedReport->policy_published->p);
            $newReportEntity->setPolicyPublishedSp    (isset($parsedReport->policy_published->sp) ? $parsedReport->policy_published->sp : "");
            $newReportEntity->setPolicyPublishedPct   ($parsedReport->policy_published->pct);

            // Create new report
            $Report = $this->createFromEntity($newReportEntity);

            // Create all report records too
            $RrRepo = $this->getModelRepository('ReportRecord');
            foreach ($parsedReport->records as $parsedRecord) {
                $newReportRecordEntity = $RrRepo->getNewEntity();
                $newReportRecordEntity->setReportId                  ($Report->id);
                $newReportRecordEntity->setSourceIp                  ($parsedRecord->row->source_ip);
                $newReportRecordEntity->setMessageCount              ($parsedRecord->row->count);

                $newReportRecordEntity->setPolicyEvaluatedDisposition($parsedRecord->row->policy_evaluated->disposition);
                $newReportRecordEntity->setPolicyEvaluatedDkim       ($parsedRecord->row->policy_evaluated->dkim);
                $newReportRecordEntity->setPolicyEvaluatedSpf        ($parsedRecord->row->policy_evaluated->spf);

                $newReportRecordEntity->setIdentifierHeaderFrom      ($parsedRecord->identifiers->header_from);
                $newReportRecordEntity->setIdentifierEnvelopeFrom    (isset($parsedRecord->identifiers->envelope_from) ? $parsedRecord->identifiers->envelope_from : "");

                if (isset($parsedRecord->auth_results->dkim)) {
                    $newReportRecordEntity->setAuthResultDkimDomain      (isset($parsedRecord->auth_results->dkim->domain) ? $parsedRecord->auth_results->dkim->domain : "");
                    $newReportRecordEntity->setAuthResultDkimResult      (isset($parsedRecord->auth_results->dkim->result) ? $parsedRecord->auth_results->dkim->result : "");
                }
                if (isset($parsedRecord->auth_results->spf)) {
                    $newReportRecordEntity->setAuthResultSpfDomain       ($parsedRecord->auth_results->spf->domain);
                    $newReportRecordEntity->setAuthResultSpfResult       ($parsedRecord->auth_results->spf->result);
                }

                $RrRepo->createFromEntity($newReportRecordEntity);
            }

        // Evaluate outcome
        } catch (\Exception $e) {
            $this->getEntityManager()->getConnection()->rollback();
            throw $e;
        }

        // All went fine, commit
        $this->getEntityManager()->getConnection()->commit();

        return $Report;
    }



    /**
     * Get daily totals - pass
     *
     * @return   int       Number of errors in given hour
     */
    public function getDailyCountByUser_pass ($User, $tsDay)
    {
        return $this->getDailyCountByUser_status($User, $tsDay, array('pass', 'neutral'));
    }



    /**
     * Get daily totals - fail
     *
     * @return   int       Number of errors in given hour
     */
    public function getDailyCountByUser_fail ($User, $tsDay)
    {
        return $this->getDailyCountByUser_status($User, $tsDay, array('soft', 'fail'));
    }



    /**
     * Get daily totals
     *
     * @return   int       Number of errors in given hour
     */
    public function getDailyCountByUser_status ($User, $tsDay, $statuses)
    {
        // Set times
        $tsStartMin = $tsDay;
        $tsStartMax = $tsStartMin + 86399;   // +10: Cisco sends reports with +3 seconds

        $qb = $this->em->createQueryBuilder();
        $qb->add('select', 'SUM(rr.messageCount) as cnt')
            ->from(    'DmarcDash:Domain',       'd')
            ->leftJoin('DmarcDash:Report',       'r',  'WITH', 'd.id = r.domainId')
            ->leftJoin('DmarcDash:ReportRecord', 'rr', 'WITH', 'r.id = rr.reportId')
            ->where('d.userId = :userId')
            ->setParameter("userId", $User->id)
            ->andWhere('r.timestampStart >= :tsStartMin')
            ->andWhere('r.timestampStart <= :tsStartMax')
            ->setParameter("tsStartMin", $tsStartMin)
            ->setParameter("tsStartMax", $tsStartMax)
            ->andWhere('rr.authResultSpfResult IN (:passStrings)')
            ->setParameter("passStrings", $statuses);

        return $this->queryAndReturnCount($qb);
    }



    /**
     * Get daily totals
     *
     * @param    Integer   Start of hour to get stats for
     * @return   int       Number of errors in given hour
     */
    protected function queryAndReturnCount ($qb)
    {
        $query = $qb->getQuery();

        $requestStats = $query->getResult();
        $countAll     = 0;
        foreach ($requestStats as $rs) {
            $countAll += $rs['cnt'];
        }

        return $countAll;
    }
}
