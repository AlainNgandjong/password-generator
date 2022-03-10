<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PagesControllerTest extends WebTestCase
{
    /** @test */
    public function homepage_should_work(): void
    {
        $client = static::createClient();
        $client->request('GET', '/');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Password Generator');
        $this->assertPageTitleSame('Password Generator');

    }

    /** @test */
    public function homepage_password_page_should_work(): void
    {
        $client = static::createClient();
        $client->request('GET', '/generate-password');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Generated Password 🚀');
        $this->assertPageTitleSame('Generated Password');

    }

    /** @test */
    public function first_time_homepage_visit_shouldnt_have_cookies_set(): void
    {
        $client = static::createClient();
        $client->request('GET', '/');

        $this->assertBrowserNotHasCookie('app_length');
        $this->assertBrowserNotHasCookie('app_uppercase_letters');
        $this->assertBrowserNotHasCookie('app_digits');
        $this->assertBrowserNotHasCookie('app_special_characters');
    }

    /** @test */
    public function first_time_form_submission_should_cookies_set(): void
    {
        $client = static::createClient();
        $client->request('GET', '/generate-password');

        $this->assertBrowserHasCookie('app_length');
        $this->assertBrowserHasCookie('app_uppercase_letters');
        $this->assertBrowserHasCookie('app_digits');
        $this->assertBrowserHasCookie('app_special_characters');
    }
}
