<?php

namespace Org\WeixinAPI\API;

use Org\WeixinAPI\Api;
/**
 * 微信卡券相关接口.
 *
 */
class CardApi extends BaseApi
{
	/**
     * 创建卡券.
     *
     * @date   2016-05-13
     *
     * @param  array $card   创建卡券数据
     *
     * @return array 卡券id
     */
    public function createCard(array $card)
    {
    	if(empty($card)){
    		$this->setError('参数错误');

    		return false;
    	}

    	$data = array();
    	$data['card'] = $card;

    	$node = 'create';

    	return $this->_post($node, $data);
    }

    /**
     * 获取卡券详情.
     *
     * @date   2016-05-12
     *
     * @param  str   $card_id  卡券id
     *
     * @return array 当前卡券详情
     */
    public function getInfoByCardId($card_id)
    {
    	if (!$card_id) {
    		$this->setError('参数错误');

            return false;
        }

        $node = 'get';

        $data['card_id'] = $card_id;

        return $this->_post($node, $data);
    }

    /**
     * 获取卡券摘要列表.
     *
     * @date   2016-05-12
     *
     * @param  str $offset 查询卡列表起始数 从0开始
     * @param  str $count  需要查询的卡片的数量（数量最大 50）
     * @param  str $status 拉出指定状态的卡券列表.不填默认拉取所有状态
     *
     * @return array 卡卷列表
     */
    public function getCardList($offset, $count, $status='CARD_STATUS_VERIFY_OK')
    {
    	if (!$offset && $offset < 0) {
    		$this->setError('参数错误');

            return false;
        }

        if (!$count && $count > 50) {
    		$this->setError('参数错误');

            return false;
        }

        $node = 'batchget';

        $data['offset'] = $offset;
        $data['count']  = $count;
        $data['status'] = $status;

        return $this->_post($node, $data);
    }


    /**
     * 修改卡券库存.
     *
     * @date   2016-05-12
     *
     * @param  str $card_id       卡券id
     * @param  str $increaseStock 增加多少库存
     * @param  str $reduceStock   减少多少库存
     *
     * @return 状态 ok
     */
    public function updateStock($card_id, $increaseStock = '', $reduceStock = '')
    {
    	if (!$card_id) {
    		$this->setError('参数错误');

            return false;
        }

        if(!$increaseStock && !$reduceStock){
        	$this->setError('增、减库存不能同时为空');

        	return false;
        }

        $node = 'modifystock';

        $data['card_id']			  = $card_id;
        $data['increase_stock_value'] = $increaseStock;
        $data['reduce_stock_value']   = $reduceStock;

        return $this->_post($node, $data);
    }


     /**
     * 删除卡券.
     *
     * @date   2016-05-13
     *
     * @param  str  $card_id    卡券id
     *
     * @return 状态 ok
     */
    public function deleteCard($card_id)
    {
    	if (!$card_id) {
    		$this->setError('参数错误');

            return false;
        }

		$node = 'delete';

		$data['card_id'] = $card_id;

        return $this->_post($node, $data);
	}


	/**
     * 发送卡券消息.
     *
     * @date   2016-05-13
     *
     * @param  str  $userList    多个接收者用‘|’分隔 的字符串 例:"UserID1|UserID2|UserID3"
     * @param  str  $agentid     应用id
     * @param  str  $card_id     卡券id
     *
     * @return 状态 ok
     */
	public function sendCardNews($userList, $agentid, $card_id)
    {
    	if (!$userList) {
    		$this->setError('接收者参数错误');

            return false;
        }
        if (!$agentid) {
    		$this->setError('应用参数错误');

            return false;
        }
        if (!$card_id) {
    		$this->setError('卡券参数错误');

            return false;
        }

    	$module = 'message';
    	$node   = 'send';

    	$data['touser'] 		 = $userList;
    	$data['msgtype'] 		 = 'card';
    	$data['agentid'] 		 = $agentid;
    	$data['card']['card_id'] = $card_id;

    	return Api::_post($module, $node, $data);
    }


    /**
     * 获取卡券图文消息内容.
     *
     * @date   2016-05-13
     *
     * @param  str  $agentid     应用id
     * @param  str  $card_id     卡券id
     *
     * @return string 卡券图文内容
     */
	public function getCardHtmlContent($agentid, $card_id)
    {
    	if (!$agentid) {
    		$this->setError('应用参数错误');

            return false;
        }
        if (!$card_id) {
    		$this->setError('卡券参数错误');

            return false;
        }

        $node = 'mpnews/gethtml';

        $data['agentid'] = $agentid;
        $data['card_id'] = $card_id;

        return $this->_post($node, $data);
    }


	/**
     * 查询 code.
     *
     * @date   2016-05-13
     *
     * @param  str   $code    卡券code
     *
     * @return array 卡券状态数组
     */
	public function getCode($code)
    {
    	if (!$code) {
    		$this->setError('参数错误');

            return false;
        }

        $node = 'code/get';

        $data['code'] = $code;

        return $this->_post($node, $data);
    }


    /**
     * 核销 code.
     *
     * @date   2016-05-13
     *
     * @param  str   $code    卡券code
     *
     * @return array 核销卡券状态数组
     */
    public function consumeCode($code)
    {
    	if (!$code) {
    		$this->setError('参数错误');

            return false;
        }

    	$node = 'code/consume';

    	$data['code'] = $code;

    	return $this->_post($node, $data);
    }


    /**
     * 获取 卡券api_ticket.
     *
     * @date   2016-05-13
     *
     * @param  
     *
     * @return array  数组ticket
     */
    public function getTicket()
    {
    	$module = 'ticket';
    	$node   = 'getticket';

    	$data['type'] = 'wx_card';

    	return Api::_get($module, $node, $data);
    }


}