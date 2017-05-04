<?php

use Dot\Authentication\Web\ErrorHandler\UnauthorizedHandler;
use Dot\Navigation\NavigationMiddleware;
use Dot\Rbac\Guard\Middleware\ForbiddenHandler;
use Dot\Rbac\Guard\Middleware\RbacGuardMiddleware;
use Dot\Session\SessionMiddleware;
use Dot\User\Middleware\AutoLogin;
use Zend\Expressive\Helper\ServerUrlMiddleware;
use Zend\Expressive\Helper\UrlHelperMiddleware;
use Zend\Expressive\Middleware\ImplicitHeadMiddleware;
use Zend\Expressive\Middleware\ImplicitOptionsMiddleware;
use Zend\Expressive\Middleware\NotFoundHandler;
use Zend\Stratigility\Middleware\ErrorHandler;

/**
 * Setup middleware pipeline:
 */

// The error handler should be the first (most outer) middleware to catch
// all Exceptions.
/** @var \Zend\Expressive\Application $app */
$app->pipe(ErrorHandler::class);
$app->pipe(ServerUrlMiddleware::class);

// starts the session and tracks session activity
$app->pipe(SessionMiddleware::class);

// automatically login the user if it has a valid remember token
$app->pipe(AutoLogin::class);

// Pipe more middleware here that you want to execute on every request:
// - bootstrapping
// - pre-conditions
// - modifications to outgoing responses

// Register the routing middleware in the middleware pipeline
$app->pipeRoutingMiddleware();

// zend expressive middleware
$app->pipe(ImplicitHeadMiddleware::class);
$app->pipe(ImplicitOptionsMiddleware::class);
$app->pipe(UrlHelperMiddleware::class);

// authentication and authorization error handlers
// this is piped here to have access to the route result
// it should be ok, as these particular errors are generated from below middleware or routed middleware
$app->pipe(ForbiddenHandler::class);
$app->pipe(UnauthorizedHandler::class);

// Add more middleware here that needs to introspect the routing results; this
// ...

// navigation middleware makes sure the navigation service is injected the RouteResult
$app->pipe(NavigationMiddleware::class);

// the RBAC guards protect chunks of the application(routes or controllers or controller actions)
// the authorization service can be used together with the guards for maximum security and finer control
$app->pipe(RbacGuardMiddleware::class);

// Register the dispatch middleware in the middleware pipeline
$app->pipeDispatchMiddleware();

// At this point, if no Response is return by any middleware, the
// NotFoundHandler kicks in; alternately, you can provide other fallback
// middleware to execute.
$app->pipe(NotFoundHandler::class);
