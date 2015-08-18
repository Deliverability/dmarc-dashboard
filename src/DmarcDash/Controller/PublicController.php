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
class PublicController extends Controller
{



    /**
     * @Route("/public", name="public-index")
     */
    public function indexAction()
    {
        $ChartService       = $this->get('Chart');
        $chartPublicOverall = $ChartService->getPublicOverall();

        return $this->render('public/index.html.twig', array(
            'chartPublicOverall' => $chartPublicOverall,
        ));
    }
}
