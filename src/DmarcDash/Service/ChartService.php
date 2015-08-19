<?php



/**
 * Namespace definition
 */
namespace DmarcDash\Service;



/**
 * Namespace imports
 */
use Teon\Symfony\Service\AbstractService as ParentService;

use Ob\HighchartsBundle\Highcharts\Highchart;



/**
 * Chart creation (not rendering) service
 */
class    ChartService
extends  ParentService
{



    /**
     * Get publicOverall chart
     *
     * @return   HighChart object
     */
    public function getPublicOverall ()
    {
        // Initialize chart
        $ob = $this->getEmptyChart();
        $ob->chart->renderTo('chart-public-overall');  // The #id of the div where to render the chart


        // Get stats service
        $Stats = $this->Core->getService('Stats');


        // Render categories and fake data
        $categories = array();
        $dataAll    = array();
        $ts         = time();
        $tsDayLast  = $ts - ($ts % 86400);
        for ($d=-60 ; $d<=-1 ; $d++) {   // Only display up to yesterday. Today's reports will start arriving no sooner than tomorrow

            // Data point value
            $dataPointDayStart = $tsDayLast + ($d * 86400);

            // X chart label
            $categories[] = gmdate("d M", $dataPointDayStart);

            // Y values
            $dataPass[] = $Stats->getDailyOverallCount_pass($dataPointDayStart);
            $dataFail[] = $Stats->getDailyOverallCount_fail($dataPointDayStart);

        }


        // X-axis content
        $ob->xAxis->categories($categories);

        // Chart content
        $series = array(
            array("data" => $dataPass, "color" => "#50D923", "name" => "Pass (matches either DKIM or SPF)"),
            array("data" => $dataFail, "color" => "#D95123", "name" => "Fail"),
        );
        $ob->series($series);


        // Render template
        return $ob;
    }



    /**
     * Get userOverall chart
     *
     * @return   HighChart object
     */
    public function getUserOverall ($User)
    {
        // Initialize chart
        $ob = $this->getEmptyChart();
        $ob->chart->renderTo('chart-user-overall');  // The #id of the div where to render the chart


        // Get stats service
        $Stats = $this->Core->getService('Stats');


        // Render categories and fake data
        $categories = array();
        $dataAll    = array();
        $ts         = time();
        $tsDayLast  = $ts - ($ts % 86400);
        for ($d=-60 ; $d<=-1 ; $d++) {   // Only display up to yesterday. Today's reports will start arriving no sooner than tomorrow

            // Data point value
            $dataPointDayStart = $tsDayLast + ($d * 86400);

            // X chart label
            $categories[] = gmdate("d M", $dataPointDayStart);

            // Y values
            $dataPass[] = $Stats->getDailyUserOverallCount_pass($dataPointDayStart, $User);
            $dataFail[] = $Stats->getDailyUserOverallCount_fail($dataPointDayStart, $User);
        }


        // X-axis content
        $ob->xAxis->categories($categories);

        // Chart content
        $series = array(
            array("data" => $dataPass, "color" => "#50D923", "name" => "Pass (matches either DKIM or SPF)"),
            array("data" => $dataFail, "color" => "#D95123", "name" => "Fail"),
        );
        $ob->series($series);


        // Render template
        return $ob;
    }



    /**
     * Get userFailureByDomain chart
     *
     * @return   HighChart object
     */
    public function getFailuresByDomain_forUser ($User)
    {
        // Initialize chart
        $ob = $this->getEmptyChart();


        // Get stats service
        $Stats = $this->Core->getService('Stats');

        // Get all user's domains
        $domains = $User->getDomains();

        // Init empty data arrays
        $chartData = array();
        foreach ($domains as $Domain) {
            $chartData[$Domain->domainName] = array();
        }

        // Render categories and fake data
        $categories = array();
        $dataAll    = array();
        $ts         = time();
        $tsDayLast  = $ts - ($ts % 86400);
        for ($d=-60 ; $d<=0 ; $d++) {

            // Data point value
            $dataPointDayStart = $tsDayLast + ($d * 86400);

            // X chart label
            $categories[] = gmdate("d M", $dataPointDayStart);

            foreach ($domains as $Domain) {
                $chartData[$Domain->domainName][] = $Stats->getDailyDomainCount_fail($dataPointDayStart, $Domain);
            }
        }


        // X-axis content
        $ob->xAxis->categories($categories);

        // Chart content
        $series = array();
        foreach ($domains as $Domain) {
            $series[] = array(
                "name"  => $Domain->domainName ."",
                "data"  => $chartData[$Domain->domainName]
//                "color" => "#50D923",
            );
        }
        $ob->series($series);


        // Render template
        return $ob;
    }



    /**
     * Initializes empty chart
     *
     * @return   Highchart object with no data
     */
    protected function getEmptyChart ()
    {
        // Initialize chart
        $ob = new Highchart();
        $ob->title->text('');   // Ignore title, it is usually set on web page
//        $ob->chart->type('spline');   // Set chart type
        $ob->chart->type('area');   // Set chart type
        $ob->chart->renderTo('chart-default');  // The #id of the div where to render the chart


        // X-axis configuration
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


        return $ob;
    }
}
