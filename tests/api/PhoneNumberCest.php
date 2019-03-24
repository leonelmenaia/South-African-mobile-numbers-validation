<?php


use Codeception\Util\HttpCode;

/**
 * Class PhoneNumberCest
 *
 * @property string $token
 */
class PhoneNumberCest
{

    private $token;

    public function _before(ApiTester $apiTester)
    {
        $apiTester->haveHttpHeader('Content-Type', 'application/json');
        $apiTester->sendPOST('auth', ['username' => 'username', 'password' => 'password']);
        $response = json_decode($apiTester->grabResponse(),true);
        $this->token = $response['data']['jwt'] ?? null;

        if(empty($this->token)){
            throw new UnexpectedValueException('Token must not be null');
        }

    }

    public function missingParams(ApiTester $apiTester)
    {
        $apiTester->haveHttpHeader('Authorization', 'Bearer ' . $this->token);
        $apiTester->haveHttpHeader('Content-Type', 'application/json');
        $apiTester->sendPOST('phone', ['random' => true]);
        $apiTester->seeResponseCodeIs(HttpCode::BAD_REQUEST);
        $apiTester->seeResponseIsJson();
        $apiTester->seeResponseContainsJson(['type' => 'MISSING_PARAMS']);
    }

    public function invalidParams(ApiTester $apiTester)
    {
        $apiTester->haveHttpHeader('Authorization', 'Bearer ' . $this->token);
        $apiTester->haveHttpHeader('Content-Type', 'application/json');
        $apiTester->sendPOST('phone', ['number' => true]);
        $apiTester->seeResponseCodeIs(HttpCode::BAD_REQUEST);
        $apiTester->seeResponseIsJson();
        $apiTester->seeResponseContainsJson(['type' => 'INVALID_NUMBER']);
    }

    public function validateCorrectNumber(ApiTester $apiTester)
    {
        $apiTester->haveHttpHeader('Authorization', 'Bearer ' . $this->token);
        $apiTester->haveHttpHeader('Content-Type', 'application/json');
        $apiTester->sendPOST('phone', ['number' => '27831234567']);
        $apiTester->seeResponseCodeIs(HttpCode::OK);
        $apiTester->seeResponseIsJson();
    }

    public function validateWithoutToken(ApiTester $apiTester)
    {
        $apiTester->haveHttpHeader('Content-Type', 'application/json');
        $apiTester->sendPOST('phone', ['number' => '27831234567']);
        $apiTester->seeResponseCodeIs(HttpCode::UNAUTHORIZED);
        $apiTester->seeResponseIsJson();
    }


}
