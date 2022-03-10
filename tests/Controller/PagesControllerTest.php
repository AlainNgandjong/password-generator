<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PagesControllerTest extends WebTestCase
{
    /** @test */
    public function homepage_is_display_successfully(): void
    {
        $client = static::createClient();
        $client->request('GET', '/');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Password Generator');
        $this->assertPageTitleSame('Password Generator');

    }

    /** @test */
    public function generated_password_page_is_display_successfully(): void
    {
        $client = static::createClient();
        $client->request('GET', '/generate-password');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Generated Password 🚀');
        $this->assertPageTitleSame('Generated Password');

    }

    /** @test */
    public function cookies_are_not_present_when_visiting_homepage_for_the_first_time(): void
    {
        $client = static::createClient();
        $client->request('GET', '/');

        $this->assertBrowserNotHasCookie('app_length');
        $this->assertBrowserNotHasCookie('app_uppercase_letters');
        $this->assertBrowserNotHasCookie('app_digits');
        $this->assertBrowserNotHasCookie('app_special_characters');
    }

    /** @test */
    public function cookies_are_set_when_generating_new_password(): void
    {
        $client = static::createClient();
        $client->request('GET', '/generate-password');

        $this->assertBrowserHasCookie('app_length');
        $this->assertBrowserHasCookie('app_uppercase_letters');
        $this->assertBrowserHasCookie('app_digits');
        $this->assertBrowserHasCookie('app_special_characters');
    }

    /** @test */
    public function password_generation_form_should_work(): void
    {
        $client = static::createClient();

        $client->request('GET', '/');

        $crawler = $client->submitForm('Generate password', [], 'GET');

        $this->assertRouteSame('app_generate_password');
        $this->assertSame(12, mb_strlen( $crawler->filter('.alert.alert-success > strong')->text()));

        $client->clickLink('Go back to the homepage');
        $this->assertRouteSame('app_home');

    }

    /** @test */
    public function password_generation_form_with_values_should_work(): void
    {
        $client = static::createClient();

        $client->request('GET', '/');

        $crawler = $client->submitForm('Generate password', [
            'length' => 15,
            'uppercase_letters' => false,
            'digits' => true,
            'special_characters' => true
        ], 'GET');

        $this->assertRouteSame('app_generate_password');
        $this->assertSame(
            15, mb_strlen($crawler->filter('.alert.alert-success > strong')->text())
        );
        $crawler = $client->clickLink('Go back to the homepage');
        $this->assertRouteSame('app_home');

        $this->assertBrowserCookieValueSame('app_length', '15');
        $this->assertBrowserCookieValueSame('app_uppercase_letters', '0');
        $this->assertBrowserCookieValueSame('app_digits', '1');
        $this->assertBrowserCookieValueSame('app_special_characters', '1');

        $this->assertSame('15', $crawler->filter('select[name="length"] > option[selected]')->attr('value'));
        $this->assertCheckboxNotChecked('uppercase_letters');
        $this->assertCheckboxChecked('digits');
        $this->assertCheckboxChecked('special_characters');
    }

    /** @test */
    public function password_min_length_should_be_8(): void
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/generate-password?length=2');

        $this->assertRouteSame('app_generate_password');
        $this->assertSame(
            8, mb_strlen($crawler->filter('.alert.alert-success > strong')->text())
        );
    }

    /** @test */
    public function password_max_length_should_be_60(): void
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/generate-password?length=100');

        $this->assertRouteSame('app_generate_password');
        $this->assertSame(
            60, mb_strlen($crawler->filter('.alert.alert-success > strong')->text())
        );
    }
}
