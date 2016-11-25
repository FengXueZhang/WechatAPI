<?php

namespace Org\WeixinAPI\API;

use Org\WeixinAPI\Api;
/**
 * 微信-通讯录 用户相关接口.
 *
 * @author XueFeng.
 */
class ServiceApi extends BaseApi
{
	
    /**
     * 创建微信成员登录协议的链接.
     *
     * @author XueFeng
     *
     * @date   2016-04-21
     *
     * @param string $redirectUri 协议的回调地址
     * @param string $state       可携带的参数, 选填.
     * @param string $usertype    redirect_uri支持登录的类型,默认是admin.
     *
     * @return string 协议地址
     */
    public function createOAuthUrl($redirectUri, $state = '', $usertype = 'member')
    {
        if (!$redirectUri) {
            $this->setError('参数错误!');

            return false;
        }

        $host = isset($_SERVER['HTTP_HOST']) ? 'http://' . $_SERVER['HTTP_HOST'] : '';
        $api = 'https://qy.weixin.qq.com/cgi-bin/loginpage';

        $state = $state ? $state = base64_encode($state) : '';

        $url = array();
        $url['corp_id'] = Api::getCorpId();
        $url['redirect_uri'] = $host . $redirectUri;
        $url['usertype'] = $usertype;
        $url['state'] = $state;

        $url = http_build_query($url);

        $url .= '#wechat_redirect';
        $url = $api . '?' . $url;

        return $url;
    }

    /**
     * 请求.
     *
     * @author XueFeng
     *
     * @date   2016-04-22
     */
    public function request($redirectUri, $state = '')
    {
        $code = I('get.auth_code', false, 'trim');
        if ($code) {
            return;
        }

        $url = $this->createOAuthUrl($redirectUri, $state);
        header('Location:' . $url);
        exit;
    }

    /**
     * 获取登录回调的信息.
     *
     * @author XueFeng
     *
     * @date   2016-04-21
     *
     * @return array 回调信息.
     */
    public function receive()
    {
        $code = I('get.auth_code', false, 'trim');

        if (!$code) {
            $this->setError('非法参数');

            return false;
        }

        $res = $this->getIdByCode($code);
        if (false == $res || !isset($res['user_info']) || !$res['user_info']) {
            $this->setError('对不起,您尚不是本站用户.');

            return false;
        }

        $arr = array();
        $arr['userid'] = $res['user_info']['userid'];
        $arr['state'] = I('get.state', '', 'trim,base64_decode');
        $arr['auth_code'] = $code;

        return $arr;
    }

    /**
     * 根据协议换回的code换取用户的userid.
     *
     * @author XueFeng
     *
     * @date   2016-04-21
     *
     * @param string $code 协议换回的code
     *
     * @return string userid
     */
    public function getIdByCode($code)
    {
        if (false == $code) {
            $this->setError('参数错误');

            return false;
        }

        $node = 'get_login_info';

        $queryStr = array(
            'auth_code' => $code,
        );

        return $this->_post($node, $queryStr);
    }

    /**
     * 根据用户ID获取用户信息.
     *
     * @author XueFeng
     *
     * @date   2016-04-21
     *
     * @param string $userId 用户在微信端的userid.
     *
     * @return array 用户信息
     */
    public function getInfoById($userId)
    {
        return Api::factory("User")->getInfoById($userId);
        //return $this->_get($node, $queryStr);
    }
}