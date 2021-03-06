<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

//Request::setTrustedProxies(array('127.0.0.1'));

/* *********************
* Default Controller
********************* */
/* homepage */
$app->get('/', 'Controller\DefaultController::homepageAction')
    ->bind('homepage');
/* support */
$app->get('/support', 'Controller\DefaultController::supportAction')
    ->bind('support');
/* about */
$app->get('/about', 'Controller\DefaultController::aboutAction')
    ->bind('about');
/* dashboard */
$app->get('/dashboard', 'Controller\DefaultController::dashboardAction')
    ->bind('dashboard');

/* *********************
* Comment Controller
********************* */
/* comment */
$app->match('/comment/{loanId}', 'Controller\CommentController::commentAction')
    ->bind('comment');

/* *********************
* User Controller
********************* */
/* profile edit */
$app->match('/profile', 'Controller\UserController::editAction')
    ->bind('profile');
/* profile */
$app->get('/user/{username}', 'Controller\UserController::showAction')
    ->bind('user');
/* signin */
$app->get('/signin', 'Controller\UserController::signinAction')
    ->bind('signin');
/* signup */
$app->match('/signup', 'Controller\UserController::signupAction')
    ->bind('signup');
/* reset */
$app->match('/reset', 'Controller\UserController::resetAction')
    ->bind('reset');
/* admin panel */
$app->get('/admin/user', 'Controller\UserController::adminPanelAction')
    ->bind('adminPanel');
/* delete */
$app->delete('/admin/user/{userId}', 'Controller\UserController::deleteAction')
    ->bind('deleteUser');

/* *********************
* Item Controller
********************* */
$app->match('/item/add', 'Controller\ItemController::addAction')
    ->bind('addItem');
/* edit item */
/* item */
$app->get('/item/{itemId}', 'Controller\ItemController::showAction')
    ->bind('item');
/* add item */
$app->match('/item/edit/{itemId}', 'Controller\ItemController::editAction')
    ->bind('editItem');
/* delete item */
$app->delete('/item/{itemId}', 'Controller\ItemController::deleteAction')
    ->bind('deleteItem');
/* search item */
$app->get('/search', 'Controller\ItemController::searchAction')
    ->bind('searchItem');

/* *********************
* Loan Controller
********************* */
/* request loan - borrower action, creates loan object */
$app->match('/loan/request/{itemId}', 'Controller\LoanController::requestAction')
    ->bind('requestItem');
/* accept loan - owner action */
$app->patch('/loan/accept/{loanId}', 'Controller\LoanController::acceptAction')
    ->bind('acceptRequest');
/* reject loan  - owner action */
$app->patch('/loan/reject/{loanId}', 'Controller\LoanController::rejectAction')
    ->bind('rejectRequest');
/* close loan  - owner action */
$app->patch('/loan/close/{loanId}', 'Controller\LoanController::closeAction')
    ->bind('closeLoan');
/* close loan  - borrower action, borrower cancels their request, should not possible after loan is accepted */
$app->patch('/loan/cancel/{loanId}', 'Controller\LoanController::cancelAction')
    ->bind('cancelRequest');


/* *********************
* Error route
********************* */
$app->error(function (\Exception $e, Request $request, $code) use ($app) {
    if ($app['debug']) {
        return;
    }

    // 404.html, or 40x.html, or 4xx.html, or error.html
    $templates = array(
        'errors/'.$code.'.html.twig',
        'errors/'.substr($code, 0, 2).'x.html.twig',
        'errors/'.substr($code, 0, 1).'xx.html.twig',
        'errors/default.html.twig',
    );

    return new Response($app['twig']->resolveTemplate($templates)->render(array('code' => $code)), $code);
});
