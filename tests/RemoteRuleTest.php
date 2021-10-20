<?php

namespace Maize\RemoteRule\Tests;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\MultipleRecordsFoundException;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Maize\RemoteRule\Models\RemoteRuleConfig;
use Maize\RemoteRule\Tests\Rules\EmailRule;

class RemoteRuleTest extends TestCase
{
    /** @test */
    public function it_should_throw_exception_when_config_is_empty_and_debug_is_enabled()
    {
        config()->set('remote-rule.debug', true);

        $this->expectException(ModelNotFoundException::class);

        (new EmailRule())
            ->passes('attribute', 'value');
    }

    /** @test */
    public function it_should_return_false_when_config_is_empty_and_debug_is_disabled()
    {
        config()->set('remote-rule.debug', false);

        $result = (new EmailRule)
            ->passes('attribute', 'value');

        $this->assertFalse($result);
    }

    /** @test */
    public function it_should_throw_exception_with_multiple_configs_with_same_name_and_debug_enabled()
    {
        config()->set('remote-rule.debug', true);

        RemoteRuleConfig::factory(2)->create([
            'name' => 'email_rule',
        ]);

        $this->expectException(MultipleRecordsFoundException::class);

        (new EmailRule)
            ->passes('attribute', 'value');
    }

    /** @test */
    public function it_should_return_false_with_multiple_configs_with_same_name_and_debug_disabled()
    {
        config()->set('remote-rule.debug', false);

        RemoteRuleConfig::factory(2)->create([
            'name' => 'email_rule',
        ]);

        $result = (new EmailRule)
            ->passes('attribute', 'value');

        $this->assertFalse($result);
    }

    /** @test */
    public function it_should_send_request_with_default_options()
    {
        RemoteRuleConfig::factory()->create([
            'name' => 'email_rule',
            'url' => 'https://test.test/test',
            'method' => 'POST',
        ]);

        Http::fake();

        (new EmailRule)
            ->passes('attribute', 'value');

        Http::assertSent(function (Request $request) {
            return $request->url() === 'https://test.test/test' &&
                $request->hasHeader('Content-Type', 'application/json') &&
                $request['attribute'] === 'value';
        });
    }

    /** @test */
    public function it_should_send_request_with_custom_options()
    {
        RemoteRuleConfig::factory()->create([
            'name' => 'email_rule',
            'url' => 'https://test.test/test',
            'method' => 'POST',
            'headers' => [
                'X-Test' => 'test',
            ],
            'json' => [
                'test' => 'test',
            ],
        ]);

        Http::fake();

        (new EmailRule)
            ->passes('attribute', 'value');

        Http::assertSent(function (Request $request) {
            return $request->url() === 'https://test.test/test' &&
                $request->hasHeaders([
                    'Content-Type' => 'application/json',
                    'X-Test' => 'test',
                ]) &&
                $request['attribute'] === 'value' &&
                $request['test'] === 'test';
        });
    }

    /** @test */
    public function it_should_return_true_on_successful_response()
    {
        RemoteRuleConfig::factory()->create([
            'name' => 'email_rule',
            'url' => 'https://test.test/test',
            'method' => 'POST',
        ]);

        Http::fake(
            fn ($request) => Http::response('success', 200)
        );

        $result = (new EmailRule)
            ->passes('attribute', 'value');

        $this->assertTrue($result);
    }

    /** @test */
    public function it_should_return_false_on_unsuccessful_response()
    {
        RemoteRuleConfig::factory()->create([
            'name' => 'email_rule',
            'url' => 'https://test.test/test',
            'method' => 'POST',
        ]);

        Http::fake(
            fn ($request) => Http::response('fail', 400)
        );

        $result = (new EmailRule)
            ->passes('attribute', 'value');

        $this->assertFalse($result);
    }

    /** @test */
    public function it_should_throw_validation_exception_on_unsuccessful_response()
    {
        RemoteRuleConfig::factory()->create([
            'name' => 'email_rule',
            'url' => 'https://test.test/test',
            'method' => 'POST',
        ]);

        Http::fake(
            fn ($request) => Http::response('fail', 400)
        );

        $validation = Validator::make([
            'email' => 'info@example.org',
        ], [
            'email' => [
                'string',
                'email',
                new EmailRule,
            ],
        ]);

        $this->expectException(ValidationException::class);

        $validation->validate();
    }
}
