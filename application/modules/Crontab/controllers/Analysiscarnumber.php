<?php
/**
 * User: wgg
 * Date: 19-4-14
 * Time: 下午4:12
 */
class AnalysisCarNumberController extends Yaf_Controller_Abstract
{
    //查询 车牌信息  处理车牌归属地存储
    public function AnalysisCarAction()
    {
        $carDayData = CarInfoModel::getDayCarData();
        
        $data_values = '';
        foreach ($carDayData['car_data'] as $v)
        {
            $data_values .= "('".implode("','",$v)."'),";
        }
        $data_values = substr($data_values,0,-1);
        if($data_values != ''){
            $conn = Db::Get();
            $conn->Begin();
            $result = CarFromModel::InsertBySql($data_values);
            if(!$result){
                $conn->Rollback();
                return false;
            }
            $result = CarInfoModel::ChangeAnalysisid($carDayData['ids']);
            if(!$result){
                $conn->Rollback();
                return false;
            }
            $conn->Commit();
        }
        echo 'done';
        return false;
    }
    
}