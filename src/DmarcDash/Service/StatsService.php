<?php



/**
 * Namespace definition
 */
namespace DmarcDash\Service;



use Teon\Symfony\Service\AbstractService as ParentService;



/**
 */
class    StatsService
extends  ParentService
{



    /**
     * Get daily overall count - base query
     *
     * @param    Start of the day
     * @return   QueryBuilder instance for this query
     */
    protected function getDailyOverallCount_getBaseQuery ($tsDay)
    {
        // Set times
        $tsStartMin = $tsDay;
        $tsStartMax = $tsStartMin + 86399;   // +10: Cisco sends reports with +3 seconds

        $qb = $this->em->createQueryBuilder();
        $qb->add('select', 'SUM(rr.messageCount) as cnt')
            ->from(    'DmarcDash:Domain',       'd')
            ->leftJoin('DmarcDash:Report',       'r',  'WITH', 'd.id = r.domainId')
            ->leftJoin('DmarcDash:ReportRecord', 'rr', 'WITH', 'r.id = rr.reportId')
            ->andWhere('r.timestampStart >= :tsStartMin')
            ->andWhere('r.timestampStart <= :tsStartMax')
            ->setParameter("tsStartMin", $tsStartMin)
            ->setParameter("tsStartMax", $tsStartMax);

        return $qb;
    }



    /**
     * Get daily overall count - pass
     *
     * @param    Start of the day
     * @return   QueryBuilder instance for this query
     */
    public function getDailyOverallCount_pass_queryOnly ($tsDay)
    {
        $qb = $this->getDailyOverallCount_getBaseQuery($tsDay);
        $qb->andWhere('((rr.policyEvaluatedDkim = :result) OR (rr.policyEvaluatedSpf = :result))');
        $qb->setParameter("result", 'pass');
        return $qb;
    }

    public function getDailyOverallCount_pass ($tsDay)
    {
        $qb = $this->getDailyOverallCount_pass_queryOnly($tsDay);
        return $this->queryAndReturnCount($qb);
    }



    /**
     * Get daily overall count - fail
     *
     * @param    Start of the day
     * @return   QueryBuilder instance for this query
     */
    public function getDailyOverallCount_fail_queryOnly ($tsDay)
    {
        $qb = $this->getDailyOverallCount_getBaseQuery($tsDay);
        $qb->andWhere('((rr.policyEvaluatedDkim = :result) AND (rr.policyEvaluatedSpf = :result))');
        $qb->setParameter("result", 'fail');

        return $qb;
    }

    public function getDailyOverallCount_fail ($tsDay)
    {
        $qb = $this->getDailyOverallCount_fail_queryOnly($tsDay);
        return $this->queryAndReturnCount($qb);
    }



    /**
     * Get daily overall count for user (pass and fail)
     *
     * @param    Start of the day
     * @param    User
     * @return   QueryBuilder instance for this query
     */
    public function getDailyUserOverallCount_pass ($tsDay, $User)
    {
        $qb = $this->getDailyOverallCount_pass_queryOnly($tsDay);
        $qb->andWhere('d.userId = :userId');
        $qb->setParameter("userId", $User->id);

        return $this->queryAndReturnCount($qb);
    }

    public function getDailyUserOverallCount_fail ($tsDay, $User)
    {
        $qb = $this->getDailyOverallCount_fail_queryOnly($tsDay);
        $qb->andWhere('d.userId = :userId');
        $qb->setParameter("userId", $User->id);

        return $this->queryAndReturnCount($qb);
    }



    public function getDailyDomainCount_fail ($tsDay, $Domain)
    {
        $qb = $this->getDailyOverallCount_fail_queryOnly($tsDay);
        $qb->andWhere('d.id = :domainId');
        $qb->setParameter("domainId", $Domain->id);

        return $this->queryAndReturnCount($qb);
    }



    /**
     * Convert queryBuilder instance into query, execute it, and return number of items
     *
     * @param    QueryBuilder
     * @return   int       Number of items calucalted
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
