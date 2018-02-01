<?php
header ( 'Content-type:text/html;charset=utf-8' );
include_once $_SERVER ['DOCUMENT_ROOT'] . '/upacp_demo_app/sdk/acp_service.php';

/**
 * 对控件给商户APP返回的应答信息验签，前段请直接把string型的json串post上来
 */
$data = file_get_contents('php://input', 'r');
echo validateAppResponse($data) ? "true" : "false";

/**
 * 对控件支付成功返回的结果信息中data域进行验签
 * @param $jsonData json格式数据，例如：{"sign" : "J6rPLClQ64szrdXCOtV1ccOMzUmpiOKllp9cseBuRqJ71pBKPPkZ1FallzW18gyP7CvKh1RxfNNJ66AyXNMFJi1OSOsteAAFjF5GZp0Xsfm3LeHaN3j/N7p86k3B1GrSPvSnSw1LqnYuIBmebBkC1OD0Qi7qaYUJosyA1E8Ld8oGRZT5RR2gLGBoiAVraDiz9sci5zwQcLtmfpT5KFk/eTy4+W9SsC0M/2sVj43R9ePENlEvF8UpmZBqakyg5FO8+JMBz3kZ4fwnutI5pWPdYIWdVrloBpOa+N4pzhVRKD4eWJ0CoiD+joMS7+C0aPIEymYFLBNYQCjM0KV7N726LA==",  "data" : "pay_result=success&tn=201602141008032671528&cert_id=68759585097"}
 * @return 是否成功
 */
function validateAppResponse($jsonData) {

	$data = json_decode($jsonData);
	$sign = $data->sign;
	$data = $data->data;
    $public_key = openssl_x509_read(file_get_contents("d:/certs/acp_test_app_verify_sign.cer"));//TODO，这个是测试环境的证书，切换生产需要改生产证书。
	$signature = base64_decode ( $sign );
	$params_sha1x16 = sha1 ( $data, FALSE );
	$isSuccess = openssl_verify ( $params_sha1x16, $signature,$public_key, OPENSSL_ALGO_SHA1 );
	return $isSuccess;
}
