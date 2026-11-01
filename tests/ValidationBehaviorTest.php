<?php

declare(strict_types=1);

namespace Zana\Validation\Tests;

use Nyholm\Psr7\Response;
use Nyholm\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zana\Container\Contracts\ServiceCollectionInterface;
use Zana\Container\Contracts\ServiceResolverInterface;
use Zana\Core\Application;
use Zana\Http\HttpKernel;
use Zana\Http\HttpServiceProvider;
use Zana\Mediator\Attribute\Handles;
use Zana\Mediator\HandlerMap;
use Zana\Mediator\MediatorInterface;
use Zana\Mediator\MediatorServiceProvider;
use Zana\Validation\Constraint\Email;
use Zana\Validation\Constraint\NotBlank;
use Zana\Validation\ValidationBehavior;
use Zana\Validation\ValidationException;
use Zana\Validation\ValidationServiceProvider;

/**
 * FE-53, ADR-025: once listed in mediator.behaviors, every message is
 * validated before its handler runs, and over HTTP the failure is a 422 with
 * the field errors.
 */
final class ValidationBehaviorTest extends TestCase
{
    public function testMessagesAreValidatedBeforeTheirHandlerRuns(): void
    {
        $app = self::app(['mediator.behaviors' => [ValidationBehavior::class]]);
        $mediator = $app->container()->createScope()->get(MediatorInterface::class);
        assert($mediator instanceof MediatorInterface);

        $this->assertSame('invited ada@example.com', $mediator->send(new InviteMember('ada@example.com', 'Ada')));

        try {
            $mediator->send(new InviteMember('not an address', ''));
            $this->fail('An invalid command must not reach its handler.');
        } catch (ValidationException $e) {
            $this->assertSame(['email' => ['This value is not a valid email address.'], 'name' => ['This value should not be blank.']], $e->violations->toArray());
        }
        $this->assertSame(1, InviteMemberHandler::$calls, 'the handler ran for the valid command only');
    }

    public function testOverHttpTheFailureIsA422WithTheFieldErrors(): void
    {
        $kernel = self::app(['mediator.behaviors' => [ValidationBehavior::class]])->container()->get(HttpKernel::class);
        assert($kernel instanceof HttpKernel);

        $response = $kernel->handle(new ServerRequest('POST', '/members', ['Accept' => 'application/json']));

        $this->assertSame(422, $response->getStatusCode());
        $body = json_decode((string) $response->getBody(), true);
        $this->assertIsArray($body);
        $this->assertSame(['email' => ['This value is not a valid email address.'], 'name' => ['This value should not be blank.']], $body['errors']);
        $this->assertSame(0, InviteMemberHandler::$calls);
    }

    public function testWithoutTheBehaviourNothingIsValidated(): void
    {
        $mediator = self::app([])->container()->createScope()->get(MediatorInterface::class);
        assert($mediator instanceof MediatorInterface);

        $this->assertSame('invited not an address', $mediator->send(new InviteMember('not an address', '')), 'opt-in: listed in mediator.behaviors or not at all');
    }

    /**
     * @param array<string, mixed> $config
     */
    private static function app(array $config): \Zana\Core\ApplicationInterface
    {
        InviteMemberHandler::$calls = 0;
        $app = Application::configure(sys_get_temp_dir() . '/zana-validation-behavior')
            ->withEnvironment('testing')
            ->withProviders(HttpServiceProvider::class, MediatorServiceProvider::class, ValidationServiceProvider::class)
            ->withConfiguration($config)
            ->withServices(static fn (ServiceCollectionInterface $s) => $s
                ->scoped(InviteMemberHandler::class)
                // A request handler that sends an invalid command, as an API action would.
                ->replace(HttpKernel::HANDLER, static fn (ServiceResolverInterface $r): RequestHandlerInterface => new SendsAnInvalidInvitation($r->get(MediatorInterface::class))))
            ->build();
        $app->container()->get(HandlerMap::class)->register(InviteMemberHandler::class);

        return $app;
    }
}

final class InviteMember
{
    public function __construct(
        #[NotBlank, Email]
        public readonly string $email,
        #[NotBlank]
        public readonly string $name,
    ) {
    }
}

final readonly class SendsAnInvalidInvitation implements RequestHandlerInterface
{
    public function __construct(private MediatorInterface $mediator)
    {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $this->mediator->send(new InviteMember('not an address', ''));

        return new Response(204);
    }
}

#[Handles(InviteMember::class)]
final class InviteMemberHandler
{
    public static int $calls = 0;

    public function __invoke(InviteMember $command): string
    {
        self::$calls++;

        return "invited {$command->email}";
    }
}
