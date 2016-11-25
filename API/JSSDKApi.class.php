<?php

namespace Org\WeixinAPI\API;

use Org\WeixinAPI\Api;

/**
 * 微信JSSDK相关接口.
 *
 * @author Cui.
 */
class JSSDKApi extends BaseApi
{
    /**
     * 获取JSSDK接口认证.
     *
     * @author Cui
     *
     * @date   2015-07-27
     *
     * @return string 认证签名.
     */
    public function getTicket()
    {
        $key = md5('JSAPI_TICKET' . Api::getSecrect());
        $ticket = Api::Cache($key);
        if (!$ticket) {
            $this->module = 'get_jsapi_ticket';
            $res = $this->_get('');
            if (!$res) {
                exit($this->getError());
            }

            $ticket = $res['ticket'];
            $expires = $res['expires_in'];

            Api::Cache($key, $ticket, $expires - 300);
        }

        return $ticket;
    }

    /**
     * 获取JSSDK签名.
     *
     * @author Cui 
     *
     * @date   2015-07-27
     *
     * @return string
     */
    public function getSignature()
    {
        $timestamp = $this->getTimeStamp();
        $nonceStr = $this->getNonceStr();
        $ticket = $this->getTicket();

        $data = array();
        $data['jsapi_ticket'] = $ticket;
        $data['noncestr'] = $nonceStr;
        $data['timestamp'] = $timestamp;
        $data['url'] = "https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        //$data['url'] = "http://o365.weflame.com/chatList";
        $signature = '';
        foreach ($data as $key => $value) {
            $signature .= $key . '=' . $value . '&';
        }

        $signature = rtrim($signature, '&');

        $signature = sha1($signature);

        return $signature;
    }

    /**
     * 获取唯一时间戳.
     *
     * @author Cui
     *
     * @date   2015-07-27
     *
     * @return timestamp
     */
    public function getTimeStamp()
    {
        static $timestamp;
        if (!$timestamp) {
            $timestamp = time();
        }

        return $timestamp;
    }

    /**
     * 获取唯一随机串.
     *
     * @author Cui
     *
     * @date   2015-07-27
     *
     * @return string
     */
    public function getNonceStr()
    {
        static $nonceStr;
        if (!$nonceStr) {
            $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
            $nonceStr = '';
            for ($i = 0; $i < 16; $i++) {
                $nonceStr .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
            }
        }

        return $nonceStr;
    }

    /**
     * 获取联系人控件票据和管理组id
     *
     * @author Cui
     *
     * @date   2015-12-25
     *
     * @return array
     */
    public function getContactTicket()
    {
        $key = 'CONTACT_TICKET-' . md5(Api::getSecrect());
        $data = Api::Cache($key);
        if (!$data) {
            $this->module = 'ticket';
            $node = 'get';
            $queryStr = array('type' => 'contact');
            $res = $this->_get($node, $queryStr);
            if (!$res) {
                exit($this->getError());
            }

            $data = array('ticket' => $res['ticket'], 'group_id' => $res['group_id']);
            $expires = $res['expires_in'];

            Api::Cache($key, $data, $expires - 300);
        }

        return $data;
    }

    /**
     * 获取JSSDK联系人控件签名.
     *
     * @author Cui
     *
     * @date   2015-07-27
     *
     * @return string
     */
    public function getContactSignature()
    {
        $timestamp = $this->getTimeStamp();
        $nonceStr = $this->getNonceStr();
        $ticket = $this->getContactTicket();

        $data = array();
        $data['group_ticket'] = $ticket['ticket'];
        $data['noncestr'] = $nonceStr;
        $data['timestamp'] = $timestamp;
        $data['url'] = "https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        $signature = '';
        foreach ($data as $key => $value) {
            $signature .= $key . '=' . $value . '&';
        }

        $signature = rtrim($signature, '&');

        $signature = sha1($signature);

        return $signature;
    }
}
