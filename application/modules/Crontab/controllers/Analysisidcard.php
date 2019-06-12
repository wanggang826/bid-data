<?php
/**
 * User: wgg
 * Date: 19-4-14
 * Time: 下午4:11
 */
class AnalysisIdCardController extends Yaf_Controller_Abstract
{
    //查询 单日 身份证信息  处理身份属性存储
    public function AnalysisIdCardAction()
    {
        $data = VisitorInfoModel::getDayIdCardData();

        $data_values = '';
        foreach ($data['visitorAttData'] as $v)
        {
            $data_values .= "('".implode("','",$v)."'),";
        }
        $data_values = substr($data_values,0,-1);
        $conn = Db::Get();
        $conn->Begin();
        if($data_values != ''){
            $result = VisitorAttModel::InsertBySql($data_values);
            if(!$result){
                $conn->Rollback();
                return false;
            }
        }
        if(!empty($data['provinceData'])){
            $result = VisitorFromModel::Insert($data['provinceData']);
            if(!$result){
                $conn->Rollback();
                return false;
            }
        }
        if(!empty($data['ids'])){
            $result = VisitorInfoModel::ChangeAnalysisid($data['ids']);
            if(!$result){
                $conn->Rollback();
                return false;
            }
        }
        $conn->Commit();
        echo 'done';
        return false;
    }
}