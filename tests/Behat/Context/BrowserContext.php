<?php

declare(strict_types=1);

namespace App\Tests\Behat\Context;

use App\AccountManager\Infrastructure\Doctrine\Entity\User\User;
use App\AccountManager\Infrastructure\Doctrine\Repository\UserRepository;
use App\Common\Infrastructure\Symfony\Security\Firewall;
use App\Tests\Behat\Helper\HttpMethod;
use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Behat\Step\Given;
use Behat\Step\Then;
use Behat\Step\When;
use PHPUnit\Framework\Assert;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;

final class BrowserContext implements Context
{
    private readonly KernelBrowser $browser;
    //    private readonly UserRepository $userRepository;

    private ?Response $response = null;
    private ?Crawler $crawler = null;
    //    private ?User $currentUser = null;

    public function __construct(KernelBrowser $browser/* , UserRepository $userRepository */)
    {
        $this->browser = $browser;
        //        $this->userRepository = $userRepository;
    }

    #[Given('I am signed in as :email')]
    public function iAmSignedInAs(string $email): void
    {
        $user = $this->userRepository->findOneBy(['email' => $email]);
        Assert::assertInstanceOf(User::class, $user, 'User with this email was not found.');

        $this->browser->loginUser($user, Firewall::NAME_GUI);
        $this->currentUser = $user;
    }

    #[Given('I follow redirects')]
    public function iFollowRedirects(): void
    {
        $this->browser->followRedirects();
    }

    #[Given('I follow redirect')]
    public function iFollowRedirect(): void
    {
        $this->browser->followRedirect();
    }

    #[Given('I collect request profile')]
    public function iCollectRequestProfile(): void
    {
        $this->browser->enableProfiler();
    }

    #[When('I go to :url')]
    public function iGoTo(string $url): void
    {
        $this->crawler = $this->browser->request(HttpMethod::GET->value, $url);
        $this->response = $this->browser->getResponse();
    }

    #[When('I submit two-factor authentication form with:')]
    public function iSubmitTwoFactorAuthenticationForm(PyStringNode $node): void
    {
        $values = json_decode($node->getRaw(), true);

        $this->browser->followRedirects();

        $crawler = $this->browser->submitForm('Check code', $values);
        $this->crawler = $crawler;
    }

    #[Then('I expect redirect to :url')]
    public function iExpectRedirectTo(string $url): void
    {
        \Webmozart\Assert\Assert::notNull($this->response, 'No response found.');
        $locationHeaderContent = $this->response->headers->get('Location');
        Assert::assertNotNull($locationHeaderContent, 'No location header found.');

        $hostLessUrl = str_replace('http://localhost', '', $locationHeaderContent);
        Assert::assertSame($url, $hostLessUrl);
    }

    #[Then('I expect to be on :url')]
    public function iExpectToBeOn(string $url): void
    {
        Assert::assertNotNull($this->response, 'No response found.');

        $currentUrl = $this->browser->getHistory()->current()->getUri();

        $hostLessUrl = str_replace('http://localhost', '', $currentUrl);
        Assert::assertSame($url, $hostLessUrl);
    }

    /**
     * @param non-empty-string $url
     */
    #[Then('I expect to be on url starting with :url')]
    public function iExpectToBeOnUrlStartingWith(string $url): void
    {
        Assert::assertNotNull($this->response, 'No response found.');

        $currentUrl = $this->browser->getHistory()->current()->getUri();

        $hostLessUrl = str_replace('http://localhost', '', $currentUrl);
        Assert::assertStringStartsWith($url, $hostLessUrl);
    }

    #[Then('I expect response status code to be :code')]
    public function iExpectResponseStatusCodeToBe(int $code): void
    {
        Assert::assertNotNull($this->response);
        Assert::assertSame($code, $this->response->getStatusCode());
    }

    #[Then('I submit the :form_name form with:')]
    public function iSubmitTheFormWith(string $formName, TableNode $table): void
    {
        $rows = $table->getRows();

        $formData = [];
        foreach ($rows as $row) {
            $key = $this->parseFormFieldName($formName, $row[0]);
            $formData[$key] = $row[1];
        }

        $this->browser->followRedirects();
        $crawler = $this->browser->submitForm("{$formName}[save]", $formData);
        $this->crawler = $crawler;
    }

    #[Then('I click :name')]
    public function iClickLink(string $name): void
    {
        $this->browser->followRedirects();
        $this->crawler = $this->browser->clickLink($name);
    }

    #[Then('I expect :amount email is sent')]
    public function iExpectEmailIsSent(int $amount): void
    {
        $profile = $this->browser->getProfile();
        Assert::assertNotFalse($profile);
        Assert::assertNotNull($profile, 'Request profile is null. Did you enabled profiling?');

        /** @var MessageDataCollector $emailCollector */
        $emailCollector = $profile->getCollector('mailer');
        $messageEvents = $emailCollector->getEvents();

        // Symfony produces two events for each email message
        Assert::assertCount($amount * 2, $messageEvents->getMessages());
    }

    #[Then('I expect cookie :name is sent with content:')]
    public function iHaveCookie(string $name, PyStringNode $content): void
    {
        $cookieJar = $this->browser->getCookieJar();
        $cookie = $cookieJar->get($name);

        Assert::assertNotNull($cookie);

        $minimizedJson = json_encode(json_decode($content->getRaw(), true));
        Assert::assertSame($minimizedJson, $cookie->getValue());
    }

    #[Then('I expect not having any cookies')]
    public function iExpectNotHavingAnyCookies(): void
    {
        $cookieJar = $this->browser->getCookieJar();
        $cookies = $cookieJar->all();

        // symfony mock session id cookie will be always
        Assert::assertCount(1, $cookies);
    }

    #[Given('I set :name cookie with content:')]
    public function iSetCookieWithContent(string $name, PyStringNode $content): void
    {
        $minimizedJson = json_encode(json_decode($content->getRaw(), true));

        Assert::assertIsString($minimizedJson);

        $cookieJar = $this->browser->getCookieJar();
        $cookieJar->set(new Cookie($name, $minimizedJson));
    }

    #[Then('I expect :name input to be :value')]
    public function iExpectInputToBe(string $name, string $value): void
    {
        Assert::assertNotNull($this->crawler);
        $emailField = $this->crawler->filter($name);

        Assert::assertSame($value, $emailField->attr('value'));
    }

    #[Then('I dump the response')]
    public function iDumpTheResponse(): void
    {
        \Webmozart\Assert\Assert::notNull($this->response, 'Response does not exist. Did you sent the request?');
        Assert::assertNotEmpty($this->response->getContent(), 'Response content is empty. It is valid only for 204 HTTP response.');

        dump($this->response->getContent());
    }

    #[Given('I have :level flash message :translationKey')]
    public function iHaveFlashMessage(string $level, string $translationKey): void
    {
        $session = $this->browser->getRequest()->getSession();

        /** @var FlashBagInterface $flashes */
        $flashes = $session->getBag('flashes');

        Assert::assertTrue($flashes->has($level));
        Assert::assertTrue(
            in_array($translationKey, $flashes->get($level), true),
            "Flash message '{$translationKey}' of level '{$level}' was not found."
        );
    }

    #[Given('I do not follow redirects')]
    public function iDoNotFollowRedirects(): void
    {
        $this->browser->followRedirects(false);
    }

    #[Then('I see a :type flash message with message :content')]
    public function iSeeASuccessFlashMessageWithMessage(string $type, string $content): void
    {
        Assert::assertNotNull($this->crawler, 'Crawler is null, is the request made?');

        $classSelector = '.alert-'.$type;
        $element = $this->crawler->filter($classSelector);
        Assert::assertEquals(1, $element->count(), 'Element not found in DOM, is the selector correct?');

        Assert::assertSame(
            $content,
            trim($element->text()),
            'Element was found, but the content is different than expected.'
        );
    }

    #[Then('I see :count form error')]
    #[Then('I see :count form errors')]
    public function iSeeFormErrors(int $count): void
    {
        Assert::assertNotNull($this->crawler, 'Crawler is null, is the request made?');
        Assert::assertCount($count, $this->crawler->filter('.invalid-feedback'));
    }

    #[Then('I see form error message :content')]
    public function iSeeFormErrorMessage(string $content): void
    {
        Assert::assertNotNull($this->crawler, 'Crawler is null, is the request made?');

        $elements = $this->crawler->filter('.invalid-feedback');
        Assert::assertGreaterThan(0, $elements->count(), 'No form errors found on the page.');

        $formErrorMessages = $elements->each(fn (Crawler $node) => $node->text());

        Assert::assertTrue(
            in_array($content, $formErrorMessages, true),
            'Some form errors exists on the page, but the expected error message was not found.'
        );
    }

    #[Then('I dump the form errors')]
    public function dumpFormErrors(): void
    {
        Assert::assertNotNull($this->crawler, 'Crawler is null, is the request made?');

        $elements = $this->crawler->filter('.invalid-feedback');
        Assert::assertGreaterThan(0, $elements->count(), 'No form errors found on the page.');

        $formErrorMessages = $elements->each(fn (Crawler $node) => $node->text());

        dump($formErrorMessages);
    }

    private function parseFormFieldName(string $name, string $field): string
    {
        if (str_contains($field, '.')) {
            $elements = explode('.', $field);

            $converted = $name;
            foreach ($elements as $element) {
                $converted .= "[{$element}]";
            }

            return $converted;
        }

        return "{$name}[{$field}]";
    }

    #[Given('I expect user to have role :role')]
    public function iExpectUserToHaveRole(string $role): void
    {
        Assert::assertNotNull($this->currentUser);
        $user = $this->userRepository->find($this->currentUser->getId());
        Assert::assertNotNull($user);
        $roles = $user->getRoles();

        Assert::assertTrue(in_array($role, $roles, true), 'Role was not found.');
    }

    #[Given('I expect user to not have role :role')]
    public function iExpectUserToNotHaveRole(string $role): void
    {
        Assert::assertNotNull($this->currentUser);
        $user = $this->userRepository->find($this->currentUser->getId());
        Assert::assertNotNull($user);
        $roles = $user->getRoles();

        Assert::assertFalse(in_array($role, $roles, true), 'Role was not found.');
    }
}
