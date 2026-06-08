<?php

use App\Core\Router;

$router = new Router();

// Public routes
$router->get('/', 'AuthController@welcome');
$router->get('/about', 'AuthController@about');
$router->get('/how_it_work', 'AuthController@howItWork');
// $router->get('/proof_of_funds', 'AuthController@proofOfFunds');
$router->get('/contact', 'AuthController@contact');

// Onboarding request
$router->post('/request-access', 'AuthController@requestAccess');
$router->get('/register', 'AuthController@showRegistrationForm');
$router->post('/register', 'AuthController@registerProfile');

// Authentication routes
$router->get('/login', 'AuthController@showLoginForm');
$router->post('/login', 'AuthController@login');
$router->post('/logout', 'AuthController@logout');

// Interactive simulated mailbox API
$router->get('/api/simulated-emails', 'AuthController@getSimulatedEmails');
$router->post('/api/simulated-emails/clear', 'AuthController@clearSimulatedEmails');

// Interactive stepper registration progress save
$router->post('/api/register/save', 'AuthController@saveApplicationProgress');
$router->post('/api/profile/upload-file', 'AuthController@uploadProfileFile', ['auth']);

// Recovery engine routes
$router->get('/recovery', 'AuthController@showRecoveryForm');
$router->post('/recovery', 'AuthController@sendRecoveryLink');
$router->get('/recovery/reset', 'AuthController@showResetForm');
$router->post('/recovery/reset', 'AuthController@resetPassword');

// 2FA Challenge loop routes
$router->get('/login/2fa', 'AuthController@showLogin2FA');
$router->post('/login/2fa', 'AuthController@verify2FA');

// Gated customer routes
$router->get('/dashboard', 'AuthController@dashboard', ['auth', 'gated2fa']);
$router->get('/profile', 'AuthController@showProfile', ['auth', 'gated2fa']);
$router->post('/profile/request-edit', 'AuthController@requestProfileEdit', ['auth', 'gated2fa']);
$router->post('/profile/update', 'AuthController@submitProfileUpdate', ['auth', 'gated2fa']);
$router->get('/buy-usdt', 'AuthController@showBuyUSDTForm', ['auth', 'gated2fa']);
$router->post('/buy-usdt', 'AuthController@submitBuyUSDTRequest', ['auth', 'gated2fa']);
$router->get('/sell-usdt', 'AuthController@showSellUSDTForm', ['auth', 'gated2fa']);
$router->post('/sell-usdt', 'AuthController@submitSellUSDTRequest', ['auth', 'gated2fa']);
$router->get('/uploads/{filename}', 'AuthController@serveUpload', ['auth']);

// Gated 2FA setup routes
$router->get('/setup-2fa', 'AuthController@showSetup2FA', ['auth']);
$router->post('/setup-2fa', 'AuthController@saveSetup2FA', ['auth']);
$router->get('/setup-2fa/skip', 'AuthController@skip2FA', ['auth']);

// Gated admin routes
$router->get('/admin/dashboard', 'AdminController@dashboard', ['auth', 'gated2fa']);
$router->get('/admin/user/{id}/download', 'AdminController@downloadCustomerData', ['auth', 'gated2fa']);
$router->get('/admin/dropdowns', 'AdminController@showDropdownOptions', ['auth', 'gated2fa']);
$router->post('/admin/dropdowns/add', 'AdminController@addDropdownOption', ['auth', 'gated2fa']);
$router->post('/admin/dropdowns/delete/{id}', 'AdminController@deleteDropdownOption', ['auth', 'gated2fa']);
$router->post('/admin/approve/{id}', 'AdminController@approveUser', ['auth', 'gated2fa']);
$router->post('/admin/suspend/{id}', 'AdminController@suspendUser', ['auth', 'gated2fa']);
$router->post('/admin/activate/{id}', 'AdminController@activateUser', ['auth', 'gated2fa']);
$router->post('/admin/buy-usdt/approve/{id}', 'AdminController@approveUSDTRequest', ['auth', 'gated2fa']);
$router->post('/admin/buy-usdt/reject/{id}', 'AdminController@rejectUSDTRequest', ['auth', 'gated2fa']);
$router->post('/admin/sell-usdt/approve/{id}', 'AdminController@approveSellRequest', ['auth', 'gated2fa']);
$router->post('/admin/sell-usdt/reject/{id}', 'AdminController@rejectSellRequest', ['auth', 'gated2fa']);
$router->get('/admin/user/{id}/profile', 'AdminController@viewUserProfile', ['auth', 'gated2fa']);
$router->post('/admin/user/{id}/allow-edit', 'AdminController@allowEditPermission', ['auth', 'gated2fa']);
$router->post('/admin/user/{id}/deny-edit', 'AdminController@denyEditPermission', ['auth', 'gated2fa']);
$router->post('/admin/user/{id}/approve-updates', 'AdminController@approveProfileUpdates', ['auth', 'gated2fa']);
$router->post('/admin/user/{id}/reject-updates', 'AdminController@rejectProfileUpdates', ['auth', 'gated2fa']);
$router->post('/admin/user/{id}/sdm-selfie', 'AdminController@submitSdmSelfie', ['auth', 'gated2fa']);
$router->post('/admin/user/{id}/request-docs', 'AdminController@requestDocuments', ['auth', 'gated2fa']);
$router->post('/admin/user/{id}/buy-bank', 'AdminController@updateBuyBankDetails', ['auth', 'gated2fa']);

return $router;
