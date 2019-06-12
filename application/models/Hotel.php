<?php
/**
 * User: wgg
 * Date: 19-4-12
 * Time: 下午2:54
 */
class HotelModel
{
    private static $table = 'hotel_occupancy';

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

    public static function getHotelOccupancy()
    {
        $date = date('Y-m-d');
        $qtxHotelStandardRoom = Yaf_Registry::get(COMMON)-> qtxHotel->get('standardRoom');
        $qtxHotelKingSizeRoom = Yaf_Registry::get(COMMON)-> qtxHotel->get('kingSizeRoom');
        $qtxHotelOtherRoom    = Yaf_Registry::get(COMMON)-> qtxHotel->get('otherRoom');
        
        $lhHotelStandardRoom = Yaf_Registry::get(COMMON)-> lhHotel->get('standardRoom');
        $lhHotelKingSizeRoom = Yaf_Registry::get(COMMON)-> lhHotel->get('kingSizeRoom');
        $lhHotelOtherRoom  = Yaf_Registry::get(COMMON)-> lhHotel->get('otherRoom');

        $glhtHotelStandardRoom = Yaf_Registry::get(COMMON)-> glhtHotel->get('standardRoom');
        $glhtHotelKingSizeRoom = Yaf_Registry::get(COMMON)-> glhtHotel->get('kingSizeRoom');
        $glhtHotelOtherRoom  = Yaf_Registry::get(COMMON)-> glhtHotel->get('otherRoom');

        $kpHotelStandardRoom = Yaf_Registry::get(COMMON)-> kpHotel->get('standardRoom');
        $kpHotelKingSizeRoom = Yaf_Registry::get(COMMON)-> kpHotel->get('kingSizeRoom');
        $kpHotelOtherRoom  = Yaf_Registry::get(COMMON)-> kpHotel->get('otherRoom');

        $htHotelStandardRoom = Yaf_Registry::get(COMMON)-> htHotel->get('standardRoom');
        $htHotelKingSizeRoom = Yaf_Registry::get(COMMON)-> htHotel->get('kingSizeRoom');
        $htHotelOtherRoom  = Yaf_Registry::get(COMMON)-> htHotel->get('otherRoom');

        $qyHotelStandardRoom = Yaf_Registry::get(COMMON)-> qyHotel->get('standardRoom');
        $qyHotelKingSizeRoom = Yaf_Registry::get(COMMON)-> qyHotel->get('kingSizeRoom');
        $qyHotelOtherRoom  = Yaf_Registry::get(COMMON)-> qyHotel->get('otherRoom');

        $sxHotelStandardRoom = Yaf_Registry::get(COMMON)-> sxHotel->get('standardRoom');
        $sxHotelKingSizeRoom = Yaf_Registry::get(COMMON)-> sxHotel->get('kingSizeRoom');
        $sxHotelOtherRoom  = Yaf_Registry::get(COMMON)-> sxHotel->get('otherRoom');

        $whHotelStandardRoom = Yaf_Registry::get(COMMON)-> whHotel->get('standardRoom');
        $whHotelKingSizeRoom = Yaf_Registry::get(COMMON)-> whHotel->get('kingSizeRoom');
        $whHotelOtherRoom  = Yaf_Registry::get(COMMON)-> whHotel->get('otherRoom');
        $OtherHotelStandardRoom = Yaf_Registry::get(COMMON)-> OtherHotel->get('standardRoom');
        $OtherHotelKingSizeRoom = Yaf_Registry::get(COMMON)-> OtherHotel->get('kingSizeRoom');
        $OtherHotelOtherRoom  = Yaf_Registry::get(COMMON)-> OtherHotel->get('otherRoom');

        $total_rome = $qtxHotelStandardRoom+$qtxHotelKingSizeRoom+$qtxHotelOtherRoom
                    + $lhHotelStandardRoom+$lhHotelKingSizeRoom+$lhHotelOtherRoom
                    + $OtherHotelStandardRoom+$OtherHotelKingSizeRoom+$OtherHotelOtherRoom
                    +$glhtHotelStandardRoom+$glhtHotelKingSizeRoom+$glhtHotelOtherRoom
                    +$kpHotelStandardRoom+$kpHotelKingSizeRoom+$kpHotelOtherRoom
                    +$htHotelStandardRoom+$htHotelKingSizeRoom+$htHotelOtherRoom
                    +$qyHotelStandardRoom+$qyHotelKingSizeRoom+$qyHotelOtherRoom
                    +$sxHotelStandardRoom+$sxHotelKingSizeRoom+$sxHotelOtherRoom
                    +$whHotelStandardRoom+$whHotelKingSizeRoom+$whHotelOtherRoom;
        $hotelData = Db::Table(self::$table)
            ->Col(['sum(hotel_occupancy_num) as hotel_occupancy_num'])
            ->Where("date = '{$date}'")
            ->Find()[0];
        if(!$hotelData){
            return ['hotel_room_num'=>0,'hotel_occupancy_num'=>0,'rate'=>0];
        }
        $hotelOccupancyRate = $hotelData  &&
        $total_rome ? $hotelData['hotel_occupancy_num']/$total_rome : 0;
        $hotelData['hotel_room_num'] = $total_rome;
        $hotelData['rate'] = round($hotelOccupancyRate,3);
        return $hotelData;
    }

    public static function getHotelOccupancyByTime($startTime,$endTime)
    {
        $diffTime = strtotime($endTime) - strtotime($startTime);
        $day = $diffTime/86400+1;
        $qtxHotelStandardRoom = Yaf_Registry::get(COMMON)-> qtxHotel->get('standardRoom');
        $qtxHotelKingSizeRoom = Yaf_Registry::get(COMMON)-> qtxHotel->get('kingSizeRoom');
        $qtxHotelOtherRoom    = Yaf_Registry::get(COMMON)-> qtxHotel->get('otherRoom');

        $lhHotelStandardRoom = Yaf_Registry::get(COMMON)-> lhHotel->get('standardRoom');
        $lhHotelKingSizeRoom = Yaf_Registry::get(COMMON)-> lhHotel->get('kingSizeRoom');
        $lhHotelOtherRoom  = Yaf_Registry::get(COMMON)-> lhHotel->get('otherRoom');

        $glhtHotelStandardRoom = Yaf_Registry::get(COMMON)-> glhtHotel->get('standardRoom');
        $glhtHotelKingSizeRoom = Yaf_Registry::get(COMMON)-> glhtHotel->get('kingSizeRoom');
        $glhtHotelOtherRoom  = Yaf_Registry::get(COMMON)-> glhtHotel->get('otherRoom');

        $kpHotelStandardRoom = Yaf_Registry::get(COMMON)-> kpHotel->get('standardRoom');
        $kpHotelKingSizeRoom = Yaf_Registry::get(COMMON)-> kpHotel->get('kingSizeRoom');
        $kpHotelOtherRoom  = Yaf_Registry::get(COMMON)-> kpHotel->get('otherRoom');

        $htHotelStandardRoom = Yaf_Registry::get(COMMON)-> htHotel->get('standardRoom');
        $htHotelKingSizeRoom = Yaf_Registry::get(COMMON)-> htHotel->get('kingSizeRoom');
        $htHotelOtherRoom  = Yaf_Registry::get(COMMON)-> htHotel->get('otherRoom');

        $qyHotelStandardRoom = Yaf_Registry::get(COMMON)-> qyHotel->get('standardRoom');
        $qyHotelKingSizeRoom = Yaf_Registry::get(COMMON)-> qyHotel->get('kingSizeRoom');
        $qyHotelOtherRoom  = Yaf_Registry::get(COMMON)-> qyHotel->get('otherRoom');

        $sxHotelStandardRoom = Yaf_Registry::get(COMMON)-> sxHotel->get('standardRoom');
        $sxHotelKingSizeRoom = Yaf_Registry::get(COMMON)-> sxHotel->get('kingSizeRoom');
        $sxHotelOtherRoom  = Yaf_Registry::get(COMMON)-> sxHotel->get('otherRoom');

        $whHotelStandardRoom = Yaf_Registry::get(COMMON)-> whHotel->get('standardRoom');
        $whHotelKingSizeRoom = Yaf_Registry::get(COMMON)-> whHotel->get('kingSizeRoom');
        $whHotelOtherRoom  = Yaf_Registry::get(COMMON)-> whHotel->get('otherRoom');

        $OtherHotelStandardRoom = Yaf_Registry::get(COMMON)-> OtherHotel->get('standardRoom');
        $OtherHotelKingSizeRoom = Yaf_Registry::get(COMMON)-> OtherHotel->get('kingSizeRoom');
        $OtherHotelOtherRoom  = Yaf_Registry::get(COMMON)-> OtherHotel->get('otherRoom');

        $total_rome = $qtxHotelStandardRoom+$qtxHotelKingSizeRoom+$qtxHotelOtherRoom
            + $lhHotelStandardRoom+$lhHotelKingSizeRoom+$lhHotelOtherRoom
            + $OtherHotelStandardRoom+$OtherHotelKingSizeRoom+$OtherHotelOtherRoom
            +$glhtHotelStandardRoom+$glhtHotelKingSizeRoom+$glhtHotelOtherRoom
            +$kpHotelStandardRoom+$kpHotelKingSizeRoom+$kpHotelOtherRoom
            +$htHotelStandardRoom+$htHotelKingSizeRoom+$htHotelOtherRoom
            +$qyHotelStandardRoom+$qyHotelKingSizeRoom+$qyHotelOtherRoom
            +$sxHotelStandardRoom+$sxHotelKingSizeRoom+$sxHotelOtherRoom
            +$whHotelStandardRoom+$whHotelKingSizeRoom+$whHotelOtherRoom;
        $total_rome = $total_rome*$day;
        $hotelData = Db::Table(self::$table)
            ->Col(['sum(hotel_occupancy_num) as hotel_occupancy_num'])
            ->Where("date between '{$startTime}' and '{$endTime}'")
            ->Find()[0];
        if(!$hotelData){
            return ['hotel_room_num'=>0,'hotel_occupancy_num'=>0,'rate'=>0];
        }
        $hotelOccupancyRate = $hotelData  &&
        $total_rome ? $hotelData['hotel_occupancy_num']/$total_rome : 0;
        $hotelData['hotel_room_num'] = $total_rome;
        $hotelData['rate'] = round($hotelOccupancyRate,3);
        return $hotelData;
    }

    public static function getHotelDetailData()
    {
        $date = date('Y-m-d');
        $qtxHotelStandardRoom = Yaf_Registry::get(COMMON)-> qtxHotel->get('standardRoom');
        $qtxHotelKingSizeRoom = Yaf_Registry::get(COMMON)-> qtxHotel->get('kingSizeRoom');
        $qtxHotelOtherRoom    = Yaf_Registry::get(COMMON)-> qtxHotel->get('otherRoom');

        $lhHotelStandardRoom = Yaf_Registry::get(COMMON)-> lhHotel->get('standardRoom');
        $lhHotelKingSizeRoom = Yaf_Registry::get(COMMON)-> lhHotel->get('kingSizeRoom');
        $lhHotelOtherRoom  = Yaf_Registry::get(COMMON)-> lhHotel->get('otherRoom');

        $glhtHotelStandardRoom = Yaf_Registry::get(COMMON)-> glhtHotel->get('standardRoom');
        $glhtHotelKingSizeRoom = Yaf_Registry::get(COMMON)-> glhtHotel->get('kingSizeRoom');
        $glhtHotelOtherRoom  = Yaf_Registry::get(COMMON)-> glhtHotel->get('otherRoom');

        $kpHotelStandardRoom = Yaf_Registry::get(COMMON)-> kpHotel->get('standardRoom');
        $kpHotelKingSizeRoom = Yaf_Registry::get(COMMON)-> kpHotel->get('kingSizeRoom');
        $kpHotelOtherRoom  = Yaf_Registry::get(COMMON)-> kpHotel->get('otherRoom');

        $htHotelStandardRoom = Yaf_Registry::get(COMMON)-> htHotel->get('standardRoom');
        $htHotelKingSizeRoom = Yaf_Registry::get(COMMON)-> htHotel->get('kingSizeRoom');
        $htHotelOtherRoom  = Yaf_Registry::get(COMMON)-> htHotel->get('otherRoom');

        $qyHotelStandardRoom = Yaf_Registry::get(COMMON)-> qyHotel->get('standardRoom');
        $qyHotelKingSizeRoom = Yaf_Registry::get(COMMON)-> qyHotel->get('kingSizeRoom');
        $qyHotelOtherRoom  = Yaf_Registry::get(COMMON)-> qyHotel->get('otherRoom');

        $sxHotelStandardRoom = Yaf_Registry::get(COMMON)-> sxHotel->get('standardRoom');
        $sxHotelKingSizeRoom = Yaf_Registry::get(COMMON)-> sxHotel->get('kingSizeRoom');
        $sxHotelOtherRoom  = Yaf_Registry::get(COMMON)-> sxHotel->get('otherRoom');

        $whHotelStandardRoom = Yaf_Registry::get(COMMON)-> whHotel->get('standardRoom');
        $whHotelKingSizeRoom = Yaf_Registry::get(COMMON)-> whHotel->get('kingSizeRoom');
        $whHotelOtherRoom  = Yaf_Registry::get(COMMON)-> whHotel->get('otherRoom');

        $OtherHotelStandardRoom = Yaf_Registry::get(COMMON)-> OtherHotel->get('standardRoom');
        $OtherHotelKingSizeRoom = Yaf_Registry::get(COMMON)-> OtherHotel->get('kingSizeRoom');
        $OtherHotelOtherRoom  = Yaf_Registry::get(COMMON)-> OtherHotel->get('otherRoom');

        $qtxHotelNum   = Yaf_Registry::get(COMMON)-> get('qtxHotelNum');
        $lhHotelNum    = Yaf_Registry::get(COMMON)-> get('lhHotelNum');
        $glhtHotelNum  = Yaf_Registry::get(COMMON)-> get('glhtHotelNum');
        $kpHotelNum    = Yaf_Registry::get(COMMON)-> get('kpHotelNum');
        $htHotelNum    = Yaf_Registry::get(COMMON)-> get('htHotelNum');
        $qyHotelNum    = Yaf_Registry::get(COMMON)-> get('qyHotelNum');
        $sxHotelNum    = Yaf_Registry::get(COMMON)-> get('sxHotelNum');
        $whHotelNum    = Yaf_Registry::get(COMMON)-> get('whHotelNum');
        $otherHotelNum = Yaf_Registry::get(COMMON)-> get('otherHotelNum');

        $standardRoomType = Yaf_Registry::get(COMMON)-> get('standardRoomType');
        $kingSizeRoomType = Yaf_Registry::get(COMMON)-> get('kingSizeRoomType');
        $otherRoomType    = Yaf_Registry::get(COMMON)-> get('otherRoomType');

        $data = Db::Table(static::$table)
            ->Col(["hotel_id,sum(CASE WHEN room_type='{$standardRoomType}' THEN hotel_occupancy_num ELSE 0 END) as standard_occupancy,
            sum(CASE WHEN room_type='{$kingSizeRoomType}' THEN hotel_occupancy_num ELSE 0 END) as kingsize_occupancy,
            sum(CASE WHEN room_type='{$otherRoomType}' THEN hotel_occupancy_num ELSE 0 END) as other_occupancy"])
            ->Where("date ='{$date}'")
            ->GroupBy('hotel_id')
            ->Find();
        foreach ($data as $k => $v)
        {
            $hotelData[$v['hotel_id']] = $v;
            unset($hotelData[$v['hotel_id']]['hotel_id']);
        }

        $hotelData[$qtxHotelNum] = array_key_exists($qtxHotelNum,$hotelData)
            ? [
                'left_data'=>[
                    'hotel' => '青铜峡宾馆',
                    'hotel_occupancy' => array_sum($hotelData[$qtxHotelNum]),
                    'hotel_room' => $qtxHotelStandardRoom+$qtxHotelKingSizeRoom+$qtxHotelOtherRoom,
                    'hotel_rate' => round(array_sum($hotelData[$qtxHotelNum])/
                        ($qtxHotelStandardRoom+$qtxHotelKingSizeRoom+$qtxHotelOtherRoom) ,3),
                    ],
                'right_data'=>[
                    'standard'=>[
                        'occupancy' => $hotelData[$qtxHotelNum]['standard_occupancy'],
                        'room' => $qtxHotelStandardRoom,
                        'rate' => $qtxHotelStandardRoom ? round($hotelData[$qtxHotelNum]['standard_occupancy']/$qtxHotelStandardRoom,3):0
                    ],
                    'kingsize'=>[
                        'occupancy' => $hotelData[$qtxHotelNum]['kingsize_occupancy'],
                        'room' => $qtxHotelKingSizeRoom,
                        'rate' => $qtxHotelKingSizeRoom ? round($hotelData[$qtxHotelNum]['kingsize_occupancy']/$qtxHotelKingSizeRoom,3):0
                    ],
                    'other'=>[
                        'occupancy' => $hotelData[$qtxHotelNum]['other_occupancy'],
                        'room' => $qtxHotelOtherRoom,
                        'rate' => $qtxHotelOtherRoom ? round($hotelData[$qtxHotelNum]['other_occupancy']/$qtxHotelOtherRoom,3):0
                    ]
                ]
            ]
            : [
                'left_data'=>[
                    'hotel' => '青铜峡宾馆',
                    'hotel_occupancy' => 0,
                    'hotel_room' => $qtxHotelStandardRoom+$qtxHotelKingSizeRoom+$qtxHotelOtherRoom,
                    'hotel_rate' => 0,
                ],
                'right_data'=>[
                    'standard'=>[
                        'occupancy' => 0,
                        'room' => $qtxHotelStandardRoom,
                        'rate' => 0
                    ],
                    'kingsize'=>[
                        'occupancy' => 0,
                        'room' => $qtxHotelKingSizeRoom,
                        'rate' => 0
                    ],
                    'other'=>[
                        'occupancy' => 0,
                        'room' => $qtxHotelOtherRoom,
                        'rate' => 0
                    ]
                ]
            ];

        $hotelData[$lhHotelNum] = array_key_exists($lhHotelNum,$hotelData)
            ? [
                'left_data'=>[
                    'hotel' => '龙海宾馆',
                    'hotel_occupancy' => array_sum($hotelData[$lhHotelNum]),
                    'hotel_room' => $lhHotelStandardRoom+$lhHotelKingSizeRoom+$lhHotelOtherRoom,
                    'hotel_rate' => round(array_sum($hotelData[$lhHotelNum])/
                        ($lhHotelStandardRoom+$lhHotelKingSizeRoom+$lhHotelOtherRoom) ,3),
                    ],
                'right_data'=>[
                    'standard'=>[
                        'occupancy' => $hotelData[$lhHotelNum]['standard_occupancy'],
                        'room' => $lhHotelStandardRoom,
                        'rate' => $lhHotelStandardRoom ? round($hotelData[$lhHotelNum]['standard_occupancy']/$lhHotelStandardRoom,3):0
                    ],
                    'kingsize'=>[
                        'occupancy' => $hotelData[$lhHotelNum]['kingsize_occupancy'],
                        'room' => $lhHotelKingSizeRoom,
                        'rate' => $lhHotelKingSizeRoom ? round($hotelData[$lhHotelNum]['kingsize_occupancy']/$lhHotelKingSizeRoom,3):0
                    ],
                    'other'=>[
                        'occupancy' => $hotelData[$lhHotelNum]['other_occupancy'],
                        'room' => $lhHotelOtherRoom,
                        'rate' => $lhHotelOtherRoom ? round($hotelData[$lhHotelNum]['other_occupancy']/$lhHotelOtherRoom,3):0
                    ]
                ]
            ]
            : [
                'left_data'=>[
                    'hotel' => '龙海宾馆',
                    'hotel_occupancy' => 0,
                    'hotel_room' => $lhHotelStandardRoom+$lhHotelKingSizeRoom+$lhHotelOtherRoom,
                    'hotel_rate' => 0,
                    ],
                'right_data'=>[
                    'standard'=>[
                        'occupancy' => 0,
                        'room' => $lhHotelStandardRoom,
                        'rate' => 0
                    ],
                    'kingsize'=>[
                        'occupancy' => 0,
                        'room' => $lhHotelKingSizeRoom,
                        'rate' => 0
                    ],
                    'other'=>[
                        'occupancy' => 0,
                        'room' => $lhHotelOtherRoom,
                        'rate' => 0
                    ]
                ]
            ];
        $hotelData[$glhtHotelNum] = array_key_exists($glhtHotelNum,$hotelData)
            ? [
                'left_data'=>[
                    'hotel' => '格林豪泰',
                    'hotel_occupancy' => array_sum($hotelData[$glhtHotelNum]),
                    'hotel_room' => $glhtHotelStandardRoom+$glhtHotelKingSizeRoom+$glhtHotelOtherRoom,
                    'hotel_rate' => round(array_sum($hotelData[$glhtHotelNum])/
                        ($glhtHotelStandardRoom+$glhtHotelKingSizeRoom+$glhtHotelOtherRoom) ,3),
                    ],
                'right_data'=>[
                    'standard'=>[
                        'occupancy' => $hotelData[$glhtHotelNum]['standard_occupancy'],
                        'room' => $glhtHotelStandardRoom,
                        'rate' => $glhtHotelStandardRoom ? round($hotelData[$glhtHotelNum]['standard_occupancy']/$glhtHotelStandardRoom,3):0
                    ],
                    'kingsize'=>[
                        'occupancy' => $hotelData[$glhtHotelNum]['kingsize_occupancy'],
                        'room' => $glhtHotelKingSizeRoom,
                        'rate' => $glhtHotelKingSizeRoom ? round($hotelData[$glhtHotelNum]['kingsize_occupancy']/$glhtHotelKingSizeRoom,3):0
                    ],
                    'other'=>[
                        'occupancy' => $hotelData[$glhtHotelNum]['other_occupancy'],
                        'room' => $glhtHotelOtherRoom,
                        'rate' => $glhtHotelOtherRoom ? round($hotelData[$glhtHotelNum]['other_occupancy']/$glhtHotelOtherRoom,3):0
                    ]
                ]
            ]
            : [
                'left_data'=>[
                    'hotel' => '格林豪泰',
                    'hotel_occupancy' => 0,
                    'hotel_room' => $glhtHotelStandardRoom+$glhtHotelKingSizeRoom+$glhtHotelOtherRoom,
                    'hotel_rate' => 0,
                    ],
                'right_data'=>[
                    'standard'=>[
                        'occupancy' => 0,
                        'room' => $glhtHotelStandardRoom,
                        'rate' => 0
                    ],
                    'kingsize'=>[
                        'occupancy' => 0,
                        'room' => $glhtHotelKingSizeRoom,
                        'rate' => 0
                    ],
                    'other'=>[
                        'occupancy' => 0,
                        'room' => $glhtHotelOtherRoom,
                        'rate' => 0
                    ]
                ]
            ];
        $hotelData[$kpHotelNum] = array_key_exists($kpHotelNum,$hotelData)
            ? [
                'left_data'=>[
                    'hotel' => '凯鹏宾馆',
                    'hotel_occupancy' => array_sum($hotelData[$kpHotelNum]),
                    'hotel_room' => $kpHotelStandardRoom+$kpHotelKingSizeRoom+$kpHotelOtherRoom,
                    'hotel_rate' => round(array_sum($hotelData[$kpHotelNum])/
                        ($kpHotelStandardRoom+$kpHotelKingSizeRoom+$kpHotelOtherRoom) ,3),
                    ],
                'right_data'=>[
                    'standard'=>[
                        'occupancy' => $hotelData[$kpHotelNum]['standard_occupancy'],
                        'room' => $kpHotelStandardRoom,
                        'rate' => $kpHotelStandardRoom ? round($hotelData[$kpHotelNum]['standard_occupancy']/$kpHotelStandardRoom,3):0
                    ],
                    'kingsize'=>[
                        'occupancy' => $hotelData[$kpHotelNum]['kingsize_occupancy'],
                        'room' => $kpHotelKingSizeRoom,
                        'rate' => $kpHotelKingSizeRoom ? round($hotelData[$kpHotelNum]['kingsize_occupancy']/$kpHotelKingSizeRoom,3):0
                    ],
                    'other'=>[
                        'occupancy' => $hotelData[$kpHotelNum]['other_occupancy'],
                        'room' => $kpHotelOtherRoom,
                        'rate' => $kpHotelOtherRoom ? round($hotelData[$kpHotelNum]['other_occupancy']/$kpHotelOtherRoom,3):0
                    ]
                ]
            ]
            : [
                'left_data'=>[
                    'hotel' => '凯鹏宾馆',
                    'hotel_occupancy' => 0,
                    'hotel_room' => $kpHotelStandardRoom+$kpHotelKingSizeRoom+$kpHotelOtherRoom,
                    'hotel_rate' => 0,
                    ],
                'right_data'=>[
                    'standard'=>[
                        'occupancy' => 0,
                        'room' => $kpHotelStandardRoom,
                        'rate' => 0
                    ],
                    'kingsize'=>[
                        'occupancy' => 0,
                        'room' => $kpHotelStandardRoom,
                        'rate' => 0
                    ],
                    'other'=>[
                        'occupancy' => 0,
                        'room' => $kpHotelOtherRoom,
                        'rate' => 0
                    ]
                ]
            ];
        $hotelData[$htHotelNum] = array_key_exists($htHotelNum,$hotelData)
            ? [
                'left_data'=>[
                    'hotel' => '豪泰大酒店',
                    'hotel_occupancy' => array_sum($hotelData[$htHotelNum]),
                    'hotel_room' => $htHotelStandardRoom+$htHotelKingSizeRoom+$htHotelOtherRoom,
                    'hotel_rate' => round(array_sum($hotelData[$htHotelNum])/
                        ($htHotelStandardRoom+$htHotelKingSizeRoom+$htHotelOtherRoom) ,3),
                    ],
                'right_data'=>[
                    'standard'=>[
                        'occupancy' => $hotelData[$htHotelNum]['standard_occupancy'],
                        'room' => $htHotelStandardRoom,
                        'rate' => $htHotelStandardRoom ? round($hotelData[$htHotelNum]['standard_occupancy']/$htHotelStandardRoom,3):0
                    ],
                    'kingsize'=>[
                        'occupancy' => $hotelData[$htHotelNum]['kingsize_occupancy'],
                        'room' => $htHotelKingSizeRoom,
                        'rate' => $htHotelKingSizeRoom ? round($hotelData[$htHotelNum]['kingsize_occupancy']/$htHotelKingSizeRoom,3):0
                    ],
                    'other'=>[
                        'occupancy' => $hotelData[$htHotelNum]['other_occupancy'],
                        'room' => $htHotelOtherRoom,
                        'rate' => $htHotelOtherRoom ? round($hotelData[$htHotelNum]['other_occupancy']/$htHotelOtherRoom,3):0
                    ]
                ]
            ]
            : [
                'left_data'=>[
                    'hotel' => '豪泰大酒店',
                    'hotel_occupancy' => 0,
                    'hotel_room' => $htHotelStandardRoom+$htHotelKingSizeRoom+$htHotelOtherRoom,
                    'hotel_rate' => 0,
                    ],
                'right_data'=>[
                    'standard'=>[
                        'occupancy' => 0,
                        'room' => $htHotelStandardRoom,
                        'rate' => 0
                    ],
                    'kingsize'=>[
                        'occupancy' => 0,
                        'room' => $htHotelKingSizeRoom,
                        'rate' => 0
                    ],
                    'other'=>[
                        'occupancy' => 0,
                        'room' => $htHotelOtherRoom,
                        'rate' => 0
                    ]
                ]
            ];
        $hotelData[$qyHotelNum] = array_key_exists($qyHotelNum,$hotelData)
            ? [
                'left_data'=>[
                    'hotel' => '庆阳大酒店',
                    'hotel_occupancy' => array_sum($hotelData[$qyHotelNum]),
                    'hotel_room' => $qyHotelStandardRoom+$qyHotelKingSizeRoom+$qyHotelOtherRoom,
                    'hotel_rate' => round(array_sum($hotelData[$qyHotelNum])/
                        ($qyHotelStandardRoom+$qyHotelKingSizeRoom+$qyHotelOtherRoom) ,3),
                    ],
                'right_data'=>[
                    'standard'=>[
                        'occupancy' => $hotelData[$qyHotelNum]['standard_occupancy'],
                        'room' => $qyHotelStandardRoom,
                        'rate' => $qyHotelStandardRoom ? round($hotelData[$qyHotelNum]['standard_occupancy']/$qyHotelStandardRoom,3):0
                    ],
                    'kingsize'=>[
                        'occupancy' => $hotelData[$qyHotelNum]['kingsize_occupancy'],
                        'room' => $qyHotelKingSizeRoom,
                        'rate' => $qyHotelKingSizeRoom ? round($hotelData[$qyHotelNum]['kingsize_occupancy']/$qyHotelKingSizeRoom,3):0
                    ],
                    'other'=>[
                        'occupancy' => $hotelData[$qyHotelNum]['other_occupancy'],
                        'room' => $qyHotelOtherRoom,
                        'rate' => $qyHotelOtherRoom ? round($hotelData[$qyHotelNum]['other_occupancy']/$qyHotelOtherRoom,3):0
                    ]
                ]
            ]
            : [
                'left_data'=>[
                    'hotel' => '庆阳大酒店',
                    'hotel_occupancy' => 0,
                    'hotel_room' => $qyHotelStandardRoom+$qyHotelKingSizeRoom+$qyHotelOtherRoom,
                    'hotel_rate' => 0,
                    ],
                'right_data'=>[
                    'standard'=>[
                        'occupancy' => 0,
                        'room' => $qyHotelStandardRoom,
                        'rate' => 0
                    ],
                    'kingsize'=>[
                        'occupancy' => 0,
                        'room' => $qyHotelKingSizeRoom,
                        'rate' => 0
                    ],
                    'other'=>[
                        'occupancy' => 0,
                        'room' => $qyHotelOtherRoom,
                        'rate' => 0
                    ]
                ]
            ];
        $hotelData[$sxHotelNum] = array_key_exists($sxHotelNum,$hotelData)
            ? [
                'left_data'=>[
                    'hotel' => '时兴饭店',
                    'hotel_occupancy' => array_sum($hotelData[$sxHotelNum]),
                    'hotel_room' => $sxHotelStandardRoom+$sxHotelKingSizeRoom+$sxHotelOtherRoom,
                    'hotel_rate' => round(array_sum($hotelData[$sxHotelNum])/
                        ($sxHotelStandardRoom+$sxHotelKingSizeRoom+$sxHotelOtherRoom) ,3),
                    ],
                'right_data'=>[
                    'standard'=>[
                        'occupancy' => $hotelData[$sxHotelNum]['standard_occupancy'],
                        'room' => $sxHotelStandardRoom,
                        'rate' => $sxHotelStandardRoom ? round($hotelData[$sxHotelNum]['standard_occupancy']/$sxHotelStandardRoom,3):0
                    ],
                    'kingsize'=>[
                        'occupancy' => $hotelData[$sxHotelNum]['kingsize_occupancy'],
                        'room' => $sxHotelKingSizeRoom,
                        'rate' => $sxHotelKingSizeRoom ? round($hotelData[$sxHotelNum]['kingsize_occupancy']/$sxHotelKingSizeRoom,3):0
                    ],
                    'other'=>[
                        'occupancy' => $hotelData[$sxHotelNum]['other_occupancy'],
                        'room' => $sxHotelOtherRoom,
                        'rate' => $sxHotelOtherRoom ? round($hotelData[$sxHotelNum]['other_occupancy']/$sxHotelOtherRoom,3):0
                    ]
                ]
            ]
            : [
                'left_data'=>[
                    'hotel' => '时兴饭店',
                    'hotel_occupancy' =>0,
                    'hotel_room' => $sxHotelStandardRoom+$sxHotelKingSizeRoom+$sxHotelOtherRoom,
                    'hotel_rate' => 0,
                    ],
                'right_data'=>[
                    'standard'=>[
                        'occupancy' => 0,
                        'room' => $sxHotelStandardRoom,
                        'rate' => 0
                    ],
                    'kingsize'=>[
                        'occupancy' => 0,
                        'room' => $sxHotelKingSizeRoom,
                        'rate' => 0
                    ],
                    'other'=>[
                        'occupancy' => 0,
                        'room' => $sxHotelOtherRoom,
                        'rate' => 0
                    ]
                ]
            ];
        $hotelData[$whHotelNum] = array_key_exists($whHotelNum,$hotelData)
            ? [
                'left_data'=>[
                    'hotel' => '万豪宾馆',
                    'hotel_occupancy' => array_sum($hotelData[$whHotelNum]),
                    'hotel_room' => $whHotelStandardRoom+$whHotelKingSizeRoom+$whHotelOtherRoom,
                    'hotel_rate' => round(array_sum($hotelData[$whHotelNum])/
                        ($whHotelStandardRoom+$whHotelKingSizeRoom+$whHotelOtherRoom) ,3),
                    ],
                'right_data'=>[
                    'standard'=>[
                        'occupancy' => $hotelData[$whHotelNum]['standard_occupancy'],
                        'room' => $whHotelStandardRoom,
                        'rate' => $whHotelStandardRoom ? round($hotelData[$whHotelNum]['standard_occupancy']/$whHotelStandardRoom,3):0
                    ],
                    'kingsize'=>[
                        'occupancy' => $hotelData[$whHotelNum]['kingsize_occupancy'],
                        'room' => $whHotelKingSizeRoom,
                        'rate' => $whHotelKingSizeRoom ? round($hotelData[$whHotelNum]['kingsize_occupancy']/$whHotelKingSizeRoom,3):0
                    ],
                    'other'=>[
                        'occupancy' => $hotelData[$whHotelNum]['other_occupancy'],
                        'room' => $whHotelOtherRoom,
                        'rate' => $whHotelOtherRoom ? round($hotelData[$whHotelNum]['other_occupancy']/$whHotelOtherRoom,3):0
                    ]
                ]
            ]
            : [
                'left_data'=>[
                    'hotel' => '万豪宾馆',
                    'hotel_occupancy' => 0,
                    'hotel_room' => $whHotelStandardRoom+$whHotelKingSizeRoom+$whHotelOtherRoom,
                    'hotel_rate' => 0,
                    ],
                'right_data'=>[
                    'standard'=>[
                        'occupancy' => 0,
                        'room' => $whHotelStandardRoom,
                        'rate' => 0
                    ],
                    'kingsize'=>[
                        'occupancy' => 0,
                        'room' => $whHotelKingSizeRoom,
                        'rate' => 0
                    ],
                    'other'=>[
                        'occupancy' => 0,
                        'room' => $whHotelOtherRoom,
                        'rate' => 0
                    ]
                ]
            ];
        $hotelData[$otherHotelNum] = array_key_exists($otherHotelNum,$hotelData)
            ? [
                'left_data'=>[
                    'hotel' => '其他',
                    'hotel_occupancy' => array_sum($hotelData[$otherHotelNum]),
                    'hotel_room' => $OtherHotelStandardRoom+$OtherHotelKingSizeRoom+$OtherHotelOtherRoom,
                    'hotel_rate' => round(array_sum($hotelData[$otherHotelNum])/
                        ($OtherHotelStandardRoom+$OtherHotelKingSizeRoom+$OtherHotelOtherRoom) ,3),
                    ],
                'right_data'=>[
                    'standard'=>[
                        'occupancy' => $hotelData[$otherHotelNum]['standard_occupancy'],
                        'room' => $OtherHotelStandardRoom,
                        'rate' => $OtherHotelStandardRoom ?round($hotelData[$otherHotelNum]['standard_occupancy']/$OtherHotelStandardRoom,3):0
                    ],
                    'kingsize'=>[
                        'occupancy' => $hotelData[$otherHotelNum]['kingsize_occupancy'],
                        'room' => $OtherHotelKingSizeRoom,
                        'rate' => $OtherHotelKingSizeRoom ? round($hotelData[$otherHotelNum]['kingsize_occupancy']/$OtherHotelKingSizeRoom,3):0
                    ],
                    'other'=>[
                        'occupancy' => $hotelData[$otherHotelNum]['other_occupancy'],
                        'room' => $OtherHotelOtherRoom,
                        'rate' => $OtherHotelOtherRoom ? round($hotelData[$otherHotelNum]['other_occupancy']/$OtherHotelOtherRoom,3):0
                    ]
                ]
            ]
            : [
                'left_data'=>[
                    'hotel' => '其他',
                    'hotel_occupancy' => 0,
                    'hotel_room' => $OtherHotelStandardRoom+$OtherHotelKingSizeRoom+$OtherHotelOtherRoom,
                    'hotel_rate' => 0,
                    ],
                'right_data'=>[
                    'standard'=>[
                        'occupancy' => 0,
                        'room' => $OtherHotelStandardRoom,
                        'rate' => 0
                    ],
                    'kingsize'=>[
                        'occupancy' => 0,
                        'room' => $OtherHotelKingSizeRoom,
                        'rate' => 0
                    ],
                    'other'=>[
                        'occupancy' => 0,
                        'room' => $OtherHotelOtherRoom,
                        'rate' => 0
                    ]
                ]
            ];
        return $hotelData;
    }

    public static function getHotelDetailDataByTime($startTime,$endTime)
    {
        $diffTime = strtotime($endTime) - strtotime($startTime);
        $day = $diffTime/86400+1;

        $qtxHotelStandardRoom = Yaf_Registry::get(COMMON)-> qtxHotel->get('standardRoom') *$day;
        $qtxHotelKingSizeRoom = Yaf_Registry::get(COMMON)-> qtxHotel->get('kingSizeRoom') *$day;
        $qtxHotelOtherRoom    = Yaf_Registry::get(COMMON)-> qtxHotel->get('otherRoom') *$day;

        $lhHotelStandardRoom = Yaf_Registry::get(COMMON)-> lhHotel->get('standardRoom') *$day;
        $lhHotelKingSizeRoom = Yaf_Registry::get(COMMON)-> lhHotel->get('kingSizeRoom') *$day;
        $lhHotelOtherRoom  = Yaf_Registry::get(COMMON)-> lhHotel->get('otherRoom') *$day;

        $glhtHotelStandardRoom = Yaf_Registry::get(COMMON)-> glhtHotel->get('standardRoom') *$day;
        $glhtHotelKingSizeRoom = Yaf_Registry::get(COMMON)-> glhtHotel->get('kingSizeRoom') *$day;
        $glhtHotelOtherRoom  = Yaf_Registry::get(COMMON)-> glhtHotel->get('otherRoom') *$day;

        $kpHotelStandardRoom = Yaf_Registry::get(COMMON)-> kpHotel->get('standardRoom') *$day;
        $kpHotelKingSizeRoom = Yaf_Registry::get(COMMON)-> kpHotel->get('kingSizeRoom') *$day;
        $kpHotelOtherRoom  = Yaf_Registry::get(COMMON)-> kpHotel->get('otherRoom') *$day;

        $htHotelStandardRoom = Yaf_Registry::get(COMMON)-> htHotel->get('standardRoom') *$day;
        $htHotelKingSizeRoom = Yaf_Registry::get(COMMON)-> htHotel->get('kingSizeRoom') *$day;
        $htHotelOtherRoom  = Yaf_Registry::get(COMMON)-> htHotel->get('otherRoom') *$day;

        $qyHotelStandardRoom = Yaf_Registry::get(COMMON)-> qyHotel->get('standardRoom') *$day;
        $qyHotelKingSizeRoom = Yaf_Registry::get(COMMON)-> qyHotel->get('kingSizeRoom') *$day;
        $qyHotelOtherRoom  = Yaf_Registry::get(COMMON)-> qyHotel->get('otherRoom') *$day;

        $sxHotelStandardRoom = Yaf_Registry::get(COMMON)-> sxHotel->get('standardRoom') *$day;
        $sxHotelKingSizeRoom = Yaf_Registry::get(COMMON)-> sxHotel->get('kingSizeRoom') *$day;
        $sxHotelOtherRoom  = Yaf_Registry::get(COMMON)-> sxHotel->get('otherRoom') *$day;

        $whHotelStandardRoom = Yaf_Registry::get(COMMON)-> whHotel->get('standardRoom') *$day;
        $whHotelKingSizeRoom = Yaf_Registry::get(COMMON)-> whHotel->get('kingSizeRoom') *$day;
        $whHotelOtherRoom  = Yaf_Registry::get(COMMON)-> whHotel->get('otherRoom') *$day;

        $OtherHotelStandardRoom = Yaf_Registry::get(COMMON)-> OtherHotel->get('standardRoom') *$day;
        $OtherHotelKingSizeRoom = Yaf_Registry::get(COMMON)-> OtherHotel->get('kingSizeRoom') *$day;
        $OtherHotelOtherRoom  = Yaf_Registry::get(COMMON)-> OtherHotel->get('otherRoom') *$day;

        $qtxHotelNum   = Yaf_Registry::get(COMMON)-> get('qtxHotelNum');
        $lhHotelNum    = Yaf_Registry::get(COMMON)-> get('lhHotelNum');
        $glhtHotelNum  = Yaf_Registry::get(COMMON)-> get('glhtHotelNum');
        $kpHotelNum    = Yaf_Registry::get(COMMON)-> get('kpHotelNum');
        $htHotelNum    = Yaf_Registry::get(COMMON)-> get('htHotelNum');
        $qyHotelNum    = Yaf_Registry::get(COMMON)-> get('qyHotelNum');
        $sxHotelNum    = Yaf_Registry::get(COMMON)-> get('sxHotelNum');
        $whHotelNum    = Yaf_Registry::get(COMMON)-> get('whHotelNum');
        $otherHotelNum = Yaf_Registry::get(COMMON)-> get('otherHotelNum');

        $standardRoomType = Yaf_Registry::get(COMMON)-> get('standardRoomType');
        $kingSizeRoomType = Yaf_Registry::get(COMMON)-> get('kingSizeRoomType');
        $otherRoomType    = Yaf_Registry::get(COMMON)-> get('otherRoomType');

        $data = Db::Table(static::$table)
            ->Col(["hotel_id,sum(CASE WHEN room_type='{$standardRoomType}' THEN hotel_occupancy_num ELSE 0 END) as standard_occupancy,
            sum(CASE WHEN room_type='{$kingSizeRoomType}' THEN hotel_occupancy_num ELSE 0 END) as kingsize_occupancy,
            sum(CASE WHEN room_type='{$otherRoomType}' THEN hotel_occupancy_num ELSE 0 END) as other_occupancy"])
            ->Where("date between '{$startTime}' and '{$endTime}'")
            ->GroupBy('hotel_id')
            ->Find();
        foreach ($data as $k => $v)
        {
            $hotelData[$v['hotel_id']] = $v;
            unset($hotelData[$v['hotel_id']]['hotel_id']);
        }

        $hotelData[$qtxHotelNum] = array_key_exists($qtxHotelNum,$hotelData)
            ? [

                'hotel' => '青铜峡宾馆',
                'hotel_occupancy' => array_sum($hotelData[$qtxHotelNum]),
                'hotel_room' => $qtxHotelStandardRoom+$qtxHotelKingSizeRoom+$qtxHotelOtherRoom,
                'hotel_rate' => round(array_sum($hotelData[$qtxHotelNum])/
                    ($qtxHotelStandardRoom+$qtxHotelKingSizeRoom+$qtxHotelOtherRoom) ,3),
                'standard'=>[
                    'occupancy' => $hotelData[$qtxHotelNum]['standard_occupancy'],
                    'room' => $qtxHotelStandardRoom,
                    'rate' => round($hotelData[$qtxHotelNum]['standard_occupancy']/$qtxHotelStandardRoom,3)
                ],
                'kingsize'=>[
                    'occupancy' => $hotelData[$qtxHotelNum]['kingsize_occupancy'],
                    'room' => $qtxHotelKingSizeRoom,
                    'rate' => round($hotelData[$qtxHotelNum]['kingsize_occupancy']/$qtxHotelKingSizeRoom,3)
                ],
                'other'=>[
                    'occupancy' => $hotelData[$qtxHotelNum]['other_occupancy'],
                    'room' => $qtxHotelOtherRoom,
                    'rate' => round($hotelData[$qtxHotelNum]['other_occupancy']/$qtxHotelOtherRoom,3)
                ]
            ]
            : [
                'hotel' => '青铜峡宾馆',
                'hotel_occupancy' => 0,
                'hotel_room' => $qtxHotelStandardRoom+$qtxHotelKingSizeRoom+$qtxHotelOtherRoom,
                'hotel_rate' => 0,
                'standard'=>[
                    'occupancy' => 0,
                    'room' => $qtxHotelStandardRoom,
                    'rate' => 0
                ],
                'kingsize'=>[
                    'occupancy' => 0,
                    'room' => $qtxHotelKingSizeRoom,
                    'rate' => 0
                ],
                'other'=>[
                    'occupancy' => 0,
                    'room' => $qtxHotelOtherRoom,
                    'rate' => 0
                ]
            ];

        $hotelData[$lhHotelNum] = array_key_exists($lhHotelNum,$hotelData)
            ? [
                'hotel' => '龙海宾馆',
                'hotel_occupancy' => array_sum($hotelData[$lhHotelNum]),
                'hotel_room' => $lhHotelStandardRoom+$lhHotelKingSizeRoom+$lhHotelOtherRoom,
                'hotel_rate' => round(array_sum($hotelData[$lhHotelNum])/
                    ($lhHotelStandardRoom+$lhHotelKingSizeRoom+$lhHotelOtherRoom) ,3),
                'standard'=>[
                    'occupancy' => $hotelData[$lhHotelNum]['standard_occupancy'],
                    'room' => $lhHotelStandardRoom,
                    'rate' => $lhHotelStandardRoom ? round($hotelData[$lhHotelNum]['standard_occupancy']/$lhHotelStandardRoom,3):0
                ],
                'kingsize'=>[
                    'occupancy' => $hotelData[$lhHotelNum]['kingsize_occupancy'],
                    'room' => $lhHotelKingSizeRoom,
                    'rate' => $lhHotelKingSizeRoom ? round($hotelData[$lhHotelNum]['kingsize_occupancy']/$lhHotelKingSizeRoom,3):0
                ],
                'other'=>[
                    'occupancy' => $hotelData[$lhHotelNum]['other_occupancy'],
                    'room' => $lhHotelOtherRoom,
                    'rate' => $lhHotelOtherRoom ? round($hotelData[$lhHotelNum]['other_occupancy']/$lhHotelOtherRoom,3) :0
                ]
            ]
            : [
                'hotel' => '龙海宾馆',
                'hotel_occupancy' => 0,
                'hotel_room' => $lhHotelStandardRoom+$lhHotelKingSizeRoom+$lhHotelOtherRoom,
                'hotel_rate' => 0,
                'standard'=>[
                    'occupancy' => 0,
                    'room' => $lhHotelStandardRoom,
                    'rate' => 0
                ],
                'kingsize'=>[
                    'occupancy' => 0,
                    'room' => $lhHotelKingSizeRoom,
                    'rate' => 0
                ],
                'other'=>[
                    'occupancy' => 0,
                    'room' => $lhHotelOtherRoom,
                    'rate' => 0
                ]
            ];
        $hotelData[$glhtHotelNum] = array_key_exists($glhtHotelNum,$hotelData)
            ? [
                'hotel' => '格林豪泰',
                'hotel_occupancy' => array_sum($hotelData[$glhtHotelNum]),
                'hotel_room' => $glhtHotelStandardRoom+$glhtHotelKingSizeRoom+$glhtHotelOtherRoom,
                'hotel_rate' => round(array_sum($hotelData[$glhtHotelNum])/
                    ($glhtHotelStandardRoom+$glhtHotelKingSizeRoom+$glhtHotelOtherRoom) ,3),
                'standard'=>[
                    'occupancy' => $hotelData[$glhtHotelNum]['standard_occupancy'],
                    'room' => $glhtHotelStandardRoom,
                    'rate' => $glhtHotelStandardRoom ? round($hotelData[$glhtHotelNum]['standard_occupancy']/$glhtHotelStandardRoom,3):0
                ],
                'kingsize'=>[
                    'occupancy' => $hotelData[$glhtHotelNum]['kingsize_occupancy'],
                    'room' => $glhtHotelKingSizeRoom,
                    'rate' => $glhtHotelKingSizeRoom ? round($hotelData[$glhtHotelNum]['kingsize_occupancy']/$glhtHotelKingSizeRoom,3):0
                ],
                'other'=>[
                    'occupancy' => $hotelData[$glhtHotelNum]['other_occupancy'],
                    'room' => $glhtHotelOtherRoom,
                    'rate' => $glhtHotelOtherRoom ? round($hotelData[$glhtHotelNum]['other_occupancy']/$glhtHotelOtherRoom,3):0
                ]
            ]
            : [
                'hotel' => '格林豪泰',
                'hotel_occupancy' => 0,
                'hotel_room' => $glhtHotelStandardRoom+$glhtHotelKingSizeRoom+$glhtHotelOtherRoom,
                'hotel_rate' => 0,
                'standard'=>[
                    'occupancy' => 0,
                    'room' => $glhtHotelStandardRoom,
                    'rate' => 0
                ],
                'kingsize'=>[
                    'occupancy' => 0,
                    'room' => $glhtHotelKingSizeRoom,
                    'rate' => 0
                ],
                'other'=>[
                    'occupancy' => 0,
                    'room' => $glhtHotelOtherRoom,
                    'rate' => 0
                ]
            ];
        $hotelData[$kpHotelNum] = array_key_exists($kpHotelNum,$hotelData)
            ? [
                'hotel' => '凯鹏宾馆',
                'hotel_occupancy' => array_sum($hotelData[$kpHotelNum]),
                'hotel_room' => $kpHotelStandardRoom+$kpHotelKingSizeRoom+$kpHotelOtherRoom,
                'hotel_rate' => round(array_sum($hotelData[$kpHotelNum])/
                    ($kpHotelStandardRoom+$kpHotelKingSizeRoom+$kpHotelOtherRoom) ,3),
                'standard'=>[
                    'occupancy' => $hotelData[$kpHotelNum]['standard_occupancy'],
                    'room' => $kpHotelStandardRoom,
                    'rate' => $kpHotelStandardRoom ? round($hotelData[$kpHotelNum]['standard_occupancy']/$kpHotelStandardRoom,3):0
                ],
                'kingsize'=>[
                    'occupancy' => $hotelData[$kpHotelNum]['kingsize_occupancy'],
                    'room' => $kpHotelKingSizeRoom,
                    'rate' => $kpHotelKingSizeRoom ? round($hotelData[$kpHotelNum]['kingsize_occupancy']/$kpHotelKingSizeRoom,3):0
                ],
                'other'=>[
                    'occupancy' => $hotelData[$kpHotelNum]['other_occupancy'],
                    'room' => $kpHotelOtherRoom,
                    'rate' => $kpHotelOtherRoom ? round($hotelData[$kpHotelNum]['other_occupancy']/$kpHotelOtherRoom,3):0
                ]
            ]
            : [
                'hotel' => '凯鹏宾馆',
                'hotel_occupancy' => 0,
                'hotel_room' => $kpHotelStandardRoom+$kpHotelKingSizeRoom+$kpHotelOtherRoom,
                'hotel_rate' => 0,
                'standard'=>[
                    'occupancy' => 0,
                    'room' => $kpHotelStandardRoom,
                    'rate' => 0
                ],
                'kingsize'=>[
                    'occupancy' => 0,
                    'room' => $kpHotelStandardRoom,
                    'rate' => 0
                ],
                'other'=>[
                    'occupancy' => 0,
                    'room' => $kpHotelOtherRoom,
                    'rate' => 0
                ]
            ];
        $hotelData[$htHotelNum] = array_key_exists($htHotelNum,$hotelData)
            ? [
                'hotel' => '豪泰大酒店',
                'hotel_occupancy' => array_sum($hotelData[$htHotelNum]),
                'hotel_room' => $htHotelStandardRoom+$htHotelKingSizeRoom+$htHotelOtherRoom,
                'hotel_rate' => round(array_sum($hotelData[$htHotelNum])/
                    ($htHotelStandardRoom+$htHotelKingSizeRoom+$htHotelOtherRoom) ,3),
                'standard'=>[
                    'occupancy' => $hotelData[$htHotelNum]['standard_occupancy'],
                    'room' => $htHotelStandardRoom,
                    'rate' => $htHotelStandardRoom ? round($hotelData[$htHotelNum]['standard_occupancy']/$htHotelStandardRoom,3):0
                ],
                'kingsize'=>[
                    'occupancy' => $hotelData[$htHotelNum]['kingsize_occupancy'],
                    'room' => $htHotelKingSizeRoom,
                    'rate' => $htHotelKingSizeRoom ? round($hotelData[$htHotelNum]['kingsize_occupancy']/$htHotelKingSizeRoom,3):0
                ],
                'other'=>[
                    'occupancy' => $hotelData[$htHotelNum]['other_occupancy'],
                    'room' => $htHotelOtherRoom,
                    'rate' => $htHotelOtherRoom ? round($hotelData[$htHotelNum]['other_occupancy']/$htHotelOtherRoom,3):0
                ]
            ]
            : [
                'hotel' => '豪泰大酒店',
                'hotel_occupancy' => 0,
                'hotel_room' => $htHotelStandardRoom+$htHotelKingSizeRoom+$htHotelOtherRoom,
                'hotel_rate' => 0,
                'standard'=>[
                    'occupancy' => 0,
                    'room' => $htHotelStandardRoom,
                    'rate' => 0
                ],
                'kingsize'=>[
                    'occupancy' => 0,
                    'room' => $htHotelKingSizeRoom,
                    'rate' => 0
                ],
                'other'=>[
                    'occupancy' => 0,
                    'room' => $htHotelOtherRoom,
                    'rate' => 0
                ]
            ];
        $hotelData[$qyHotelNum] = array_key_exists($qyHotelNum,$hotelData)
            ? [
                'hotel' => '庆阳大酒店',
                'hotel_occupancy' => array_sum($hotelData[$qyHotelNum]),
                'hotel_room' => $qyHotelStandardRoom+$qyHotelKingSizeRoom+$qyHotelOtherRoom,
                'hotel_rate' => round(array_sum($hotelData[$qyHotelNum])/
                    ($qyHotelStandardRoom+$qyHotelKingSizeRoom+$qyHotelOtherRoom) ,3),
                'standard'=>[
                    'occupancy' => $hotelData[$qyHotelNum]['standard_occupancy'],
                    'room' => $qyHotelStandardRoom,
                    'rate' => $qyHotelStandardRoom ? round($hotelData[$qyHotelNum]['standard_occupancy']/$qyHotelStandardRoom,3):0
                ],
                'kingsize'=>[
                    'occupancy' => $hotelData[$qyHotelNum]['kingsize_occupancy'],
                    'room' => $qyHotelKingSizeRoom,
                    'rate' => $qyHotelKingSizeRoom ? round($hotelData[$qyHotelNum]['kingsize_occupancy']/$qyHotelKingSizeRoom,3):0
                ],
                'other'=>[
                    'occupancy' => $hotelData[$qyHotelNum]['other_occupancy'],
                    'room' => $qyHotelOtherRoom,
                    'rate' => $qyHotelOtherRoom ? round($hotelData[$qyHotelNum]['other_occupancy']/$qyHotelOtherRoom,3):0
                ]
            ]
            : [
                'hotel' => '庆阳大酒店',
                'hotel_occupancy' => 0,
                'hotel_room' => $qyHotelStandardRoom+$qyHotelKingSizeRoom+$qyHotelOtherRoom,
                'hotel_rate' => 0,
                'standard'=>[
                    'occupancy' => 0,
                    'room' => $qyHotelStandardRoom,
                    'rate' => 0
                ],
                'kingsize'=>[
                    'occupancy' => 0,
                    'room' => $qyHotelKingSizeRoom,
                    'rate' => 0
                ],
                'other'=>[
                    'occupancy' => 0,
                    'room' => $qyHotelOtherRoom,
                    'rate' => 0
                ]
            ];
        $hotelData[$sxHotelNum] = array_key_exists($sxHotelNum,$hotelData)
            ? [
                'hotel' => '时兴饭店',
                'hotel_occupancy' => array_sum($hotelData[$sxHotelNum]),
                'hotel_room' => $sxHotelStandardRoom+$sxHotelKingSizeRoom+$sxHotelOtherRoom,
                'hotel_rate' => round(array_sum($hotelData[$sxHotelNum])/
                    ($sxHotelStandardRoom+$sxHotelKingSizeRoom+$sxHotelOtherRoom) ,3),
                'standard'=>[
                    'occupancy' => $hotelData[$sxHotelNum]['standard_occupancy'],
                    'room' => $sxHotelStandardRoom,
                    'rate' => $sxHotelStandardRoom ?round($hotelData[$sxHotelNum]['standard_occupancy']/$sxHotelStandardRoom,3):0
                ],
                'kingsize'=>[
                    'occupancy' => $hotelData[$sxHotelNum]['kingsize_occupancy'],
                    'room' => $sxHotelKingSizeRoom,
                    'rate' => $sxHotelKingSizeRoom ? round($hotelData[$sxHotelNum]['kingsize_occupancy']/$sxHotelKingSizeRoom,3):0
                ],
                'other'=>[
                    'occupancy' => $hotelData[$sxHotelNum]['other_occupancy'],
                    'room' => $sxHotelOtherRoom,
                    'rate' => $sxHotelOtherRoom ? round($hotelData[$sxHotelNum]['other_occupancy']/$sxHotelOtherRoom,3):0
                ]
            ]
            : [
                'hotel' => '时兴饭店',
                'hotel_occupancy' =>0,
                'hotel_room' => $sxHotelStandardRoom+$sxHotelKingSizeRoom+$sxHotelOtherRoom,
                'hotel_rate' => 0,
                'standard'=>[
                    'occupancy' => 0,
                    'room' => $sxHotelStandardRoom,
                    'rate' => 0
                ],
                'kingsize'=>[
                    'occupancy' => 0,
                    'room' => $sxHotelKingSizeRoom,
                    'rate' => 0
                ],
                'other'=>[
                    'occupancy' => 0,
                    'room' => $sxHotelOtherRoom,
                    'rate' => 0
                ]
            ];
        $hotelData[$whHotelNum] = array_key_exists($whHotelNum,$hotelData)
            ? [
                'hotel' => '万豪宾馆',
                'hotel_occupancy' => array_sum($hotelData[$whHotelNum]),
                'hotel_room' => $whHotelStandardRoom+$whHotelKingSizeRoom+$whHotelOtherRoom,
                'hotel_rate' => round(array_sum($hotelData[$whHotelNum])/
                    ($whHotelStandardRoom+$whHotelKingSizeRoom+$whHotelOtherRoom) ,3),
                'standard'=>[
                    'occupancy' => $hotelData[$whHotelNum]['standard_occupancy'],
                    'room' => $whHotelStandardRoom,
                    'rate' => $whHotelStandardRoom ? round($hotelData[$whHotelNum]['standard_occupancy']/$whHotelStandardRoom,3):0
                ],
                'kingsize'=>[
                    'occupancy' => $hotelData[$whHotelNum]['kingsize_occupancy'],
                    'room' => $whHotelKingSizeRoom,
                    'rate' => $whHotelKingSizeRoom ? round($hotelData[$whHotelNum]['kingsize_occupancy']/$whHotelKingSizeRoom,3):0
                ],
                'other'=>[
                    'occupancy' => $hotelData[$whHotelNum]['other_occupancy'],
                    'room' => $whHotelOtherRoom,
                    'rate' => $whHotelOtherRoom ? round($hotelData[$whHotelNum]['other_occupancy']/$whHotelOtherRoom,3):0
                ]
            ]
            : [
                'hotel' => '万豪宾馆',
                'hotel_occupancy' => 0,
                'hotel_room' => $whHotelStandardRoom+$whHotelKingSizeRoom+$whHotelOtherRoom,
                'hotel_rate' => 0,
                'standard'=>[
                    'occupancy' => 0,
                    'room' => $whHotelStandardRoom,
                    'rate' => 0
                ],
                'kingsize'=>[
                    'occupancy' => 0,
                    'room' => $whHotelKingSizeRoom,
                    'rate' => 0
                ],
                'other'=>[
                    'occupancy' => 0,
                    'room' => $whHotelOtherRoom,
                    'rate' => 0
                ]
            ];
        $hotelData[$otherHotelNum] = array_key_exists($otherHotelNum,$hotelData)
            ? [
                'hotel' => '其他',
                'hotel_occupancy' => array_sum($hotelData[$otherHotelNum]),
                'hotel_room' => $OtherHotelStandardRoom+$OtherHotelKingSizeRoom+$OtherHotelOtherRoom,
                'hotel_rate' => round(array_sum($hotelData[$otherHotelNum])/
                    ($OtherHotelStandardRoom+$OtherHotelKingSizeRoom+$OtherHotelOtherRoom) ,3),
                'standard'=>[
                    'occupancy' => $hotelData[$otherHotelNum]['standard_occupancy'],
                    'room' => $OtherHotelStandardRoom,
                    'rate' => $OtherHotelStandardRoom ? round($hotelData[$otherHotelNum]['standard_occupancy']/$OtherHotelStandardRoom,3) :0
                ],
                'kingsize'=>[
                    'occupancy' => $hotelData[$otherHotelNum]['kingsize_occupancy'],
                    'room' => $OtherHotelKingSizeRoom,
                    'rate' => $OtherHotelKingSizeRoom ? round($hotelData[$otherHotelNum]['kingsize_occupancy']/$OtherHotelKingSizeRoom,3):0
                ],
                'other'=>[
                    'occupancy' => $hotelData[$otherHotelNum]['other_occupancy'],
                    'room' => $OtherHotelOtherRoom,
                    'rate' => $OtherHotelOtherRoom ? round($hotelData[$otherHotelNum]['other_occupancy']/$OtherHotelOtherRoom,3):0
                ]
            ]
            : [
                'hotel' => '其他',
                'hotel_occupancy' => 0,
                'hotel_room' => $OtherHotelStandardRoom+$OtherHotelKingSizeRoom+$OtherHotelOtherRoom,
                'hotel_rate' => 0,
                'standard'=>[
                    'occupancy' => 0,
                    'room' => $OtherHotelStandardRoom,
                    'rate' => 0
                ],
                'kingsize'=>[
                    'occupancy' => 0,
                    'room' => $OtherHotelKingSizeRoom,
                    'rate' => 0
                ],
                'other'=>[
                    'occupancy' => 0,
                    'room' => $OtherHotelOtherRoom,
                    'rate' => 0
                ]
            ];
        foreach ($hotelData as $k=>$v){
            $newHotelData[$k]['left_data'] = [
                'hotel'=>$v['hotel'],'hotel_occupancy'=>$v['hotel_occupancy'],
                'hotel_room'=>$v['hotel_room'],'hotel_rate'=>$v['hotel_rate']
            ];
            $newHotelData[$k]['right_data'] = [
                'standard'=>$v['standard'],'kingsize'=>$v['kingsize'],
                'other'=>$v['other']
            ];
        }
        return $newHotelData;
    }

    public static function getRateSevenDay()
    {
        $qtxHotelStandardRoom = Yaf_Registry::get(COMMON)-> qtxHotel->get('standardRoom');
        $qtxHotelKingSizeRoom = Yaf_Registry::get(COMMON)-> qtxHotel->get('kingSizeRoom');
        $qtxHotelOtherRoom    = Yaf_Registry::get(COMMON)-> qtxHotel->get('otherRoom');

        $lhHotelStandardRoom = Yaf_Registry::get(COMMON)-> lhHotel->get('standardRoom');
        $lhHotelKingSizeRoom = Yaf_Registry::get(COMMON)-> lhHotel->get('kingSizeRoom');
        $lhHotelOtherRoom  = Yaf_Registry::get(COMMON)-> lhHotel->get('otherRoom');

        $glhtHotelStandardRoom = Yaf_Registry::get(COMMON)-> glhtHotel->get('standardRoom');
        $glhtHotelKingSizeRoom = Yaf_Registry::get(COMMON)-> glhtHotel->get('kingSizeRoom');
        $glhtHotelOtherRoom  = Yaf_Registry::get(COMMON)-> glhtHotel->get('otherRoom');

        $kpHotelStandardRoom = Yaf_Registry::get(COMMON)-> kpHotel->get('standardRoom');
        $kpHotelKingSizeRoom = Yaf_Registry::get(COMMON)-> kpHotel->get('kingSizeRoom');
        $kpHotelOtherRoom  = Yaf_Registry::get(COMMON)-> kpHotel->get('otherRoom');

        $htHotelStandardRoom = Yaf_Registry::get(COMMON)-> htHotel->get('standardRoom');
        $htHotelKingSizeRoom = Yaf_Registry::get(COMMON)-> htHotel->get('kingSizeRoom');
        $htHotelOtherRoom  = Yaf_Registry::get(COMMON)-> htHotel->get('otherRoom');

        $qyHotelStandardRoom = Yaf_Registry::get(COMMON)-> qyHotel->get('standardRoom');
        $qyHotelKingSizeRoom = Yaf_Registry::get(COMMON)-> qyHotel->get('kingSizeRoom');
        $qyHotelOtherRoom  = Yaf_Registry::get(COMMON)-> qyHotel->get('otherRoom');

        $sxHotelStandardRoom = Yaf_Registry::get(COMMON)-> sxHotel->get('standardRoom');
        $sxHotelKingSizeRoom = Yaf_Registry::get(COMMON)-> sxHotel->get('kingSizeRoom');
        $sxHotelOtherRoom  = Yaf_Registry::get(COMMON)-> sxHotel->get('otherRoom');

        $whHotelStandardRoom = Yaf_Registry::get(COMMON)-> whHotel->get('standardRoom');
        $whHotelKingSizeRoom = Yaf_Registry::get(COMMON)-> whHotel->get('kingSizeRoom');
        $whHotelOtherRoom  = Yaf_Registry::get(COMMON)-> whHotel->get('otherRoom');

        $OtherHotelStandardRoom = Yaf_Registry::get(COMMON)-> OtherHotel->get('standardRoom');
        $OtherHotelKingSizeRoom = Yaf_Registry::get(COMMON)-> OtherHotel->get('kingSizeRoom');
        $OtherHotelOtherRoom  = Yaf_Registry::get(COMMON)-> OtherHotel->get('otherRoom');

        $total_rome = $qtxHotelStandardRoom+$qtxHotelKingSizeRoom+$qtxHotelOtherRoom
            + $lhHotelStandardRoom+$lhHotelKingSizeRoom+$lhHotelOtherRoom
            + $OtherHotelStandardRoom+$OtherHotelKingSizeRoom+$OtherHotelOtherRoom
            +$glhtHotelStandardRoom+$glhtHotelKingSizeRoom+$glhtHotelOtherRoom
            +$kpHotelStandardRoom+$kpHotelKingSizeRoom+$kpHotelOtherRoom
            +$htHotelStandardRoom+$htHotelKingSizeRoom+$htHotelOtherRoom
            +$qyHotelStandardRoom+$qyHotelKingSizeRoom+$qyHotelOtherRoom
            +$sxHotelStandardRoom+$sxHotelKingSizeRoom+$sxHotelOtherRoom
            +$whHotelStandardRoom+$whHotelKingSizeRoom+$whHotelOtherRoom;

        $date = date('Y-m-d');
        $beforeOneDay = date('Y-m-d',(strtotime($date)-1*86400));
        $beforeTwoDay = date('Y-m-d',(strtotime($date)-2*86400));
        $beforeThreeDay = date('Y-m-d',(strtotime($date)-3*86400));
        $beforeFourDay = date('Y-m-d',(strtotime($date)-4*86400));
        $beforeFiveDay = date('Y-m-d',(strtotime($date)-5*86400));
        $beforeSixDay = date('Y-m-d',(strtotime($date)-6*86400));


        $data = Db::Table(self::$table)
            ->Col(['date,sum(hotel_occupancy_num) as occupancy'])
            ->Where("date between '$beforeSixDay' and '{$date}'")
            ->GroupBy('date')
            ->Find();
        foreach ($data as $k =>$v)
        {
            $rateData[$v['date']] = $v;
        }
        $rateData[$beforeSixDay] = array_key_exists($beforeSixDay,$rateData) ?
            [
                'occupancy'=>$rateData[$beforeSixDay]['occupancy'],
                'rate'=> round($rateData[$beforeSixDay]['occupancy']/$total_rome,3)
            ]
            :[
                'occupancy'=>0,
                'rate'=>0
            ];
        $rateData[$beforeFiveDay] = array_key_exists($beforeFiveDay,$rateData) ?
            [
                'occupancy'=>$rateData[$beforeFiveDay]['occupancy'],
                'rate'=> round($rateData[$beforeFiveDay]['occupancy']/$total_rome,3)
            ]
            :[
                'occupancy'=>0,
                'rate'=>0
            ];
        $rateData[$beforeFourDay] = array_key_exists($beforeFourDay,$rateData) ?
            [
                'occupancy'=>$rateData[$beforeFourDay]['occupancy'],
                'rate'=> round($rateData[$beforeFourDay]['occupancy']/$total_rome,3)
            ]
            :[
                'occupancy'=>0,
                'rate'=>0
            ];
        $rateData[$beforeThreeDay] = array_key_exists($beforeThreeDay,$rateData) ?
            [
                'occupancy'=>$rateData[$beforeThreeDay]['occupancy'],
                'rate'=> round($rateData[$beforeThreeDay]['occupancy']/$total_rome,3)
            ]
            :[
                'occupancy'=>0,
                'rate'=>0
            ];
        $rateData[$beforeTwoDay] = array_key_exists($beforeTwoDay,$rateData) ?
            [
                'occupancy'=>$rateData[$beforeTwoDay]['occupancy'],
                'rate'=> round($rateData[$beforeTwoDay]['occupancy']/$total_rome,3)
            ]
            :[
                'occupancy'=>0,
                'rate'=>0
            ];
        $rateData[$beforeOneDay] = array_key_exists($beforeOneDay,$rateData) ?
            [
                'occupancy'=>$rateData[$beforeOneDay]['occupancy'],
                'rate'=> round($rateData[$beforeOneDay]['occupancy']/$total_rome,3)
            ]
            :[
                'occupancy'=>0,
                'rate'=>0
            ];
        $rateData[$date] = array_key_exists($date,$rateData) ?
            [
                'occupancy'=>$rateData[$date]['occupancy'],
                'rate'=> round($rateData[$date]['occupancy']/$total_rome,3)
            ]
            :[
                'occupancy'=>0,
                'rate'=>0
            ];
        ksort($rateData);
        return $rateData;
    }
}
