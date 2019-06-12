<?php
/**
 * Created by PhpStorm.
 * User: panxiongfei
 * Date: 2018/11/28
 * Time: 15:43
 */

class ErrorNo {
    const SUCC = 0;                // 成功
    const ERR_PARAM = -1;          // 用户传过来的参数错误
    const ERR_LOGIC = -2;          // control层逻辑错误
    const ERR_PAGE = -3;           // page server错误
    const ERR_DATA = -4;           // data server错误
    const ERR_FAILED = -5;         // 结果不正确
    const ERR_RIGHT = -6;          // 鉴权错误
    const ERR_CODE_USED = -9;      // 二维码已失效
    const ERR_CODE_UNREADY = -10;      //二维码不在待核验状态

    const ERR_LOGIN_REQUIRED = -20002; // 需要登录
    const ERR_LOGIN_PASSWORD_RESET_REQUIRED = -20003; // 首次登陆需要重置密码
}