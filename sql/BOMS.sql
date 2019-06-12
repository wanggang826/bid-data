create database if not exists `qingtongxia` default charset utf8mb4 collate utf8mb4_bin;
use qingtongxia;

    #客流数据表-接待人数
    CREATE TABLE IF NOT EXISTS `reception_num` (
        `id` bigint(20) PRIMARY KEY AUTO_INCREMENT COMMENT '自增id',
        `date` date not null COMMENT '日期，如2019-04-20',
        `scenic_spot_id` tinyint(3) not null COMMENT '景区id 1:黄河大峡谷，2：黄河楼，3：黄河坛,4:黄河生态园',
        `num` int(11) DEFAULT 0 COMMENT '接待人数',
        `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
        `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间'
    )ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 collate utf8mb4_bin COMMENT='客流数据表-接待人数';

    #行业数据表-酒店入住率
    CREATE TABLE IF NOT EXISTS `hotel_occupancy` (
        `id` bigint(20) PRIMARY KEY AUTO_INCREMENT COMMENT '自增id',
        `date` date not null COMMENT '日期，如2019-04-20',
        `hotel_room_num` int(11) default 0 COMMENT '酒店房间数',
        `hotel_occupancy_num` int(11) DEFAULT 0 COMMENT '酒店入住人数',
        `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
        `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间'
    )ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 collate utf8mb4_bin COMMENT='行业数据表-酒店入住率';

    #修改酒店入住率表 增加字段
    ALTER TABLE `hotel_occupancy`
    ADD `hotel_id` int(11) DEFAULT 0 COMMENT '酒店编号',
    ADD `room_type` int(11) DEFAULT 0 COMMENT '房型编号';


    #行业数据表-游客属性
    CREATE TABLE IF NOT EXISTS `visitor_att` (
        `id` bigint(20) PRIMARY KEY AUTO_INCREMENT COMMENT '自增id',
        `date` date not null COMMENT '日期，如2019-04-20',
        `man_count` int(11) default 0 COMMENT '男性人数',
        `women_count` int(11) default 0 COMMENT '女性人数',
        `age1_count`  int(11) default 0 COMMENT '18岁以下 人数',
        `age2_count`  int(11) default 0 COMMENT '18-25岁 人数',
        `age3_count`  int(11) default 0 COMMENT '25-35岁 人数',
        `age4_count`  int(11) default 0 COMMENT '35-45岁 人数',
        `age5_count`  int(11) default 0 COMMENT '45-55岁 人数',
        `age6_count`  int(11) default 0 COMMENT '55-60岁 人数',
        `age7_count`  int(11) default 0 COMMENT '60岁以上 人数',
        `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
        `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间'
    )ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 collate utf8mb4_bin COMMENT='行业数据表-游客属性';

    #行业数据表-游客来源
    CREATE TABLE IF NOT EXISTS `visitor_from`(
      `id` bigint (20) PRIMARY KEY AUTO_INCREMENT COMMENT '自增id',
      `shanxi_visitor_count`int(11) default 0 COMMENT '来自陕西游客数',
      `beijing_visitor_count`int(11) default 0 COMMENT '来自北京游客数',
      `tianjin_visitor_count`int(11) default 0 COMMENT '来自天津游客数',
      `hebei_visitor_count`int(11) default 0 COMMENT '来自河北游客数',
      `sanxi_visitor_count`int(11) default 0 COMMENT '来自山西游客数',
      `neimeng_visitor_count`int(11) default 0 COMMENT '来自内蒙游客数',
      `liaoning_visitor_count`int(11) default 0 COMMENT '来自辽宁游客数',
      `jilin_visitor_count`int(11) default 0 COMMENT '来自吉林游客数',
      `heilongjiang_visitor_count`int(11) default 0 COMMENT '来自黑龙江游客数',
      `shanghai_visitor_count`int(11) default 0 COMMENT '来自上海游客数',
      `jiangsu_visitor_count`int(11) default 0 COMMENT '来自江苏游客数',
      `zhejiang_visitor_count`int(11) default 0 COMMENT '来自浙江游客数',
      `anhui_visitor_count`int(11) default 0 COMMENT '来自安徽游客数',
      `fujian_visitor_count`int(11) default 0 COMMENT '来自福建游客数',
      `jiangxi_visitor_count`int(11) default 0 COMMENT '来自江西游客数',
      `shandong_visitor_count`int(11) default 0 COMMENT '来自山东游客数',
      `henan_visitor_count`int(11) default 0 COMMENT '来自河南游客数',
      `hubei_visitor_count`int(11) default 0 COMMENT '来自河北游客数',
      `hunan_visitor_count`int(11) default 0 COMMENT '来自湖南游客数',
      `guangdong_visitor_count`int(11) default 0 COMMENT '来自广东游客数',
      `guangxi_visitor_count`int(11) default 0 COMMENT '来自广西游客数',
      `hainan_visitor_count`int(11) default 0 COMMENT '来自海南游客数',
      `chongqing_visitor_count`int(11) default 0 COMMENT '来自重庆游客数',
      `sichuan_visitor_count`int(11) default 0 COMMENT '来自四川游客数',
      `guizhou_visitor_count`int(11) default 0 COMMENT '来自贵州游客数',
      `yunnan_visitor_count`int(11) default 0 COMMENT '来自云南游客数',
      `xizang_visitor_count`int(11) default 0 COMMENT '来自西藏游客数',
      `gansu_visitor_count`int(11) default 0 COMMENT '来自甘肃游客数',
      `qinghai_visitor_count`int(11) default 0 COMMENT '来自青海游客数',
      `ningxia_visitor_count`int(11) default 0 COMMENT '来自宁夏游客数',
      `xinjiang_visitor_count`int(11) default 0 COMMENT '来自新疆游客数',
      `xianggang_visitor_count`int(11) default 0 COMMENT '来自香港游客数',
      `taiwan_visitor_count`int(11) default 0 COMMENT '来自台湾游客数',
      `aomen_visitor_count`int(11) default 0 COMMENT '来自澳门游客数',
      `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
      `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间'
    )ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 collate utf8mb4_bin COMMENT='营销数据－游客来源';


    #营销数据-旅游收入
    CREATE TABLE IF NOT EXISTS `tourism_income`(
      `id` bigint (20) PRIMARY KEY AUTO_INCREMENT COMMENT '自增id',
      `date` date not null COMMENT '日期，如2019-4-20',
      `income` decimal(11,2) default 0 COMMENT '收入金额',
      `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
      `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间'
    )ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 collate utf8mb4_bin COMMENT='营销数据-旅游收入';

    #营销数据-团散门票
    CREATE TABLE IF NOT EXISTS `group_scattered_ticket`(
      `id` bigint (20) PRIMARY KEY AUTO_INCREMENT COMMENT '自增id',
      `date` date not null COMMENT '日期，如2019-4-20',
      `scattered_ticket_num` int(11) default 0 COMMENT '散票数',
      `group_ticket_num`     int(11) default 0 COMMENT '团票数',
      `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
      `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间'
    )ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 collate utf8mb4_bin COMMENT='营销数据－团散门票';

    #营销数据-门票票种
    CREATE TABLE IF NOT EXISTS `ticket_type`(
      `id` bigint (20) PRIMARY KEY AUTO_INCREMENT COMMENT '自增id',
      `date` date not null COMMENT '日期，如2019-4-20',
      `scenic_spot_id` tinyint(3) not null COMMENT '景区id 1:黄河大峡谷，2：黄河楼，3：黄河坛,4:黄河生态园',
      `ticket_type_id` int(11) default 0 COMMENT '票种id 1: 成人票  2:学生票 3：免票 4：生态园1元票',
      `ticket_of` int(11) default 0 COMMENT '门票|船票 1:门票   2: 船票',
      `ticket_from` int(11) default 0 COMMENT '散客|旅行社 1: 散客  2: 旅行社',
      `ticket_num`     int(11) default 0 COMMENT '票数',
      `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
      `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间'
    )ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 collate utf8mb4_bin COMMENT='营销数据－门票票种';

    #天气预警信息表
    CREATE TABLE IF NOT EXISTS `warning`(
      `id` INT (11) PRIMARY KEY AUTO_INCREMENT COMMENT '自增id',
      `warn_msg` VARCHAR (50) default '' COMMENT '天气预警信息',
      `warn_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '预警时间',
      `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
      `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间'
    )ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 collate utf8mb4_bin COMMENT='天气预警信息表';

    #交通数据表-车源地数量
     CREATE TABLE IF NOT EXISTS `car_from`(
      `id` bigint (20) PRIMARY KEY AUTO_INCREMENT COMMENT '自增id',
      `date` date not null COMMENT '日期，如2019-04-20',
      `shanxi_car_count`int(11) default 0 COMMENT '来自陕西车辆数',
      `beijing_car_count`int(11) default 0 COMMENT '来自北京车辆数',
      `tianjin_car_count`int(11) default 0 COMMENT '来自天津车辆数',
      `hebei_car_count`int(11) default 0 COMMENT '来自河北车辆数',
      `sanxi_car_count`int(11) default 0 COMMENT '来自山西车辆数',
      `neimeng_car_count`int(11) default 0 COMMENT '来自内蒙车辆数',
      `liaoning_car_count`int(11) default 0 COMMENT '来自辽宁车辆数',
      `jilin_car_count`int(11) default 0 COMMENT '来自吉林车辆数',
      `heilongjiang_car_count`int(11) default 0 COMMENT '来自黑龙江车辆数',
      `shanghai_car_count`int(11) default 0 COMMENT '来自上海车辆数',
      `jiangsu_car_count`int(11) default 0 COMMENT '来自江苏车辆数',
      `zhejiang_car_count`int(11) default 0 COMMENT '来自浙江车辆数',
      `anhui_car_count`int(11) default 0 COMMENT '来自安徽车辆数',
      `fujian_car_count`int(11) default 0 COMMENT '来自福建车辆数',
      `jiangxi_car_count`int(11) default 0 COMMENT '来自江西车辆数',
      `shandong_car_count`int(11) default 0 COMMENT '来自山东车辆数',
      `henan_car_count`int(11) default 0 COMMENT '来自河南车辆数',
      `hubei_car_count`int(11) default 0 COMMENT '来自河北车辆数',
      `hunan_car_count`int(11) default 0 COMMENT '来自湖南车辆数',
      `guangdong_car_count`int(11) default 0 COMMENT '来自广东车辆数',
      `guangxi_car_count`int(11) default 0 COMMENT '来自广西车辆数',
      `hainan_car_count`int(11) default 0 COMMENT '来自海南车辆数',
      `chongqing_car_count`int(11) default 0 COMMENT '来自重庆车辆数',
      `sichuan_car_count`int(11) default 0 COMMENT '来自四川车辆数',
      `guizhou_car_count`int(11) default 0 COMMENT '来自贵州车辆数',
      `yunnan_car_count`int(11) default 0 COMMENT '来自云南车辆数',
      `xizang_car_count`int(11) default 0 COMMENT '来自西藏车辆数',
      `gansu_car_count`int(11) default 0 COMMENT '来自甘肃车辆数',
      `qinghai_car_count`int(11) default 0 COMMENT '来自青海车辆数',
      `ningxia_car_count`int(11) default 0 COMMENT '来自宁夏车辆数',
      `xinjiang_car_count`int(11) default 0 COMMENT '来自新疆车辆数',
      `xianggang_car_count`int(11) default 0 COMMENT '来自香港车辆数',
      `taiwan_car_count`int(11) default 0 COMMENT '来自台湾车辆数',
      `aomen_car_count`int(11) default 0 COMMENT '来自澳门车辆数',
      `car_number_json` int(11) default 0 COMMENT '车票号json',
      `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
      `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间'
    )ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 collate utf8mb4_bin COMMENT='营销数据－车源地数量';

    #游客身份信息
    CREATE TABLE IF NOT EXISTS `visitor_info`(
      `id` bigint (20) PRIMARY KEY AUTO_INCREMENT COMMENT '自增id',
      `date` date not null COMMENT '日期，如2019-4-20',
      `id_card` varchar(18) default '' COMMENT '身份证号',
      `province` varchar(30) default '' COMMENT '客源地',
      `birthday` date not null COMMENT '出生日期',
      `sex` varchar(10) not null default '' COMMENT '性别',
      `is_analysisided` tinyint(1) not null DEFAULT 0 COMMENT '该条记录是否已统计分析过 0否 1是',
      `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
      `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间'
    )ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 collate utf8mb4_bin COMMENT='游客身份信息';

    #修改游客信息表 增加字段
    ALTER TABLE `visitor_info`
    ADD `travel_agency` varchar(64) DEFAULT '' COMMENT '旅行社',
    ADD `group_num` VARCHAR (32) DEFAULT '' COMMENT '团号';

    #车辆信息表
    CREATE TABLE IF NOT EXISTS `car_info`(
      `id` bigint (20) PRIMARY KEY AUTO_INCREMENT COMMENT '自增id',
      `date` date not null COMMENT '日期，如2019-4-20',
      `scenic_spot_id` tinyint(3) not null COMMENT '景点停车场id 1:黄河大峡谷，2：黄河楼，3：黄河坛',
      `car_number` varchar(18) default '' COMMENT '车牌号',
      `is_analysisided` tinyint(1) not null DEFAULT 0 COMMENT '该条记录是否已统计分析过 0否 1是',
      `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
      `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间'
    )ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 collate utf8mb4_bin COMMENT='车辆信息表';











