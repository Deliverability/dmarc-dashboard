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
class IndexController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction()
    {
        return $this->render('index/index.html.twig', array(
            'data'     => "asdf",
        ));
    }
}
