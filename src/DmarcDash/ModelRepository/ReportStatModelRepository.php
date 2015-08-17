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
class     ReportStatModelRepository
extends   AbstractModelRepository
{



    /**
     * Add given request to statistics
     *
     * @param    Request to add
     * @return   void
     */
    public function addRequestToStats ($Request)
    {
        $Domain        = $Request->getDomain();
        $RequestMethod = $Request->getRequestMethod();
        $RequestPath   = $Request->getRequestPath();
        $ResponseCode  = $Request->getResponseCode();

        // Only store stats once per hour
        $ts     = $Request->timestamp;
        $tsStat = $ts - ($ts % 3600);
        $dtStat = new \DateTime(gmdate('Y-m-d H:00:00', $tsStat), new \DateTimeZone("UTC"));

        // Get (existing or create new) ReqStat object
        $ReqStat = $this->findOneBy(array(
            'domainId'        => $Domain->id,
            'requestMethodId' => $RequestMethod->id,
            'requestPathId'   => $RequestPath->id,
            'responseCodeId'  => $ResponseCode->id,
            'datetime'        => $dtStat,
        ));
        if (NULL === $ReqStat) {
            $newEntity = new $this->entityClass();
            $newEntity->setDomainId       ($Domain->id);
            $newEntity->setRequestMethodId($RequestMethod->id);
            $newEntity->setRequestPathId  ($RequestPath->id);
            $newEntity->setResponseCodeId ($ResponseCode->id);
            $newEntity->setDatetime       ($dtStat);
            $ReqStat = $this->createFromEntity($newEntity);
        }

        // Bump occurence counter
        $ReqStat->bumpCounter();
    }



    /**
     * Get hourly totals
     *
     * @param    Integer   Start of hour to get stats for
     * @return   int       Number of errors in given hour
     */
    public function getHourlyTotal ($tsStart)
    {
        // Get datetime object for start and end
        $dtStart = new \DateTime(gmdate('Y-m-d H:00:00', $tsStart), new \DateTimeZone("UTC"));
        $dtEnd   = new \DateTime(gmdate('Y-m-d H:59:59', $tsStart), new \DateTimeZone("UTC"));

        $qb = $this->em->createQueryBuilder();
        $qb->add('select', 'SUM(rs.count) as cnt')
            ->from('DmarcDash:RequestStat', 'rs')
            ->where('rs.datetime >= :dtStart')
            ->andWhere('rs.datetime <= :dtEnd')
            ->setParameter('dtStart', $dtStart)
            ->setParameter('dtEnd',   $dtEnd);
        $query = $qb->getQuery();

        $requestStats = $query->getResult();
        $countAll     = 0;
        foreach ($requestStats as $rs) {
            $countAll += $rs['cnt'];
        }

        return $countAll;
    }
}
