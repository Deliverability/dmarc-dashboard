<?php



namespace DmarcDash\Controller;



use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormError;


use DmarcDash\Entity\Alias;
use DmarcDash\Entity\Domain;
use DmarcDash\Form\DomainType;



class DomainController extends Controller
{

    /**
     * @Route("/domain/list", name="domain-list")
     */
    public function listAction()
    {
        // Get current user
        $CurUser = $this->get('Core')->getAuthenticatedUser();

        // Get his domains
        $domains = $this->get('Core')->getModelRepository('Domain')->findByUser($CurUser);

        return $this->render('domain/list.html.twig', array(
            'domains'  => $domains,
        ));
    }



    /**
     * @Route(
     *      "/domain/add",
     *      name="domain-add",
     * )
     */
    public function addAction (Request $Request)
    {
        // Get current user
        $CurUser = $this->get('Core')->getAuthenticatedUser();

        // Get form
        $dmRepo = $this->get('Core')->getModelRepository('Domain');
        $form  = $dmRepo->getCreateForm($Request);

        // Assign current user manually
        $entity = $form->getData();
        $entity->setUserId($CurUser->id);


        // Handle submission
        if ($dmRepo->isCreateFormValid($form)) {

            $Domain = $dmRepo->createFromForm($form);
            $this->addFlash('success', 'Domain added: '. $Domain->domainName);

            return $this->redirectToRoute('domain-list');
        }


        // Render template in case of
        return $this->render('domain/add.html.twig', array(
            'form' => $form->createView(),
        ));
    }



    /**
     * @Route(
     *      "/domain/{domain}/delete",
     *      name="domain-delete",
     *      requirements={
     *          "domain": "[-.a-z0-9]+"
     *      }
     * )
     */
    public function deleteAction ($domain)
    {
        // Find the domain
        $dmr = $this->get('ModelRepository_Domain');
        $Domain = $dmr->getByDomain($domain);

        // Delete it
        $Domain->delete();

        // FW to domain list
        return $this->redirectToRoute('domain-list');
    }
}
