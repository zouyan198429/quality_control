<?php


namespace App\Services\alipaySdk;

// require_once app_path('Library') . '/alipayWapPay/aop/AopClient.php';
require_once 'aop/AopCertClient.php';
require_once 'aop/AopCertification.php';
require_once 'aop/request/AlipayTradeQueryRequest.php';
require_once 'aop/request/AlipayTradeWapPayRequest.php';
require_once 'aop/request/AlipayTradeAppPayRequest.php';

/**
 * 证书类型AopCertClient功能方法使用测试，特别注意支付宝根证书预计2037年会过期，请在适当时间下载更新支付更证书
 * 1、execute 证书模式调用示例
 * 2、sdkExecute 证书模式调用示例
 * 3、pageExecute 证书模式调用示例
 */

class alipayTest
{
   public static function test(){

        //1、execute 使用
       $aop = new \AopCertClient ();
       $appCertPath = "/srv/www/certFile/alipay/onlinePay/appCertPublicKey_2021002125656270.crt";// "应用证书路径（要确保证书文件可读），例如：/home/admin/cert/appCertPublicKey.crt";
       $alipayCertPath = "/srv/www/certFile/alipay/onlinePay/alipayCertPublicKey_RSA2.crt";// "支付宝公钥证书路径（要确保证书文件可读），例如：/home/admin/cert/alipayCertPublicKey_RSA2.crt";
       $rootCertPath = "/srv/www/certFile/alipay/onlinePay/alipayRootCert.crt";// "支付宝根证书路径（要确保证书文件可读），例如：/home/admin/cert/alipayRootCert.crt";

       $aop->gatewayUrl = 'https://openapi.alipay.com/gateway.do';
       $aop->appId = '2021002125656270';// '你的appid';
       $aop->rsaPrivateKey = 'MIIEpAIBAAKCAQEArpGoIPQSkXvsEARuAAw2RwPugCD2PxLVRPZL+/l0tzHdwDfCFhoChEvU4eIn1daoXnauYgXa5AJsExGnJAxZMCd+Xk/IE4zwu55JuYASprWWKtwVRJHWhIiZIYzUIbNNtlICDQiis4uJlzsvk3N5Fx9n6yagG6tHKeeXrV/eIfIsSZbWM4CRdJAr+xSR0aBbOULTUZ77NXCiAq5iU6RmltMz3WI1zrMaCjcDbKbcgQl5BSMHq9fk8ge2ehiXyodDPGpQLbyVwjU5pFNS7GjclvMHbaRjguFCh3cs5aG+h0YB2mLDTkOHuskD7Zb2pFUTApVrWuEVtRsLfbjryWe4JQIDAQABAoIBAGITi3YYKmZ6TQIiuvpj4gq79r8dxbtiBVgdWEtUt3JFHeTkbrKkIk3ZnNzbhq3pT+bins6Lo5L+mWGNRW5HUcRwK9soz6vMP2PpZlpFzjTDSH8D7x77IJ6NcBsdWAEnD74jDOvtshtVhhPlOLGK6rlG98dxh/qDnBO6/ch2WAKRb+eQvmfwgdIDv0PA+ZXBY9wAnxz64CiJhQHlfntVPbSmueiZYLIhryk7EqVYLG3fU3uWXr7imQ/vnSvSeOJ+uYixEx+O/dx5uHKfRErwlUmIViBSMSl0QQOYQ7bTnWJGNTPEO+RzSIHHDFEAD/O6ohcXNVjlCnEHuMkHaBAii3ECgYEA+Yo3vLkytKmj/t5AiTmmI1rUrFxPW5DLuAucuGNTHQJxynaZUCuqNSep0hpX+TZIpCYelQUKv6gLPqBhboPvnIVy69BiktKBz20fhp5HQJS2ArLw/0OzDePh9yOpA1pO6zyUKZNTI4fONCDTxQf5FQ5PrEby62KOUDWjy+T2zeMCgYEAsxaVCgKV4XKHROT7brJST+pp2P4yW8nX9tFeJDClb6rgaBldLRYu9VQr8ETM2/ankdqxnE29xD0pH/jBR44f+vxriC5H3myaUq9nQYNa+1ucjTFzjhrSW0wbw6fVfkGozdllXby5g4HFdKm3z/7nSNZPncJ0lJLjp/KeOZ7lQFcCgYEAi/H6BDsQtsUUldDq/Ip6JAXCk89JKh7wQQ0yHS6G5BQE4PjWTmHOmPTfhlcD012gK369U/F577Y8aCjlu2b+sBcfNiStw42PA1c9gO+vIgbEdIsKIP5EopuUlFdJZ/nznHVi2lYnIBv/hriKS0uHY0mdYQ6BGYUyLsWGUMjdqOUCgYB+pXecbpujpSR32h7c94oyLanQ5GKkeqcZSpJysVwtDgBIXbeAzPFEWNQBxAXDgDiccrWrvWZ3wnC0xWuZuK5xBdTdpyz35IF1+8jfkY7jtrONPe1kDx+3pKj1wli+QpynhhejJHVkxH7os6TjmHXjEnuXKw7ais4n1PGD+hRRXQKBgQC89zke6O+IKg8R1m2IuYkZHceQmbThZ6apj+c2S9YEXrtzTa/yPZiBwuJP4KUo2Xrz66vIX5+6cMihvROWGcLe5IO1NlYl9q+qbrBGuC4B2m1wMwJZrFXiequaT1xKceVDJ0vaWVCUyeBeluobnDFwt/7Je7a32cYaFXi3zjSLLw==';// '你的应用私钥';
       $aop->alipayrsaPublicKey = $aop->getPublicKey($alipayCertPath);//调用getPublicKey从支付宝公钥证书中提取公钥
       $aop->apiVersion = '1.0';
       $aop->signType = 'RSA2';
       $aop->postCharset = 'utf-8';
       $aop->format = 'json';
       $aop->isCheckAlipayPublicCert = true;//是否校验自动下载的支付宝公钥证书，如果开启校验要保证支付宝根证书在有效期内
       $aop->appCertSN = $aop->getCertSN($appCertPath);//调用getCertSN获取证书序列号
       $aop->alipayRootCertSN = $aop->getRootCertSN($rootCertPath);//调用getRootCertSN获取支付宝根证书序列号
       $request = new \AlipayTradeQueryRequest();
       $request->setBizContent("{" .
           "\"out_trade_no\":\"20150320010101001\"," .
           "\"trade_no\":\"2014112611001004680 073956707\"," .
           "\"org_pid\":\"2088101117952222\"," .
           "      \"query_options\":[" .
           "        \"TRADE_SETTE_INFO\"" .
           "      ]" .
           "  }");
       $result = $aop->execute($request);
       pr($result);
      // echo $result;
       pr('111');
   }
}
