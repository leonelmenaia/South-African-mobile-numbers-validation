<?php


use Codeception\Util\HttpCode;

/**
 * Class FileCest
 *
 * @property string $token
 */
class FileCest
{
    private $token;

    public function _before(ApiTester $apiTester)
    {
        $apiTester->haveHttpHeader('Content-Type', 'application/json');
        $apiTester->sendPOST('auth', ['username' => 'username', 'password' => 'password']);
        $response = json_decode($apiTester->grabResponse(), true);
        $this->token = $response['data']['jwt'] ?? null;

        if (empty($this->token)) {
            throw new UnexpectedValueException('Token must not be null');
        }

    }

    public function validateFileAndGetStats(ApiTester $apiTester)
    {
        $apiTester->haveHttpHeader('Authorization', 'Bearer ' . $this->token);
        $apiTester->haveHttpHeader('Content-Type', 'application/json');
        $apiTester->sendPOST('file', 'id,sms_phone
123,27831234567
123,831234561
123,831sadadaasda234dadsada5adasdadad62
123,1213131312311312');
        $apiTester->seeResponseCodeIs(HttpCode::OK);
        $apiTester->seeResponseIsJson();
        $response = json_decode($apiTester->grabResponse(), true);
        $file_id = $response['data']['id'] ?? null;

        if (empty($file_id)) {
            throw new UnexpectedValueException('File Id must not be null');
        }

        $apiTester->haveHttpHeader('Authorization', 'Bearer ' . $this->token);
        $apiTester->haveHttpHeader('Content-Type', 'application/json');
        $apiTester->sendGET('file/' . $file_id);
        $apiTester->seeResponseCodeIs(HttpCode::OK);
        $apiTester->seeResponseIsJson();

        $expected = [
            'data' => [
                'stats' => [
                    'total' => 4,
                    'validated' => 1,
                    'invalidated' => 1,
                    'validated_with_fix' => 2,
                    'percentage' => [
                        'validated' => 25,
                        'invalidated' => 25,
                        'validated_with_fix' => 50,
                    ]
                ]
            ]
        ];

        $apiTester->seeResponseContainsJson($expected);
    }

    public function validateFileMissingParams(ApiTester $apiTester)
    {
        $apiTester->haveHttpHeader('Authorization', 'Bearer ' . $this->token);
        $apiTester->sendPOST('file', null);
        $apiTester->seeResponseCodeIs(HttpCode::BAD_REQUEST);
        $apiTester->seeResponseIsJson();
        $apiTester->seeResponseContainsJson(['type' => 'MISSING_PARAMS']);
    }

    public function validateFileInvalidArgument(ApiTester $apiTester)
    {
        $apiTester->haveHttpHeader('Authorization', 'Bearer ' . $this->token);
        $apiTester->sendPOST('file', ['file' => '123456789']);
        $apiTester->seeResponseCodeIs(HttpCode::BAD_REQUEST);
        $apiTester->seeResponseIsJson();
        $apiTester->seeResponseContainsJson(['type' => 'INVALID_ARGUMENT']);
    }

    public function getFileStatsMissingParams(ApiTester $apiTester)
    {
        $apiTester->haveHttpHeader('Authorization', 'Bearer ' . $this->token);
        $apiTester->sendGET('file');
        $apiTester->seeResponseCodeIs(HttpCode::NOT_FOUND);
        $apiTester->seeResponseIsJson();
    }

    public function getFileStatsInvalidId(ApiTester $apiTester)
    {
        $apiTester->haveHttpHeader('Authorization', 'Bearer ' . $this->token);
        $apiTester->sendGET('file/999999999');
        $apiTester->seeResponseCodeIs(HttpCode::BAD_REQUEST);
        $apiTester->seeResponseIsJson();
        $apiTester->seeResponseContainsJson(['type' => 'INVALID_ID']);

    }
}
