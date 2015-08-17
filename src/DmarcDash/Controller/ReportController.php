<?php



namespace DmarcDash\Controller;



use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;



class ReportController extends Controller
{



    /**
     * @Route(
     *      "/reports",
     *      name = "reports",
     * )
     */
    public function reportsAction ()
    {
        $CurUser = $this->get('Core')->getAuthenticatedUser();

        return $this->render('report/list.html.twig', array(
            'reports' => $CurUser->getReports(),
        ));
    }



    /**
     * @Route(
     *      "/report/upload/form",
     *      name = "report-upload-form",
     * )
     */
    public function uploadFormAction (Request $Request)
    {
        // Get form
        $Repo = $this->get('Core')->getModelRepository('Report');
        $form = $Repo->getUploadForm($Request);

        return $this->renderUploadFormView($form);
    }



    /**
     * @Route(
     *      "/report/upload",
     *      name = "report-upload",
     * )
     */
    public function uploadAction (Request $Request)
    {
        // Get current user
        $CurUser = $this->get('Core')->getAuthenticatedUser();

        // Get form
        $rRepo = $this->get('Core')->getModelRepository('Report');
        $form = $rRepo->getUploadForm($Request);

        // Validate form
        $falseOrParsedReport = $rRepo->isUploadFormValidForUser($form, $CurUser);
        if (!$falseOrParsedReport) {
            return $this->renderUploadFormView($form);
        }
        $parsedReport = $falseOrParsedReport;

        // Try to parse file
        $Report = $rRepo->createReportFromParsedReport($parsedReport);
        $this->addFlash('success', 'Report successfully processed: '. $Report->reportDomain .', id='. $Report->submittedReportId);
        return $this->redirectToRoute('reports');
    }



    /**
     * Return upload form
     */
    protected function renderUploadFormView ($form)
    {
        // Display form
        return $this->render('report/upload.html.twig', array(
            'form' => $form->createView(),
        ));
    }
}
