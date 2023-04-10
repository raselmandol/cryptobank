<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
 */

$email_verification = env('APP_INSTALLED', true) == true ? get_option('email_verification') : 'disabled';
$allow_signup       = env('APP_INSTALLED', true) == true ? get_option('allow_singup') : 'no';

Route::middleware(['install'])->group(function () use ($email_verification, $allow_signup) {

    Auth::routes([
        'verify'   => $email_verification == 'enabled' ? true : false,
        'register' => $allow_signup == 'yes' ? true : false,
    ]);

    Route::get('verify/resend', 'Auth\TwoFactorController@resend')->name('verify.resend');
    Route::resource('verify', 'Auth\TwoFactorController')->only(['index', 'store']);

    Route::get('/logout', 'Auth\LoginController@logout');

    Route::group(['middleware' => $email_verification == 'enabled' ? ['auth', 'verified'] : ['auth']], function () {

        Route::group(['middleware' => ['Email2FA']], function () {

            Route::get('dashboard', 'DashboardController@index')->name('dashboard.index');

            //Profile Controller
            Route::get('profile', 'ProfileController@index')->name('profile.index');
            Route::get('profile/edit', 'ProfileController@edit')->name('profile.edit');
            Route::post('profile/update', 'ProfileController@update')->name('profile.update');
            Route::get('profile/change_password', 'ProfileController@change_password')->name('profile.change_password');
            Route::post('profile/update_password', 'ProfileController@update_password')->name('profile.update_password');
            Route::get('profile/notification_mark_as_read/{id}', 'ProfileController@notification_mark_as_read')->name('profile.notification_mark_as_read');
            Route::get('profile/show_notification/{id}', 'ProfileController@show_notification')->name('profile.show_notification');
            Route::match(['get', 'post'], 'profile/mobile_verification', 'ProfileController@mobile_verification')->name('profile.mobile_verification');
            Route::match(['get', 'post'], 'profile/document_verification', 'ProfileController@document_verification')->name('profile.document_verification');

            /** Admin Only Route **/
            Route::group(['middleware' => ['admin'], 'prefix' => 'admin'], function () {

                //Branch Controller
                Route::resource('branches', 'BranchController');

                //Other Banks
                Route::resource('other_banks', 'OtherBankController');

                //Currency List
                Route::resource('currency', 'CurrencyController');

                //Deposit Methods
                Route::resource('deposit_methods', 'DepositMethodController')->except([
                    'show',
                ]);

                //Withdraw Methods
                Route::resource('withdraw_methods', 'WithdrawMethodController')->except([
                    'show',
                ]);

                //Payment Gateways
                Route::resource('payment_gateways', 'PaymentGatewayController')->except([
                    'create', 'store', 'show', 'destroy',
                ]);

                //System User Controller
                Route::resource('system_users', 'SystemUserController');

                //User Roles
                Route::resource('roles', 'RoleController');

                //Permission Controller
                Route::get('permission/control/{user_id?}', 'PermissionController@index')->name('permission.index');
                Route::post('permission/store', 'PermissionController@store')->name('permission.store');

                //Language Controller
                Route::resource('languages', 'LanguageController');

                //Utility Controller
                Route::match(['get', 'post'], 'administration/general_settings/{store?}', 'UtilityController@settings')->name('settings.update_settings');
                Route::post('administration/upload_logo', 'UtilityController@upload_logo')->name('settings.uplaod_logo');
                Route::get('administration/database_backup_list', 'UtilityController@database_backup_list')->name('database_backups.list');
                Route::get('administration/create_database_backup', 'UtilityController@create_database_backup')->name('database_backups.create');
                Route::delete('administration/destroy_database_backup/{id}', 'UtilityController@destroy_database_backup');
                Route::get('administration/download_database_backup/{id}', 'UtilityController@download_database_backup')->name('database_backups.download');
                Route::post('administration/remove_cache', 'UtilityController@remove_cache')->name('settings.remove_cache');
                Route::post('administration/send_test_email', 'UtilityController@send_test_email')->name('settings.send_test_email');

                Route::match(['get', 'post'], 'administration/system_settings/{view?}', 'UtilityController@system_settings')->name('settings.system_settings');
                Route::match(['get', 'post'], 'theme_option/{store?}', 'UtilityController@theme_option')->name('theme_option.update');

                //Email Template
                Route::resource('email_templates', 'EmailTemplateController')->only([
                    'index', 'show', 'edit', 'update',
                ]);

                //SMS Template
                Route::resource('sms_templates', 'SMSTemplateController')->only([
                    'index', 'show', 'edit', 'update',
                ]);

                //Website Management
                Route::resource('services', 'ServiceController');
                Route::resource('faqs', 'FaqController')->except('show');
                Route::resource('testimonials', 'TestimonialController')->except('show');
                Route::resource('pages', 'PageController')->except('show');
                Route::resource('teams', 'TeamController')->except('show');
                Route::resource('partners', 'PartnerController')->except('show');

                //Navigation Controller
                Route::resource('navigations', 'NavigationController');
                Route::post('navigations/store_sorting', 'NavigationController@store_sorting')->name('navigations.store_sorting');
                Route::get('navigation_items/{navigation_id}/create', 'NavigationItemController@create')->name('navigation_items.create');
                Route::post('navigation_items/store/{navigation_id}', 'NavigationItemController@store')->name('navigation_items.store');
                Route::get('navigation_items/edit/{id}', 'NavigationItemController@edit')->name('navigation_items.edit');
                Route::patch('navigation_items/update/{id}', 'NavigationItemController@update')->name('navigation_items.update');
                Route::get('navigation_items/destroy/{id}', 'NavigationItemController@destroy')->name('navigation_items.destroy');

            });

            /** Dynamic Permission **/
            Route::group(['middleware' => ['permission'], 'prefix' => 'admin'], function () {
                //Dashboard Widget
                Route::get('dashboard/active_users_widget', 'DashboardController@active_users_widget')->name('dashboard.active_users_widget');
                Route::get('dashboard/pending_kyc_widget', 'DashboardController@pending_kyc_widget')->name('dashboard.pending_kyc_widget');
                Route::get('dashboard/pending_tickets_widget', 'DashboardController@pending_tickets_widget')->name('dashboard.pending_tickets_widget');
                Route::get('dashboard/deposit_requests_widget', 'DashboardController@deposit_requests_widget')->name('dashboard.deposit_requests_widget');
                Route::get('dashboard/withdraw_requests_widget', 'DashboardController@withdraw_requests_widget')->name('dashboard.withdraw_requests_widget');
                Route::get('dashboard/loan_requests_widget', 'DashboardController@loan_requests_widget')->name('dashboard.loan_requests_widget');
                Route::get('dashboard/fdr_requests_widget', 'DashboardController@fdr_requests_widget')->name('dashboard.fdr_requests_widget');
                Route::get('dashboard/wire_transfer_widget', 'DashboardController@wire_transfer_widget')->name('dashboard.wire_transfer_widget');
                Route::get('dashboard/total_deposit_widget', 'DashboardController@total_deposit_widget')->name('dashboard.total_deposit_widget');
                Route::get('dashboard/total_withdraw_widget', 'DashboardController@total_withdraw_widget')->name('dashboard.total_withdraw_widget');
                Route::get('dashboard/recent_transaction_widget', 'DashboardController@recent_transaction_widget')->name('dashboard.recent_transaction_widget');

                //User Controller
                Route::get('users/get_table_data/{status?}', 'UserController@get_table_data');
                Route::post('users/send_email', 'UserController@send_email')->name('users.send_email');
                Route::post('users/send_sms', 'UserController@send_sms')->name('users.send_sms');
                Route::get('users/filter/{status?}', 'UserController@index')->name('users.filter');
                Route::get('users/documents', 'UserController@documents')->name('users.documents');
                Route::get('users/documents/{id}', 'UserController@view_documents')->name('users.view_documents');
                Route::get('users/documents/varify/{id}', 'UserController@varify')->name('users.documents.varify');
                Route::get('users/documents/unvarify/{id}', 'UserController@unvarify')->name('users.documents.unvarify');
                Route::resource('users', 'UserController');

                //Transfer Request
                Route::post('wire_transfer_requests/get_table_data', 'TransferRequestController@get_table_data');
                Route::delete('wire_transfer_requests/{id}', 'TransferRequestController@destroy')->name('transfer_requests.destroy');
                Route::get('wire_transfer_requests/{id}', 'TransferRequestController@show')->name('transfer_requests.show');
                Route::get('wire_transfer_requests/approve/{id}', 'TransferRequestController@approve')->name('transfer_requests.approve');
                Route::get('wire_transfer_requests/reject/{id}', 'TransferRequestController@reject')->name('transfer_requests.reject');
                Route::get('wire_transfer_requests', 'TransferRequestController@index')->name('transfer_requests.index');

                //Internal Transfer Request
                Route::post('transfer_requests/get_table_data', 'InternalTransferRequestController@get_table_data');
                Route::delete('transfer_requests/{id}', 'InternalTransferRequestController@destroy')->name('internal_transfer_requests.destroy');
                Route::get('transfer_requests/{id}', 'InternalTransferRequestController@show')->name('internal_transfer_requests.show');
                Route::get('transfer_requests/approve/{id}', 'InternalTransferRequestController@approve')->name('internal_transfer_requests.approve');
                Route::get('transfer_requests/reject/{id}', 'InternalTransferRequestController@reject')->name('internal_transfer_requests.reject');
                Route::get('transfer_requests', 'InternalTransferRequestController@index')->name('internal_transfer_requests.index');

                //Deposit Requests
                Route::post('deposit_requests/get_table_data', 'DepositRequestController@get_table_data');
                Route::get('deposit_requests/approve/{id}', 'DepositRequestController@approve')->name('deposit_requests.approve');
                Route::get('deposit_requests/reject/{id}', 'DepositRequestController@reject')->name('deposit_requests.reject');
                Route::delete('deposit_requests/{id}', 'DepositRequestController@destroy')->name('deposit_requests.destroy');
                Route::get('deposit_requests/{id}', 'DepositRequestController@show')->name('deposit_requests.show');
                Route::get('deposit_requests', 'DepositRequestController@index')->name('deposit_requests.index');

                //Deposit Controller
                Route::get('deposits/get_table_data', 'DepositController@get_table_data');
                Route::resource('deposits', 'DepositController')->except([
                    'edit', 'update',
                ]);

                //Withdraw Requests
                Route::post('withdraw_requests/get_table_data', 'WithdrawRequestController@get_table_data');
                Route::get('withdraw_requests/approve/{id}', 'WithdrawRequestController@approve')->name('withdraw_requests.approve');
                Route::get('withdraw_requests/reject/{id}', 'WithdrawRequestController@reject')->name('withdraw_requests.reject');
                Route::delete('withdraw_requests/{id}', 'WithdrawRequestController@destroy')->name('withdraw_requests.destroy');
                Route::get('withdraw_requests/{id}', 'WithdrawRequestController@show')->name('withdraw_requests.show');
                Route::get('withdraw_requests', 'WithdrawRequestController@index')->name('withdraw_requests.index');

                //Withdraw Controller
                Route::get('withdraw/get_table_data', 'WithdrawController@get_table_data');
                Route::resource('withdraw', 'WithdrawController')->except([
                    'edit', 'update',
                ]);

                //All transactions
                Route::post('transactions/get_table_data', 'TransactionController@get_table_data');
                Route::get('transactions', 'TransactionController@index')->name('transactions.index');

                //Loan Product Controller
                Route::resource('loan_products', 'LoanProductController');

                //Loan Controller
                Route::post('loans/get_table_data', 'LoanController@get_table_data');
                Route::get('loans/calculator', 'LoanController@calculator')->name('loans.admin_calculator');
                Route::post('loans/calculator/calculate', 'LoanController@calculate')->name('loans.calculate');
                Route::get('loans/approve/{id}', 'LoanController@approve')->name('loans.approve');
                Route::get('loans/reject/{id}', 'LoanController@reject')->name('loans.reject');
                Route::resource('loans', 'LoanController');

                //Loan Collateral Controller
                Route::get('loan_collaterals/loan/{loan_id}', 'LoanCollateralController@index')->name('loan_collaterals.index');
                Route::resource('loan_collaterals', 'LoanCollateralController');

                //Loan Payment Controller
			    Route::get('loan_payments/get_repayment_by_loan_id/{loan_id}','LoanPaymentController@get_repayment_by_loan_id');
			    Route::get('loan_payments/get_table_data','LoanPaymentController@get_table_data');
			    Route::resource('loan_payments','LoanPaymentController');

                //FDR Plans
                Route::resource('fdr_plans', 'FDRPlanController');

                //FDR Controller
                Route::get('fixed_deposits/completed/{id}', 'FixedDepositController@completed')->name('fixed_deposits.completed');
                Route::get('fixed_deposits/approve/{id}', 'FixedDepositController@approve')->name('fixed_deposits.approve');
                Route::get('fixed_deposits/reject/{id}', 'FixedDepositController@reject')->name('fixed_deposits.reject');
                Route::post('fixed_deposits/get_table_data', 'FixedDepositController@get_table_data');
                Route::resource('fixed_deposits', 'FixedDepositController');

                //Gift Cards
                Route::get('gift_cards/status/{status}', 'GiftCardController@index')->name('gift_cards.filter');
                Route::resource('gift_cards', 'GiftCardController');

                //Support Tickets
                Route::get('support_tickets/assign_staff/{id}/{userId}', 'SupportTicketController@assign_staff')->name('support_tickets.assign_staff');
                Route::get('support_tickets/mark_as_closed/{id}', 'SupportTicketController@mark_as_closed')->name('support_tickets.mark_as_closed');
                Route::post('support_tickets/reply/{id}', 'SupportTicketController@reply')->name('support_tickets.reply');
                Route::get('support_tickets/get_table_data/{status}', 'SupportTicketController@get_table_data');
                Route::resource('support_tickets', 'SupportTicketController')->except([
                    'edit', 'update',
                ]);

                //Report Controller
                Route::match(['get', 'post'], 'reports/transactions_report', 'ReportController@transactions_report')->name('reports.transactions_report');
                Route::match(['get', 'post'], 'reports/loan_report', 'ReportController@loan_report')->name('reports.loan_report');
                Route::match(['get', 'post'], 'reports/fdr_report', 'ReportController@fdr_report')->name('reports.fdr_report');
                Route::match(['get', 'post'], 'reports/bank_revenues', 'ReportController@bank_revenues')->name('reports.bank_revenues');

            });

            Route::group(['middleware' => ['customer']], function () {
                Route::match(['get', 'post'], 'transfer/send_money/{otp?}', 'Customer\TransferController@send_money')->name('transfer.send_money');
                Route::match(['get', 'post'], 'transfer/exchange_money/{otp?}', 'Customer\TransferController@exchange_money')->name('transfer.exchange_money');
                Route::match(['get', 'post'], 'transfer/wire_transfer/{otp?}', 'Customer\TransferController@wire_transfer')->name('transfer.wire_transfer');
                Route::get('transfer/get_other_bank_details/{id?}', 'Customer\TransferController@get_other_bank_details')->name('transfer.get_other_bank_details');
                Route::get('transfer/get_exchange_amount/{from?}/{to?}/{amount?}', 'Customer\TransferController@get_exchange_amount')->name('transfer.get_exchange_amount');

                //Payment Request
                Route::get('payment_requests/get_table_data', 'Customer\PaymentRequestController@get_table_data');
                Route::match(['get', 'post'], 'payment_requests/pay_now/{id}', 'Customer\PaymentRequestController@pay_now')->name('payment_requests.pay_now');
                Route::get('payment_requests/cancel/{id}', 'Customer\PaymentRequestController@cancel')->name('payment_requests.cancel');
                Route::resource('payment_requests', 'Customer\PaymentRequestController')->except([
                    'edit', 'update', 'destroy',
                ]);

                //Deposit Money
                Route::match(['get', 'post'], 'deposit/redeem_gift_card', 'Customer\DepositController@redeem_gift_card')->name('deposit.redeem_gift_card');
                Route::match(['get', 'post'], 'deposit/manual_deposit/{id}', 'Customer\DepositController@manual_deposit')->name('deposit.manual_deposit');
                Route::get('deposit/manual_methods', 'Customer\DepositController@manual_methods')->name('deposit.manual_methods');

                //Automatic Deposit
                Route::match(['get', 'post'], 'deposit/automatic_deposit/{id}', 'Customer\DepositController@automatic_deposit')->name('deposit.automatic_deposit');
                Route::get('deposit/automatic_methods', 'Customer\DepositController@automatic_methods')->name('deposit.automatic_methods');

                //Withdraw Money
                Route::match(['get', 'post'], 'withdraw/manual_withdraw/{id}/{otp?}', 'Customer\WithdrawController@manual_withdraw')->name('withdraw.manual_withdraw');
                Route::get('withdraw/manual_methods', 'Customer\WithdrawController@manual_methods')->name('withdraw.manual_methods');

                //Loan Controller
                Route::match(['get', 'post'], 'loans/calculator', 'Customer\LoanController@calculator')->name('loans.calculator');
                Route::match(['get', 'post'], 'loans/apply_loan', 'Customer\LoanController@apply_loan')->name('loans.apply_loan');
                Route::get('loans/loan_details/{id}', 'Customer\LoanController@loan_details')->name('loans.loan_details');
                Route::match(['get', 'post'], 'loans/payment/{loan_id}', 'Customer\LoanController@loan_payment')->name('loans.loan_payment');
                Route::get('loans/my_loans', 'Customer\LoanController@index')->name('loans.my_loans');

                //Fixed Deposits
                Route::match(['get', 'post'], 'fixed_deposits/apply', 'Customer\FixedDepositController@apply')->name('fixed_deposits.apply');
                Route::get('fixed_deposits/history', 'Customer\FixedDepositController@index')->name('fixed_deposits.history');

                //Support Tickets
                Route::match(['get', 'post'], 'tickets/my_tickets', 'Customer\SupportTicketController@my_tickets')->name('tickets.my_tickets');
                Route::get('tickets/show/{id}', 'Customer\SupportTicketController@show')->name('tickets.show');
                Route::get('tickets/mark_as_closed/{id}', 'Customer\SupportTicketController@show')->name('tickets.mark_as_closed');
                Route::post('tickets/reply/{id}', 'Customer\SupportTicketController@reply')->name('tickets.reply');
                Route::match(['get', 'post'], 'tickets/create_ticket', 'Customer\SupportTicketController@create_ticket')->name('tickets.create_ticket');

                //Transaction Details
                Route::get('transaction_details/{id}', 'Customer\TransferController@show_transaction')->name('transaction_details');

                //OPT Controller
                Route::get('otp/generate', 'Customer\OTPController@generateOtp')->name('otp.generate');
                Route::get('otp/resend', 'Customer\OTPController@resendOtp')->name('otp.resend');

                //Reports Controller
                Route::match(['get', 'post'], 'reports/customer/transactions_report', 'Customer\ReportController@transactions_report')->name('customer_reports.transactions_report');
            });

        });
    });

    //Public Website
    Route::get('/about', 'Website\WebsiteController@about');
    Route::get('/services', 'Website\WebsiteController@services');
    Route::get('/faq', 'Website\WebsiteController@faq');
    Route::get('/contact', 'Website\WebsiteController@contact');
    Route::post('/send_message', 'Website\WebsiteController@send_message');
    Route::get('/cookie/accept', 'Website\WebsiteController@cookie_accept')->name('cookie.accept');
    if (env('APP_INSTALLED', true)) {
        Route::get('/{slug?}', 'Website\WebsiteController@index');
    } else {
        Route::get('/', function () {
            echo "Installation";
        });
    }

});

Route::namespace('Gateway')->prefix('callback')->name('callback.')->group(function () {
    Route::get('paypal', 'PayPal\ProcessController@callback')->name('PayPal')->middleware('auth');
    Route::post('stripe', 'Stripe\ProcessController@callback')->name('Stripe')->middleware('auth');
    Route::post('razorpay', 'Razorpay\ProcessController@callback')->name('Razorpay')->middleware('auth');
    Route::get('paystack', 'Paystack\ProcessController@callback')->name('Paystack')->middleware('auth');
    Route::get('flutterwave', 'Flutterwave\ProcessController@callback')->name('Flutterwave')->middleware('auth');
    Route::get('blockchain', 'BlockChain\ProcessController@callback')->name('BlockChain');
});

//Socila Login
Route::get('/login/{provider}', 'Auth\SocialController@redirect');
Route::get('/login/{provider}/callback', 'Auth\SocialController@callback');

//Ajax Select2 Controller
Route::get('ajax/get_table_data', 'Select2Controller@get_table_data');

Route::get('/installation', 'Install\InstallController@index');
Route::get('install/database', 'Install\InstallController@database');
Route::post('install/process_install', 'Install\InstallController@process_install');
Route::get('install/create_user', 'Install\InstallController@create_user');
Route::post('install/store_user', 'Install\InstallController@store_user');
Route::get('install/system_settings', 'Install\InstallController@system_settings');
Route::post('install/finish', 'Install\InstallController@final_touch');

//Update System
Route::get('migration/update', 'Install\UpdateController@update_migration');

//Cron Job Controller
Route::get('console/run', 'CronJobsController@run')->name('console.run');