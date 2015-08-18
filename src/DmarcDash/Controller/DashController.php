<?php



/**
 * Namespace definition
 */
namespace DmarcDash\Controller;



/**
 * Namespace imports
 */
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;



/**
 * Default page controller
 */
class DashController extends Controller
{



    /**
     * @Route("/dash", name="dash-index")
     */
    public function indexAction()
    {
        // Get current user
        $CurUser = $this->get('Core')->getAuthenticatedUser();

        //
        $ChartService       = $this->get('Chart');
        $chartUserOverall = $ChartService->getUserOverall($CurUser);

        return $this->render('dash/index.html.twig', array(
            'chartUserOverall' => $chartUserOverall,
        ));
    }



    /**
     * @Route("/dash/failures-by-domain", name="dash-failures-by-domain")
     */
    public function failuresByDomainAction()
    {
        // Get current user
        $CurUser = $this->get('Core')->getAuthenticatedUser();

        //
        $ChartService = $this->get('Chart');
        $chart        = $ChartService->getFailuresByDomain_forUser($CurUser);
        $divId        = 'chart-failures-by-domain';
        $chart->chart->renderTo($divId);

        return $this->render('dash/failures-by-domain.html.twig', array(
            'chart' => $chart,
            'divId' => $divId,
        ));
    }
}
