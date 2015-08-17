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

use Ob\HighchartsBundle\Highcharts\Highchart;



/**
 * Default page controller
 */
class StatController extends Controller
{
    /**
     * @Route("/stat/chart/totals", name="stat-chart-monthly-totals")
     */
    public function chartMonthlyTotalsAction()
    {
        // Displaying stats for this user
        $CurUser = $this->get('Core')->getAuthenticatedUser();

        // Initialize chart
        $ob = new Highchart();
//        $ob->chart->type('spline');   // Set chart type
        $ob->chart->type('area');   // Set chart type
        $ob->title->text('');
        $ob->chart->renderTo('chart-monthly-totals');  // The #id of the div where to render the chart



        // Render categories and fake data
        $categories = array();
        $dataAll    = array();
        $ts         = time();
        $tsDayLast  = $ts - ($ts % 86400);


        $rsRepo = $this->get('Core')->getModelRepository('Report');
        for ($d=-30 ; $d<=0 ; $d++) {

            // Data point value
            $dataPointDayStart = $tsDayLast + ($d * 86400);
            $dataPass[] = $rsRepo->getDailyCountByUser_pass($CurUser, $dataPointDayStart);
            $dataFail[] = $rsRepo->getDailyCountByUser_fail($CurUser, $dataPointDayStart);

            $categories[] = gmdate("d.m.", $dataPointDayStart);
        }

        $series = array(
            array("name" => "Pass", "data" => $dataPass, "color" => "#8888ee"),
            array("name" => "Fail", "data" => $dataFail, "color" => "#D51D1D"),
        );
        $ob->series($series);


        // X-axis configuration
        $ob->xAxis->categories($categories);
        $ob->xAxis->tickLength(0);
        $ob->xAxis->labels(array('step' => 2));


        // Y-axis configuration
        $ob->yAxis->title(array('text'  => ""));
        $ob->yAxis->min(0);


        // Line configuration
        $ob->plotOptions->area(array(
            'lineWidth' => 4,
            'stacking'  => 'normal',
            'marker'    => array(
                'enabled' => false,
            ),
        ));
        $ob->plotOptions->spline(array(
            'lineWidth' => 8,
            'marker'    => array(
                'enabled' => false,
            ),
        ));


        return $this->render('stat/chart-monthly-totals.html.twig', array(
            'chart'     => $ob,
        ));
    }
}
