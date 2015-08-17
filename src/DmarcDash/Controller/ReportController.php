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

        // Just display if applicable
        if (!$form->isValid()) {
            return $this->renderUploadFormView($form);
        }

        // One or multiple files?
        $uploadedStuff = $form->get('reportFile')->getData();
        if (is_array($uploadedStuff)) {
            $uploadedFiles = $uploadedStuff;
        } else {
            $uploadedFiles = array($uploadedStuff);
        }
        $uploadedFilesCount = count($uploadedFiles);

        // Try to parse each of them
        $errorCount   = 0;
        $successCount = 0;
        foreach ($uploadedFiles as $uploadedFile) {
            try {
                $falseOrParsedReport = $rRepo->isUploadedFileValidForUser(
                    $uploadedFile->getPathname(),
                    $CurUser,
                    $uploadedFile->getClientOriginalName(),
                    $form
                );
            } catch (\Exception $e) {
                $form->addError(new FormError("Report processing error: ". $uploadedFile->getClientOriginalName() .", reason: ". $e->getMessage()));
                $errorCount++;
                continue;
            }

            // Was parsing successfuly?
            if (!$falseOrParsedReport) {
                // Error was already added to form by parser
                $errorCount++;
                continue;
            }
            $parsedReport = $falseOrParsedReport;

            $Report = $rRepo->createReportFromParsedReport($parsedReport);
            $successCount++;
        }

        // Single flashmessage for success, as there seems to be a limit
        if ($successCount > 0) {
            $this->addFlash('success', "Sucessfully parsed $successCount out of $uploadedFilesCount uploaded files.");
        }

        if ($errorCount > 0) {
            $this->addFlash('error', "Processing of some of uploaded files failed ($errorCount out of $uploadedFilesCount)!");
            return $this->renderUploadFormView($form);
        } else {
            return $this->redirectToRoute('reports');
        }
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
