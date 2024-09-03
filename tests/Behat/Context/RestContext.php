<?php

declare(strict_types=1);

namespace App\Tests\Behat\Context;

use App\AccountManager\Infrastructure\Doctrine\Repository\UserRepository;
use App\Common\Infrastructure\Symfony\Security\Firewall;
use App\Tests\Behat\Helper\JsonTraverseTrait;
use App\Tests\Behat\Helper\StructureTraversePath;
use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Step\Given;
use Behat\Step\Then;
use Behat\Step\When;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\HttpFoundation\Response;
use Webmozart\Assert\Assert;

final class RestContext implements Context
{
    use JsonTraverseTrait;

    private readonly KernelBrowser $browser;
    //    private readonly UserRepository $userRepository;

    private ?Response $response = null;

    /** @var array<string, string> */
    private array $headers = [];

    public function __construct(KernelBrowser $browser /* UserRepository $userRepository */)
    {
        $this->browser = $browser;
        //        $this->userRepository = $userRepository;
    }

    //    #[Given('I am authenticated as :email')]
    //    public function iAmAuthenticatedAs(string $email): void
    //    {
    //        $user = $this->userRepository->findOneBy(['email' => $email]);
    //        Assert::notNull($user);
    //
    //        $this->browser->loginUser($user, Firewall::NAME_API);
    //    }

    #[Given('I set up HTTP header :name with value :value')]
    public function iSetupHeader(string $name, string $value): void
    {
        $this->headers["HTTP_{$name}"] = $value;
    }

    #[When('I send a :method request to :url')]
    public function iSendRequest(string $method, string $url): void
    {
        $this->browser->request($method, $url, server: $this->headers);
        $this->response = $this->browser->getResponse();
    }

    #[Then('I dump the response')]
    public function iDumpTheResponse(): void
    {
        Assert::notNull($this->response, 'Response does not exist. Did you sent the request?');
        Assert::notNull($this->response->getContent(), 'Response content is null.');
        Assert::notEmpty($this->response->getContent(), 'Response content is empty. It is valid only for 204 HTTP response.');

        dump($this->response->getContent());
    }

    #[Then('I expect the path :path in the response to be type :type')]
    public function iExpectThePathInTheResponseToBeType(string $path, string $expectedType): void
    {
        $response = $this->getResponseContentAsArray();
        Assert::notNull($response);

        $value = $this->traverse($response, StructureTraversePath::fromString($path));

        switch ($expectedType) {
            case 'int':
                Assert::integer($value);

                break;

            case 'string':
                Assert::string($value);

                break;
        }
    }

    #[Then('I expect the path :path in the response to be boolean :expected_boolean_value')]
    public function iWantThePathInTheResponseToBeBoolean(string $path, bool $expectedBooleanValue): void
    {
        $response = $this->getResponseContentAsArray();
        Assert::notNull($response);

        $actualValue = $this->traverse($response, StructureTraversePath::fromString($path));

        Assert::same($actualValue, $expectedBooleanValue);
    }

    #[Then('I expect the path :path in the response to have value :expectedValue')]
    public function iExpectThePathInTheResponseToHaveValue(string $path, mixed $expectedValue): void
    {
        $response = $this->getResponseContentAsArray();
        Assert::notNull($response);

        $actualValue = $this->traverse($response, StructureTraversePath::fromString($path));

        Assert::same($actualValue, $expectedValue);
    }

    #[Then('I expect the path :path in the response to be:')]
    public function iExpectThePathInTheResponseToBe(string $path, PyStringNode $content): void
    {
        $response = $this->getResponseContentAsArray();
        Assert::notNull($response);

        $actualValue = $this->traverse($response, StructureTraversePath::fromString($path));
        $expectedValue = json_decode($content->getRaw(), true);

        Assert::same(JSON_ERROR_NONE, json_last_error());
        Assert::same($actualValue, $expectedValue);
    }

    #[Then('I expect response status code to be :code')]
    public function iExpectResponseStatusCodeToBe(int $code): void
    {
        Assert::notNull($this->response);
        Assert::same($code, $this->response->getStatusCode());
    }

    #[Then('I expect the response content to be:')]
    public function iExpectTheResponseContentToBe(PyStringNode $body): void
    {
        $expected = $this->getResponseContentAsArray();
        $actual = json_decode($body->getRaw(), true);

        Assert::same(JSON_ERROR_NONE, json_last_error());
        Assert::eq($actual, $expected);
    }

    /** @return null|mixed[] */
    private function getResponseContentAsArray(): ?array
    {
        Assert::notNull($this->response, 'Response does not exist. Did you sent the request?');
        Assert::notNull($this->response->getContent(), 'Response content is null.');
        Assert::notEmpty($this->response->getContent(), 'Response content is empty. It is valid only for 204 HTTP response.');

        if (Response::HTTP_NO_CONTENT === $this->response->getStatusCode()) {
            return null;
        }

        $content = json_decode($this->response->getContent(), true);
        Assert::same(
            JSON_ERROR_NONE,
            json_last_error(),
            'Failed decoding the response content from JSON. Is the response showing the HTML instead of JSON?'
        );

        return $content;
    }
}
