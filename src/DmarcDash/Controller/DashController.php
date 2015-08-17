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
$response = $this->forward('DmarcDash:Stat:chartMonthlyTotals', array());

        return $this->render('dash/index.html.twig', array(
            'data'     => "asdf",
        ));
    }
}
