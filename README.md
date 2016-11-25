# WechatAPI
微信企业号主动服务API接口

调用示例

Api::init(C("APP_ID"), C("APP_SECRET"), '', C("TOKEN"), C("ENCODING_AESKEY"));

$voice = Api::factory("Media")->get($meidaId);
