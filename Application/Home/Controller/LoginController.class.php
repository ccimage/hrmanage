<?php
/**
 * File: LoginController.class.php
 * User: chuan zhang
 * Date: 15-4-6
 * Time: 上午19:19
 */
namespace Home\Controller;
use Common\Controller\BaseController;
use Common\Event\UserEvent;
//use Think\Verify;
/**
 * Class LoginController
 * @package \Controller
 */
class LoginController extends BaseController
{
    public function __construct()
    {
        parent::__construct();
    }
    /**
     * 自动登陆处理
     */
    public function _before_index()
    {
        $user_session = cookie('user_session');
        if ($user_session) {
            //auto login
            $map['user_session'] = $user_session;
            $UserEvent = new UserEvent();
            $loginRes = $UserEvent->auth($map);
            $loginResArray = json_decode($loginRes, true);
            if ($loginResArray['status'] == 1) {
                //登陆成功
                $authInfo = D('User', 'Logic')->where($map)->find();
                $this->_savelog($authInfo['user_id'], $authInfo['user_login'], $authInfo['user_pass']);

                if(cookie("last_visit_page")){
                    redirect(base64_decode(cookie("last_visit_page")));
                }else{
                    $this->redirect('Index/index');
                }
            }
        }
    }

    /**
     * 首页
     */
    public function index()
    {
        $this->_empty();
    }
    /**
     * 登陆
     */
    public function login()
    {
        $userInput = trim(I('post.username'));
        $pwdInput = trim(I('post.password'));
        $jsonObj = null;
        if($userInput==''||$userInput==null){
            $resultstring = $this->_savelog(-1, -1);
            $jsonObj = $this->jsonResult(0, $resultstring);
            $this->json2Response($jsonObj);
            return ;
        }
        else if($pwdInput==''||$pwdInput==null){
            $resultstring = $this->_savelog(-1, -2);
            $jsonObj = $this->jsonResult(0, $resultstring);
            $this->json2Response($jsonObj);
            return;
        }
        //验证图片
        //$this->vertifyHandle();
        $map = array();
        $map['username'] = $userInput;
        $map['password'] = encrypt($pwdInput);
        $UserEvent = new UserEvent();
        $loginRes = $UserEvent->auth($map);
        $this->json2Response($loginRes);
    }
    /**
     * 验证码
     */
    public function vertifyHandle()
    {
        if (C('vertify_code', true, true)) {
            $verify = new Verify();
            if (!$verify->check(I('post.vertify'), "AdminLogin")) {
                $this->error("验证码错误");
            }
        }
    }
    /**
     * 注册
     */
    public function register()
    {
        $this->registerJudge();
        $this->display();
    }
    /**
     * 判断是否注册
     */
    public function registerJudge()
    {
        $user_can_regist = get_opinion('user_can_regist', true, 1);
        if ($user_can_regist) {
        } else {
            $this->error("不开放注册");
        }
    }
    /**
     * 注册用户处理
     */
    public function registerHandle()
    {
        $this->registerJudge();
        $this->vertifyHandle();
        $username = I('post.username');
        $nickname = I('post.nickname');
        $password = I('post.password');
        $email = I('post.email');
        if (!($username && $nickname && $password && $email)) {
            $this->error("字段不能为空");
        }
        $UserEvent = new UserEvent();
        $registerRes = $UserEvent->register($username, $nickname, $password, $email);
        $this->json2Response($registerRes);
    }
    /**
     * 找回密码
     */
    public function forgetpassword()
    {
        $this->display();
    }
    /**
     * 找回密码处理
     */
    public function forgetpasswordHandle()
    {
        $this->vertifyHandle();
        if (IS_POST) {
            $username = I('post.username');
            $UserEvent = new UserEvent();
            $forgetPasswordRes = $UserEvent->forgetPassword($username);
            $this->json2Response($forgetPasswordRes);
        }
    }
    /**
     * 注销
     */
    public function logout()
    {
        $UserEvent = new UserEvent();
        $logoutRes = $UserEvent->logout();
        $this->json2Response($logoutRes);
    }
    /**
     * 验证码
     */
    public function vertify()
    {
        $config = array(
            'fontSize' => 20,
            'length' => 4,
            'useCurve' => true,
            'useNoise' => true,
        );
        $Verify = new Verify($config);
        $Verify->entry("AdminLogin");
    }
    //ajax输出
    protected function success($message='',$jumpUrl='',$ajax=false){
        echo($this->jsonResult(true,$message, $jumpUrl));
    }
    protected function error($message='',$jumpUrl='',$ajax=false) {
        echo($this->jsonResult(false,$message, $jumpUrl));
    }
}