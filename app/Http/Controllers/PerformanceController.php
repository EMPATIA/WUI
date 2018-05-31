<?php

namespace App\Http\Controllers;

use App\ComModules\LogsRequest;
use App\ComModules\Orchestrator;
use Illuminate\Http\Request;
use App\One\One;
use Exception;

class PerformanceController extends Controller
{
    public function saveDataToDB($cpu, $ioData, $memoryUsed){

        LogsRequest::setDatatoDB($cpu, $memoryUsed, $ioData);
        //dd($response->content());

    }

    public function getPerformanceFromDBToBarGraphs($serverIp, $component,$startRange, $endRange){

        $response = LogsRequest::setPerformanceFromDBForBarsGraph($serverIp, $component,$startRange, $endRange);

        if($response->content()=="time error") return $response->content();
        $result= json_decode( $response->content());

        return $result;

    }

    public function getPerformanceFromDBByComponentServer($name,$timeFilter, $serverIp, $component,$startRange, $endRange){

        $response = LogsRequest::setPerformanceFromDBByComponentServer($name,$timeFilter, $serverIp, $component,$startRange, $endRange);

        if($response->content()=="time error") return $response->content();
        $result= json_decode( $response->content());

        return $result;

    }


    public function getAllServersDB(){

        $result = LogsRequest::getAllServers();
        return $result;

    }


    public function showGraphics(Request $request){
        $componentsData = LogsRequest::getAllComponents();
        $data["comp"]=$componentsData;

        $serversData = LogsRequest::getAllServers();
        $data["ser"]=$serversData;

        return view('private.performance.performance',$data);
    }


    public function loadAllServers(Request $request){

        try{
            $serversData = LogsRequest::getAllServers();
            $componentData = LogsRequest::getAllComponents();
           if($serversData==null){
                return "No servers found";
            }
            $data["servers"]=$serversData;
            $data["components"]=$componentData;

            return view('private.performance.servers', $data);

        }catch (Exception $e){
            throw new Exception('Error getting all servers');
        }
    }

    public function loadDataPerformance(Request $request){

        try{

            $serverIp=$request->serverIp;

            $componentData = LogsRequest::getAllComponents();

            $timeFilter = $request->timeFilter;
            $startRange=null;
            $endRange=null;
            if($timeFilter=="range"){
                $startRange=$request->startRange;
                $endRange=$request->endRange;

            }

            $dataPerformances=[];
            foreach($componentData as $component) {
                $name=PerformanceController::getNameComponent($component);
                $component = $component["name"];
                $dataPerformances[]= PerformanceController::getPerformanceFromDBByComponentServer($name,$timeFilter, $serverIp, $component, $startRange, $endRange);
            }

            $cpus=[];

            if(empty($dataPerformances)) throw new Exception('No performance data to show');
            foreach($dataPerformances as $key=>&$data){

                if($data->dataCollections==null){
                    unset($dataPerformances[$key]);
                }else{
                    foreach($data->cpus as &$cpus){
                        $total = 0;
                        foreach($cpus as $cpu){
                            $total = $total + $cpu->value;
                        }
                        $cpus['total']=['value'=>floatval($total), 'created_at'=>$cpus[0]->created_at];
                    }
                }
            }


            return view('private.performance.graphics', compact('dataPerformances'));
        }catch (Exception $e){
            return view('private.performance.warning', ["message"=> $e->getMessage()]);
        }
    }

    public function loadDataPerformanceBars(Request $request){

        try{
            $serverIp=$request->serverIp;

            $componentData = LogsRequest::getAllComponents();
            if($componentData == null)throw new Exception('no components found');
            $startRange=$request->startRange;
            $endRange=$request->endRange;

            $dataPerformances=[];
            foreach($componentData as $component) {
                $component = $component["name"];
                $dataPerformances[] = PerformanceController::getPerformanceFromDBToBarGraphs($serverIp, $component, $startRange, $endRange);
            }

            if($dataPerformances==null)  throw new Exception('no data performance found');//return "Sem dados a apresentar2.";
            else if($dataPerformances=="time error") return "Confirme a opção de seleção de tempo.";
            $cpus=[];

            foreach($dataPerformances as &$data){
                foreach($data->cpus as &$cpus){
                    $total = 0;
                    foreach($cpus as &$cpu){
                        $total = $total + $cpu->value;
                    }
                    $temp = $cpus[0]->created_at;
                    $day = explode(' ', $temp);
                    $cpus['total']=['value'=>floatval($total), 'created_at'=>$day[0]];
                }

            }
            $times=[];
            $allDaysTemp=[];

           foreach ($dataPerformances as $key=>$data){
               if($data->dataCollections==null){
                    unset($dataPerformances[$key]);
               }else{
                    foreach($data->cpus as $cpu){
                        $day = $cpu["total"]["created_at"];
                        $allDaysTemp[]= $day;
                    }
               }
            }
            $allDays = array_unique ( $allDaysTemp );
            $cpuAvgs=[];
            foreach ($allDays as $day){
                $cpuAvgs[$day]=["count" => 0, "sum"=> 0, "day"=>" "];
                foreach ($dataPerformances as $data){
                    foreach ($data->cpus as $cpu) {
                        if ($cpu["total"]["created_at"] === $day) {
                            $cpuAvgs[$day]["count"]++;
                            $cpuAvgs[$day]["sum"] += $cpu["total"]["value"];
                            $cpuAvgs[$day]["day"] = $day;
                            $cpuAvgs[$day]["values"][] = $cpu["total"]["value"];
                        }
                    }
                }
            }

            if($cpuAvgs==null) throw new Exception('No performance data to show');
            $avgs=[];
            foreach ($cpuAvgs as $cpuAvg){
                try {
                    $avgs[]= ["value" => $cpuAvg["sum"] / $cpuAvg["count"], "day" => $cpuAvg["day"]];

                } catch (Exception $e) {
                    $cpuAvg["avg"]=0;
                }
            }

            $tempArray = [];

            foreach ($allDays as $day){
                foreach($avgs as $avg){

                    if($avg["day"]==$day){
                        $avgTemp = $avg["value"];
                    }

                }
                foreach ($cpuAvgs[$day]["values"] as $value){
                        $temp= $value - $avgTemp;
                        $temp = $temp*$temp;
                        $tempArray[]=$temp;
                }
                $tempStdev = array_sum($tempArray)/count($tempArray);
                $avgDatas[] = ["stDev" => sqrt($tempStdev), "avg" => $avgTemp, "day" => $day];
            }
            return view('private.performance.graphicBars', compact('avgDatas'));
        }catch (Exception $e){
            return view('private.performance.warning', ["message"=> $e->getMessage()]);
        }
    }
    public function getNameComponent($component){
        try {
            $modules = Orchestrator::getModulesList();
            $modulesArray = [];
            foreach ($modules->data as $module) {
                $modulesArray[$module->token] = $module->name;
            }
            $name=$modulesArray[$component["name"]];
            return $name;
            }catch(Exception $e) {
            return 'error getting name of the component';
        }
    }

}
