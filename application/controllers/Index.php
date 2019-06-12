<?php


class IndexController extends Controller
{


    public function indexAction()
    {
        $password = Request::Param('password');
        $dateStr = date('m').substr(date('Y'),2,2).date('d');
        $check_password = date('H') < 12 ? $dateStr.'A' :$dateStr.'B';
        if($password != $check_password){
           header('Location:../login');
        }
    }

    public function loginAction()
    {
        $ajax = Request::Post('ajax');
        if($ajax){
            $username = Request::Post('username');
            $password = Request::Post('password');
            $dateStr = date('m').substr(date('Y'),2,2).date('d');
            $checkPassword = date('H') < 12 ? $dateStr.'A' :$dateStr.'B';
            if($checkPassword == $password && $username == 'qingtongxia')
            {
                Response::Json(['username'=>$username,'password'=>$password]);
            }else{
                Response::Error('用户名或密码错误');
            }
        }
    }
    
    public function formDataAction()
    {
        $data = $this->_parseParameter();
        $conn = Db::Get();
        $conn->Begin();
//        $qtxStandarRoom = Yaf_Registry::get(COMMON)->qtxHotel->get('standardRoom');
        //酒店入住
        //获取格式化酒店数据
        $hotel_data = $this->_getHotelData($data);
        if(!empty($hotel_data))
        {
            foreach ($hotel_data as $k => $v)
            {
                $isExist = HotelModel::Detail("hotel_id=".$v['hotel_id']." AND room_type=".$v['room_type']." AND date = '".$data['date']."'");
                if($isExist){
                    $updateData['hotel_occupancy_num'] = $v['hotel_occupancy_num'];
                    $result = HotelModel::Update($isExist['id'],$updateData);
                }else{
                    $result = HotelModel::Insert([
                        'date'                 => $data['date'],
                        'hotel_id'             => $v['hotel_id'],
                        'room_type'            => $v['room_type'],
                        'hotel_occupancy_num'  => $v['hotel_occupancy_num']
                    ]);
                }
                if( $result==0 )
                {
                    $conn->Rollback();
                    Response::Error('保存失败');
                }
            }
//            $data['hotel_occupancy_num'] = $data['hotel_occupancy_num'] == '' ? 0 : $data['hotel_occupancy_num'];
//            $isExist = HotelModel::Detail("date = '".$data['date']."'");
//
//            if($isExist){
//                if($data['hotel_room_num']){
//                    $updateData['hotel_room_num'] = $data['hotel_room_num'];
//                }
//
//                if($data['hotel_occupancy_num']){
//                    $updateData['hotel_occupancy_num'] = $data['hotel_occupancy_num'];
//                }
//                $result = HotelModel::Update($isExist['id'],$updateData);
//            }else{
//                $result = HotelModel::Insert([
//                    'date'                 => $data['date'],
//                    'hotel_room_num'       => $data['hotel_room_num'],
//                    'hotel_occupancy_num'  => $data['hotel_occupancy_num']
//                ]);
//            }
//
//            if( $result==0 )
//            {
//                $conn->Rollback();
//                Response::Error('保存失败');
//            }
        }
        
        //接待人数数据
        if($data['hhdxg'])
        {
            $isExist = ReceptionModel::Detail("date = '".$data['date']."' AND scenic_spot_id = 1");

            if($isExist){
                $result = ReceptionModel::Update($isExist['id'],['num'=>$data['hhdxg']]);
            }else{
                $result = ReceptionModel::Insert([
                    'date'                 => $data['date'],
                    'scenic_spot_id'       => 1,
                    'num'  => $data['hhdxg']
                ]);
            }

            if( $result ==0 )
            {
                $conn->Rollback();
                Response::Error('保存失败');
            }
        }
        if($data['hhl'])
        {
            $isExist = ReceptionModel::Detail("date = '".$data['date']."' AND scenic_spot_id = 2");

            if($isExist){
                $result = ReceptionModel::Update($isExist['id'],['num'=>$data['hhl']]);
            }else{
                $result = ReceptionModel::Insert([
                    'date'                 => $data['date'],
                    'scenic_spot_id'       => 2,
                    'num'  => $data['hhl']
                ]);
            }

            if( $result ==0 )
            {
                $conn->Rollback();
                Response::Error('保存失败');
            }
        }
        if($data['hht'])
        {
            $isExist = ReceptionModel::Detail("date = '".$data['date']."' AND scenic_spot_id = 3");

            if($isExist){
                $result = ReceptionModel::Update($isExist['id'],['num'=>$data['hht']]);
            }else{
                $result = ReceptionModel::Insert([
                    'date'                 => $data['date'],
                    'scenic_spot_id'       => 3,
                    'num'  => $data['hht']
                ]);
            }

            if( $result ==0 )
            {
                $conn->Rollback();
                Response::Error('保存失败');
            }
        }
        if($data['hhsty'])
        {
            $isExist = ReceptionModel::Detail("date = '".$data['date']."' AND scenic_spot_id = 3");

            if($isExist){
                $result = ReceptionModel::Update($isExist['id'],['num'=>$data['hhsty']]);
            }else{
                $result = ReceptionModel::Insert([
                    'date'                 => $data['date'],
                    'scenic_spot_id'       => 4,
                    'num'  => $data['hhsty']
                ]);
            }

            if( $result ==0 )
            {
                $conn->Rollback();
                Response::Error('保存失败');
            }
        }
        //旅游收入
        if($data['tourism_income'])
        {
            $isExist = TourismIncomeModel::Detail("date = '".$data['date']."'");

            if($isExist){
                $result = TourismIncomeModel::Update($isExist['id'],['income'  => $data['tourism_income']]);
            }else{
                $result = TourismIncomeModel::Insert([
                    'date'                 => $data['date'],
                    'income'  => $data['tourism_income']
                ]);
            }

            if( $result ==0 )
            {
                $conn->Rollback();
                Response::Error('保存失败');
            }
        }

        //团散票
        if($data['group_ticket'] || $data['scattered_ticket'])
        {
            $data['group_ticket'] = $data['group_ticket'] == '' ? 0 : $data['group_ticket'];
            $data['scattered_ticket'] = $data['scattered_ticket'] == '' ? 0 : $data['scattered_ticket'];

            $isExist = TicketModel::Detail("date = '".$data['date']."'");

            if($isExist){

                if($data['group_ticket']){
                    $updateData['group_ticket'] = $data['group_ticket'];
                }

                if($data['scattered_ticket']){
                    $updateData['scattered_ticket'] = $data['scattered_ticket'];
                }

                $result = TicketModel::Update($isExist['id'],$updateData);
            }else{
                $result = TicketModel::Insert([
                    'date'                 => $data['date'],
                    'scattered_ticket_num'       => $data['scattered_ticket'],
                    'group_ticket_num'  => $data['group_ticket']
                ]);
            }

            if( $result ==0 )
            {
                $conn->Rollback();
                Response::Error('保存失败');
            }
        }
        //预警数据
        if($data['warn_time'] &&  $data['warn_msg'])
        {
            $insertId = WarnModel::Insert([
                'warn_time'  => $data['warn_time'],
                'warn_msg'   => $data['warn_msg']
            ]);
            if( $insertId==0 )
            {
                $conn->Rollback();
                Response::Error('保存失败');
            }
        }
        //票种数据
        $ticket_data = $this->_getTicketData($data);
        if(!empty($ticket_data))
        {
            foreach ($ticket_data as $k => $v)
            {
                $isExist = TicketModel::TypeDetail(
                    "scenic_spot_id=".$v['scenic_spot_id']." AND ticket_type_id=".$v['ticket_type_id']
                    ." AND ticket_of=".$v['ticket_of']." AND ticket_from=".$v['ticket_from']
                    ." AND date = '".$data['date']."'"
                );
                if($isExist){
                    $updateData['ticket_num'] = $v['ticket_num'];
                    $result = TicketModel::UpdateType($isExist['id'],$updateData);
                }else{
                    $result = TicketModel::InsertType([
                        'date'                 => $data['date'],
                        'scenic_spot_id'       => $v['scenic_spot_id'],
                        'ticket_type_id'       => $v['ticket_type_id'],
                        'ticket_of'            => $v['ticket_of'],
                        'ticket_from'          => $v['ticket_from'],
                        'ticket_num'           => $v['ticket_num']
                    ]);
                }
                if( $result==0 )
                {
                    $conn->Rollback();
                    Response::Error('保存失败');
                }
            }
        }
        $conn->Commit();
        Response::Json([]);
        return false;
    }

    private function _parseParameter()
    {
        $return = [
            'date' => Request::Post('date_time'),
            'hhdxg' => Request::Post('hhdxg'),
            'hhl' => Request::Post('hhl'),
            'hht' => Request::Post('hht'),
            'hhsty' => Request::Post('hhsty'),
            'tourism_income' => Request::Post('tourism_income'),
            'group_ticket' => Request::Post('group_ticket'),
            'scattered_ticket' => Request::Post('scattered_ticket'),
            'warn_time'=> Request::Post('warn_time'),
            'warn_msg'=> Request::Post('warn_msg'),
            'qtx_standard_num'=> Request::Post('qtx_standard_num'),
            'qtx_kingsize_num'=> Request::Post('qtx_kingsize_num'),
            'qtx_other_num'=> Request::Post('qtx_other_num'),
            'lh_standard_num'=> Request::Post('lh_standard_num'),
            'lh_kingsize_num'=> Request::Post('lh_kingsize_num'),
            'lh_other_num'=> Request::Post('lh_other_num'),
            'glht_standard_num'=> Request::Post('glht_standard_num'),
            'glht_kingsize_num'=> Request::Post('glht_kingsize_num'),
            'glht_other_num'=> Request::Post('glht_other_num'),
            'kp_standard_num'=> Request::Post('kp_standard_num'),
            'kp_kingsize_num'=> Request::Post('kp_kingsize_num'),
            'kp_other_num'=> Request::Post('kp_other_num'),
            'ht_standard_num'=> Request::Post('ht_standard_num'),
            'ht_kingsize_num'=> Request::Post('ht_kingsize_num'),
            'ht_other_num'=> Request::Post('ht_other_num'),
            'qy_standard_num'=> Request::Post('qy_standard_num'),
            'qy_kingsize_num'=> Request::Post('qy_kingsize_num'),
            'qy_other_num'=> Request::Post('qy_other_num'),
            'sx_standard_num'=> Request::Post('sx_standard_num'),
            'sx_kingsize_num'=> Request::Post('sx_kingsize_num'),
            'sx_other_num'=> Request::Post('sx_other_num'),
            'wh_standard_num'=> Request::Post('wh_standard_num'),
            'wh_kingsize_num'=> Request::Post('wh_kingsize_num'),
            'wh_other_num'=> Request::Post('wh_other_num'),
            'other_standard_num'=> Request::Post('other_standard_num'),
            'other_kingsize_num'=> Request::Post('other_kingsize_num'),
            'other_other_num'=> Request::Post('other_other_num'),
            'hhl_single_door_student'=>Request::Post('hhl_single_door_student'),
            'hhl_single_door_man'=> Request::Post('hhl_single_door_man'),
            'hhl_single_door_free'=> Request::Post('hhl_single_door_free'),
            'hhl_group_door_student'=> Request::Post('hhl_group_door_student'),
            'hhl_group_door_man'=> Request::Post('hhl_group_door_man'),
            'hhl_group_door_free'=> Request::Post('hhl_group_door_free'),
            'dxg_single_door_student'=> Request::Post('dxg_single_door_student'),
            'dxg_single_door_man'=> Request::Post('dxg_single_door_man'),
            'dxg_single_door_free'=> Request::Post('dxg_single_door_free'),
            'dxg_single_boat_man'=> Request::Post('dxg_single_boat_man'),
            'dxg_single_boat_student'=> Request::Post('dxg_single_boat_student'),
            'dxg_group_door_student'=> Request::Post('dxg_group_door_student'),
            'dxg_group_door_man'=> Request::Post('dxg_group_door_man'),
            'dxg_group_door_free'=> Request::Post('dxg_groupe_door_free'),
            'dxg_group_boat_man'=> Request::Post('dxg_group_boat_man'),
            'dxg_group_boat_student'=> Request::Post('dxg_group_boat_student'),
            'hht_single_door_student'=> Request::Post('hht_single_door_student'),
            'hht_single_door_man'=> Request::Post('hht_single_door_man'),
            'hht_single_door_free'=> Request::Post('hht_single_door_free'),
            'hht_single_boat_man'=> Request::Post('hht_single_boat_man'),
            'hht_single_boat_student'=> Request::Post('hht_single_boat_student'),
            'hht_group_door_student'=> Request::Post('hht_group_door_student'),
            'hht_group_door_man'=> Request::Post('hht_group_door_man'),
            'hht_group_door_free'=> Request::Post('hht_group_door_free'),
            'hht_group_boat_man'=> Request::Post('hht_group_boat_man'),
            'hht_group_boat_student'=> Request::Post('hht_group_boat_student'),
            'sty_single_door_special'=> Request::Post('sty_single_door_special'),

        ];

        if(!$return['date'])
        {
            Response::Error('日期不可为空');
        }
        
        if($return['warn_time'] != '' && $return['warn_msg'] == '')
        {
            Response::Error('预警信息不可为空');
        }
        if($return['warn_msg'] != '' && $return['warn_time'] == '')
        {
            Response::Error('预警时间不可为空');
        }

        return $return;
    }

    public  function  idCardExcelAction()
    {
        $data = $this->_parseFile();

        //区分上传文件格式
        if($data['extension'] == 'xlsx') {
            $objReader = \PHPExcel_IOFactory::createReader('Excel2007');

        }else if($data['extension'] == 'xls'){
            $objReader = \PHPExcel_IOFactory::createReader('Excel5');
        }

        $objPHPExcel = $objReader->load($data['file'], $encode = 'utf-8');
        $excel_array = $objPHPExcel->getSheet(0)->toArray();
        array_shift($excel_array);

        $data_values = '';
        foreach ($excel_array as $k => $v)
        {
            if(empty($v[0]) || empty($v[1]))
            {
                Response::Error('旅行社和团号不可为空');
            }
            $isExist = VisitorInfoModel::Detail("travel_agency ='".$v[0]."' AND group_num =".$v[1]." AND date='".$v['5']."'");
            if($isExist)
            {
                Response::Error('存在重复数据');
            }
            $birthday_date = date('Y-m-d',strtotime($v[4]));
            $date = date('Y-m-d',strtotime($v[5]));
            $province = getProvinceByIdCard($v[2]);
            $data_values .= "('$date','$v[2]','$birthday_date','$v[3]','$v[0]','$v[1]','$province'),";
        }
        $data_values = substr($data_values,0,-1);
        $result = VisitorInfoModel::Inserts($data_values);
        if(!$result)
        {
            Response::Error('身份信息导入失败');
        }

//        $data_values = '';
//        $date = date('Y-m-d',strtotime('-1 day'));
//        foreach ($excel_array as $k => $v){
//            if($k < 2){
//                unset($excel_array[$k]);
//            }else{
//                if($v[1] && $v[3] && $v[4])
//                {
//                    $v[4] = date('Y-m-d',strtotime($v[4]));
//                    $data_values .= "('$date','$v[1]','$v[6]','$v[4]','$v[3]'),";
//                }
//            }
//        }
//        $data_values = substr($data_values,0,-1);
//
//        $result = VisitorInfoModel::Inserts($data_values);
//        if(!$result)
//        {
//            Response::Error('身份信息导入失败');
//        }
        Response::Json([]);
    }

    public  function  dxgCarExcelAction()
    {
        $data = $this->_parseFile();

        //区分上传文件格式
        if($data['extension'] == 'xlsx') {
            $objReader = \PHPExcel_IOFactory::createReader('Excel2007');

        }else if($data['extension'] == 'xls'){
            $objReader = \PHPExcel_IOFactory::createReader('Excel5');
        }

        $objPHPExcel = $objReader->load($data['file'], $encode = 'utf-8');
        $excel_array = $objPHPExcel->getSheet(0)->toArray();
        $data_values = '';
        foreach ($excel_array as $k => $v){
            if($k < 1){
                unset($excel_array[$k]);
            }else{

                if($v[8] && $v[1])
                {
                    $v[8] = date('Y-m-d',strtotime($v[8]));
                    $data_values .= "('$v[8]',1,'$v[1]'),";
                }
            }
        }
        $data_values = substr($data_values,0,-1);
        $result = CarInfoModel::Inserts($data_values);
        if(!$result)
        {
            Response::Error('大峡谷车辆信息导入失败');
        }
        Response::Json([]);
    }

    public  function  hhlCarExcelAction()
    {
        $data = $this->_parseFile();
        //区分上传文件格式
        if($data['extension'] == 'xlsx') {
            $objReader = \PHPExcel_IOFactory::createReader('Excel2007');

        }else if($data['extension'] == 'xls'){
            $objReader = \PHPExcel_IOFactory::createReader('Excel5');
        }

        $objPHPExcel = $objReader->load($data['file'], $encode = 'utf-8');
        $excel_array = $objPHPExcel->getSheet(0)->toArray();

        $data_values = '';
        foreach ($excel_array as $k => $v){
            if($k < 7){
                unset($excel_array[$k]);
            }else{
                if($v[9] && $v[7]){
                    $v[9] = date('Y-m-d',strtotime($v[9]));
                    $data_values .= "('$v[9]',2,'$v[7]'),";
                }
            }
        }
        $data_values = substr($data_values,0,-1);

        $result = CarInfoModel::Inserts($data_values);
        if(!$result)
        {
            Response::Error('黄河楼车辆信息导入失败');
        }
        Response::Json([]);
    }

    public  function receptionNumExcelAction()
    {
        $data = $this->_parseFile();
        //区分上传文件格式
        if($data['extension'] == 'xlsx') {
            $objReader = \PHPExcel_IOFactory::createReader('Excel2007');

        }else if($data['extension'] == 'xls'){
            $objReader = \PHPExcel_IOFactory::createReader('Excel5');
        }

        $objPHPExcel = $objReader->load($data['file'], $encode = 'utf-8');
        $excel_array = $objPHPExcel->getSheet(0)->toArray();
        array_shift($excel_array);
        if(empty($excel_array))
        {
            Response::Error('空数据记录');
        }
        $dxgScenicSpotId = Yaf_Registry::get(COMMON)->get('dxgScenicSpotId');
        $hhlScenicSpotId = Yaf_Registry::get(COMMON)->get('hhlScenicSpotId');
        $hhtScenicSpotId = Yaf_Registry::get(COMMON)->get('hhtScenicSpotId');
        $styScenicSpotId = Yaf_Registry::get(COMMON)->get('styScenicSpotId');
        foreach ($excel_array as $k => $v)
        {
            if($v[1]){
                $insertData[] = ['scenic_spot_id'=>$dxgScenicSpotId,'date'=>$v[0],'num'=>$v[1]];
            }
            if($v[2]){
                $insertData[] = ['scenic_spot_id'=>$hhlScenicSpotId,'date'=>$v[0],'num'=>$v[2]];
            }
            if($v[3]){
                $insertData[] = ['scenic_spot_id'=>$hhtScenicSpotId,'date'=>$v[0],'num'=>$v[3]];
            }
            if($v[4]){
                $insertData[] = ['scenic_spot_id'=>$styScenicSpotId,'date'=>$v[0],'num'=>$v[4]];
            }
        }
        foreach ($insertData as $k => $v)
        {
            $isExist = ReceptionModel::Detail("date = '".$v['date']."' AND scenic_spot_id = ".$v['scenic_spot_id']);

            if($isExist){
                $result = ReceptionModel::Update($isExist['id'],['num'=>$v['num']]);
            }else{
                $result = ReceptionModel::Insert($v);
            }
        }
        if(!$result)
        {
            Response::Error('接待人数数据导入失败');
        }
        Response::Json([]);
    }

    private function _parseFile()
    {
        $return = [
            'filename' => $_FILES['file']['name'],
            'file'     => $_FILES['file']['tmp_name']
        ];
        if(empty($_FILES['file'])){
            Response::Error('未上传数据文件');
        }
        $return['extension'] = strtolower( pathinfo($return['filename'], PATHINFO_EXTENSION) );

        if(!in_array($return['extension'],['xlsx','xls']))
        {
            Response::Error('上传数据文件格式不正确');
        }

        return $return;
    }

    private function _getHotelData($data)
    {
        $qtxHotelNum   =  Yaf_Registry::get(COMMON)->get('qtxHotelNum');
        $lhHotelNum    =  Yaf_Registry::get(COMMON)->get('lhHotelNum');
        $glhtHotelNum  =  Yaf_Registry::get(COMMON)->get('glhtHotelNum');
        $kpHotelNum    =  Yaf_Registry::get(COMMON)->get('kpHotelNum');
        $htHotelNum    =  Yaf_Registry::get(COMMON)->get('htHotelNum');
        $qyHotelNum    =  Yaf_Registry::get(COMMON)->get('qyHotelNum');
        $sxHotelNum    =  Yaf_Registry::get(COMMON)->get('sxHotelNum');
        $whHotelNum    =  Yaf_Registry::get(COMMON)->get('whHotelNum');
        $otherHotelNum =  Yaf_Registry::get(COMMON)->get('otherHotelNum');

        $standardRoomType = Yaf_Registry::get(COMMON)->get('standardRoomType');
        $kingSizeRoomType = Yaf_Registry::get(COMMON)->get('kingSizeRoomType');
        $otherRoomType    = Yaf_Registry::get(COMMON)->get('otherRoomType');
        $hotel_data = [];
        if($data['qtx_standard_num'] != ''){
            $hotel_data[] = [
                'hotel_id'=>$qtxHotelNum,'room_type'=> $standardRoomType,'hotel_occupancy_num'=>$data['qtx_standard_num']
            ];
        }
        if($data['qtx_kingsize_num'] != ''){
            $hotel_data[] = [
                'hotel_id'=>$qtxHotelNum,'room_type'=> $kingSizeRoomType,'hotel_occupancy_num'=>$data['qtx_kingsize_num']
            ];
        }
        if($data['qtx_other_num'] != ''){
            $hotel_data[] = [
                'hotel_id'=>$qtxHotelNum,'room_type'=> $otherRoomType,'hotel_occupancy_num'=>$data['qtx_other_num']
            ];
        }
        if($data['lh_standard_num'] != ''){
            $hotel_data[] = [
                'hotel_id'=>$lhHotelNum,'room_type'=> $standardRoomType,'hotel_occupancy_num'=>$data['lh_standard_num']
            ];
        }
        if($data['lh_kingsize_num'] != ''){
            $hotel_data[] = [
                'hotel_id'=>$lhHotelNum,'room_type'=> $kingSizeRoomType,'hotel_occupancy_num'=>$data['lh_kingsize_num']
            ];
        }
        if($data['lh_other_num'] != ''){
            $hotel_data[] = [
                'hotel_id'=>$lhHotelNum,'room_type'=> $otherRoomType,'hotel_occupancy_num'=>$data['lh_other_num']
            ];
        }
        if($data['glht_standard_num'] != ''){
            $hotel_data[] = [
                'hotel_id'=>$glhtHotelNum,'room_type'=> $standardRoomType,'hotel_occupancy_num'=>$data['glht_standard_num']
            ];
        }
        if($data['glht_kingsize_num'] != ''){
            $hotel_data[] = [
                'hotel_id'=>$glhtHotelNum,'room_type'=> $kingSizeRoomType,'hotel_occupancy_num'=>$data['glht_kingsize_num']
            ];
        }
        if($data['glht_other_num'] != ''){
            $hotel_data[] = [
                'hotel_id'=>$glhtHotelNum,'room_type'=> $otherRoomType,'hotel_occupancy_num'=>$data['glht_other_num']
            ];
        }
        if($data['kp_standard_num'] != ''){
            $hotel_data[] = [
                'hotel_id'=>$kpHotelNum,'room_type'=> $standardRoomType,'hotel_occupancy_num'=>$data['kp_standard_num']
            ];
        }
        if($data['kp_kingsize_num'] != ''){
            $hotel_data[] = [
                'hotel_id'=>$kpHotelNum,'room_type'=> $kingSizeRoomType,'hotel_occupancy_num'=>$data['kp_kingsize_num']
            ];
        }
        if($data['kp_other_num'] != ''){
            $hotel_data[] = [
                'hotel_id'=>$kpHotelNum,'room_type'=> $otherRoomType,'hotel_occupancy_num'=>$data['kp_other_num']
            ];
        }
        if($data['ht_standard_num'] != ''){
            $hotel_data[] = [
                'hotel_id'=>$htHotelNum,'room_type'=> $standardRoomType,'hotel_occupancy_num'=>$data['ht_standard_num']
            ];
        }
        if($data['ht_kingsize_num'] != ''){
            $hotel_data[] = [
                'hotel_id'=>$htHotelNum,'room_type'=> $kingSizeRoomType,'hotel_occupancy_num'=>$data['ht_kingsize_num']
            ];
        }
        if($data['ht_other_num'] != ''){
            $hotel_data[] = [
                'hotel_id'=>$htHotelNum,'room_type'=> $otherRoomType,'hotel_occupancy_num'=>$data['ht_other_num']
            ];
        }
        if($data['qy_standard_num'] != ''){
            $hotel_data[] = [
                'hotel_id'=>$qyHotelNum,'room_type'=> $standardRoomType,'hotel_occupancy_num'=>$data['qy_standard_num']
            ];
        }
        if($data['qy_kingsize_num'] != ''){
            $hotel_data[] = [
                'hotel_id'=>$qyHotelNum,'room_type'=> $kingSizeRoomType,'hotel_occupancy_num'=>$data['qy_kingsize_num']
            ];
        }
        if($data['qy_other_num'] != ''){
            $hotel_data[] = [
                'hotel_id'=>$qyHotelNum,'room_type'=> $otherRoomType,'hotel_occupancy_num'=>$data['qy_other_num']
            ];
        }
        if($data['sx_standard_num'] != ''){
            $hotel_data[] = [
                'hotel_id'=>$sxHotelNum,'room_type'=> $standardRoomType,'hotel_occupancy_num'=>$data['sx_standard_num']
            ];
        }
        if($data['sx_kingsize_num'] != ''){
            $hotel_data[] = [
                'hotel_id'=>$sxHotelNum,'room_type'=> $kingSizeRoomType,'hotel_occupancy_num'=>$data['sx_kingsize_num']
            ];
        }
        if($data['sx_other_num'] != ''){
            $hotel_data[] = [
                'hotel_id'=>$sxHotelNum,'room_type'=> $otherRoomType,'hotel_occupancy_num'=>$data['sx_other_num']
            ];
        }
        if($data['wh_standard_num'] != ''){
            $hotel_data[] = [
                'hotel_id'=>$whHotelNum,'room_type'=> $standardRoomType,'hotel_occupancy_num'=>$data['wh_standard_num']
            ];
        }
        if($data['wh_kingsize_num'] != ''){
            $hotel_data[] = [
                'hotel_id'=>$whHotelNum,'room_type'=> $kingSizeRoomType,'hotel_occupancy_num'=>$data['wh_kingsize_num']
            ];
        }
        if($data['wh_other_num'] != ''){
            $hotel_data[] = [
                'hotel_id'=>$whHotelNum,'room_type'=> $otherRoomType,'hotel_occupancy_num'=>$data['wh_other_num']
            ];
        }
        if($data['other_standard_num'] != ''){
            $hotel_data[] = [
                'hotel_id'=>$otherHotelNum,'room_type'=> $standardRoomType,'hotel_occupancy_num'=>$data['other_standard_num']
            ];
        }
        if($data['other_kingsize_num'] != ''){
            $hotel_data[] = [
                'hotel_id'=>$otherHotelNum,'room_type'=> $kingSizeRoomType,'hotel_occupancy_num'=>$data['other_kingsize_num']
            ];
        }
        if($data['other_other_num'] != ''){
            $hotel_data[] = [
                'hotel_id'=>$otherHotelNum,'room_type'=> $otherRoomType,'hotel_occupancy_num'=>$data['other_other_num']
            ];
        }
        return $hotel_data;
    }

    private function _getTicketData($data)
    {
        $dxgScenicSpotId = Yaf_Registry::get(COMMON)->get('dxgScenicSpotId');
        $hhlScenicSpotId = Yaf_Registry::get(COMMON)->get('hhlScenicSpotId');
        $hhtScenicSpotId = Yaf_Registry::get(COMMON)->get('hhtScenicSpotId');
        $styScenicSpotId = Yaf_Registry::get(COMMON)->get('styScenicSpotId');

        $ticketTypeStudent   =  Yaf_Registry::get(COMMON)->get('ticketTypeStudent');
        $ticketTypeMan   =  Yaf_Registry::get(COMMON)->get('ticketTypeMan');
        $ticketTypeFree   =  Yaf_Registry::get(COMMON)->get('ticketTypeFree');
        $ticketTypeSpecial   =  Yaf_Registry::get(COMMON)->get('ticketTypeSpecial');

        $ticketOfDoor   =  Yaf_Registry::get(COMMON)->get('ticketOfDoor');
        $ticketOfBoat   =  Yaf_Registry::get(COMMON)->get('ticketOfBoat');

        $ticketFromScattered   =  Yaf_Registry::get(COMMON)->get('ticketFromScattered');
        $ticketFromGroup   =  Yaf_Registry::get(COMMON)->get('ticketFromGroup');

        $ticketData = [];
        if($data['hhl_single_door_student'] != ''){
            $ticketData[] = [
                'scenic_spot_id'=>$hhlScenicSpotId,'ticket_type_id'=>$ticketTypeStudent,'ticket_of'=>$ticketOfDoor,
                'ticket_from'=>$ticketFromScattered,'ticket_num'=>$data['hhl_single_door_student']
            ];
        }
        if($data['hhl_single_door_man'] != ''){
            $ticketData[] = [
                'scenic_spot_id'=>$hhlScenicSpotId,'ticket_type_id'=>$ticketTypeMan,'ticket_of'=>$ticketOfDoor,
                'ticket_from'=>$ticketFromScattered,'ticket_num'=>$data['hhl_single_door_man']
            ];
        }
        if($data['hhl_single_door_free'] != ''){
            $ticketData[] = [
                'scenic_spot_id'=>$hhlScenicSpotId,'ticket_type_id'=>$ticketTypeFree,'ticket_of'=>$ticketOfDoor,
                'ticket_from'=>$ticketFromScattered,'ticket_num'=>$data['hhl_single_door_free']
            ];
        }
        if($data['hhl_group_door_student'] != ''){
            $ticketData[] = [
                'scenic_spot_id'=>$hhlScenicSpotId,'ticket_type_id'=>$ticketTypeStudent,'ticket_of'=>$ticketOfDoor,
                'ticket_from'=>$ticketFromGroup,'ticket_num'=>$data['hhl_group_door_student']
            ];
        }
        if($data['hhl_group_door_man'] != ''){
            $ticketData[] = [
                'scenic_spot_id'=>$hhlScenicSpotId,'ticket_type_id'=>$ticketTypeMan,'ticket_of'=>$ticketOfDoor,
                'ticket_from'=>$ticketFromGroup,'ticket_num'=>$data['hhl_group_door_man']
            ];
        }
        if($data['hhl_group_door_free'] != ''){
            $ticketData[] = [
                'scenic_spot_id'=>$hhlScenicSpotId,'ticket_type_id'=>$ticketTypeFree,'ticket_of'=>$ticketOfDoor,
                'ticket_from'=>$ticketFromGroup,'ticket_num'=>$data['hhl_group_door_free']
            ];
        }

        if($data['dxg_single_door_student'] != ''){
            $ticketData[] = [
                'scenic_spot_id'=>$dxgScenicSpotId,'ticket_type_id'=>$ticketTypeStudent,'ticket_of'=>$ticketOfDoor,
                'ticket_from'=>$ticketFromScattered,'ticket_num'=>$data['dxg_single_door_student']
            ];
        }
        if($data['dxg_single_door_man'] != ''){
            $ticketData[] = [
                'scenic_spot_id'=>$dxgScenicSpotId,'ticket_type_id'=>$ticketTypeMan,'ticket_of'=>$ticketOfDoor,
                'ticket_from'=>$ticketFromScattered,'ticket_num'=>$data['dxg_single_door_man']
            ];
        }
        if($data['dxg_single_door_free'] != ''){
            $ticketData[] = [
                'scenic_spot_id'=>$dxgScenicSpotId,'ticket_type_id'=>$ticketTypeFree,'ticket_of'=>$ticketOfDoor,
                'ticket_from'=>$ticketFromScattered,'ticket_num'=>$data['dxg_single_door_free']
            ];
        }
        if($data['dxg_single_boat_man'] != ''){
            $ticketData[] = [
                'scenic_spot_id'=>$dxgScenicSpotId,'ticket_type_id'=>$ticketTypeMan,'ticket_of'=>$ticketOfBoat,
                'ticket_from'=>$ticketFromScattered,'ticket_num'=>$data['dxg_single_boat_man']
            ];
        }
        if($data['dxg_single_boat_student'] != ''){
            $ticketData[] = [
                'scenic_spot_id'=>$dxgScenicSpotId,'ticket_type_id'=>$ticketTypeStudent,'ticket_of'=>$ticketOfBoat,
                'ticket_from'=>$ticketFromScattered,'ticket_num'=>$data['dxg_single_boat_student']
            ];
        }
        if($data['dxg_group_door_student'] != ''){
            $ticketData[] = [
                'scenic_spot_id'=>$dxgScenicSpotId,'ticket_type_id'=>$ticketTypeStudent,'ticket_of'=>$ticketOfDoor,
                'ticket_from'=>$ticketFromGroup,'ticket_num'=>$data['dxg_group_door_student']
            ];
        }
        if($data['dxg_group_door_man'] != ''){
            $ticketData[] = [
                'scenic_spot_id'=>$dxgScenicSpotId,'ticket_type_id'=>$ticketTypeMan,'ticket_of'=>$ticketOfDoor,
                'ticket_from'=>$ticketFromGroup,'ticket_num'=>$data['dxg_group_door_man']
            ];
        }
        if($data['dxg_group_door_free'] != ''){
            $ticketData[] = [
                'scenic_spot_id'=>$hhlScenicSpotId,'ticket_type_id'=>$ticketTypeFree,'ticket_of'=>$ticketOfDoor,
                'ticket_from'=>$ticketFromGroup,'ticket_num'=>$data['dxg_group_door_free']
            ];
        }
        if($data['dxg_group_boat_man'] != ''){
            $ticketData[] = [
                'scenic_spot_id'=>$dxgScenicSpotId,'ticket_type_id'=>$ticketTypeMan,'ticket_of'=>$ticketOfBoat,
                'ticket_from'=>$ticketFromGroup,'ticket_num'=>$data['dxg_group_boat_man']
            ];
        }
        if($data['dxg_group_boat_student'] != ''){
            $ticketData[] = [
                'scenic_spot_id'=>$dxgScenicSpotId,'ticket_type_id'=>$ticketTypeStudent,'ticket_of'=>$ticketOfBoat,
                'ticket_from'=>$ticketFromGroup,'ticket_num'=>$data['dxg_group_boat_student']
            ];
        }


        if($data['hht_single_door_student'] != ''){
            $ticketData[] = [
                'scenic_spot_id'=>$hhtScenicSpotId,'ticket_type_id'=>$ticketTypeStudent,'ticket_of'=>$ticketOfDoor,
                'ticket_from'=>$ticketFromScattered,'ticket_num'=>$data['hht_single_door_student']
            ];
        }
        if($data['hht_single_door_man'] != ''){
            $ticketData[] = [
                'scenic_spot_id'=>$hhtScenicSpotId,'ticket_type_id'=>$ticketTypeMan,'ticket_of'=>$ticketOfDoor,
                'ticket_from'=>$ticketFromScattered,'ticket_num'=>$data['hht_single_door_man']
            ];
        }
        if($data['hht_single_door_free'] != ''){
            $ticketData[] = [
                'scenic_spot_id'=>$hhtScenicSpotId,'ticket_type_id'=>$ticketTypeFree,'ticket_of'=>$ticketOfDoor,
                'ticket_from'=>$ticketFromScattered,'ticket_num'=>$data['hht_single_door_free']
            ];
        }
        if($data['hht_single_boat_man'] != ''){
            $ticketData[] = [
                'scenic_spot_id'=>$hhtScenicSpotId,'ticket_type_id'=>$ticketTypeMan,'ticket_of'=>$ticketOfBoat,
                'ticket_from'=>$ticketFromScattered,'ticket_num'=>$data['hht_single_boat_man']
            ];
        }
        if($data['hht_single_boat_student'] != ''){
            $ticketData[] = [
                'scenic_spot_id'=>$hhtScenicSpotId,'ticket_type_id'=>$ticketTypeStudent,'ticket_of'=>$ticketOfBoat,
                'ticket_from'=>$ticketFromScattered,'ticket_num'=>$data['hht_single_boat_student']
            ];
        }
        if($data['hht_group_door_student'] != ''){
            $ticketData[] = [
                'scenic_spot_id'=>$hhtScenicSpotId,'ticket_type_id'=>$ticketTypeStudent,'ticket_of'=>$ticketOfDoor,
                'ticket_from'=>$ticketFromGroup,'ticket_num'=>$data['hht_group_door_student']
            ];
        }
        if($data['hht_group_door_man'] != ''){
            $ticketData[] = [
                'scenic_spot_id'=>$hhtScenicSpotId,'ticket_type_id'=>$ticketTypeMan,'ticket_of'=>$ticketOfDoor,
                'ticket_from'=>$ticketFromGroup,'ticket_num'=>$data['hht_group_door_man']
            ];
        }
        if($data['hht_group_door_free'] != ''){
            $ticketData[] = [
                'scenic_spot_id'=>$hhtScenicSpotId,'ticket_type_id'=>$ticketTypeFree,'ticket_of'=>$ticketOfDoor,
                'ticket_from'=>$ticketFromGroup,'ticket_num'=>$data['hht_group_door_free']
            ];
        }
        if($data['hht_group_boat_man'] != ''){
            $ticketData[] = [
                'scenic_spot_id'=>$hhtScenicSpotId,'ticket_type_id'=>$ticketTypeMan,'ticket_of'=>$ticketOfBoat,
                'ticket_from'=>$ticketFromGroup,'ticket_num'=>$data['hht_group_boat_man']
            ];
        }
        if($data['hht_group_boat_student'] != ''){
            $ticketData[] = [
                'scenic_spot_id'=>$hhtScenicSpotId,'ticket_type_id'=>$ticketTypeStudent,'ticket_of'=>$ticketOfBoat,
                'ticket_from'=>$ticketFromGroup,'ticket_num'=>$data['hht_group_boat_student']
            ];
        }

        if($data['sty_single_door_special'] != ''){
            $ticketData[] = [
                'scenic_spot_id'=>$styScenicSpotId,'ticket_type_id'=>$ticketTypeSpecial,'ticket_of'=>$ticketOfDoor,
                'ticket_from'=>$ticketFromScattered,'ticket_num'=>$data['sty_single_door_special']
            ];
        }

        return $ticketData;
    }

}