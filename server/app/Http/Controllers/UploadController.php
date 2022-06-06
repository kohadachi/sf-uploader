<?php

namespace App\Http\Controllers;

use App\Http\Request\Upload\UploadFormUpdateRequest;
use App\Http\Response\AccommodationForm\AccommodationFormIndexResponse;
use App\Usecase\Account\User\AccommodationForm\AccommodationFormIndexUsecase;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

class UploadController extends Controller
{
    /**
     * ユーザー側 ホテル情報を取得する
     *
     * @group アカウント
     */
    public function index()
    {
        // $usecase($request);
        // $result = $usecase->index();

        return "test";
    }

    /**
     * ユーザー側 ホテル情報を取得する
     *
     * @group アカウント
     */
    public function store(UploadFormUpdateRequest $request)
    {
        $fileName = $request->file->getClientOriginalName();
				$content = base64_encode(file_get_contents($request->file->getRealPath()));
//↑ここまでアクセストークン取得　。　以下は$this->tokens['access_token']を使うものとして書いています。↑

	  $cvJson = [
	    "Title" => $fileName,  //アップされるファイルのタイトル。拡張子込み
	    "PathOnClient" => $fileName,
	    "VersionData" => $content,
	  ];
		var_dump($content);
	  //Salesforceにcontent versionとしてアップ。成功したらcontentVirsionのIDが還ってくる。
	  $client  = new Client();
				$url = "https://jasmine-lc.my.salesforce.com/services/data/v54.0/sobjects/ContentVersion/";
				$params = [
					'headers' => [
						'Authorization' => 'OAuth ' . "",
						'Content-Type' => 'application/json',
					],
					'body'=>json_encode($cvJson),
				];
				try{
					$resp = $client->request('POST', $url, $params);
					$code = $resp->getStatusCode(); // 200
					$reason = $resp->getReasonPhrase();
					$result = json_decode($resp->getBody()->getContents(),true);
					$contentVirsionId = $result["id"];
				} catch (ClientException $exception) {
					$response = $exception->getMessage();
					var_dump($response);
			}
	//   //content versionに紐付いて作成されたcontent documentのIDを取得、リーダー機能をつかって検索する
				  $sql="SELECT ContentDocumentId FROM ContentVersion WHERE Id='{$contentVirsionId}'";
				  $url = "https://jasmine-lc.my.salesforce.com/services/data/v54.0/query";
				  $params = [
				    'headers' => [
				      'Authorization' => 'OAuth ' . ""
				    ],
				    'query' => [ 'q' => $sql ]
				  ];
				  try {
				    $resp = $client->request ( 'GET', $url, $params );
				    $result = json_decode($resp->getBody()->getContents(),true);
				    $cdoc_id = $result["records"][0]["ContentDocumentId"];
				    if(empty($cdoc_id)){
				      return false;
				    }
				  } catch (ClientException $exception) {
						$response = $exception->getResponse();
				}

	//   //指定のオブジェクトアイテムのIDを用いて、contentDocumentのリンクを張る
					$url = "https://jasmine-lc.my.salesforce.com/services/data/v54.0/sobjects/ContentDocumentLink/";
					$linkJson = [
						"ContentDocumentId"=>$cdoc_id,
						"LinkedEntityId"=>$request->account_id, //アイテムを指定しなくてもレコードIDだけで可能
						"Visibility"=>"AllUsers" //閲覧できる人の制限
					];
					//REST APIを実行する。成功したらcontentVirsionのIDが還ってくる。
					$params = [
						'headers' => [
							'Authorization' => 'OAuth ' . "",
							'Content-Type' => 'application/json',
						],
						'body'=>json_encode($linkJson),
					];
					try{
						$resp = $client->request('POST', $url, $params);
					} catch (ClientException $exception) {
						$response = $exception->getResponse();
				}
					return $resp->getStatusCode();

        return "test";
    }
}
