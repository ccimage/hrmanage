<?php
namespace Common\Event;
use Common\Controller\BaseController;
use Common\Util\SendMail;
/**
 * 用户事件
 * Class UserEvent
 * @package Common\Event
 */
class UserEvent extends BaseController
{

    /**
     * 用户忘记密码找回
     * @param $username
     * @return string
     */
    public function forgetPassword($username)
    {
        $User = new UserLogic();
        $SendMail =new SendMail();
        $userDetail = $User->where(array('user_login' => $username))->find();
        if (!$userDetail) {
            return $this->jsonResult(0, "不存在用户");
        }
        $new_pass = encrypt($userDetail['user_session']);
        $User->where(array('user_email' => $userDetail['user_email']))->data(array('user_pass' => $new_pass))->save();
        $res =  $SendMail->sendMail( $userDetail['user_email'], "", "用户密码重置", "新密码: " . $userDetail['user_session']);
        if ($res['statue']) {
            return $this->jsonResult(1, "新密码的邮件已经发送到注册邮箱");
        } else {
            return $this->jsonResult(0, "请检查邮件发送设置".$res['info'] );
        }
    }
    /**
     * 认证用户，传入where查询 $map['user表字段']
     * @param $map
     * @return string
     */
    public function auth($map)
    {
        $authInfo = M("admin")->where($map)->find();
        if (false === $authInfo || $authInfo == null) {
            $resultstring = $this->_savelog(-1, -3);
            return $this->jsonResult(0, $resultstring);
        }
        else {
            //记住我
            if (I('post.remember') == 1) {
                cookie(C('USER_AUTH_KEY'), $authInfo['id'], 3600 * 24 * 30);
                cookie(C('USER_AUTH_NAME'),  $authInfo['username'], 3600 * 24 * 30);
            }
            else{
                $_SESSION[C('USER_AUTH_KEY')] = $authInfo['id'];
                $_SESSION[C('USER_AUTH_NAME')] = $authInfo['username'];
            }
            // 缓存访问权限
            $resultstring = $this->_savelog($authInfo['id'], 1);
            return $this->jsonResult(1, $resultstring, U("Admin/Index/index"));
        }
    }
    /**
     * 退出
     * @return string
     */
    public function logout()
    {
        $User = new UserLogic();
        $authInfo = $User->detail(session(C('USER_AUTH_KEY')));
        $User->genHash($authInfo);
        cookie('user_session', null);
        session_unset();
        session_destroy();
        return $this->jsonResult(1, "退出成功", U("Login/index"));
    }
    /**
     * 注册用户
     * @param $username
     * @param $nickname
     * @param $password
     * @param $email
     * @return string
     */
    public function register($username, $nickname, $password, $email)
    {
        $new_user_role = get_opinion('new_user_role', true, 5);
        $new_user_statue = get_opinion('new_user_statue', true, 1);
        $User = new UserLogic();
        $userDetail = $User->where(array('user_login' => $username))->select();
        if ($userDetail != '') {
            return $this->jsonResult(0, "用户名已存在");
        } else {
            // 组合用户信息并添加
            $newUserDetail = array(
                'user_login' => $username,
                'user_nicename' => $nickname,
                'user_pass' => encrypt($password),
                'user_email' => $email,
                'user_status' => $new_user_statue,
                // 'logintime'=>time(),
                // 'loginip'=>get_client_ip(),
                // 'lock'=>$_POST['lock']
            );
            // 添加用户与角色关系
            $newUserDetail ['user_level'] = $new_user_role;
            $Role_users = D('Role_users');
            if ($new_id = $User->add($newUserDetail)) {
                $role = array(
                    'role_id' => $new_user_role,
                    'user_id' => $new_id
                );
                if ($Role_users->add($role)) {
                    return $this->jsonResult(1, "注册成功！", U('Admin/Access/index'));
                } else {
                    return $this->jsonResult(0, "注册成功，添加用户权限失败！");
                }
            } else {
                return $this->jsonResult(0, "注册用户失败");
            }
        }
    }
}