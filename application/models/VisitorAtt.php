<?php
/**
 * User: wgg
 * Date: 19-4-12
 * Time: 下午2:43
 */
class VisitorAttModel
{
    private static $table = 'visitor_att';

    public static function Insert($data)
    {
        $result = Db::Table(self::$table)->Insert($data);
        if ($result['result']) {
            return $result['insertId'];
        }
        return false;
    }

    public static function InsertBySql($sqlValues)
    {
        $sql = "insert into  `visitor_att`(
          `date`,`man_count`,`women_count`,`age1_count`,`age2_count`,
          `age3_count`,`age4_count`,`age5_count`,`age6_count`,`age7_count`
          ) values ".$sqlValues;
        $result = Db::Table(self::$table)->executeSql($sql);
        if ($result['result']) {
            return $result['insertId'];
        }
        return false;
    }
    public static function getVisitorAttData()
    {
        $visitorCount = Db::Table(self::$table)
            ->Col(['sum(man_count) as man_count,sum(women_count) as women_count'])
            ->find();
        $manCount   = $visitorCount[0]['man_count'];
        $womenCount = $visitorCount[0]['women_count'];
        $totalCount = $manCount + $womenCount;
        $data = [
            'manRate' => $totalCount ? round($manCount/$totalCount,3) : 0,
            'womenRate' => $totalCount ? round($womenCount/$totalCount,3) : 0
        ];

        $beforeTwoYear = date('Y',strtotime("-2 year"));
        $beforeOneYear = date('Y',strtotime("-1 year"));
        $nowYear = date('Y');
        $ageData = Db::Table(static::$table)
            ->Col([
                'year(date) as year,
                sum(age1_count) as age1,
                sum(age2_count) as age2,
                sum(age3_count) as age3,
                sum(age4_count) as age4,
                sum(age5_count) as age5,
                sum(age6_count) as age6,
                sum(age7_count) as age7'
            ])
            ->GroupBy('year')
            ->Find();
        foreach ($ageData as $k=>$v){
            $value = $v;
            unset($value['year']);
            $ageData[$v['year']] =  array_sum($value) ? [
                'age1_rate'=>round($value['age1']/array_sum($value),3),
                'age2_rate'=>round($value['age2']/array_sum($value),3),
                'age3_rate'=>round($value['age3']/array_sum($value),3),
                'age4_rate'=>round($value['age4']/array_sum($value),3),
                'age5_rate'=>round($value['age5']/array_sum($value),3),
                'age6_rate'=>round($value['age6']/array_sum($value),3),
                'age7_rate'=>round($value['age7']/array_sum($value),3)
            ] : [];
            unset($ageData[$k]);
        }

        $data[$beforeTwoYear] = $ageData[$beforeTwoYear] ? :[];
        $data[$beforeOneYear] = $ageData[$beforeOneYear] ? :[];
        $data[$nowYear] = $ageData[$nowYear]? :[];
        return $data;
    }
}