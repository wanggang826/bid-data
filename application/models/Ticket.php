<?php
/**
 * User: wgg
 * Date: 19-4-12
 * Time: 下午2:54
 */
class TicketModel
{
    private static $table = 'group_scattered_ticket';

    private static $type_table = 'ticket_type';


    public static function Insert($data)
    {
        $result = Db::Table(self::$table)->Insert($data);
        if ($result['result']) {
            return $result['insertId'];
        }
        return false;
    }

    public static function Detail($where)
    {
        return Db::Table(static::$table)->Where($where)->Get();
    }

    public static function Update($id, $data)
    {
        return Db::Table(static::$table)->Where("id={$id}")->Update($data)['result'];
    }

    public static function getTicketRate()
    {
        $outTicketData = [];
        $ticketData = $ageDataBeforeTwoYear = Db::Table(static::$table)
            ->Col([
                'year(date) as year,
                sum(scattered_ticket_num) as scattered_num,
                sum(group_ticket_num) as group_num'
            ])
            ->GroupBy('year')
            ->Find();
        foreach ($ticketData as $k=>$v){
            $value = $v;
            unset($value['year']);
            $ticketData[$v['year']] = array_sum($value) ? [
                    'scattered_rate' => round($value['scattered_num']/array_sum($value),2),
                    'group_rate'     => round($value['group_num']/array_sum($value),2)
            ] : [];
            unset($ticketData[$k]);
        }
        $beforeTwoYear = date('Y',strtotime("-2 year"));
        $beforeOneYear = date('Y',strtotime("-1 year"));
        $nowYear = date('Y');
        if($ticketData[$beforeTwoYear]){
            $outTicketData[$beforeTwoYear] = $ticketData[$beforeTwoYear];
        }
        if($ticketData[$beforeOneYear]){
            $outTicketData[$beforeOneYear] = $ticketData[$beforeOneYear];
        }
        if($ticketData[$nowYear]){
            $outTicketData[$nowYear] = $ticketData[$nowYear];
        }
        return $outTicketData;
    }

    public static function InsertType($data)
    {
        $result = Db::Table(self::$type_table)->Insert($data);
        if ($result['result']) {
            return $result['insertId'];
        }
        return false;
    }

    public static function TypeDetail($where)
    {
        return Db::Table(static::$type_table)->Where($where)->Get();
    }

    public static function UpdateType($id,$data)
    {
        return Db::Table(static::$type_table)->Where("id={$id}")->Update($data)['result'];
    }

    public static function getDateTicketType()
    {
        $dxgScenicSpotId = Yaf_Registry::get(COMMON)->get('dxgScenicSpotId');
        $hhlScenicSpotId = Yaf_Registry::get(COMMON)->get('hhlScenicSpotId');
        $hhtScenicSpotId = Yaf_Registry::get(COMMON)->get('hhtScenicSpotId');
        $styScenicSpotId = Yaf_Registry::get(COMMON)->get('styScenicSpotId');

        $typeStudent   = Yaf_Registry::get(COMMON)->get('ticketTypeStudent');
        $typeMan = Yaf_Registry::get(COMMON)->get('ticketTypeMan');
        $typeFree = Yaf_Registry::get(COMMON)->get('ticketTypeFree');
        $typeSpecial = Yaf_Registry::get(COMMON)->get('ticketTypeSpecial');

        $ticketOfDoor = Yaf_Registry::get(COMMON)->get('ticketOfDoor');
        $ticketOfBoat = Yaf_Registry::get(COMMON)->get('ticketOfBoat');

        $date = date('Y-m-d');

        //黄河楼票种
        $hhlData = Db::Table(static::$type_table)
            ->Col(["ticket_from,sum(case when ticket_type_id='{$typeStudent}' THEN ticket_num ELSE 0 END) as student, 
                    sum(case when ticket_type_id='{$typeMan}' THEN ticket_num ELSE 0 END) as man,
                    sum(case when ticket_type_id='{$typeFree}' THEN ticket_num ELSE 0 END) as free"])
            ->Where("scenic_spot_id = '{$hhlScenicSpotId}' AND date = '{$date}'")
            ->GroupBy('ticket_from')
            ->Find();
        foreach ($hhlData as $k=>$v)
        {
            $hhlOutData[$v['ticket_from']] = $v;
            unset($hhlOutData[$v['ticket_from']]['ticket_from']);
        }

        foreach ($hhlOutData as $key=>$value)
        {
            $hhlOutData[$key]['student_rate'] = round($value['student']/array_sum($value),3);
            $hhlOutData[$key]['man_rate'] = round($value['man']/array_sum($value),3);
            $hhlOutData[$key]['free_rate'] = round($value['free']/array_sum($value),3);
        }

        //大峡谷门票票种
        $dxgDoorData = Db::Table(static::$type_table)
            ->Col(["ticket_from,sum(case when ticket_type_id='{$typeStudent}' THEN ticket_num ELSE 0 END) as student, 
                    sum(case when ticket_type_id='{$typeMan}' THEN ticket_num ELSE 0 END) as man,
                    sum(case when ticket_type_id='{$typeFree}' THEN ticket_num ELSE 0 END) as free"])
            ->Where("scenic_spot_id = '{$dxgScenicSpotId}' AND ticket_of = '{$ticketOfDoor}' AND date = '{$date}'")
            ->GroupBy('ticket_from')
            ->Find();
        foreach ($dxgDoorData as $k=>$v)
        {
            $dxgDoorOutData[$v['ticket_from']] = $v;
            unset($dxgDoorOutData[$v['ticket_from']]['ticket_from']);
        }

        foreach ($dxgDoorOutData as $key=>$value)
        {
            $dxgDoorOutData[$key]['student_rate'] = round($value['student']/array_sum($value),3);
            $dxgDoorOutData[$key]['man_rate'] = round($value['man']/array_sum($value),3);
            $dxgDoorOutData[$key]['free_rate'] = round($value['free']/array_sum($value),3);
        }
        //大峡谷船票票种
        $dxgBoatData = Db::Table(static::$type_table)
            ->Col(["ticket_from,sum(case when ticket_type_id='{$typeStudent}' THEN ticket_num ELSE 0 END) as student, 
                    sum(case when ticket_type_id='{$typeMan}' THEN ticket_num ELSE 0 END) as man,
                    sum(case when ticket_type_id='{$typeFree}' THEN ticket_num ELSE 0 END) as free"])
            ->Where("scenic_spot_id = '{$dxgScenicSpotId}' AND ticket_of = '{$ticketOfBoat}' AND date = '{$date}'")
            ->GroupBy('ticket_from')
            ->Find();
        foreach ($dxgBoatData as $k=>$v)
        {
            $dxgBoatOutData[$v['ticket_from']] = $v;
            unset($dxgBoatOutData[$v['ticket_from']]['ticket_from']);
        }
        foreach ($dxgBoatOutData as $key=>$value)
        {
            $dxgBoatOutData[$key]['student_rate'] = round($value['student']/array_sum($value),3);
            $dxgBoatOutData[$key]['man_rate'] = round($value['man']/array_sum($value),3);
            $dxgBoatOutData[$key]['free_rate'] = round($value['free']/array_sum($value),3);
        }

        //黄河坛门票票种
        $hhtDoorData = Db::Table(static::$type_table)
            ->Col(["ticket_from,sum(case when ticket_type_id='{$typeStudent}' THEN ticket_num ELSE 0 END) as student, 
                    sum(case when ticket_type_id='{$typeMan}' THEN ticket_num ELSE 0 END) as man,
                    sum(case when ticket_type_id='{$typeFree}' THEN ticket_num ELSE 0 END) as free"])
            ->Where("scenic_spot_id = '{$hhtScenicSpotId}' AND ticket_of = '{$ticketOfDoor}' AND date = '{$date}'")
            ->GroupBy('ticket_from')
            ->Find();
        foreach ($hhtDoorData as $k=>$v)
        {
            $hhtDoorOutData[$v['ticket_from']] = $v;
            unset($hhtDoorOutData[$v['ticket_from']]['ticket_from']);
        }

        foreach ($hhtDoorOutData as $key=>$value)
        {
            $hhtDoorOutData[$key]['student_rate'] = round($value['student']/array_sum($value),3);
            $hhtDoorOutData[$key]['man_rate'] = round($value['man']/array_sum($value),3);
            $hhtDoorOutData[$key]['free_rate'] = round($value['free']/array_sum($value),3);
        }
        //黄河坛船票票种
        $hhtBoatData = Db::Table(static::$type_table)
            ->Col(["ticket_from,sum(case when ticket_type_id='{$typeStudent}' THEN ticket_num ELSE 0 END) as student, 
                    sum(case when ticket_type_id='{$typeMan}' THEN ticket_num ELSE 0 END) as man,
                    sum(case when ticket_type_id='{$typeFree}' THEN ticket_num ELSE 0 END) as free"])
            ->Where("scenic_spot_id = '{$hhtScenicSpotId}' AND ticket_of = '{$ticketOfBoat}' AND date = '{$date}'")
            ->GroupBy('ticket_from')
            ->Find();
        foreach ($hhtBoatData as $k=>$v)
        {
            $hhtBoatOutData[$v['ticket_from']] = $v;
            unset($hhtBoatOutData[$v['ticket_from']]['ticket_from']);
        }

        foreach ($hhtBoatOutData as $key=>$value)
        {
            $hhtBoatOutData[$key]['student_rate'] = round($value['student']/array_sum($value),3);
            $hhtBoatOutData[$key]['man_rate'] = round($value['man']/array_sum($value),3);
            $hhtBoatOutData[$key]['free_rate'] = round($value['free']/array_sum($value),3);
        }

        //生态园门票票种
        $styData = Db::Table(static::$type_table)
            ->Col(['sum(ticket_num) as special'])
            ->Where("scenic_spot_id = '{$styScenicSpotId}' AND date = '{$date}'")
            ->Find()[0];
        $styData = $styData['special'] ? ['special'=>$styData['special'],'special_rate'=>1]:['special'=>0,'special_rate'=>0];

        $hhlOutData = self::numKeyToStringKey($hhlOutData,1,'scattered');
        $dxgDoorOutData = self::numKeyToStringKey($dxgDoorOutData,1,'scattered');
        $dxgBoatOutData = self::numKeyToStringKey($dxgBoatOutData,1,'scattered');
        $hhtDoorOutData = self::numKeyToStringKey($hhtDoorOutData,1,'scattered');
        $hhtBoatOutData = self::numKeyToStringKey($hhtBoatOutData,1,'scattered');

        $hhlOutData = self::numKeyToStringKey($hhlOutData,2,'group');
        $dxgDoorOutData = self::numKeyToStringKey($dxgDoorOutData,2,'group');
        $dxgBoatOutData = self::numKeyToStringKey($dxgBoatOutData,2,'group');
        $hhtDoorOutData = self::numKeyToStringKey($hhtDoorOutData,2,'group');
        $hhtBoatOutData = self::numKeyToStringKey($hhtBoatOutData,2,'group');

        $outData = [
            'hhl'=>$hhlOutData ? :['student'=>0,'man'=>0,'free'=>0,'student_rate'=>0,'man_rate'=>0,'free_rate'=>0],
            'dxg'=>['door'=>$dxgDoorOutData ? : [
                    'student'=>0,'man'=>0,'free'=>0,'student_rate'=>0,'man_rate'=>0,'free_rate'=>0],
                    'boat'=>$dxgBoatOutData ? : [
                    'student'=>0,'man'=>0,'free'=>0,'student_rate'=>0,'man_rate'=>0,'free_rate'=>0]
            ],
            'hht'=>['door'=>$hhtDoorOutData ? :[
                'student'=>0,'man'=>0,'free'=>0,'student_rate'=>0,'man_rate'=>0,'free_rate'=>0],
                'boat'=>$hhtBoatOutData ? : [
                    'student'=>0,'man'=>0,'free'=>0,'student_rate'=>0,'man_rate'=>0,'free_rate'=>0]
            ],
            'sty'=>$styData
        ];
        return $outData;
    }

    public static function getTicketTypeByTime($startTime,$endTime)
    {
        $dxgScenicSpotId = Yaf_Registry::get(COMMON)->get('dxgScenicSpotId');
        $hhlScenicSpotId = Yaf_Registry::get(COMMON)->get('hhlScenicSpotId');
        $hhtScenicSpotId = Yaf_Registry::get(COMMON)->get('hhtScenicSpotId');
        $styScenicSpotId = Yaf_Registry::get(COMMON)->get('styScenicSpotId');

        $typeStudent   = Yaf_Registry::get(COMMON)->get('ticketTypeStudent');
        $typeMan = Yaf_Registry::get(COMMON)->get('ticketTypeMan');
        $typeFree = Yaf_Registry::get(COMMON)->get('ticketTypeFree');
        $typeSpecial = Yaf_Registry::get(COMMON)->get('ticketTypeSpecial');

        $ticketOfDoor = Yaf_Registry::get(COMMON)->get('ticketOfDoor');
        $ticketOfBoat = Yaf_Registry::get(COMMON)->get('ticketOfBoat');

        //黄河楼票种
        $hhlData = Db::Table(static::$type_table)
            ->Col(["ticket_from,sum(case when ticket_type_id='{$typeStudent}' THEN ticket_num ELSE 0 END) as student, 
                    sum(case when ticket_type_id='{$typeMan}' THEN ticket_num ELSE 0 END) as man,
                    sum(case when ticket_type_id='{$typeFree}' THEN ticket_num ELSE 0 END) as free"])
            ->Where("scenic_spot_id = '{$hhlScenicSpotId}' AND date between '{$startTime}' and '{$endTime}'")
            ->GroupBy('ticket_from')
            ->Find();
        foreach ($hhlData as $k=>$v)
        {
            $hhlOutData[$v['ticket_from']] = $v;
            unset($hhlOutData[$v['ticket_from']]['ticket_from']);
            unset($v,$k);
        }

        foreach ($hhlOutData as $key=>$value)
        {
            $hhlOutData[$key]['student_rate'] = round($value['student']/array_sum($value),3);
            $hhlOutData[$key]['man_rate'] = round($value['man']/array_sum($value),3);
            $hhlOutData[$key]['free_rate'] = round($value['free']/array_sum($value),3);
            unset($key,$value);
        }

        //大峡谷门票票种
        $dxgDoorData = Db::Table(static::$type_table)
            ->Col(["ticket_from,sum(case when ticket_type_id='{$typeStudent}' THEN ticket_num ELSE 0 END) as student, 
                    sum(case when ticket_type_id='{$typeMan}' THEN ticket_num ELSE 0 END) as man,
                    sum(case when ticket_type_id='{$typeFree}' THEN ticket_num ELSE 0 END) as free"])
            ->Where("scenic_spot_id = '{$dxgScenicSpotId}' AND ticket_of = '{$ticketOfDoor}' AND date between '{$startTime}' and '{$endTime}'")
            ->GroupBy('ticket_from')
            ->Find();
        foreach ($dxgDoorData as $k=>$v)
        {
            $dxgDoorOutData[$v['ticket_from']] = $v;
            unset($dxgDoorOutData[$v['ticket_from']]['ticket_from']);
            unset($k,$v);
        }

        foreach ($dxgDoorOutData as $key=>$value)
        {
            $dxgDoorOutData[$key]['student_rate'] = round($value['student']/array_sum($value),3);
            $dxgDoorOutData[$key]['man_rate'] = round($value['man']/array_sum($value),3);
            $dxgDoorOutData[$key]['free_rate'] = round($value['free']/array_sum($value),3);
            unset($key,$value);
        }
        //大峡谷船票票种
        $dxgBoatData = Db::Table(static::$type_table)
            ->Col(["ticket_from,sum(case when ticket_type_id='{$typeStudent}' THEN ticket_num ELSE 0 END) as student, 
                    sum(case when ticket_type_id='{$typeMan}' THEN ticket_num ELSE 0 END) as man,
                    sum(case when ticket_type_id='{$typeFree}' THEN ticket_num ELSE 0 END) as free"])
            ->Where("scenic_spot_id = '{$dxgScenicSpotId}' AND ticket_of = '{$ticketOfBoat}' AND date between '{$startTime}' and '{$endTime}'")
            ->GroupBy('ticket_from')
            ->Find();
        foreach ($dxgBoatData as $k=>$v)
        {
            $dxgBoatOutData[$v['ticket_from']] = $v;
            unset($dxgBoatOutData[$v['ticket_from']]['ticket_from']);
            unset($key,$v);
        }

        foreach ($dxgBoatOutData as $key=>$value)
        {
            $dxgBoatOutData[$key]['student_rate'] = round($value['student']/array_sum($value),3);
            $dxgBoatOutData[$key]['man_rate'] = round($value['man']/array_sum($value),3);
            $dxgBoatOutData[$key]['free_rate'] = round($value['free']/array_sum($value),3);
        }

        //黄河坛门票票种
        $hhtDoorData = Db::Table(static::$type_table)
            ->Col(["ticket_from,sum(case when ticket_type_id='{$typeStudent}' THEN ticket_num ELSE 0 END) as student, 
                    sum(case when ticket_type_id='{$typeMan}' THEN ticket_num ELSE 0 END) as man,
                    sum(case when ticket_type_id='{$typeFree}' THEN ticket_num ELSE 0 END) as free"])
            ->Where("scenic_spot_id = '{$hhtScenicSpotId}' AND ticket_of = '{$ticketOfDoor}' AND date between '{$startTime}' and '{$endTime}'")
            ->GroupBy('ticket_from')
            ->Find();
        foreach ($hhtDoorData as $k=>$v)
        {
            $hhtDoorOutData[$v['ticket_from']] = $v;
            unset($hhtDoorOutData[$v['ticket_from']]['ticket_from']);
        }

        foreach ($hhtDoorOutData as $key=>$value)
        {
            $hhtDoorOutData[$key]['student_rate'] = round($value['student']/array_sum($value),3);
            $hhtDoorOutData[$key]['man_rate'] = round($value['man']/array_sum($value),3);
            $hhtDoorOutData[$key]['free_rate'] = round($value['free']/array_sum($value),3);
        }
        //黄河坛船票票种
        $hhtBoatData = Db::Table(static::$type_table)
            ->Col(["ticket_from,sum(case when ticket_type_id='{$typeStudent}' THEN ticket_num ELSE 0 END) as student, 
                    sum(case when ticket_type_id='{$typeMan}' THEN ticket_num ELSE 0 END) as man,
                    sum(case when ticket_type_id='{$typeFree}' THEN ticket_num ELSE 0 END) as free"])
            ->Where("scenic_spot_id = '{$hhtScenicSpotId}' AND ticket_of = '{$ticketOfBoat}' AND date between '{$startTime}' and '{$endTime}'")
            ->GroupBy('ticket_from')
            ->Find();
        foreach ($hhtBoatData as $k=>$v)
        {
            $hhtBoatOutData[$v['ticket_from']] = $v;
            unset($hhtBoatOutData[$v['ticket_from']]['ticket_from']);
        }

        foreach ($hhtBoatOutData as $key=>$value)
        {
            $hhtBoatOutData[$key]['student_rate'] = round($value['student']/array_sum($value),3);
            $hhtBoatOutData[$key]['man_rate'] = round($value['man']/array_sum($value),3);
            $hhtBoatOutData[$key]['free_rate'] = round($value['free']/array_sum($value),3);
        }

        //生态园门票票种
        $styData = Db::Table(static::$type_table)
            ->Col(['sum(ticket_num) as special'])
            ->Where("scenic_spot_id = '{$styScenicSpotId}' AND date between '{$startTime}' and '{$endTime}'")
            ->Find()[0];
        $styData = $styData['special'] ? ['special'=>$styData['special'],'special_rate'=>1]:['special'=>0,'special_rate'=>0];

        $hhlOutData = self::numKeyToStringKey($hhlOutData,1,'scattered');
        $dxgDoorOutData = self::numKeyToStringKey($dxgDoorOutData,1,'scattered');
        $dxgBoatOutData = self::numKeyToStringKey($dxgBoatOutData,1,'scattered');
        $hhtDoorOutData = self::numKeyToStringKey($hhtDoorOutData,1,'scattered');
        $hhtBoatOutData = self::numKeyToStringKey($hhtBoatOutData,1,'scattered');

        $hhlOutData = self::numKeyToStringKey($hhlOutData,2,'group');
        $dxgDoorOutData = self::numKeyToStringKey($dxgDoorOutData,2,'group');
        $dxgBoatOutData = self::numKeyToStringKey($dxgBoatOutData,2,'group');
        $hhtDoorOutData = self::numKeyToStringKey($hhtDoorOutData,2,'group');
        $hhtBoatOutData = self::numKeyToStringKey($hhtBoatOutData,2,'group');

        $outData = [
            'hhl'=>$hhlOutData ? :['student'=>0,'man'=>0,'free'=>0,'student_rate'=>0,'man_rate'=>0,'free_rate'=>0],
            'dxg'=>['door'=>$dxgDoorOutData ? : [
                'student'=>0,'man'=>0,'free'=>0,'student_rate'=>0,'man_rate'=>0,'free_rate'=>0],
                'boat'=>$dxgBoatOutData ? : [
                    'student'=>0,'man'=>0,'free'=>0,'student_rate'=>0,'man_rate'=>0,'free_rate'=>0]
            ],
            'hht'=>['door'=>$hhtDoorOutData ? :[
                'student'=>0,'man'=>0,'free'=>0,'student_rate'=>0,'man_rate'=>0,'free_rate'=>0],
                'boat'=>$hhtBoatOutData ? : [
                    'student'=>0,'man'=>0,'free'=>0,'student_rate'=>0,'man_rate'=>0,'free_rate'=>0]
            ],
            'sty'=>$styData
        ];
        return $outData;
    }

    public static function getSingleGroup()
    {
        $date = date('Y-m-d');
        $beforeOneDay = date('Y-m-d',(strtotime($date)-1*86400));
        $beforeTwoDay = date('Y-m-d',(strtotime($date)-2*86400));
        $beforeThreeDay = date('Y-m-d',(strtotime($date)-3*86400));
        $beforeFourDay = date('Y-m-d',(strtotime($date)-4*86400));
        $beforeFiveDay = date('Y-m-d',(strtotime($date)-5*86400));
        $beforeSixDay = date('Y-m-d',(strtotime($date)-6*86400));

        $data = Db::Table(self::$table)
            ->Col(['date,sum(scattered_ticket_num) as scattered_num,sum(group_ticket_num) as group_num'])
            ->Where("date between '{$beforeSixDay}' and '{$date}'")
            ->GroupBy('date')
            ->Find();

        foreach ($data as $k => $v)
        {
            $outData[$v['date']] = $v;
        }

        $outData[$beforeSixDay] = array_key_exists($beforeSixDay,$outData) ?
            [
                'group_num'=>$outData[$beforeSixDay]['group_num'],
                'scattered_num' =>$outData[$beforeSixDay]['scattered_num'],
                'group_rate' => round($outData[$beforeSixDay]['group_num']/($outData[$beforeSixDay]['group_num']+$outData[$beforeSixDay]['scattered_num']),3),
                'scattered_rate' => round($outData[$beforeSixDay]['scattered_num']/($outData[$beforeSixDay]['group_num']+$outData[$beforeSixDay]['scattered_num']),3)
            ]
            :[
                'group_num'=>0,
                'scattered_num'=>0,
                'group_rate'=>0,
                'scattered_rate'=>0
            ];

        $outData[$beforeFiveDay] = array_key_exists($beforeFiveDay,$outData) ?
            [
                'group_num'=>$outData[$beforeFiveDay]['group_num'],
                'scattered_num' =>$outData[$beforeFiveDay]['scattered_num'],
                'group_rate' => round($outData[$beforeFiveDay]['group_num']/($outData[$beforeFiveDay]['group_num']+$outData[$beforeFiveDay]['scattered_num']),3),
                'scattered_rate' => round($outData[$beforeFiveDay]['scattered_num']/($outData[$beforeFiveDay]['group_num']+$outData[$beforeFiveDay]['scattered_num']),3)
            ]
            :[
                'group_num'=>0,
                'scattered_num'=>0,
                'group_rate'=>0,
                'scattered_rate'=>0
            ];

        $outData[$beforeFourDay] = array_key_exists($beforeFourDay,$outData) ?
            [
                'group_num'=>$outData[$beforeFourDay]['group_num'],
                'scattered_num' =>$outData[$beforeFourDay]['scattered_num'],
                'group_rate' => round($outData[$beforeFourDay]['group_num']/($outData[$beforeFourDay]['group_num']+$outData[$beforeFourDay]['scattered_num']),3),
                'scattered_rate' => round($outData[$beforeFourDay]['scattered_num']/($outData[$beforeFourDay]['group_num']+$outData[$beforeFourDay]['scattered_num']),3)
            ]
            :[
                'group_num'=>0,
                'scattered_num'=>0,
                'group_rate'=>0,
                'scattered_rate'=>0
            ];

        $outData[$beforeThreeDay] = array_key_exists($beforeThreeDay,$outData) ?
            [
                'group_num'=>$outData[$beforeThreeDay]['group_num'],
                'scattered_num' =>$outData[$beforeThreeDay]['scattered_num'],
                'group_rate' => round($outData[$beforeThreeDay]['group_num']/($outData[$beforeThreeDay]['group_num']+$outData[$beforeThreeDay]['scattered_num']),3),
                'scattered_rate' => round($outData[$beforeThreeDay]['scattered_num']/($outData[$beforeThreeDay]['group_num']+$outData[$beforeThreeDay]['scattered_num']),3)
            ]
            :[
                'group_num'=>0,
                'scattered_num'=>0,
                'group_rate'=>0,
                'scattered_rate'=>0
            ];

        $outData[$beforeTwoDay] = array_key_exists($beforeTwoDay,$outData) ?
            [
                'group_num'=>$outData[$beforeTwoDay]['group_num'],
                'scattered_num' =>$outData[$beforeTwoDay]['scattered_num'],
                'group_rate' => round($outData[$beforeTwoDay]['group_num']/($outData[$beforeTwoDay]['group_num']+$outData[$beforeTwoDay]['scattered_num']),3),
                'scattered_rate' => round($outData[$beforeTwoDay]['scattered_num']/($outData[$beforeTwoDay]['group_num']+$outData[$beforeTwoDay]['scattered_num']),3)
            ]
            :[
                'group_num'=>0,
                'scattered_num'=>0,
                'group_rate'=>0,
                'scattered_rate'=>0
            ];

        $outData[$beforeOneDay] = array_key_exists($beforeOneDay,$outData) ?
            [
                'group_num'=>$outData[$beforeOneDay]['group_num'],
                'scattered_num' =>$outData[$beforeOneDay]['scattered_num'],
                'group_rate' => round($outData[$beforeOneDay]['group_num']/($outData[$beforeOneDay]['group_num']+$outData[$beforeOneDay]['scattered_num']),3),
                'scattered_rate' => round($outData[$beforeOneDay]['scattered_num']/($outData[$beforeOneDay]['group_num']+$outData[$beforeOneDay]['scattered_num']),3)
            ]
            :[
                'group_num'=>0,
                'scattered_num'=>0,
                'group_rate'=>0,
                'scattered_rate'=>0
            ];

        $outData[$date] = array_key_exists($date,$outData) ?
            [
                'group_num'=>$outData[$date]['group_num'],
                'scattered_num' =>$outData[$date]['scattered_num'],
                'group_rate' => round($outData[$date]['group_num']/($outData[$date]['group_num']+$outData[$date]['scattered_num']),3),
                'scattered_rate' => round($outData[$date]['scattered_num']/($outData[$date]['group_num']+$outData[$date]['scattered_num']),3)
            ]
            :[
                'group_num'=>0,
                'scattered_num'=>0,
                'group_rate'=>0,
                'scattered_rate'=>0
            ];
        ksort($outData);
        return $outData;
    }

    public static function getSingleGroupByTime($startTime,$endTime)
    {
        $diffTime = strtotime($endTime) - strtotime($startTime);
        $day = $diffTime/86400+1;
        for ($i=0;$i<$day;$i++)
        {
            $dateList[$i] = date('Y-m-d',strtotime($startTime)+$i*86400);
        }
        $data = Db::Table(self::$table)
            ->Col(["date,sum(scattered_ticket_num) as scattered_num,sum(group_ticket_num) as group_num"])
            ->Where("date between '{$startTime}' and '{$endTime}'")
            ->GroupBy('date')
            ->Find();
        foreach ($data as $k => $v)
        {
            $outData[$v['date']] = $v;
        }

        foreach ($dateList as $key => $value)
        {
            $outData[$value] = array_key_exists($value,$outData) ?
                [
                    'group_num'=>$outData[$value]['group_num'],
                    'scattered_num' =>$outData[$value]['scattered_num'],
                    'group_rate' => round($outData[$value]['group_num']/($outData[$value]['group_num']+$outData[$value]['scattered_num']),3),
                    'scattered_rate' => round($outData[$value]['scattered_num']/($outData[$value]['group_num']+$outData[$value]['scattered_num']),3)
                ]
                :[
                    'group_num'=>0,
                    'scattered_num'=>0,
                    'group_rate'=>0,
                    'scattered_rate'=>0
                ]
            ;
        }
        ksort($outData);
        return $outData;
    }

    public static function getTotalSingleGroup()
    {
        $date = date('Y-m-d');
        $ticketData = $ageDataBeforeTwoYear = Db::Table(static::$table)
            ->Col([
                'sum(scattered_ticket_num) as scattered_num,
                sum(group_ticket_num) as group_num'
            ])
            ->Where("date = '{$date}'")
            ->Find()[0];
        $total_num = $ticketData['scattered_num']+$ticketData['group_num'];
        $ticketData['scattered_rate'] = $total_num ?
            round($ticketData['scattered_num']/$total_num,3) : 0;
        $ticketData['group_rate'] = $total_num ?
            round($ticketData['group_num']/$total_num,3) : 0;
        $ticketData['group_scattered_rate'] = $total_num ?
            round($ticketData['group_num']/$ticketData['scattered_num'],3) : 0;

        return $ticketData;
    }

    public static function getTotalSingleGroupByTime($startTime,$endTime)
    {
        $date = date('Y-m-d');
        $ticketData = $ageDataBeforeTwoYear = Db::Table(static::$table)
            ->Col([
                'sum(scattered_ticket_num) as scattered_num,
                sum(group_ticket_num) as group_num'
            ])
            ->Where("date between '{$startTime}' and '{$endTime}'")
            ->Find()[0];
        $total_num = $ticketData['scattered_num']+$ticketData['group_num'];
        $ticketData['scattered_rate'] = $total_num ?
            round($ticketData['scattered_num']/$total_num,3) : 0;
        $ticketData['group_rate'] = $total_num ?
            round($ticketData['group_num']/$total_num,3) : 0;
        $ticketData['group_scattered_rate'] = $total_num ?
            round($ticketData['group_num']/$ticketData['scattered_num'],3) : 0;

        return $ticketData;
    }

    public static function  numKeyToStringKey($data,$numKey=1,$stringKey='scattered')
    {
        if(array_key_exists($numKey,$data)){
            $data[$stringKey] = $data[$numKey];
        }else{
            $data[$stringKey] = ['student'=>0,'man'=>0,'free'=>0,'student_rate'=>0,'man_rate'=>0,'free_rate'=>0];
        }
        unset($data[$numKey]);
        return $data;
    }
}