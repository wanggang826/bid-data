<?php
/**
 * By yubin at 2019/1/11 12:35 PM.
 */

class Date
{

    /**
     * 获取昨天对应的 年 月 日
     * @param int $year
     * @param int $month
     * @param int $day
     * @return bool|array
     */
    public static function GetYesterdayNum(int $year, int $month, int $day)
    {
        $timestamp = strtotime("{$year}-{$month}-{$day}");
        if( !$timestamp )
        {
            return false;
        }
        $newTimeStamp = date('Y-m-d',$timestamp-86400);
        $dateArray    = explode('-', $newTimeStamp);
        return [
            'year'  => (int)$dateArray[0],
            'month' => (int)$dateArray[1],
            'day'   => (int)$dateArray[2],
        ];
    }

    /**
     * 获取上月对应的 年 和 月
     * @param int $year
     * @param int $month
     * @return array
     */
    public static function GetLastMonthNum(int $year, int $month)
    {
        if( $month==1 )
        {
            return [
                'year'  => $year-1,
                'month' => 12
            ];
        }
        return [
            'year'  => $year,
            'month' => $month-1
        ];
    }


    /**
     * 获取当前年二月有多少天
     * @param int $year
     * @return int
     */
    public static function FebruaryDays(int $year) : int
    {
        if( $year%400 == 0 || ($year%4==0 && $year%100!= 0) )
        {
            return 29;
        }
        return 28;
    }

}