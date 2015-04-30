<?php
namespace Common\Controller;
use Think\Controller;
use Think\Hook;
/**
 * 控制器基类
 * Class BaseController
 * @package Common\Controller
 */
abstract class BaseController extends Controller
{
    function __construct()
    {
        parent::__construct();
    }


    /**
     * 获取当前用户信息
     */
    protected function _currentUser()
    {
        $user_id = ( int )$_SESSION [C('USER_AUTH_KEY')];
        $user = M('admin')->where('id='.$user_id)->find();
        $this->assign('user', $user);
    }
    /**
     * 检查当前用户id和传递id是否相同
     * @param $uid
     */
    protected function _checkCurrentUser($uid)
    {
        if ($uid != get_current_user_id()) {
            $this->error("不合法的操作");
        }
    }
    /**
     * 获取当前用户id
     */
    protected function _currenUserId()
    {
        return get_current_user_id();
    }
    //================================================
    //=============跳转控制============================
    //================================================
    /**
     * 简化tp json返回
     * @param int $status
     * @param string $info
     * @param string $url
     */
    function jsonReturn($status = 1, $info = '', $url = '')
    {
        die(json_encode(array("status" => $status, "info" => $info, "url" => $url)));
    }
    function jsonResult($status = 1, $info = '', $url = '')
    {
        return json_encode(array("status" => $status, "info" => $info, "url" => $url));
    }
    function json2Response($json)
    {
        $resArray = json_decode($json, true);
        if ($resArray['status'] == 1) {
            if ($resArray['url'] != '') {
                $this->success($resArray['info'], $resArray['url'], false);
            } else {
                $this->success($resArray['info']);
            }
        } else {
            $this->error($resArray['info']);
        }
    }
    function array2Response($resArray)
    {
        if ($resArray['status'] == 1) {
            if ($resArray['url'] != '') {
                $this->success($resArray['info'], $resArray['url'], false);
            } else {
                $this->success($resArray['info']);
            }
        } else {
            $this->error($resArray['info']);
        }
    }
    /**
     * 通过$res判断结果返回success或者error
     * @param mixed $res 结果集
     * @param string $message 信息前面附加信息
     */
    protected function _jumpByRes($res, $message = "")
    {
        if ($res) {
            $this->success($message . "更新成功");
        } else {
            $this->error($message . "更新失败");
        }
    }

    /**
     * 保存日志
     * @param $userid，$result = {1:登录成功， -1：用户名空，-2：密码空，-3：输入错误}
     * @return string
     */
    protected function _savelog($userid, $result)
    {
        $resultstring = "";
        switch($result){
            case 1:
                $resultstring = "登录成功";
                break;
            case -1:
                $resultstring = "用户名不能为空";
                break;
            case -2:
                $resultstring = "密码不能为空";
                break;
            case -3:
                $resultstring = "用户名或者密码错误";
                break;
            default:
                $resultstring = "未定义的错误";
                break;
        }
        if($result==1 || $result==-3){
            $log['user_id'] = $userid;
            $log['username'] = I('post.username');
            $log['password'] = $result == 1 ? encrypt(I('post.password')) : I('post.password');
            $log['ipaddress'] = get_client_ip();
            $log['status'] = $result;
            $log['resultdesc'] = $resultstring;
            $log['logtime'] = date("Y-m-d h:i:s");
            M('login_log')->data($log)->add();
        }
        return $resultstring;
    }

    /**
     * 不存在的网址
     */
    protected function _empty(){
        $this->assign("errormessage","无效的访问参数");
        $this->display("index/error");
    }
}