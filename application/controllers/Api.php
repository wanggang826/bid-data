<?php


class ApiController extends Yaf_Controller_Abstract
{

    public function getDataAction()
    {
        //获取各景点接待人数　按本日　月　年
        //接待人数同比 按月份统计 同比三年
        $data['reception_num'] = ReceptionModel::getReception();
        $data['reception_compared'] = ReceptionModel::getReceptionCompared();

        //车源地TOP10地区及对应车辆数
        $data['car_from'] = CarFromModel::getCarFromSortData();

        //旅客源地TOP10及对应数量
        $data['visitor_from'] = VisitorFromModel::getVisitorFromSortData();

        //游客属性性别比例及  年龄段比例三年的
        $data['visitor_att'] = VisitorAttModel::getVisitorAttData();

        //酒店入住率当日
        $data['hotel'] = HotelModel::getHotelOccupancy();

        //门票团散比例  三年的
        $data['ticket'] = TicketModel::getTicketRate();

        //旅游收入同比 按月份同比三年
        $data['income'] = TourismIncomeModel::getIncomeData();

        //预警数据
        $data['warn'] = WarnModel::getWarnInfo();

        Response::Json($data, 'success');
        return false;
    }

    //接待人数及票种
    public function getReceptionNumAction()
    {
        $startTime = Request::Post("startTime");
        $endTime   = Request::Post("endTime") ? : date('Y-m-d');
        if(empty($startTime))
        {
            //接待人数
            $receptionData = ReceptionModel::getDateReception();
            //各票种数量及比率
            $ticketData    = TicketModel::getDateTicketType();
        }else
        {
            $receptionData = ReceptionModel::getReceptionByTime($startTime,$endTime);
            $ticketData    = TicketModel::getTicketTypeByTime($startTime,$endTime);
        }
        $outData = ['reception_num'=>$receptionData,'ticket_type'=>$ticketData];
        Response::Json($outData);
        return false;
    }

    //酒店房型入住率
    public function getHotelDataAction()
    {
        $startTime = Request::Post("startTime");
        $endTime   = Request::Post("endTime") ? : date('Y-m-d');
        if(empty($startTime))
        {
            //总入住率
            $totalData  = HotelModel::getHotelOccupancy();
            //各酒店个房型入住率
            $detailData = HotelModel::getHotelDetailData();
        }else
        {
            $totalData  = HotelModel::getHotelOccupancyByTime($startTime,$endTime);
            $detailData = HotelModel::getHotelDetailDataByTime($startTime,$endTime);
        }
        //近七天入住率
        $rateSevenDay = HotelModel::getRateSevenDay();
        $outData = ['total_data'=>$totalData,'detailData'=>$detailData,'rateSevenDay'=>$rateSevenDay];
        Response::Json($outData);
        return false;
    }

    //停车场
    public function getParkingDataAction()
    {
        $startTime = Request::Post("startTime");
        $endTime   = Request::Post("endTime") ? : date('Y-m-d');

        if(empty($startTime))
        {
            $parkingData  = CarInfoModel::getSevenDayPark();
        }
        else
        {
            $parkingData  = CarInfoModel::getParkByTime($startTime,$endTime);
        }
        Response::Json($parkingData);
        return false;
    }

    //团散比
    public function getSingleGroupAction()
    {
        $startTime = Request::Post("startTime");
        $endTime   = Request::Post("endTime") ? : date('Y-m-d');

        $totalStartTime = Request::Post("startTimeTotal");
        $totalEndTime = Request::Post("endTimeTotal")? : date('Y-m-d');

        if(empty($startTime))
        {
            $daySingleGroupData  = TicketModel::getSingleGroup();
        }else
        {
            $daySingleGroupData  = TicketModel::getSingleGroupByTime($startTime,$endTime);
        }

        if(empty($totalStartTime))
        {
            $totalSingleGroupData = TicketModel::getTotalSingleGroup();
        }else
        {
            $totalSingleGroupData = TicketModel::getTotalSingleGroupByTime($totalStartTime,$totalEndTime);
        }
        $outData = ['daySingleGroupData'=>$daySingleGroupData,'totalSingleGroupData'=>$totalSingleGroupData];
        Response::Json($outData);

        return false;
    }
    //游客源地及属性
    public function getVisitorDataAction()
    {
        $startTime = Request::Post("startTime");
        $endTime   = Request::Post("endTime") ? : date('Y-m-d');
        $startTimeSecond = Request::Post("startTimeSecond");
        $endTimeSecond   = Request::Post("endTimeSecond") ? : date('Y-m-d');
        $province  = Request::Post("province") ? : ['宁夏'];

        if (!is_array($province)) {
            Response::Error("提交地区省份数据有误");
        }
        //获取旅行社团人数排名
        $travelAgencyData = VisitorInfoModel::getTravelAgencyData();

        //查询游客属性信息
        if(empty($startTime))
        {
            $visitorAttr  = VisitorInfoModel::getDateVisitorData($province);
        }else
        {
            $visitorAttr  = VisitorInfoModel::getVisitorDataByTime($province,$startTime,$endTime);
        }

        $outData = ['travelAgencyData'=>$travelAgencyData,'visitorAttr'=>['firstData'=>$visitorAttr]];
        //查询两阶段数据对比
        if(!empty($startTime) && !empty($startTimeSecond)){
            $visitorAttrDiff = VisitorInfoModel::getDiffData($province,$startTime,$endTime,$startTimeSecond,$endTimeSecond);
            $outData = ['travelAgencyData'=>$travelAgencyData,'visitorAttr'=>$visitorAttrDiff];
        }
        Response::Json($outData);
        return false;
    }

}
