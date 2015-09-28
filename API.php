<?php
/**
 * Piwik - free/libre analytics platform
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 *
 */
namespace Piwik\Plugins\ReportesPersonalizados;

use Piwik\DataTable;
use Piwik\DataTable\Row;

/**
 * API for plugin PluginPrueba
 *
 * @method static \Piwik\Plugins\PluginPrueba\API getInstance()
 */
class API extends \Piwik\Plugin\API
{

    /**
     * Another example method that returns a data table.
     * @param int    $idSite
     * @cparam string $period

     * @return DataTable
     */
    public function getListanavegadores($idSite, $period, $date, $segment = false)
    {
	$data = \Piwik\API\Request::processRequest('Live.getLastVisitsDetails',array(
	'idSite' => $idSite,
	'period' => $period,
	'date' => $date,
	'segment' => $segment /*,
	'numLastVisitorsToFetch' => 100,
	'minTimestamp' => false,
	'flat' => false,
	'doNotFetchActions' => true*/
	));

	$data->applyQueuedFilters();

	$result = $data->getEmptyClone($KeepFilters = false);
	
	foreach ($data->getRows() as $visitRow){
		$browserName = $visitRow->getColumn('browserName');
		
		$browserRow = $result->getRowFromLabel($browserName);
		if($browserRow === false){
			$result->addRowFromSimpleArray(array(
				'label' => $browserName,
				'nb_visits' => 1));
		}
		else {
			$counter = $browserRow->getColumn('nb_visits');
			$browserRow->setColumn('nb_visits',$counter + 1);
		}
	
	} 
        return $result;
    }

    /**
     * Another example method that returns a data table.
     * @param int    $idSite
     * @param string $period
     * @param string $date
     * @param bool|string $segment
     * @return DataTable
     */
    public function getUrlstiemporeal($idSite, $period, $date, $segment = false)
    {	

	$N = 10; // minutos tiempo real
	$now = strtotime('now');
	$data = \Piwik\API\Request::processRequest('Live.getLastVisitsDetails',array(
        	'idSite' => $idSite,
        	'period' => $period,
        	'date' => $date,
        	'segment' => $segment/* ,
        	'numLastVisitorsToFetch' => 100,
        	'minTimestamp' => false,
       	 	'flat' => false,
        	'doNotFetchActions' => false*/
        ));

        $data->applyQueuedFilters();
	$result = $data->getEmptyClone($KeepFilters = false);
	
	foreach ($data->getRows() as $visitRow){
		$timestamp = $visitRow->getColumn('lastActionTimestamp');
		$IP = $visitRow->getColumn('visitIp');
		$date = $visitRow->getColumn('lastActionDateTime');

		$actions = $visitRow->getColumn('actionDetails');
		$len = count($actions);
		$url = $actions[$len-1]['url'];
		if($timestamp >= ($now-($N*60))){
			$result->addRowFromSimpleArray(array(
				'ip' => $IP,
				'date' => $date,
				'url' => $url));
		}
	}
	
	return $result;
    }


    /**
     * Another example method that returns a data table.
     * @param int    $idSite
     * @param string $period
     * @param string $date
     * @param bool|string $segment
     * @return DataTable
     */
    public function getUsuariosNuevos($idSite, $period, $date, $segment = false)
    {

        $data = \Piwik\API\Request::processRequest('Live.getLastVisitsDetails',array(
                'idSite' => $idSite,
                'period' => $period,
                'date' => $date,
                'segment' => $segment
              /*  'numLastVisitorsToFetch' => 100,
                'minTimestamp' => false,
                'flat' => false,
                'doNotFetchActions' => true
*/
        ));

        $data->applyQueuedFilters();
        $result = $data->getEmptyClone($KeepFilters = false);
	
	foreach ($data->getRows() as $visitRow){
		$visitorType = $visitRow->getColumn('visitorType');
		$usersRow = $result->getRowFromLabel($visitorType);
		if($usersRow === false){
			$result->addRowFromSimpleArray(array(
				'label' => $visitorType,
				'numero_usuarios' => 1));
		}
		else{
			$counter = $usersRow->getColumn('numero_usuarios');
			$usersRow->setColumn('numero_usuarios',$counter+1);
		}

	}
	
	$result->filter('AddSummaryRow',array($labelSummaryRow = "Total de usuarios"));	

	return $result;

   } 

}
