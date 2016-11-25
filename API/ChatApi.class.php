<?php

namespace Org\WeixinAPI\API;

/**
 * 微信-聊天接口.
 *
 */
class ChatApi extends BaseApi
{
    private $senderData; // 发送人
    private $receiveType; // 接收类型 单聊|群聊
    private $receiverData; // 接收方
    private $msgType; // 消息类型
    private $msgData; // 消息内容

    /**
     * 创建会话
     *
     *
     * @date   2016-02-17
     *
     * @param  string  $chatid   会话ID
     * @param  string  $name     会话名称
     * @param  string  $owner    会话拥有者userid,必须是该会话userlist的成员之一
     * @param  array   $userlist 会话成员的userid
     *
     * @return Boolean
     */
    public function create($chatid, $name, $owner, array $userlist)
    {
        if (empty($chatid) || !is_string($chatid)) {
            $this->setError('参数不正确!');

            return false;
        }

        if (empty($name) || !is_string($name)) {
            $this->setError('参数不正确!');

            return false;
        }

        if (empty($owner) || !is_string($owner)) {
            $this->setError('参数不正确!');

            return false;
        }

        if (empty($chatid) || !is_array($userlist)) {
            $this->setError('参数不正确!');

            return false;
        }

        $data = array();
        $data['chatid'] = $chatid;
        $data['name'] = $name;
        $data['owner'] = $owner;
        $data['userlist'] = $userlist;

        $node = 'create';

        return $this->_post($node, $data);
    }

    /**
     * 根据会话id获取会话信息
     *
     *
     * @date   2016-02-17
     *
     * @param  string     $chatid 会话id
     *
     * @return 会话信息
     */
    public function get($chatid)
    {
        if (!$chatid) {
            $this->setError('chatid 必须');

            return false;
        }

        $queryStr = array();
        $queryStr['chatid'] = $chatid;
        $node = 'get';

        return $this->_get($node, $queryStr);
    }

    /**
     * 修改会话信息
     *
     *
     * @date   2016-02-17
     *
     * @param  string     $chatid      会话id
     * @param  string     $opUser      操作人userid
     * @param  string     $name        会话名 非必须
     * @param  string     $owner       管理员 非必须
     * @param  array      $addUserList 要添加的userid数组 非必须
     * @param  array      $delUserList 要删除的userid数据 非必须
     *
     * @return boolean
     */
    public function update($chatid, $opUser, $name = '', $owner = '', $addUserList = array(), $delUserList = array())
    {
        if (!$chatid) {
            $this->setError('会话id必须');

            return false;
        }

        if (!$opUser) {
            $this->setError('操作人id必须');

            return false;
        }

        $data = array();
        $data['chatid'] = $chatid;
        $data['name'] = $name;
        $data['op_user'] = $opUser;
        if ($owner != '') {
            $data['owner'] = $owner;
        }
        $data['add_user_list'] = $addUserList;
        $data['del_user_list'] = $delUserList;

        $node = 'update';

        return $this->_post($node, $data);
    }

    /**
     * 链式操作-发送人.
     *
     * @author Cui
     *
     * @date   2015-08-11
     *
     * @param string $sender 发送人的userid
     *
     * @return self 对象本身
     */
    public function sender($sender)
    {
        if (!$sender) {
            $this->setError('参数错误!');

            return false;
        }

        $this->senderData = $sender;

        return $this;
    }

    /**
     * 链式操作-接收人.
     *
     * @author Cui
     *
     * @date   2015-08-11
     *
     * @param string $receiver 根据接收类型不同 可以为userid或者chatid
     *
     * @return self 对象本身
     */
    public function receiver($receiver)
    {
        if (!$receiver) {
            $this->setError('参数错误!');

            return false;
        }

        $this->receiverData = $receiver;

        return $this;
    }

    /**
     * 链式操作-发送信息为text.
     *
     * @author Cui
     *
     * @date   2015-08-11
     *
     * @param string $content 文本内容
     *
     * @return self 对象本身
     */
    public function text($content)
    {
        if (!$content) {
            $this->setError('参数错误!');

            return false;
        }

        $this->msgType = 'text';
        $this->msgData = array('content' => $content);

        return $this;
    }

    /**
     * 链式操作-发送信息为iamge.
     *
     * @author Cui
     *
     * @date   2015-08-11
     *
     * @param string $media 上传到微信的素材id
     *
     * @return self 对象本身
     */
    public function image($media)
    {
        if (!$media) {
            $this->setError('参数错误!');

            return false;
        }

        $this->msgType = 'image';
        $this->msgData = array('media_id' => $media);

        return $this;
    }

    /**
     * 链式操作-发送信息为file.
     *
     * @author Zhanglu
     *
     * @date   2015-09-09
     *
     * @param string $media 上传到微信的素材id
     *
     * @return self 对象本身
     */
    public function file($media)
    {
        if (!$media) {
            $this->setError('参数错误!');

            return false;
        }

        $this->msgType = 'file';
        $this->msgData = array('media_id' => $media);

        return $this;
    }

    /**
     * 链式操作-接收类型.
     *
     * @author Cui
     *
     * @date   2015-08-11
     *
     * @param int $type 0:单聊 1:群聊
     *
     * @return self 对象本身
     */
    public function type($type)
    {
        $receiveType = array('single', 'group');

        if (!array_key_exists($type, $receiveType)) {
            $this->setError('参数错误!');

            return false;
        }

        $this->receiveType = $receiveType[$type];

        return $this;
    }

    /**
     * 链式操作-发送 发送完成后 清除对象内的相关属性.
     *
     * @author Cui
     *
     * @date   2015-08-11
     *
     * @return Boolean
     */
    public function send()
    {
        $sender = $this->senderData;
        $receivetype = $this->receiveType;
        $receiver = $this->receiverData;
        $msgtype = $this->msgType;
        $content = $this->msgData;

        if (!$sender || !$receivetype || !$receiver || !$msgtype || !$content) {
            $this->setError('参数不完整!');

            return false;
        }

        $data = array();
        $data['receiver']['type'] = $receivetype;
        $data['receiver']['id'] = $receiver;
        $data['sender'] = $sender;
        $data['msgtype'] = $msgtype;
        $data[$msgtype] = $content;
        
        $this->senderData = null;
        $this->receiveType = null;
        $this->receiverData = null;
        $this->msgType = null;
        $this->msgData = null;

        $node = 'send';

        return $this->_post($node, $data);
    }
}
