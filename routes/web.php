<?php

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

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::post('admin/leads_google', 'ZapierController@leads_google');
Route::post('admin/tasks_google', 'ZapierController@tasks_google');
Route::post('admin/lead_dubsado', 'ZapierController@lead_dubsado');
Route::post('admin/task_form', 'ZapierController@task_form');
Route::post('admin/type_form', 'ZapierController@type_form');
Route::post('admin/trello_board', 'ZapierController@trello_board');

Route::post('/consent/remove-lead-request', ['uses' => 'PublicLeadGdprController@removeLeadRequest'])->name('front.gdpr.remove-lead-request');
Route::post('/consent/l/update/{lead}', ['uses' => 'PublicLeadGdprController@updateConsent'])->name('front.gdpr.consent.update');
Route::post('/consent/l/update/{lead}', ['uses' => 'PublicLeadGdprController@updateConsent'])->name('front.gdpr.consent.update');
Route::get('/consent/l/{lead}', ['uses' => 'PublicLeadGdprController@consent'])->name('front.gdpr.consent');
Route::post('/forms/l/update/{lead}', ['uses' => 'PublicLeadGdprController@updateLead'])->name('front.gdpr.lead.update');
Route::get('/forms/l/{lead}', ['uses' => 'PublicLeadGdprController@lead'])->name('front.gdpr.lead');
Route::get('/contract/{id}', ['uses' => 'Front\PublicUrlController@contractView'])->name('front.contract.show');
Route::get('/contract/download/{id}', ['uses' => 'Front\PublicUrlController@contractDownload'])->name('front.contract.download');
Route::get('contract/sign-modal/{id}', ['uses' => 'Front\PublicUrlController@contractSignModal'])->name('front.contract.sign-modal');
Route::post('contract/sign/{id}', ['uses' => 'Front\PublicUrlController@contractSign'])->name('front.contract.sign');
Route::get('/estimate/{id}', ['uses' => 'Front\PublicUrlController@estimateView'])->name('front.estimate.show');
Route::post('/estimate/decline/{id}', ['uses' => 'Front\PublicUrlController@decline'])->name('front.estimate.decline');
Route::get('/estimate/accept/{id}', ['uses' => 'Front\PublicUrlController@acceptModal'])->name('front.estimate.accept');
Route::post('/estimate/accept/{id}', ['uses' => 'Front\PublicUrlController@accept'])->name('front.accept-estimate');
Route::get('/estimate/download/{id}', ['uses' => 'Front\PublicUrlController@estimateDownload'])->name('front.estimateDownload');
Route::post('/estimate/updateitem/{id}', ['uses' => 'Front\PublicUrlController@updateLineIteme'])->name('front.estimate.updateitem');
Route::get('/invoice/download/{id}', ['uses' => 'Front\HomeController@downloadInvoice'])->name('front.invoiceDownload');
Route::get('/task-files/{id}', ['uses' => '\App\Http\Controllers\Front\HomeController@taskFiles'])->name('front.task-files');
Route::get('/task-share/{id}', ['uses' => '\App\Http\Controllers\Front\HomeController@taskShare'])->name('front.task-share');
Route::get('/invoice/{id}', ['uses' => '\App\Http\Controllers\Front\HomeController@invoice'])->name('front.invoice');
Route::get('/', ['uses' => '\App\Http\Controllers\Front\HomeController@index'])->name('front.home');
Route::get('page/{slug?}', ['uses' => '\App\Http\Controllers\Front\HomeController@page'])->name('front.page');
Route::get('/gantt-chart-data/{id}', ['uses' => 'Front\HomeController@ganttData'])->name('front.gantt-data');
Route::get('/gantt-chart/{id}', ['uses' => 'Front\HomeController@gantt'])->name('front.gantt');
Route::post('public/pay-with-razorpay', array('as' => 'public.pay-with-razorpay', 'uses' => 'Client\RazorPayController@payWithRazorPay',));

// Added By SB
Route::get('/shorten/{code}', ['uses' => 'Front\ShortLinkController@shortenLink'])->name('shorten.link');
//Route::get('/short/{code}', ['uses' => 'Front\ShortLinkController@shortenLink'])->name('shorten.link');

Route::get('/customlogout', ['uses' => 'Front\PublicUrlController@customlogout'])->name('front.customlogout');
Route::get('/return-super-admin', ['uses' => 'Front\PublicUrlController@backSuperAsAdmin'])->name('front.return-super-admin');

Route::get('cron/exchange-rates', ['uses' => 'CronController@updateExchangeRate'])->name('cron.update-exchange-rates');
Route::get('cron/sync-google', ['uses' => 'CronController@doSyncGoogleCalendar'])->name('cron.sync-google');
Route::resource('cron', 'CronController');

Route::get('leadpublic/get-form-data/{id}', ['uses' => 'LeadFormPublicController@getFormData'])->name('leadpublic.get-form-data');
Route::resource('leadpublic', 'LeadFormPublicController');
// END

// Update Email Status
Route::get('audit/{id}/update-email-status', ['uses' => 'AuditController@updateEmailStatus'])->name('audit.update.email.status');

Route::group(
    ['namespace' => 'Front', 'as' => 'front.'],
    function () {
        Route::post('/contact-us', 'HomeController@contactUs')->name('contact-us');
        Route::get('/contact', 'HomeController@contact')->name('contact');
        Route::resource('/signup', 'RegisterController', ['only' => ['index', 'store']]);
        Route::get('/email-verification/{code}', 'RegisterController@getEmailVerification')->name('get-email-verification');
        Route::get('/features', ['uses' => 'HomeController@feature'])->name('feature');
        Route::get('/pricing', ['uses' => 'HomeController@pricing'])->name('pricing');
        Route::get('language/{lang}', ['as' => 'language.lang', 'uses' => 'HomeController@changeLanguage']);
    }
);

Route::group(
    ['namespace' => 'Client', 'prefix' => 'client', 'as' => 'client.'],
    function () {

        Route::post('stripe/{invoiceId}', array('as' => 'stripe', 'uses' => 'StripeController@paymentWithStripe',));
        Route::post('stripe-public/{invoiceId}', array('as' => 'stripe-public', 'uses' => 'StripeController@paymentWithStripePublic',));
        
        Route::post('stripe-plaid/{invoiceId}', array('as' => 'stripe-plaid', 'uses' => 'StripeController@makeStripePaymentWithPlaid',));
        Route::post('plaid-webhook', array('as' => 'plaid-webhook', 'uses' => 'StripeController@stripePlaidWebhook',));
        
        
        
        
        // route for post request
        Route::get('paypal-public/{invoiceId}', array('as' => 'paypal-public', 'uses' => 'PaypalController@paymentWithpaypalPublic',));
        Route::get('paypal/{invoiceId}', array('as' => 'paypal', 'uses' => 'PaypalController@paymentWithpaypal',));
        // route for check status responce
        Route::get('paypal', array('as' => 'status', 'uses' => 'PaypalController@getPaymentStatus',));
        Route::get('paypal-recurring', array('as' => 'paypal-recurring', 'uses' => 'PaypalController@payWithPaypalRecurrring',));

        //paystack payment
        Route::get('paystack-public/{invoiceId}', array('as' => 'paystack-public', 'uses' => 'PaystackController@redirectToGateway',));
        Route::get('/paystack/callback', 'PaystackController@handleGatewayCallback')->name('paystack.callback');
    }
);

//Paypal IPN
Route::post('verify-ipn', array('as' => 'verify-ipn', 'uses' => 'PaypalIPNController@verifyIPN'));
Route::post('verify-billing-ipn', array('as' => 'verify-billing-ipn', 'uses' => 'PaypalIPNController@verifyBillingIPN'));
Route::post('/verify-webhook', ['as' => 'verify-webhook', 'uses' => 'StripeWebhookController@verifyStripeWebhook']);
Route::post('/save-invoices', ['as' => 'save_webhook', 'uses' => 'StripeWebhookController@saveInvoices']);
Route::post('/save-razorpay-invoices', ['as' => 'save_razorpay-webhook', 'uses' => 'RazorpayWebhookController@saveInvoices']);
Route::get('/check-razorpay-invoices', ['as' => 'check_razorpay-webhook', 'uses' => 'RazorpayWebhookController@checkInvoices']);

Route::post('/save-paystack-invoices', ['as' => 'save_paystack-webhook', 'uses' => 'PaystackWebhookController@saveInvoices']);

// Social Auth
Route::get('/redirect/{provider}', ['uses' => 'Auth\LoginController@redirect', 'as' => 'social.login']);
Route::get('/callback/{provider}', ['uses' => 'Auth\LoginController@callback', 'as' => 'social.login-callback']);

Auth::routes();

Route::group(['middleware' => 'auth'], function () {

    // Super admin routes
    Route::group(
        ['namespace' => 'SuperAdmin', 'prefix' => 'super-admin', 'as' => 'super-admin.', 'middleware' => ['super-admin']],
        function () {
            //aqeel code
            
            Route::post('ticket-agents/update-group/{id}', ['uses' => 'TicketAgentsController@updateGroup'])->name('ticket-agents.update-group');
            Route::resource('ticket-agents', 'TicketAgentsController');
            Route::resource('ticket-groups', 'TicketGroupsController');

            Route::get('ticketTypes/createModal', ['uses' => 'TicketTypesController@createModal'])->name('ticketTypes.createModal');
            Route::resource('ticketTypes', 'TicketTypesController');
            
            Route::get('codeTypes/createModal', ['uses' => 'CodeTypesController@createModal'])->name('codeTypes.createModal');
            Route::resource('codeTypes', 'CodeTypesController');
            
            Route::get('salescategoryTypes/createModal', ['uses' => 'SalescategoryTypesController@createModal'])->name('salescategoryTypes.createModal');
            Route::resource('salescategoryTypes', 'SalescategoryTypesController');
            
            

            Route::get('tickets/export/{startDate?}/{endDate?}/{agentId?}/{status?}/{priority?}/{channelId?}/{typeId?}', ['uses' => 'SuperAdminTicketsController@export'])->name('tickets.export');
            Route::get('tickets/refresh-count/{startDate?}/{endDate?}/{agentId?}/{status?}/{priority?}/{channelId?}/{typeId?}', ['uses' => 'SuperAdminTicketsController@refreshCount'])->name('tickets.refreshCount');
            Route::get('tickets/reply-delete/{id?}', ['uses' => 'SuperAdminController@destroyReply'])->name('tickets.reply-delete');
            Route::post('tickets/updateOtherData/{id}', ['uses' => 'SuperAdminController@updateOtherData'])->name('tickets.updateOtherData');
            
            Route::delete('tickets/destroy/{id}', ['uses' => 'SuperAdminTicketsController@destroy'])->name('tickets.destroy');
            Route::resource('tickets', 'SuperAdminTicketsController');
            
            

            Route::post('replyTemplates/fetch-template', ['uses' => 'TicketReplyTemplatesController@fetchTemplate'])->name('replyTemplates.fetchTemplate');
            Route::resource('replyTemplates', 'TicketReplyTemplatesController');
            //super admin tickets


            Route::get('/dashboard', 'SuperAdminDashboardController@index')->name('dashboard');
            Route::post('profile/updateOneSignalId', ['uses' => 'SuperAdminProfileController@updateOneSignalId'])->name('profile.updateOneSignalId');
            Route::resource('/profile', 'SuperAdminProfileController', ['only' => ['index', 'update']]);

            // Faq routes
            Route::resource('/faq-category/{category}/faq', 'SuperAdminFaqController')->except(['index', 'show']);

            // Faq Category routes
            Route::get('faq-category/data', ['uses' => 'SuperAdminFaqCategoryController@data'])->name('faq-category.data');
            Route::resource('/faq-category', 'SuperAdminFaqCategoryController');

            // Packages routes
            Route::get('packages/data', ['uses' => 'SuperAdminPackageController@data'])->name('packages.data');
            Route::resource('/packages', 'SuperAdminPackageController');

            // Companies routes
            Route::get('companies/data', ['uses' => 'SuperAdminCompanyController@data'])->name('companies.data');
            Route::get('companies/editPackage/{companyId}', ['uses' => 'SuperAdminCompanyController@editPackage'])->name('companies.edit-package.get');
            Route::put('companies/editPackage/{companyId}', ['uses' => 'SuperAdminCompanyController@updatePackage'])->name('companies.edit-package.post');
            Route::post('/companies', ['uses' => 'SuperAdminCompanyController@store']);

            Route::resource('/companies', 'SuperAdminCompanyController');
            Route::get('invoices/data', ['uses' => 'SuperAdminInvoiceController@data'])->name('invoices.data');
            Route::resource('/invoices', 'SuperAdminInvoiceController', ['only' => ['index']]);
            Route::get('paypal-invoice-download/{id}', array('as' => 'paypal.invoice-download', 'uses' => 'SuperAdminInvoiceController@paypalInvoiceDownload',));
            Route::get('billing/invoice-download/{invoice}', 'SuperAdminInvoiceController@download')->name('stripe.invoice-download');
            Route::get('billing/razorpay-download/{invoice}', 'SuperAdminInvoiceController@razorpayInvoiceDownload')->name('razorpay.invoice-download');
            Route::get('billing/offline-download/{invoice}', 'SuperAdminInvoiceController@offlineInvoiceDownload')->name('offline.invoice-download');
            Route::get('billing/paystack-download/{id}', 'SuperAdminInvoiceController@paystackInvoiceDownload')->name('paystack.invoice-download');
            
            // login as admin
            Route::get('companies/connect-as-admin/{companyId}', ['uses' => 'SuperAdminCompanyController@connectAsAdmin'])->name('companies.connect-admin');
            

            // Storage settings


            Route::resource('/settings', 'SuperAdminSettingsController', ['only' => ['index', 'update']]);

            Route::get('super-admin/data', ['uses' => 'SuperAdminController@data'])->name('super-admin.data');
            Route::resource('/super-admin', 'SuperAdminController');

            Route::get('offline-plan/data', ['uses' => 'OfflinePlanChangeController@data'])->name('offline-plan.data');
            Route::post('offline-plan/verify', ['uses' => 'OfflinePlanChangeController@verify'])->name('offline-plan.verify');
            Route::post('offline-plan/reject', ['uses' => 'OfflinePlanChangeController@reject'])->name('offline-plan.reject');
            Route::resource('/offline-plan', 'OfflinePlanChangeController', ['only' => ['index', 'update']]);

            Route::group(
                ['prefix' => 'front-settings'],
                function () {

                    Route::get('front-theme-settings', ['uses' => 'SuperAdminFrontSettingController@themeSetting'])->name('theme-settings');
                    Route::post('front-theme-update', ['uses' => 'SuperAdminFrontSettingController@themeUpdate'])->name('theme-update');
                    Route::get('auth-settings', ['uses' => 'SuperAdminFrontSettingController@authSetting'])->name('auth-settings');
                    Route::post('auth-update', ['uses' => 'SuperAdminFrontSettingController@authUpdate'])->name('auth-update');
                    Route::resource('front-settings', 'SuperAdminFrontSettingController', ['only' => ['index', 'update']]);
                    Route::resource('seo-detail', 'SuperAdminSeoDetailController', ['only' => ['edit', 'update', 'index']]);

                    Route::post('feature-settings/title-update}', ['uses' => 'SuperAdminFeatureSettingController@updateTitles'])->name('feature-settings.title-update');
                    Route::resource('feature-settings', 'SuperAdminFeatureSettingController');

                    Route::post('testimonial-settings/title-update}', ['uses' => 'TestimonialSettingController@updateTitles'])->name('testimonial-settings.title-update');
                    Route::resource('testimonial-settings', 'TestimonialSettingController');

                    Route::post('client-settings/title-update}', ['uses' => 'FrontClientSettingController@updateTitles'])->name('client-settings.title-update');
                    Route::resource('client-settings', 'FrontClientSettingController');

                    Route::post('faq-settings/title-update}', ['uses' => 'FrontFaqSettingController@updateTitles'])->name('faq-settings.title-update');
                    Route::resource('faq-settings', 'FrontFaqSettingController');


                    Route::resource('cta-settings', 'CtaSettingController', ['only' => ['index', 'update']]);

                    Route::resource('front-menu-settings', 'FrontMenuSettingController', ['only' => ['index', 'update']]);

                    Route::get('footer-settings/footer-text}', ['uses' => 'SuperAdminFooterSettingController@footerText'])->name('footer-settings.footer-text');
                    Route::post('footer-settings/copyright-text', ['uses' => 'SuperAdminFooterSettingController@updateText'])->name('footer-settings.copyright-text');
                    Route::post('footer-settings/video-upload', ['uses' => 'SuperAdminFooterSettingController@videoUpload'])->name('footer-settings.video-upload');
                    Route::resource('footer-settings', 'SuperAdminFooterSettingController');

                    Route::post('price-settings-update', ['uses' => 'SuperAdminFrontSettingController@priceUpdate'])->name('price-setting-update');
                    Route::get('price-settings', ['uses' => 'SuperAdminFrontSettingController@price'])->name('price-settings');

                    Route::post('contactus-setting-update', ['uses' => 'SuperAdminFrontSettingController@contactUpdate'])->name('contactus-setting-update');
                    Route::get('contact-settings', ['uses' => 'SuperAdminFrontSettingController@contact'])->name('contact-settings');

                    Route::resource('front-widgets', 'FrontWidgetsController');
                }
            );
            Route::group(
                ['prefix' => 'settings'],
                function () {
                    Route::get('email-settings/sent-test-email', ['uses' => 'SuperAdminEmailSettingsController@sendTestEmail'])->name('email-settings.sendTestEmail');
                    Route::resource('/email-settings', 'SuperAdminEmailSettingsController', ['only' => ['index', 'update']]);
                    Route::post('/stripe-method-change', 'SuperAdminStripeSettingsController@changePaymentMethod')->name('stripe.method-change');
                    Route::get('offline-payment-setting/createModal', ['uses' => 'OfflinePaymentSettingController@createModal'])->name('offline-payment-setting.createModal');
                    Route::get('offline-payment/method', ['uses' => 'OfflinePaymentSettingController@offlinePaymentMethod'])->name('offline-payment-method.create');
                    Route::resource('offline-payment-setting', 'OfflinePaymentSettingController');
                    Route::resource('/payment-settings', 'SuperAdminStripeSettingsController', ['only' => ['index', 'update']]);
                    
                    Route::resource('/social-auth-settings', 'SuperAdminSocialAuthSettingsController', ['only' => ['index', 'update']]);
                    
                    //bitsclan super admin code start here
                    Route::resource('/quickbooks-settings', 'SuperAdminQuickbookSettingsController');
                    Route::get('quickbooks-connect', 'SuperAdminQuickbookSettingsController@save_token')->name('quickbooks-connect');
                    Route::resource('super-admin-quickbooks', 'SuperAdminsQuickbooksController');
                    //bitsclan superadmin code end here


                    Route::get('push-notification-settings/sent-test-notification', ['uses' => 'SuperAdminPushSettingsController@sendTestEmail'])->name('push-notification-settings.sendTestEmail');
                    Route::get('push-notification-settings/sendTestNotification', ['uses' => 'SuperAdminPushSettingsController@sendTestNotification'])->name('push-notification-settings.sendTestNotification');
                    Route::resource('/push-notification-settings', 'SuperAdminPushSettingsController', ['only' => ['index', 'update']]);

                    Route::get('currency/exchange-key', ['uses' => 'SuperAdminCurrencySettingController@currencyExchangeKey'])->name('currency.exchange-key');
                    Route::post('currency/exchange-key-store', ['uses' => 'SuperAdminCurrencySettingController@currencyExchangeKeyStore'])->name('currency.exchange-key-store');
                    Route::resource('currency', 'SuperAdminCurrencySettingController');
                    Route::get('currency/exchange-rate/{currency}', ['uses' => 'SuperAdminCurrencySettingController@exchangeRate'])->name('currency.exchange-rate');
                    Route::get('currency/update/exchange-rates', ['uses' => 'SuperAdminCurrencySettingController@updateExchangeRate'])->name('currency.update-exchange-rates');
                    Route::resource('currency', 'SuperAdminCurrencySettingController');

                    Route::post('update-settings/deleteFile', ['uses' => 'UpdateDatabaseController@deleteFile'])->name('update-settings.deleteFile');
                    Route::get('update-settings/install', ['uses' => 'UpdateDatabaseController@install'])->name('update-settings.install');
                    Route::get('update-settings/manual-update', ['uses' => 'UpdateDatabaseController@manual'])->name('update-settings.manual');
                    Route::resource('update-settings', 'UpdateDatabaseController');

                    Route::resource('storage-settings', 'StorageSettingsController');

                    // Language Settings
                    Route::post('language-settings/update-data/{id?}', ['uses' => 'SuperAdminLanguageSettingsController@updateData'])->name('language-settings.update-data');
                    Route::resource('language-settings', 'SuperAdminLanguageSettingsController');

                    Route::resource('package-settings', 'SuperAdminPackageSettingController', ['only' => ['index', 'update']]);

                    // Custom Modules
                    Route::post('custom-modules/verify-purchase', ['uses' => 'CustomModuleController@verifyingModulePurchase'])->name('custom-modules.verify-purchase');
                    Route::resource('custom-modules', 'CustomModuleController');


                    Route::post('theme-settings/activeTheme', ['uses' => 'SuperAdminThemeSettingsController@activeTheme'])->name('theme-settings.activeTheme');
                    Route::resource('theme-settings', 'SuperAdminThemeSettingsController');
                }
            );
        }
    );
    // Admin routes
    Route::group(
        ['namespace' => 'Admin', 'prefix' => 'admin', 'as' => 'admin.', 'middleware' => ['role:admin']],
        function () {
            Route::group(['middleware' => ['account-setup', 'license-expire']], function () {
                Route::get('/dashboard', 'AdminDashboardController@index')->name('dashboard');
                Route::get('/dashboard-new', 'AdminDashboardController@dashboard_new')->name('dashboard-new');
                Route::post('/dashboard/widget', 'AdminDashboardController@widget')->name('dashboard.widget');

                Route::get('/dashboard/timestamp', 'AdminDashboardController@timestamp')->name('dashboard.timestamp');

                Route::get('designations/quick-create', ['uses' => 'ManageDesignationController@quickCreate'])->name('designations.quick-create');
                Route::post('designations/quick-store', ['uses' => 'ManageDesignationController@quickStore'])->name('designations.quick-store');
                Route::resource('designations', 'ManageDesignationController');


                // FAQ
                Route::get('faqs/{id}', ['uses' => 'FaqController@details'])->name('faqs.details');
                Route::get('faqs', ['uses' => 'FaqController@index'])->name('faqs.index');


                Route::get('clients/export/{status?}/{client?}', ['uses' => 'ManageClientsController@export'])->name('clients.export');
                
                Route::get('clients/create/{clientID?}', ['uses' => 'ManageClientsController@create'])->name('clients.create');
                
                // Added by Sb
                Route::get('clients/download/template', ['uses' => 'ManageClientsController@downloadTemplate'])->name('clients.download-template');
                Route::post('clients/import', ['uses' => 'ManageClientsController@import'])->name('clients.import');
                
                
                Route::get('clients/create-client', ['uses' => 'ManageClientsController@createClient'])->name('clients.create-client');
                Route::post('clients/store-client', ['uses' => 'ManageClientsController@storeClient'])->name('clients.store-client');
                
                Route::get('clients/login-view/{id}', ['uses' => 'ManageClientsController@loginView'])->name('clients.login-view');
                Route::get('clients/connect-as-client/{id}', ['uses' => 'ManageClientsController@connectAsClient'])->name('clients.connect-as-client');
                
                // End
                
                Route::resource('clients', 'ManageClientsController', ['expect' => ['create']]);

                Route::get('leads/gdpr/{leadID}', ['uses' => 'LeadController@gdpr'])->name('leads.gdpr');
                Route::get('leads/export/{followUp?}/{client?}', ['uses' => 'LeadController@export'])->name('leads.export');
                Route::post('leads/change-status', ['uses' => 'LeadController@changeStatus'])->name('leads.change-status');
                Route::get('leads/follow-up/{leadID}', ['uses' => 'LeadController@followUpCreate'])->name('leads.follow-up');
                Route::get('leads/followup/{leadID}', ['uses' => 'LeadController@followUpShow'])->name('leads.followup');
                Route::post('leads/follow-up-store', ['uses' => 'LeadController@followUpStore'])->name('leads.follow-up-store');
                Route::get('leads/follow-up-edit/{id?}', ['uses' => 'LeadController@editFollow'])->name('leads.follow-up-edit');
                Route::post('leads/follow-up-update', ['uses' => 'LeadController@UpdateFollow'])->name('leads.follow-up-update');
                Route::post('leads/follow-up-delete/{id}', ['uses' => 'LeadController@deleteFollow'])->name('leads.follow-up-delete');
                Route::get('leads/follow-up-sort', ['uses' => 'LeadController@followUpSort'])->name('leads.follow-up-sort');
                Route::post('leads/save-consent-purpose-data/{lead}', ['uses' => 'LeadController@saveConsentLeadData'])->name('leads.save-consent-purpose-data');
                Route::get('leads/consent-purpose-data/{lead}', ['uses' => 'LeadController@consentPurposeData'])->name('leads.consent-purpose-data');
                Route::get('leads/audit/{id}', ['uses' => 'LeadController@showAudits'])->name('leads.audit');

                // Added By Adil.

                Route::get('vendor/create', ['uses' => 'AdminVendorController@create'])->name('vendor.create');
                Route::get('vendor/store', ['uses' => 'AdminVendorController@store'])->name('vendor.store');
                Route::post('vendor/update', ['uses' => 'AdminVendorController@update'])->name('vendor.update');
                Route::delete('vendor/destroy', ['uses' => 'AdminVendorController@destroy'])->name('vendor.destroy');
                Route::get('vendor/edit/{id}', ['uses' => 'AdminVendorController@edit'])->name('vendor.edit');
                Route::get('vendor/export/{status?}/{vendor?}', ['uses' => 'AdminVendorController@export'])->name('vendor.export');
                Route::get('vendor/showVendor/{id}', ['uses' => 'AdminVendorController@showVendor'])->name('vendor.showVendor');
                Route::post('vendor/sendEmail', ['uses' => 'AdminVendorController@sendEmail'])->name('vendor.sendEmail');
                
                //Added by Sb
                Route::get('vendor/download/template', ['uses' => 'AdminVendorController@downloadTemplate'])->name('vendor.download-template');
                Route::post('vendor/import', ['uses' => 'AdminVendorController@import'])->name('vendor.import');
                // End
                
                Route::get('vendor/create-vendor', ['uses' => 'AdminVendorController@createVendor'])->name('vendor.create-vendor');
                Route::post('vendor/store-vendor', ['uses' => 'AdminVendorController@storeVendor'])->name('vendor.store-vendor');
                
                Route::resource('vendor', 'AdminVendorController');
                 // End.
                
                
                
                

                // Added by Sb
                Route::get('leads/download/template', ['uses' => 'LeadController@downloadTemplate'])->name('leads.download-template');
                Route::post('leads/import', ['uses' => 'LeadController@import'])->name('leads.import');
                Route::get('leads/archive', ['uses' => 'LeadController@archive'])->name('leads.archive');
                
                // End
                
                Route::resource('leads', 'LeadController');



                // Lead Files
                Route::get('lead-files/download/{id}', ['uses' => 'LeadFilesController@download'])->name('lead-files.download');
                Route::get('lead-files/thumbnail', ['uses' => 'LeadFilesController@thumbnailShow'])->name('lead-files.thumbnail');
                Route::resource('lead-files', 'LeadFilesController');

                // Proposal routes
                Route::get('proposals/data/{id?}', ['uses' => 'ProposalController@data'])->name('proposals.data');
                Route::get('proposals/download/{id}', ['uses' => 'ProposalController@download'])->name('proposals.download');
                Route::get('proposals/create/{leadID?}', ['uses' => 'ProposalController@create'])->name('proposals.create');
                Route::get('proposals/convert-proposal/{id?}', ['uses' => 'ProposalController@convertProposal'])->name('proposals.convert-proposal');

                Route::resource('proposals', 'ProposalController', ['expect' => ['create']]);

                // Proposal section, Added By Adil.
                Route::resource('proposal', 'AdminProposalController');
                Route::get('proposal/data', ['uses' => 'AdminProposalController@data'])->name('proposal.data');
                Route::post('proposal/store', ['uses' => 'AdminProposalController@store'])->name('proposal.store');
                // Proposal section Ended.


                // Holidays
                Route::get('holidays/calendar-month', 'HolidaysController@getCalendarMonth')->name('holidays.calendar-month');
                Route::get('holidays/view-holiday/{year?}', 'HolidaysController@viewHoliday')->name('holidays.view-holiday');
                Route::get('holidays/mark_sunday', 'HolidaysController@Sunday')->name('holidays.mark-sunday');
                Route::get('holidays/calendar/{year?}', 'HolidaysController@holidayCalendar')->name('holidays.calendar');
                Route::get('holidays/mark-holiday', 'HolidaysController@markHoliday')->name('holidays.mark-holiday');
                Route::post('holidays/mark-holiday-store', 'HolidaysController@markDayHoliday')->name('holidays.mark-holiday-store');
                Route::resource('holidays', 'HolidaysController');

                Route::group(
                    ['prefix' => 'employees'],
                    function () {

                        Route::get('employees/free-employees', ['uses' => 'ManageEmployeesController@freeEmployees'])->name('employees.freeEmployees');
                        Route::get('employees/docs-create/{id}', ['uses' => 'ManageEmployeesController@docsCreate'])->name('employees.docs-create');
                        Route::get('employees/tasks/{userId}/{hideCompleted}', ['uses' => 'ManageEmployeesController@tasks'])->name('employees.tasks');
                        Route::get('employees/time-logs/{userId}', ['uses' => 'ManageEmployeesController@timeLogs'])->name('employees.time-logs');
                        Route::get('employees/export/{status?}/{employee?}/{role?}', ['uses' => 'ManageEmployeesController@export'])->name('employees.export');
                        Route::post('employees/assignRole', ['uses' => 'ManageEmployeesController@assignRole'])->name('employees.assignRole');
                        Route::post('employees/assignProjectAdmin', ['uses' => 'ManageEmployeesController@assignProjectAdmin'])->name('employees.assignProjectAdmin');
                        
                        
                        Route::resource('employees', 'ManageEmployeesController');

                        Route::get('department/quick-create', ['uses' => 'ManageTeamsController@quickCreate'])->name('teams.quick-create');
                        Route::post('department/quick-store', ['uses' => 'ManageTeamsController@quickStore'])->name('teams.quick-store');
                        Route::resource('teams', 'ManageTeamsController');
                        Route::resource('employee-teams', 'ManageEmployeeTeamsController');

                        Route::get('employee-docs/download/{id}', ['uses' => 'EmployeeDocsController@download'])->name('employee-docs.download');
                        Route::resource('employee-docs', 'EmployeeDocsController');
                    }
                );

                Route::post('projects/gantt-task-update/{id}', ['uses' => 'ManageProjectsController@updateTaskDuration'])->name('projects.gantt-task-update');

                Route::post('projects/project-duration-update/{id}', ['uses' => 'ManageAllTasksController@updateProjectDuration'])->name('projects.project-duration-update');
                
                
                

                Route::get('projects/ajaxCreate/{columnId}', ['uses' => 'ManageProjectsController@ajaxCreate'])->name('projects.ajaxCreate');
                Route::get('projects/archive-data', ['uses' => 'ManageProjectsController@archiveData'])->name('projects.archive-data');
                Route::get('projects/archive', ['uses' => 'ManageProjectsController@archive'])->name('projects.archive');
                Route::get('projects/archive-restore/{id?}', ['uses' => 'ManageProjectsController@archiveRestore'])->name('projects.archive-restore');
                Route::get('projects/archive-delete/{id?}', ['uses' => 'ManageProjectsController@archiveDestroy'])->name('projects.archive-delete');
                Route::get('projects/export/{status?}/{clientID?}', ['uses' => 'ManageProjectsController@export'])->name('projects.export');
                Route::get('projects/ganttData/{projectId?}', ['uses' => 'ManageProjectsController@ganttData'])->name('projects.ganttData');
                Route::get('projects/gantt/{projectId?}', ['uses' => 'ManageProjectsController@gantt'])->name('projects.gantt');
                Route::get('projects/burndown/{projectId?}', ['uses' => 'ManageProjectsController@burndownChart'])->name('projects.burndown-chart');
                Route::post('projects/updateStatus/{id}', ['uses' => 'ManageProjectsController@updateStatus'])->name('projects.updateStatus');
                Route::get('projects/discussion-replies/{projectId}/{discussionId}', ['uses' => 'ManageProjectsController@discussionReplies'])->name('projects.discussionReplies');
                Route::get('projects/discussion/{projectId}', ['uses' => 'ManageProjectsController@discussion'])->name('projects.discussion');
                Route::get('projects/notes/{projectId}', ['uses' => 'ManageProjectsController@showNotes'])->name('projects.notes');
                
                //Added by Sb
                Route::get('projects/download/template', ['uses' => 'ManageProjectsController@downloadTemplate'])->name('projects.download-template');
                Route::post('projects/import', ['uses' => 'ManageProjectsController@import'])->name('projects.import');
                
                Route::get('projects/free-flow-gantt', ['uses' => 'ManageProjectsController@freeFlowGantt'])->name('projects.free-flow-gantt');
                
                // End
                
                
                Route::resource('projects', 'ManageProjectsController');

                Route::get('project-template/data', ['uses' => 'ProjectTemplateController@data'])->name('project-template.data');
                Route::get('project-template/detail/{id?}', ['uses' => 'ProjectTemplateController@taskDetail'])->name('project-template.detail');
                Route::resource('project-template', 'ProjectTemplateController');

                Route::post('project-template-members/save-group', ['uses' => 'ProjectMemberTemplateController@storeGroup'])->name('project-template-members.storeGroup');
                Route::resource('project-template-member', 'ProjectMemberTemplateController');

                Route::get('project-template-task/data/{templateId?}', ['uses' => 'ProjectTemplateTaskController@data'])->name('project-template-task.data');
                Route::get('project-template-task/detail/{id?}', ['uses' => 'ProjectTemplateTaskController@taskDetail'])->name('project-template-task.detail');
                Route::post('project-template-task/upadte-order', ['uses' => 'ProjectTemplateTaskController@updateOrder'])->name('project-template-task.upadte-order');
                Route::resource('project-template-task', 'ProjectTemplateTaskController');
                
                
                Route::get('project-template-milestone/data/{templateId?}', ['uses' => 'ProjectTemplateMilestoneController@data'])->name('project-template-milestone.data');
                Route::get('project-template-milestone/detail/{id?}', ['uses' => 'ProjectTemplateMilestoneController@milestoneDetail'])->name('project-template-milestone.detail');
                Route::resource('project-template-milestone', 'ProjectTemplateMilestoneController');

                Route::post('projectCategory/store-cat', ['uses' => 'ManageProjectCategoryController@storeCat'])->name('projectCategory.store-cat');
                Route::get('projectCategory/create-cat', ['uses' => 'ManageProjectCategoryController@createCat'])->name('projectCategory.create-cat');
                Route::resource('projectCategory', 'ManageProjectCategoryController');

                Route::post('taskCategory/store-cat', ['uses' => 'ManageTaskCategoryController@storeCat'])->name('taskCategory.store-cat');
                Route::get('taskCategory/create-cat', ['uses' => 'ManageTaskCategoryController@createCat'])->name('taskCategory.create-cat');
                Route::resource('taskCategory', 'ManageTaskCategoryController');

                Route::get('notices/export/{startDate}/{endDate}', ['uses' => 'ManageNoticesController@export'])->name('notices.export');
                Route::resource('notices', 'ManageNoticesController');

                Route::get('settings/change-language', ['uses' => 'OrganisationSettingsController@changeLanguage'])->name('settings.change-language');
                Route::resource('settings', 'OrganisationSettingsController', ['only' => ['edit', 'update', 'index', 'change-language']]);
                
                // Added By SB
                Route::get('task-template/data', ['uses' => 'TaskTemplateController@data'])->name('task-template.data');
                Route::get('task-template/detail/{id?}', ['uses' => 'TaskTemplateController@taskDetail'])->name('task-template.detail');
                Route::resource('task-template', 'TaskTemplateController');
                Route::resource('pinned', 'ManagePinnedController', ['only' => ['store', 'destroy']]);
                
                
                Route::get('contract-template/data', ['uses' => 'AdminContractTemplateController@data'])->name('contract-template.data');
                Route::resource('contract-template', 'AdminContractTemplateController');
                
                // END


                Route::group(
                    ['prefix' => 'settings'],
                    function () {
                        Route::get('email-settings/sent-test-email', ['uses' => 'EmailNotificationSettingController@sendTestEmail'])->name('email-settings.sendTestEmail');
                        Route::post('email-settings/updateMailConfig', ['uses' => 'EmailNotificationSettingController@updateMailConfig'])->name('email-settings.updateMailConfig');
                        Route::resource('email-settings', 'EmailNotificationSettingController');
                        Route::resource('profile-settings', 'AdminProfileSettingsController');
                        //Bitsclan code start here

                        Route::resource('quickbooks-settings', 'AdminQuickbookSettingsController');
                        Route::resource('integrations', 'AdminQuickbookSettingsController');
                        Route::get('quickbooks-connect', 'AdminQuickbookSettingsController@save_token')->name('quickbooks-connect');
                        
                        Route::get('google-oauth', 'AdminQuickbookSettingsController@google_oauth')->name('google-oauth');
                        Route::post('google-disconnect', 'AdminQuickbookSettingsController@google_disconnect')->name('google-disconnect');

                        //bitsclan code ends here

                        Route::get('currency/exchange-key', ['uses' => 'CurrencySettingController@currencyExchangeKey'])->name('currency.exchange-key');
                        Route::post('currency/exchange-key-store', ['uses' => 'CurrencySettingController@currencyExchangeKeyStore'])->name('currency.exchange-key-store');
                        Route::resource('currency', 'CurrencySettingController');
                        Route::get('currency/exchange-rate/{currency}', ['uses' => 'CurrencySettingController@exchangeRate'])->name('currency.exchange-rate');
                        Route::get('currency/update/exchange-rates', ['uses' => 'CurrencySettingController@updateExchangeRate'])->name('currency.update-exchange-rates');
                        Route::resource('currency', 'CurrencySettingController');


                        Route::post('theme-settings/activeTheme', ['uses' => 'ThemeSettingsController@activeTheme'])->name('theme-settings.activeTheme');
                        Route::post('theme-settings/roundedTheme', ['uses' => 'ThemeSettingsController@roundedTheme'])->name('theme-settings.roundedTheme');
                        Route::resource('theme-settings', 'ThemeSettingsController');
                        Route::resource('project-settings', 'ProjectSettingsController');

                        // Log time
                        Route::resource('log-time-settings', 'LogTimeSettingsController');
                        Route::resource('task-settings', 'TaskSettingsController',  ['only' => ['index', 'store']]);

                        Route::resource('payment-gateway-credential', 'PaymentGatewayCredentialController');
                        Route::resource('invoice-settings', 'InvoiceSettingController');

                        Route::get('slack-settings/sendTestNotification', ['uses' => 'SlackSettingController@sendTestNotification'])->name('slack-settings.sendTestNotification');
                        Route::post('slack-settings/updateSlackNotification/{id}', ['uses' => 'SlackSettingController@updateSlackNotification'])->name('slack-settings.updateSlackNotification');
                        Route::resource('slack-settings', 'SlackSettingController');

                        Route::get('push-notification-settings/sendTestNotification', ['uses' => 'PushNotificationController@sendTestNotification'])->name('push-notification-settings.sendTestNotification');
                        Route::post('push-notification-settings/updatePushNotification/{id}', ['uses' => 'PushNotificationController@updatePushNotification'])->name('push-notification-settings.updatePushNotification');
                        Route::resource('push-notification-settings', 'PushNotificationController');

                        Route::post('ticket-agents/update-group/{id}', ['uses' => 'TicketAgentsController@updateGroup'])->name('ticket-agents.update-group');
                        Route::resource('ticket-agents', 'TicketAgentsController');
                        Route::resource('ticket-groups', 'TicketGroupsController');

                        Route::get('ticketTypes/createModal', ['uses' => 'TicketTypesController@createModal'])->name('ticketTypes.createModal');
                        Route::resource('ticketTypes', 'TicketTypesController');
                        
                        Route::get('codeTypes/createModal', ['uses' => 'CodeTypesController@createModal'])->name('codeTypes.createModal');
                        Route::post('codeTypes/store-type', ['uses' => 'CodeTypesController@storeType'])->name('codeTypes.store-type');
                        Route::get('codeTypes/create-type', ['uses' => 'CodeTypesController@createType'])->name('codeTypes.create-type');
                        
                        Route::resource('codeTypes', 'CodeTypesController');
                        
                        Route::get('salescategoryTypes/createModal', ['uses' => 'SalescategoryTypesController@createModal'])->name('salescategoryTypes.createModal');
                        Route::post('salescategoryTypes/store-category', ['uses' => 'SalescategoryTypesController@storeCategory'])->name('salescategoryTypes.store-category');
                        Route::get('salescategoryTypes/create-category', ['uses' => 'SalescategoryTypesController@createCategory'])->name('salescategoryTypes.create-category');
                        Route::resource('salescategoryTypes', 'SalescategoryTypesController');
                        
                        
                        Route::get('product-status/createModal', ['uses' => 'ProductStatusController@createModal'])->name('product-status.createModal');
                        Route::post('product-status/store-status', ['uses' => 'ProductStatusController@storeStatus'])->name('product-status.store-status');
                        Route::get('product-status/create-status', ['uses' => 'ProductStatusController@createStatus'])->name('product-status.create-status');
                        Route::resource('product-status', 'ProductStatusController');
                        
                        
                        

                        Route::get('lead-source-settings/createModal', ['uses' => 'LeadSourceSettingController@createModal'])->name('leadSetting.createModal');
                        Route::resource('lead-source-settings', 'LeadSourceSettingController');

                        Route::get('lead-status-settings/createModal', ['uses' => 'LeadStatusSettingController@createModal'])->name('leadSetting.createModal');
                        Route::resource('lead-status-settings', 'LeadStatusSettingController');
                        
                        // Added by SB
                        Route::get('lead-form-settings/createModal', ['uses' => 'LeadFormSettingController@createModal'])->name('leadSetting.createModal');
                        Route::get('lead-form-settings/getformData/{id}', ['uses' => 'LeadFormSettingController@getformData'])->name('lead-form-settings.get-form-data');
                        Route::resource('lead-form-settings', 'LeadFormSettingController');
                        // End

                        Route::post('lead-agent-settings/create-agent', ['uses' => 'LeadAgentSettingController@storeAgent'])->name('lead-agent-settings.create-agent');
                        Route::resource('lead-agent-settings', 'LeadAgentSettingController');

                        Route::get('offline-payment-setting/createModal', ['uses' => 'OfflinePaymentSettingController@createModal'])->name('offline-payment-setting.createModal');
                        Route::resource('offline-payment-setting', 'OfflinePaymentSettingController');

                        Route::get('ticketChannels/createModal', ['uses' => 'TicketChannelsController@createModal'])->name('ticketChannels.createModal');
                        Route::resource('ticketChannels', 'TicketChannelsController');

                        Route::post('replyTemplates/fetch-template', ['uses' => 'TicketReplyTemplatesController@fetchTemplate'])->name('replyTemplates.fetchTemplate');
                        Route::resource('replyTemplates', 'TicketReplyTemplatesController');

                        Route::resource('attendance-settings', 'AttendanceSettingController');

                        Route::resource('leaves-settings', 'LeavesSettingController');

                        Route::get('data', ['uses' => 'AdminCustomFieldsController@getFields'])->name('custom-fields.data');
                        Route::resource('custom-fields', 'AdminCustomFieldsController');

                        // Message settings
                        Route::resource('message-settings', 'MessageSettingsController');
                        
                        // Product settings
                        Route::resource('product-settings', 'ProductSettingsController');
                        

                        // Module settings
                        Route::resource('module-settings', 'ModuleSettingsController');

                        // Sales Category settings
                        Route::resource('sales-category-settings', 'SalesCategorySettingsController');

                        Route::get('gdpr/lead/approve-reject/{id}/{type}', ['uses' => 'GdprSettingsController@approveRejectLead'])->name('gdpr.lead.approve-reject');
                        Route::get('gdpr/approve-reject/{id}/{type}', ['uses' => 'GdprSettingsController@approveReject'])->name('gdpr.approve-reject');

                        Route::get('gdpr/lead/removal-data', ['uses' => 'GdprSettingsController@removalLeadData'])->name('gdpr.lead.removal-data');
                        Route::get('gdpr/removal-data', ['uses' => 'GdprSettingsController@removalData'])->name('gdpr.removal-data');
                        Route::put('gdpr/update-consent/{id}', ['uses' => 'GdprSettingsController@updateConsent'])->name('gdpr.update-consent');
                        Route::get('gdpr/edit-consent/{id}', ['uses' => 'GdprSettingsController@editConsent'])->name('gdpr.edit-consent');
                        Route::delete('gdpr/purpose-delete/{id}', ['uses' => 'GdprSettingsController@purposeDelete'])->name('gdpr.purpose-delete');
                        Route::get('gdpr/consent-data', ['uses' => 'GdprSettingsController@data'])->name('gdpr.purpose-data');
                        Route::post('gdpr/store-consent', ['uses' => 'GdprSettingsController@storeConsent'])->name('gdpr.store-consent');
                        Route::get('gdpr/add-consent', ['uses' => 'GdprSettingsController@AddConsent'])->name('gdpr.add-consent');
                        Route::get('gdpr/consent', ['uses' => 'GdprSettingsController@consent'])->name('gdpr.consent');
                        Route::get('gdpr/right-of-access', ['uses' => 'GdprSettingsController@rightOfAccess'])->name('gdpr.right-of-access');
                        Route::get('gdpr/right-to-informed', ['uses' => 'GdprSettingsController@rightToInformed'])->name('gdpr.right-to-informed');
                        Route::get('gdpr/right-to-data-portability', ['uses' => 'GdprSettingsController@rightToDataPortability'])->name('gdpr.right-to-data-portability');
                        Route::get('gdpr/right-to-erasure', ['uses' => 'GdprSettingsController@rightToErasure'])->name('gdpr.right-to-erasure');
                        Route::resource('gdpr', 'GdprSettingsController', ['only' => ['index', 'store']]);

                        //Email Template Route
                        Route::resource('email-template', 'EmailTemplateController',['only' => ['index', 'create', 'store', 'edit']]);
                        Route::post('email-template/{emailtemplate}', ['uses' => 'EmailTemplateController@update'])->name('email-template.update');
                        Route::delete('email-template/{emailtemplate}', ['uses' => 'EmailTemplateController@destroy'])->name('email-template.destroy');

                        // Email Connection Setting Route
                        Route::get('email-connection-setting', ['uses' => 'EmailConnectionSetting@index'])->name('email-connection-setting.index');
                        Route::post('email-connection-setting', ['uses' => 'EmailConnectionSetting@update'])->name('email-connection-setting.update');
                        Route::post('email-connection-setting/test-mail', ['uses' => 'EmailConnectionSetting@testEmail'])->name('email-connection-setting.test-mail');
                    }
                );

                Route::group(
                    ['prefix' => 'projects'],
                    function () {
                        Route::post('project-members/save-group', ['uses' => 'ManageProjectMembersController@storeGroup'])->name('project-members.storeGroup');
                        Route::resource('project-members', 'ManageProjectMembersController');

                        Route::post('tasks/sort', ['uses' => 'ManageTasksController@sort'])->name('tasks.sort');
                        Route::post('tasks/change-status', ['uses' => 'ManageTasksController@changeStatus'])->name('tasks.changeStatus');
                        Route::get('tasks/check-task/{taskID}', ['uses' => 'ManageTasksController@checkTask'])->name('tasks.checkTask');
                        Route::post('tasks/data/{projectId?}', 'ManageTasksController@data')->name('tasks.data');
                        Route::get('tasks/export/{projectId?}', 'ManageTasksController@export')->name('tasks.export');

                        Route::resource('tasks', 'ManageTasksController');

                        Route::post('files/store-link', ['uses' => 'ManageProjectFilesController@storeLink'])->name('files.storeLink');
                        Route::get('files/download/{id}', ['uses' => 'ManageProjectFilesController@download'])->name('files.download');
                        Route::get('files/thumbnail', ['uses' => 'ManageProjectFilesController@thumbnailShow'])->name('files.thumbnail');
                        Route::post('files/multiple-upload', ['uses' => 'ManageProjectFilesController@storeMultiple'])->name('files.multiple-upload');
                        Route::post('files/show-hide/{id}', ['uses' => 'ManageProjectFilesController@showHide'])->name('files.show-hide');
                        
                        Route::post('files/create_folder', ['uses' => 'ManageProjectFilesController@create_folder'])->name('files.create_folder');
                        Route::any('files/delete_folder/{folder_id}', ['uses' => 'ManageProjectFilesController@delete_folder'])->name('files.delete_folder');
                        
                        Route::resource('files', 'ManageProjectFilesController');

                        Route::get('invoices/download/{id}', ['uses' => 'ManageInvoicesController@download'])->name('invoices.download');
                        Route::get('invoices/create-invoice/{id}', ['uses' => 'ManageInvoicesController@createInvoice'])->name('invoices.createInvoice');
                        Route::resource('invoices', 'ManageInvoicesController');

                        Route::resource('issues', 'ManageIssuesController');

                        Route::post('time-logs/stop-timer/{id}', ['uses' => 'ManageTimeLogsController@stopTimer'])->name('time-logs.stopTimer');
                        Route::get('time-logs/data/{id}', ['uses' => 'ManageTimeLogsController@data'])->name('time-logs.data');
                        Route::resource('time-logs', 'ManageTimeLogsController');


                        Route::get('milestones/detail/{id}', ['uses' => 'ManageProjectMilestonesController@detail'])->name('milestones.detail');
                        Route::get('milestones/data/{id}', ['uses' => 'ManageProjectMilestonesController@data'])->name('milestones.data');
                        Route::resource('milestones', 'ManageProjectMilestonesController');
                        
                        Route::get('rooms/detail/{id}', ['uses' => 'ManageProjectRoomsController@detail'])->name('rooms.detail');
                        Route::get('rooms/data/{id}', ['uses' => 'ManageProjectRoomsController@data'])->name('rooms.data');
                        Route::resource('rooms', 'ManageProjectRoomsController');

                        Route::resource('project-expenses', 'ManageProjectExpensesController');
                        Route::resource('project-payments', 'ManageProjectPaymentsController');
                        
                        Route::get('products-project/data/{id}', ['uses' => 'ManageProjectProductsController@data'])->name('products-project.data');
                        Route::resource('products-project', 'ManageProjectProductsController');
                        
                        Route::get('purchase-orders-project/data/{id}', ['uses' => 'ManageProjectPurchaseOrdersController@data'])->name('purchase-orders-project.data');
                        Route::get('purchase-orders-project/archive-delete/{id?}', ['uses' => 'ManageProjectPurchaseOrdersController@archiveDestroy'])->name('purchase-orders-project.archive-delete');
                        Route::resource('purchase-orders-project', 'ManageProjectPurchaseOrdersController');
                        
                        Route::get('product-review-project/data/{id}', ['uses' => 'ManageProjectProductReviewController@data'])->name('product-review-project.data');
                        Route::get('product-review-project/view-product-notes/{productId?}', ['uses' => 'ManageProjectProductReviewController@viewProductNotes'])->name('product-review-project.view-product-notes');
                        Route::get('product-review-project/view-location-notes/{code_type_id?}', ['uses' => 'ManageProjectProductReviewController@viewLocationNotes'])->name('product-review-project.view-location-notes');
                        Route::post('product-review-project/create-finances', 'ManageProjectProductReviewController@createFinance')->name('product-review-project.create-finances');
                        
                        Route::post('product-review-project/updateSetting/{id}', 'ManageProjectProductReviewController@updateSetting')->name('product-review-project.updateSetting');
                        Route::get('product-review-project/detail/{id}/{pid}', ['uses' => 'ManageProjectProductReviewController@detail'])->name('product-review-project.detail');
                        Route::resource('product-review-project', 'ManageProjectProductReviewController');
                        
                         
                        
                        
                        Route::get('estimates-project/data/{id}', ['uses' => 'ManageProjectEstimatesController@data'])->name('estimates-project.data');
                        Route::resource('estimates-project', 'ManageProjectEstimatesController');
                        
                        Route::get('invoices-project/data/{id}', ['uses' => 'ManageProjectInvoicesController@data'])->name('invoices-project.data');
                        Route::resource('invoices-project', 'ManageProjectInvoicesController');
                        
                        Route::get('visionboards/detail/{id}', ['uses' => 'ManageProjectVisionboardsController@detail'])->name('visionboards.detail');
                        Route::get('visionboards/data/{id}', ['uses' => 'ManageProjectVisionboardsController@data'])->name('visionboards.data');
                        Route::resource('visionboards', 'ManageProjectVisionboardsController');
                        
                    }
                );

                Route::group(
                    ['prefix' => 'clients'],
                    function () {
                        Route::post('save-consent-purpose-data/{client}', ['uses' => 'ManageClientsController@saveConsentLeadData'])->name('clients.save-consent-purpose-data');
                        Route::get('consent-purpose-data/{client}', ['uses' => 'ManageClientsController@consentPurposeData'])->name('clients.consent-purpose-data');
                        Route::get('gdpr/{id}', ['uses' => 'ManageClientsController@gdpr'])->name('clients.gdpr');
                        Route::get('projects/{id}', ['uses' => 'ManageClientsController@showProjects'])->name('clients.projects');
                        Route::get('invoices/{id}', ['uses' => 'ManageClientsController@showInvoices'])->name('clients.invoices');
                        Route::get('payments/{id}', ['uses' => 'ManageClientsController@showPayments'])->name('clients.payments');
                        Route::get('audit/{id}', ['uses' => 'ManageClientsController@showAudits'])->name('clients.audit');

                        Route::get('notes/{id}', ['uses' => 'ManageClientsController@showNotes'])->name('clients.notes');

                        Route::get('contacts/data/{id}', ['uses' => 'ClientContactController@data'])->name('contacts.data');
                        Route::resource('contacts', 'ClientContactController');
                    }
                );

                Route::get('all-issues/data', ['uses' => 'ManageAllIssuesController@data'])->name('all-issues.data');
                Route::resource('all-issues', 'ManageAllIssuesController');

                Route::get('all-time-logs/members/{projectId}', ['uses' => 'ManageAllTimeLogController@membersList'])->name('all-time-logs.members');
                Route::get('all-time-logs/task-members/{taskId}', ['uses' => 'ManageAllTimeLogController@taskMembersList'])->name('all-time-logs.task-members');
                Route::get('all-time-logs/show-active-timer', ['uses' => 'ManageAllTimeLogController@showActiveTimer'])->name('all-time-logs.show-active-timer');
                Route::get('all-time-logs/export/{startDate?}/{endDate?}/{projectId?}/{employee?}', ['uses' => 'ManageAllTimeLogController@export'])->name('all-time-logs.export');
                Route::post('all-time-logs/stop-timer/{id}', ['uses' => 'ManageAllTimeLogController@stopTimer'])->name('all-time-logs.stopTimer');
                Route::post('all-time-logs/data', ['uses' => 'ManageAllTimeLogController@data'])->name('all-time-logs.data');
                Route::get('all-time-logs/by-employee', ['uses' => 'ManageAllTimeLogController@byEmployee'])->name('all-time-logs.by-employee');
                Route::post('all-time-logs/userTimelogs', ['uses' => 'ManageAllTimeLogController@userTimelogs'])->name('all-time-logs.userTimelogs');
                Route::post('all-time-logs/approve-timelog', ['uses' => 'ManageAllTimeLogController@approveTimelog'])->name('all-time-logs.approve-timelog');
                Route::post('all-time-logs/calculate-time', ['uses' => 'ManageAllTimeLogController@calculateTime'])->name('all-time-logs.calculate-time');
                
                Route::resource('all-time-logs', 'ManageAllTimeLogController');

                // task routes
                Route::resource('task', 'ManageAllTasksController', ['only' => ['edit', 'update', 'index']]); // hack to make left admin menu item active
                Route::group(
                    ['prefix' => 'task'],
                    function () {

                        Route::get('all-tasks/export/{startDate?}/{endDate?}/{projectId?}/{hideCompleted?}', ['uses' => 'ManageAllTasksController@export'])->name('all-tasks.export');
                        Route::get('all-tasks/dependent-tasks/{projectId}/{taskId?}', ['uses' => 'ManageAllTasksController@dependentTaskLists'])->name('all-tasks.dependent-tasks');
                        Route::get('all-tasks/members/{projectId}', ['uses' => 'ManageAllTasksController@membersList'])->name('all-tasks.members');
                        Route::get('all-tasks/ajaxCreate/{columnId}', ['uses' => 'ManageAllTasksController@ajaxCreate'])->name('all-tasks.ajaxCreate');
                        Route::get('all-tasks/reminder/{taskid}', ['uses' => 'ManageAllTasksController@remindForTask'])->name('all-tasks.reminder');
                        Route::get('all-tasks/files/{taskid}', ['uses' => 'ManageAllTasksController@showFiles'])->name('all-tasks.show-files');
                        Route::get('all-tasks/history/{taskid}', ['uses' => 'ManageAllTasksController@history'])->name('all-tasks.history');
                        
                        Route::get('all-tasks/template/{templateId}', ['uses' => 'ManageAllTasksController@templateData'])->name('all-tasks.template');
                        

                        Route::get('all-tasks/get-task/{taskid}', ['uses' => 'ManageAllTasksController@getTaskDetail'])->name('all-tasks.get-task');
                        
                        Route::get('all-tasks/create-taskboard/', ['uses' => 'ManageAllTasksController@createTaskboard'])->name('all-tasks.create-taskboard');
                        
                        
                        Route::get('all-tasks/pinned-task', ['uses' => 'ManageAllTasksController@pinnedItem'])->name('all-tasks.pinned-task');
                        
                        
                        //Added by Sb
                        Route::get('all-tasks/download/template', ['uses' => 'ManageAllTasksController@downloadTemplate'])->name('all-tasks.download-template');
                        Route::post('all-tasks/import', ['uses' => 'ManageAllTasksController@import'])->name('all-tasks.import');
                        
                        Route::post('all-tasks/live-update/{taskid}', ['uses' => 'ManageAllTasksController@liveUpdate'])->name('all-tasks.live-update');
                        Route::post('all-tasks/live-timeLog/{taskid}', ['uses' => 'ManageAllTasksController@liveTimeLog'])->name('all-tasks.live-timeLog');
                        Route::post('all-tasks/live-timeLog-stop/{taskid}', ['uses' => 'ManageAllTasksController@liveTimeLogStop'])->name('all-tasks.live-timeLog-stop');
                        
                        // End
                        
                        
                        Route::resource('all-tasks', 'ManageAllTasksController');

                        // taskboard resource
                        Route::post('taskboard/updateIndex', ['as' => 'taskboard.updateIndex', 'uses' => 'AdminTaskboardController@updateIndex']);
                        Route::post('taskboard/updatePriority', ['as' => 'taskboard.updatePriority', 'uses' => 'AdminTaskboardController@updatePriority']); // Added by SB
                        Route::get('taskboard/show/{id}', ['as' => 'taskboard.show', 'uses' => 'AdminTaskboardController@show']); // Added by SB
                        Route::resource('taskboard', 'AdminTaskboardController');

                        // task calendar routes
                        Route::resource('task-calendar', 'AdminCalendarController');
                        Route::get('task-files/download/{id}', ['uses' => 'TaskFilesController@download'])->name('task-files.download');
                        Route::resource('task-files', 'TaskFilesController');
                    }
                );

                Route::resource('sticky-note', 'ManageStickyNotesController');


                Route::resource('reports', 'TaskReportController', ['only' => ['edit', 'update', 'index']]); // hack to make left admin menu item active
                Route::group(
                    ['prefix' => 'reports'],
                    function () {
                        Route::post('task-report/data', ['uses' => 'TaskReportController@data'])->name('task-report.data');
                        Route::post('task-report/export', ['uses' => 'TaskReportController@export'])->name('task-report.export');
                        Route::resource('task-report', 'TaskReportController');
                        Route::resource('time-log-report', 'TimeLogReportController');
                        Route::resource('finance-report', 'FinanceReportController');
                        Route::resource('income-expense-report', 'IncomeVsExpenseReportController');
                        //region Leave Report routes
                        Route::post('leave-report/data', ['uses' => 'LeaveReportController@data'])->name('leave-report.data');
                        Route::post('leave-report/export', 'LeaveReportController@export')->name('leave-report.export');
                        Route::get('leave-report/pending-leaves/{id?}', 'LeaveReportController@pendingLeaves')->name('leave-report.pending-leaves');
                        Route::get('leave-report/upcoming-leaves/{id?}', 'LeaveReportController@upcomingLeaves')->name('leave-report.upcoming-leaves');
                        Route::resource('leave-report', 'LeaveReportController');

                        Route::post('attendance-report/report', ['uses' => 'AttendanceReportController@report'])->name('attendance-report.report');
                        Route::get('attendance-report/export/{startDate}/{endDate}/{employee}', ['uses' => 'AttendanceReportController@reportExport'])->name('attendance-report.reportExport');
                        Route::resource('attendance-report', 'AttendanceReportController');
                        //endregion
                        //project status report
                        Route::resource('projectstatus', 'ProjectStatusController');
                        Route::get('projectstatus/report', ['uses' => 'ProjectStatusController@report'])->name('projectstatus.report');



                    }
                );

                Route::resource('search', 'AdminSearchController');

                // Automation
                Route::resource('email-automation', 'EmailAutomationController')->only(['index', 'create', 'store']);
                Route::get('email-automation/{id}/edit', 'EmailAutomationController@edit')->name('email-automation.edit');
                Route::delete('email-automation/{id}/destroy', 'EmailAutomationController@destroy')->name('email-automation.destroy');
                Route::post('email-automation/get-email-template-detail', ['uses' => 'EmailAutomationController@getEmailTemplateDetail'])->name('email-automation.get.email.template.detail');
                Route::post('email-automation/get-email-file-template', ['uses' => 'EmailAutomationController@getEmailFileTemplate'])->name('email-automation.get.email.files');
                Route::post('email-automation/update-email-template', ['uses' => 'EmailAutomationController@updateEmailTemplate'])->name('email-automation.update.email.template');
                Route::post('email-automation/get-automation-template', ['uses' => 'EmailAutomationController@getAutomationTemplate'])->name('email-automation.get-automation-template');
                Route::post('email-automation/make-it-duplicate', ['uses' => 'EmailAutomationController@duplicateAutomation'])->name('email-automation.make-it-duplicate');
                Route::post('email-automation/get-projects', ['uses' => 'EmailAutomationController@getProjects'])->name('email-automation.get-projects');


                Route::resource('finance', 'ManageEstimatesController', ['only' => ['edit', 'update', 'index']]); // hack to make left admin menu item active
                //Admin Finance Section here
                Route::group(
                    ['prefix' => 'finance'],
                    function () {

                        Route::get('all-invoices/getVendors','ManageAllInvoicesController@getVendorName')->name('allInvoices.getVendors');

                        // Estimate routes
                        Route::get('estimates/download/{id}', ['uses' => 'ManageEstimatesController@download'])->name('estimates.download');
                        Route::get('estimates/export/{startDate}/{endDate}/{status}', ['uses' => 'ManageEstimatesController@export'])->name('estimates.export');
                        Route::get('estimates/create-estimate/{id}', ['uses' => 'ManageEstimatesController@createEstimate'])->name('estimates.createEstimate');
                        
                        Route::get('estimates/view/{id}', ['uses' => 'ManageEstimatesController@view'])->name('estimates.view');
                        
                        Route::resource('estimates', 'ManageEstimatesController');

                        //Expenses routes
                        Route::post('expenses/change-status', ['uses' => 'ManageExpensesController@changeStatus'])->name('expenses.changeStatus');
                        Route::get('expenses/export/{startDate}/{endDate}/{status}/{employee}', ['uses' => 'ManageExpensesController@export'])->name('expenses.export');
                        Route::post('estimates/send-estimate/{id}', ['uses' => 'ManageEstimatesController@sendEstimate'])->name('estimates.send-estimate');
                        // Added By Adil
                        Route::get('expenses/fill', ['uses' => 'ManageExpensesController@dataSource']);
                        Route::resource('expenses', 'ManageExpensesController');


                        // All invoices list routes
                        Route::post('file/store', ['uses' => 'ManageAllInvoicesController@storeFile'])->name('invoiceFile.store');
                        Route::delete('file/destroy', ['uses' => 'ManageAllInvoicesController@destroyFile'])->name('invoiceFile.destroy');
                        Route::get('all-invoices/applied-credits/{id}', ['uses' => 'ManageAllInvoicesController@appliedCredits'])->name('all-invoices.applied-credits');
                        Route::post('all-invoices/delete-applied-credit/{id}', ['uses' => 'ManageAllInvoicesController@deleteAppliedCredit'])->name('all-invoices.delete-applied-credit');
                        Route::get('all-invoices/download/{id}', ['uses' => 'ManageAllInvoicesController@download'])->name('all-invoices.download');
                        Route::get('all-invoices/export/{startDate}/{endDate}/{status}/{projectID}', ['uses' => 'ManageAllInvoicesController@export'])->name('all-invoices.export');
                        Route::get('all-invoices/convert-estimate/{id}', ['uses' => 'ManageAllInvoicesController@convertEstimate'])->name('all-invoices.convert-estimate');
                        Route::get('all-invoices/convert-milestone/{id}', ['uses' => 'ManageAllInvoicesController@convertMilestone'])->name('all-invoices.convert-milestone');
                        Route::get('all-invoices/convert-proposal/{id}', ['uses' => 'ManageAllInvoicesController@convertProposal'])->name('all-invoices.convert-proposal');
                        Route::get('all-invoices/update-item', ['uses' => 'ManageAllInvoicesController@addItems'])->name('all-invoices.update-item');
                        Route::get('all-invoices/payment-detail/{invoiceID}', ['uses' => 'ManageAllInvoicesController@paymentDetail'])->name('all-invoices.payment-detail');
                        Route::get('all-invoices/get-client-company/{projectID?}', ['uses' => 'ManageAllInvoicesController@getClientOrCompanyName'])->name('all-invoices.get-client-company');
                        Route::get('all-invoices/get-client/{projectID}', ['uses' => 'ManageAllInvoicesController@getClient'])->name('all-invoices.get-client');
                        Route::get('all-invoices/check-shipping-address', ['uses' => 'ManageAllInvoicesController@checkShippingAddress'])->name('all-invoices.checkShippingAddress');
                        Route::get('all-invoices/toggle-shipping-address/{invoice}', ['uses' => 'ManageAllInvoicesController@toggleShippingAddress'])->name('all-invoices.toggleShippingAddress');
                        Route::get('all-invoices/shipping-address-modal/{invoice}', ['uses' => 'ManageAllInvoicesController@shippingAddressModal'])->name('all-invoices.shippingAddressModal');
                        Route::post('all-invoices/add-shipping-address/{user}', ['uses' => 'ManageAllInvoicesController@addShippingAddress'])->name('all-invoices.addShippingAddress');
                        Route::get('all-invoices/payment-reminder/{invoiceID}', ['uses' => 'ManageAllInvoicesController@remindForPayment'])->name('all-invoices.payment-reminder');
                        Route::get('all-invoices/payment-verify/{invoiceID}', ['uses' => 'ManageAllInvoicesController@verifyOfflinePayment'])->name('all-invoices.payment-verify');
                        Route::post('all-invoices/payment-verify-submit/{offlinePaymentId}', ['uses' => 'ManageAllInvoicesController@verifyPayment'])->name('offline-invoice-payment.verify');
                        Route::post('all-invoices/payment-reject-submit/{offlinePaymentId}', ['uses' => 'ManageAllInvoicesController@rejectPayment'])->name('offline-invoice-payment.reject');
                        Route::get('all-invoices/update-status/{invoiceID}', ['uses' => 'ManageAllInvoicesController@cancelStatus'])->name('all-invoices.update-status');
                        Route::post('all-invoices/fetchTimelogs', ['uses' => 'ManageAllInvoicesController@fetchTimelogs'])->name('all-invoices.fetchTimelogs');
                        Route::post('all-invoices/send-invoice/{invoiceID}', ['uses' => 'ManageAllInvoicesController@sendInvoice'])->name('all-invoices.send-invoice');
                        Route::delete('all-invoices/destroy/{id}', ['uses' => 'ManageAllInvoicesController@destroy'])->name('all-invoices.destroy');
                        
                        Route::get('all-invoices/refund/{invoiceID}', ['uses' => 'ManageAllInvoicesController@refund'])->name('all-invoices.refund');
                        Route::post('all-invoices/refund-update/{invoiceID}', ['uses' => 'ManageAllInvoicesController@refundUpdate'])->name('all-invoices.refund-update');

                        // Added By SB
                        Route::resource('all-invoices', 'ManageAllInvoicesController');
                        // End
                        
                        // Client Invoices Related Routes .. Added By Adil.
                        Route::resource('client-invoice', 'ManageAllInvoicesController');
                        Route::get('clientInvoice/download/{id}', ['uses' => 'ManageAllInvoicesController@download'])->name('client-invoice.download');
                        Route::get('clientInvoice/edit/{id}', ['uses' => 'ManageAllInvoicesController@edit'])->name('client-invoice.edit');
                        Route::get('clientInvoice/show/{id}', ['uses' => 'ManageAllInvoicesController@show'])->name('client-invoice.show');
                        Route::get('clientInvoice/view/{id}', ['uses' => 'ManageAllInvoicesController@view'])->name('client-invoice.view');
                        Route::post('clientInvoice/store', ['uses' => 'ManageAllInvoicesController@store'])->name('client-invoice.store');
                        
                         //Vendor Invoice Route
                        Route::get('vendor-invoice', 'ManageAllInvoicesController@vendorIndex')->name('vendor-invoice');

                        Route::get('invoice/client', 'ManageAllInvoicesController@create')->name('invoices.client');
                        Route::get('invoice/vendor', 'ManageAllInvoicesController@createVendor')->name('invoices.vendor');


                        // Added By Adil
                        Route::get('all-invoices/show/{type}', ['uses' => 'ManageAllInvoicesController@dataSource']);

                        Route::post('invoices/createvendorinvoice', 'VendorInvoiceController@store');


                        // All Credit Note routes
                        Route::post('credit-file/store', ['uses' => 'ManageAllCreditNotesController@storeFile'])->name('creditNoteFile.store');
                        Route::delete('credit-file/destroy', ['uses' => 'ManageAllCreditNotesController@destroyFile'])->name('creditNoteFile.destroy');
                        Route::get('all-credit-notes/apply-to-invoice/{id}', ['uses' => 'ManageAllCreditNotesController@applyToInvoiceModal'])->name('all-credit-notes.apply-to-invoice-modal');
                        Route::post('all-credit-notes/apply-to-invoice/{id}', ['uses' => 'ManageAllCreditNotesController@applyToInvoice'])->name('all-credit-notes.apply-to-invoice');
                        Route::get('all-credit-notes/credited-invoices/{id}', ['uses' => 'ManageAllCreditNotesController@creditedInvoices'])->name('all-credit-notes.credited-invoices');
                        Route::post('all-credit-notes/delete-credited-invoice/{id}', ['uses' => 'ManageAllCreditNotesController@deleteCreditedInvoice'])->name('all-credit-notes.delete-credited-invoice');
                        Route::get('all-credit-notes/download/{id}', ['uses' => 'ManageAllCreditNotesController@download'])->name('all-credit-notes.download');
                        Route::get('all-credit-notes/export/{startDate}/{endDate}/{projectID}', ['uses' => 'ManageAllCreditNotesController@export'])->name('all-credit-notes.export');
                        Route::get('all-credit-notes/convert-invoice/{id}', ['uses' => 'ManageAllCreditNotesController@convertInvoice'])->name('all-credit-notes.convert-invoice');
                        // Route::get('all-credit-notes/convert-proposal/{id}', ['uses' => 'ManageAllCreditNotesController@convertProposal'])->name('all-credit-notes.convert-proposal');
                        Route::get('all-credit-notes/update-item', ['uses' => 'ManageAllCreditNotesController@addItems'])->name('all-credit-notes.update-item');
                        Route::get('all-credit-notes/payment-detail/{creditNoteID}', ['uses' => 'ManageAllCreditNotesController@paymentDetail'])->name('all-credit-notes.payment-detail');
                        
                        Route::get('products-project/data/{id}', ['uses' => 'ManageProjectProductsController@data'])->name('products-project.data');
                        
                        
                        Route::get('project/credit-notes/{id}', ['uses' => 'ManageAllCreditNotesController@projectNotesShow'])->name('project.credit-notes');
                        Route::resource('all-credit-notes', 'ManageAllCreditNotesController');

                        //Payments routes
                        Route::get('payments/export/{startDate}/{endDate}/{status}/{payment}', ['uses' => 'ManagePaymentsController@export'])->name('payments.export');
                        Route::get('payments/pay-invoice/{invoiceId}', ['uses' => 'ManagePaymentsController@payInvoice'])->name('payments.payInvoice');
                        Route::get('payments/download', ['uses' => 'ManagePaymentsController@downloadSample'])->name('payments.downloadSample');
                        Route::post('payments/import', ['uses' => 'ManagePaymentsController@importExcel'])->name('payments.importExcel');
                        Route::resource('payments', 'ManagePaymentsController');
                        
                        
                        //Added by SB
                        
//                            Route::get('purchase-orders/getVendors','ManagePurchaseOrdersController@getVendorName')->name('purchase-orders.getVendors');
//                            
//                            Route::get('purchase-orders/update-status/{invoiceID}', ['uses' => 'ManagePurchaseOrdersController@cancelStatus'])->name('purchase-orders.update-status');
//                            
//                            Route::get('purchase-orders/edit/{id}', ['uses' => 'ManagePurchaseOrdersController@edit'])->name('purchase-orders.edit');
//                            Route::get('purchase-orders/show/{id}', ['uses' => 'ManagePurchaseOrdersController@show'])->name('purchase-orders.show');
//                            Route::post('purchase-orders/store', ['uses' => 'ManagePurchaseOrdersController@store'])->name('purchase-orders.store');
//                            Route::resource('purchase-orders', 'ManagePurchaseOrdersController');
                        
                        
                                
                                //Route::get('purchase-orders/create-invoice/{id}', ['uses' => 'ManageInvoicesController@createInvoice'])->name('invoices.createInvoice');
                        
                                Route::get('purchase-orders/update-item', ['uses' => 'ManagePurchaseOrdersController@addItems'])->name('purchase-orders.update-item');
                                Route::get('purchase-orders/create', 'ManagePurchaseOrdersController@create')->name('purchase-orders.create');
                                Route::get('purchase-orders/edit/{id}', ['uses' => 'ManagePurchaseOrdersController@edit'])->name('purchase-orders.edit');
                                Route::get('purchase-orders/get-vendor-detail/{vendorid}', ['uses' => 'ManagePurchaseOrdersController@getVendorDetail'])->name('purchase-orders.get-vendor-detail');
                                Route::get('purchase-orders/download/{id}', ['uses' => 'ManagePurchaseOrdersController@download'])->name('purchase-orders.download');
                                Route::post('purchase-orders/change-status', ['uses' => 'ManagePurchaseOrdersController@changeStatus'])->name('purchase-orders.change-status');
                                Route::post('purchase-orders/sendpdf/{id}', ['uses' => 'ManagePurchaseOrdersController@sendPdf'])->name('purchase-orders.sendpdf');
                                
                                
                                Route::get('purchase-orders/archive-data/{id?}', ['uses' => 'ManagePurchaseOrdersController@archiveData'])->name('purchase-orders.archive-data');
                                Route::get('purchase-orders/archive/{id?}', ['uses' => 'ManagePurchaseOrdersController@archive'])->name('purchase-orders.archive');
                                Route::get('purchase-orders/archive-restore/{id?}', ['uses' => 'ManagePurchaseOrdersController@archiveRestore'])->name('purchase-orders.archive-restore');
                                Route::get('purchase-orders/archive-delete/{id?}', ['uses' => 'ManagePurchaseOrdersController@archiveDestroy'])->name('purchase-orders.archive-delete');
                                
                                Route::get('purchase-orders/convert-purchase-order/{id?}', ['uses' => 'ManagePurchaseOrdersController@convertPurchaseOrder'])->name('purchase-orders.convert-purchase-order');
                                
                                Route::resource('purchase-orders', 'ManagePurchaseOrdersController');
                                
                                Route::get('purchase-order-settings/createModal', ['uses' => 'PurchaseOrdersSettingController@createModal'])->name('purchase-orders-setting.createModal');
                                
                                Route::post('purchase-order-settings/store-status', ['uses' => 'PurchaseOrdersSettingController@storeStatus'])->name('purchase-order-settings.store-status');
                                Route::get('purchase-order-settings/create-status', ['uses' => 'PurchaseOrdersSettingController@createStatus'])->name('purchase-order-settings.create-status');
                                
                                Route::resource('purchase-order-settings', 'PurchaseOrdersSettingController');
                        
                        //End
                        
                        
                        
                        
                    }
                );

                //Ticket routes
                Route::get('tickets/export/{startDate?}/{endDate?}/{agentId?}/{status?}/{priority?}/{channelId?}/{typeId?}', ['uses' => 'ManageTicketsController@export'])->name('tickets.export');
                Route::get('tickets/refresh-count/{startDate?}/{endDate?}/{agentId?}/{status?}/{priority?}/{channelId?}/{typeId?}', ['uses' => 'ManageTicketsController@refreshCount'])->name('tickets.refreshCount');
                Route::get('tickets/reply-delete/{id?}', ['uses' => 'ManageTicketsController@destroyReply'])->name('tickets.reply-delete');
                Route::post('tickets/updateOtherData/{id}', ['uses' => 'ManageTicketsController@updateOtherData'])->name('tickets.updateOtherData');
                Route::get('roadmap', ['uses' => 'ManageTicketsController@roadMap'])->name('roadmap');

                Route::resource('tickets', 'ManageTicketsController');

                Route::get('ticket-files/download/{id}', ['uses' => 'TicketFilesController@download'])->name('ticket-files.download');
                Route::resource('ticket-files', 'TicketFilesController');

                // User message
                Route::post('message-submit', ['as' => 'user-chat.message-submit', 'uses' => 'AdminChatController@postChatMessage']);
                Route::get('user-search', ['as' => 'user-chat.user-search', 'uses' => 'AdminChatController@getUserSearch']);
                Route::resource('user-chat', 'AdminChatController');

                // attendance
                Route::get('attendances/export/{startDate?}/{endDate?}/{employee?}', ['uses' => 'ManageAttendanceController@export'])->name('attendances.export');

                Route::get('attendances/detail', ['uses' => 'ManageAttendanceController@attendanceDetail'])->name('attendances.detail');
                Route::get('attendances/data', ['uses' => 'ManageAttendanceController@data'])->name('attendances.data');
                Route::get('attendances/check-holiday', ['uses' => 'ManageAttendanceController@checkHoliday'])->name('attendances.check-holiday');
                Route::get('attendances/employeeData/{startDate?}/{endDate?}/{userId?}', ['uses' => 'ManageAttendanceController@employeeData'])->name('attendances.employeeData');
                Route::get('attendances/refresh-count/{startDate?}/{endDate?}/{userId?}', ['uses' => 'ManageAttendanceController@refreshCount'])->name('attendances.refreshCount');
                Route::get('attendances/attendance-by-date', ['uses' => 'ManageAttendanceController@attendanceByDate'])->name('attendances.attendanceByDate');
                Route::get('attendances/byDateData', ['uses' => 'ManageAttendanceController@byDateData'])->name('attendances.byDateData');
                Route::post('attendances/dateAttendanceCount', ['uses' => 'ManageAttendanceController@dateAttendanceCount'])->name('attendances.dateAttendanceCount');
                Route::get('attendances/info/{id}', ['uses' => 'ManageAttendanceController@detail'])->name('attendances.info');
                Route::get('attendances/summary', ['uses' => 'ManageAttendanceController@summary'])->name('attendances.summary');
                Route::post('attendances/summaryData', ['uses' => 'ManageAttendanceController@summaryData'])->name('attendances.summaryData');
                Route::post('attendances/storeMark', ['uses' => 'ManageAttendanceController@storeMark'])->name('attendances.storeMark');
                Route::get('attendances/mark/{id}/{day}/{month}/{year}', ['uses' => 'ManageAttendanceController@mark'])->name('attendances.mark');

                Route::resource('attendances', 'ManageAttendanceController');
                
                
                // attendance admin clock in/out
                Route::get('clockinout/detail', ['uses' => 'AdminAttendanceController@attendanceDetail'])->name('clockinout.detail');
                Route::get('clockinout/data', ['uses' => 'AdminAttendanceController@data'])->name('clockinout.data');
                Route::get('clockinout/check-holiday', ['uses' => 'AdminAttendanceController@checkHoliday'])->name('clockinout.check-holiday');
                Route::post('clockinout/storeAttendance', ['uses' => 'AdminAttendanceController@storeAttendance'])->name('clockinout.storeAttendance');
                Route::get('clockinout/employeeData/{startDate?}/{endDate?}/{userId?}', ['uses' => 'AdminAttendanceController@employeeData'])->name('clockinout.employeeData');
                Route::get('clockinout/refresh-count/{startDate?}/{endDate?}/{userId?}', ['uses' => 'AdminAttendanceController@refreshCount'])->name('clockinout.refreshCount');
                Route::post('clockinout/storeMark', ['uses' => 'AdminAttendanceController@storeMark'])->name('clockinout.storeMark');
                Route::get('clockinout/mark/{id}/{day}/{month}/{year}', ['uses' => 'AdminAttendanceController@mark'])->name('clockinout.mark');
                Route::get('clockinout/summary', ['uses' => 'AdminAttendanceController@summary'])->name('clockinout.summary');
                Route::post('clockinout/summaryData', ['uses' => 'AdminAttendanceController@summaryData'])->name('clockinout.summaryData');
                Route::get('clockinout/info/{id}', ['uses' => 'AdminAttendanceController@detail'])->name('clockinout.info');
                Route::post('clockinout/updateDetails/{id}', ['uses' => 'AdminAttendanceController@updateDetails'])->name('clockinout.updateDetails');
                Route::resource('clockinout', 'AdminAttendanceController');
                
                
                

                //Event Calendar
                Route::post('events/removeAttendee', ['as' => 'events.removeAttendee', 'uses' => 'AdminEventCalendarController@removeAttendee']);
                Route::get('events/doSyncGoogleCalendar', ['as' => 'events.doSyncGoogleCalendar', 'uses' => 'AdminEventCalendarController@doSyncGoogleCalendar'])->name('events.doSyncGoogleCalendar');
                Route::resource('events', 'AdminEventCalendarController');


                // Role permission routes
                Route::post('role-permission/assignAllPermission', ['as' => 'role-permission.assignAllPermission', 'uses' => 'ManageRolePermissionController@assignAllPermission']);
                Route::post('role-permission/removeAllPermission', ['as' => 'role-permission.removeAllPermission', 'uses' => 'ManageRolePermissionController@removeAllPermission']);
                Route::post('role-permission/assignRole', ['as' => 'role-permission.assignRole', 'uses' => 'ManageRolePermissionController@assignRole']);
                Route::post('role-permission/detachRole', ['as' => 'role-permission.detachRole', 'uses' => 'ManageRolePermissionController@detachRole']);
                Route::post('role-permission/storeRole', ['as' => 'role-permission.storeRole', 'uses' => 'ManageRolePermissionController@storeRole']);
                Route::post('role-permission/deleteRole', ['as' => 'role-permission.deleteRole', 'uses' => 'ManageRolePermissionController@deleteRole']);
                Route::get('role-permission/showMembers/{id}', ['as' => 'role-permission.showMembers', 'uses' => 'ManageRolePermissionController@showMembers']);
                Route::resource('role-permission', 'ManageRolePermissionController');

                //Leaves
                Route::post('leaves/leaveAction', ['as' => 'leaves.leaveAction', 'uses' => 'ManageLeavesController@leaveAction']);
                Route::get('leaves/show-reject-modal', ['as' => 'leaves.show-reject-modal', 'uses' => 'ManageLeavesController@rejectModal']);
                Route::post('leave/data/{employeeId?}', ['uses' => 'ManageLeavesController@data'])->name('leave.data');
                Route::get('leave/all-leaves', ['uses' => 'ManageLeavesController@allLeave'])->name('leave.all-leaves');
                Route::get('leaves/pending', ['as' => 'leaves.pending', 'uses' => 'ManageLeavesController@pendingLeaves']);

                Route::resource('leaves', 'ManageLeavesController');

                Route::resource('leaveType', 'ManageLeaveTypesController');

                //sub task routes
                Route::post('sub-task/changeStatus', ['as' => 'sub-task.changeStatus', 'uses' => 'ManageSubTaskController@changeStatus']);
                Route::resource('sub-task', 'ManageSubTaskController');

                //task comments
                Route::resource('task-comment', 'AdminTaskCommentController');
                
                //task Note
                Route::resource('task-note', 'AdminNoteCommentController');
                
                //client Note
                Route::resource('client-note', 'AdminNoteClientController');
                
                //project Note
                Route::resource('project-note', 'AdminNoteProjectController');

                //taxes
                Route::resource('taxes', 'TaxSettingsController');
                
                
                // Line Item Group
                Route::resource('line-tem-groups', 'LineItemGroupsController');
                
                

                //region Products Routes
                Route::get('products/download/{id}', ['as' => 'products.download', 'uses' => 'AdminProductController@download']);
                Route::get('products/export', ['uses' => 'AdminProductController@export'])->name('products.export');
                Route::post('products/uploadImage/{id}', ['uses' => 'AdminProductController@uploadImage'])->name('products.uploadImage');
                Route::post('products/removeImage/{id}', ['uses' => 'AdminProductController@removeImage'])->name('products.removeImage');
                Route::get('products/get-sales-category-detail/{code}', ['uses' => 'AdminProductController@getSalesCategoryDetail'])->name('products.get-sales-category-detail');
                Route::get('products/get-vendor-detail/{vendorID}', ['uses' => 'AdminProductController@getVendorDetail'])->name('products.get-vendor-detail');
                
                Route::get('products/download-all', ['uses' => 'AdminProductController@downloadAll'])->name('products.download-all');
                Route::post('products/live-update', ['uses' => 'AdminProductController@liveUpdate'])->name('products.live-update');
                Route::post('products/upadte-order', ['uses' => 'AdminProductController@updateOrder'])->name('products.upadte-order');
                Route::get('products/filter-products', ['uses' => 'AdminProductController@filterProducts'])->name('products.filter-products');
                Route::get('products/filter-products-v3', ['uses' => 'AdminProductController@filterProductsV3'])->name('products.filter-products-v3');
                
                 // Added by Sb
                Route::get('products/downloadTemplate', ['uses' => 'AdminProductController@downloadTemplate'])->name('products.downloadTemplate');
                Route::post('products/import', ['uses' => 'AdminProductController@import'])->name('products.import');
                
                Route::post('products/send-rfq', ['uses' => 'AdminProductController@sendRFQ'])->name('products.send-rfq');
                // End
                
                Route::post('products/product-update/{id}', ['uses' => 'AdminProductController@productUpdate'])->name('products.product-update');
                
                Route::resource('products', 'AdminProductController');
                //endregion
                
                // SB added 
                
                 Route::resource('visionboard', 'AdminVisionboardController');
                 Route::resource('bookings', 'AdminBookingController');
                 Route::resource('community', 'AdminCommunityController');
                // SB added end

                //region contracts routes
                Route::get('contracts/download/{id}', ['as' => 'contracts.download', 'uses' => 'AdminContractController@download']);
                Route::get('contracts/sign/{id}', ['as' => 'contracts.sign-modal', 'uses' => 'AdminContractController@contractSignModal']);
                Route::post('contracts/sign/{id}', ['as' => 'contracts.sign', 'uses' => 'AdminContractController@contractSign']);
                Route::get('contracts/copy/{id}', ['as' => 'contracts.copy', 'uses' => 'AdminContractController@copy']);
                Route::post('contracts/copy-submit', ['as' => 'contracts.copy-submit', 'uses' => 'AdminContractController@copySubmit']);
                Route::post('contracts/add-discussion/{id}', ['as' => 'contracts.add-discussion', 'uses' => 'AdminContractController@addDiscussion']);
                Route::get('contracts/edit-discussion/{id}', ['as' => 'contracts.edit-discussion', 'uses' => 'AdminContractController@editDiscussion']);
                Route::post('contracts/update-discussion/{id}', ['as' => 'contracts.update-discussion', 'uses' => 'AdminContractController@updateDiscussion']);
                Route::post('contracts/remove-discussion/{id}', ['as' => 'contracts.remove-discussion', 'uses' => 'AdminContractController@removeDiscussion']);
                Route::resource('contracts', 'AdminContractController');
                //endregion

                //region contracts type routes
                Route::get('contract-type/data', ['as' => 'contract-type.data', 'uses' => 'AdminContractTypeController@data']);
                Route::post('contract-type/type-store', ['as' => 'contract-type.store-contract-type', 'uses' => 'AdminContractTypeController@storeContractType']);
                Route::get('contract-type/type-create', ['as' => 'contract-type.create-contract-type', 'uses' => 'AdminContractTypeController@createContractType']);

                Route::resource('contract-type', 'AdminContractTypeController')->parameters([
                    'contract-type' => 'type'
                ]);
                //endregion

                //region contract renew routes
                Route::get('contract-renew/{id}', ['as' => 'contracts.renew', 'uses' => 'AdminContractRenewController@index']);
                Route::post('contract-renew-submit/{id}', ['as' => 'contracts.renew-submit', 'uses' => 'AdminContractRenewController@renew']);
                Route::post('contract-renew-remove/{id}', ['as' => 'contracts.renew-remove', 'uses' => 'AdminContractRenewController@destroy']);
                //endregion

                //region discussion category routes
                Route::resource('discussion-category', 'DiscussionCategoryController');
                //endregion

                //region discussion routes
                Route::post('discussion/setBestAnswer', ['as' => 'discussion.setBestAnswer', 'uses' => 'DiscussionController@setBestAnswer']);
                Route::resource('discussion', 'DiscussionController');
                //endregion

                //region discussion routes
                Route::resource('discussion-reply', 'DiscussionReplyController');
                //endregion
                
                // added by SB
                
                Route::get('show-file-manager',['uses' => 'FileManagerController@index'])->name('view-file-manager');
                Route::any('/create_folder', ['uses' => 'FileManagerController@create_folder'])->name('create_folder');
                Route::any('/preview_folder/{folder_id}', ['uses' => 'FileManagerController@preview_folder'])->name('preview_folder');
                Route::any('/add_file/{folder_id}', ['uses' => 'FileManagerController@add_file'])->name('add_file');
                Route::any('/delete_folder/{folder_id}', ['uses' => 'FileManagerController@delete_folder'])->name('delete_folder');
                Route::any('/delete_file/{folder_id}/{file_id}', ['uses' => 'FileManagerController@delete_file'])->name('delete_file');
                Route::any('/filestoreMultiple', ['uses' => 'FileManagerController@storeMultiple'])->name('filestoreMultiple');
                Route::any('/storeFile', ['uses' => 'FileManagerController@storeFile'])->name('storeFile');
                Route::post('/check_password', ['uses' => 'FileManagerController@checkPassword'])->name('check_password');
                Route::post('/change_password', ['uses' => 'FileManagerController@changePassword'])->name('change_password');
                
                // end

            });
            Route::group(['middleware' => ['account-setup']], function () {
                Route::post('billing/unsubscribe',  'AdminBillingController@cancelSubscription')->name('billing.unsubscribe');
                Route::post('billing/razorpay-payment',  'AdminBillingController@razorpayPayment')->name('billing.razorpay-payment');
                Route::post('billing/razorpay-subscription',  'AdminBillingController@razorpaySubscription')->name('billing.razorpay-subscription');
                Route::get('billing/data',  'AdminBillingController@data')->name('billing.data');
                Route::get('billing/select-package/{packageID}',  'AdminBillingController@selectPackage')->name('billing.select-package');
                Route::get('billing', 'AdminBillingController@index')->name('billing');
                Route::get('billing/packages', 'AdminBillingController@packages')->name('billing.packages');
                Route::post('billing/payment-stripe', 'AdminBillingController@payment')->name('payments.stripe');
                Route::get('billing/invoice-download/{invoice}', 'AdminBillingController@download')->name('stripe.invoice-download');
                Route::get('billing/razorpay-invoice-download/{id}', 'AdminBillingController@razorpayInvoiceDownload')->name('billing.razorpay-invoice-download');
                Route::get('billing/offline-invoice-download/{id}', 'AdminBillingController@offlineInvoiceDownload')->name('billing.offline-invoice-download');
                Route::get('billing/paystack-invoice-download/{id}', 'AdminBillingController@paystackInvoiceDownload')->name('billing.paystack-invoice-download');
                Route::get('billing/chargebee-invoice-download/{id}', 'AdminBillingController@chargebeeInvoiceDownload')->name('billing.chargebee-invoice-download');

                
                Route::get('billing/change-strip-card',  'AdminBillingController@changeStripCard')->name('billing.change-strip-card');
                Route::post('billing/save-strip-card',  'AdminBillingController@saveStripCard')->name('billing.save-strip-card');
                Route::get('billing/change-plan/{id}', 'AdminBillingController@changePlan')->name('billing.change-plan');
                Route::get('billing/add-on-bookings/{id}', 'AdminBillingController@addOnBookings')->name('billing.add-on-bookings');
                Route::post('billing/add-additional-users/{id}', 'AdminBillingController@addAdditionalUsers')->name('billing.add-additional-users');
                
                
                
                
                
                //Pay stack payment
                Route::post('/pay', 'PaystackController@redirectToGateway')->name('payments.paystack');
                Route::get('/payment/callback', 'PaystackController@handleGatewayCallback')->name('payments.paystack.callback');

                Route::get('billing/offline-payment', 'AdminBillingController@offlinePayment')->name('billing.offline-payment');
                Route::post('billing/offline-payment-submit', 'AdminBillingController@offlinePaymentSubmit')->name('billing.offline-payment-submit');

                Route::get('paypal-recurring', array('as' => 'paypal-recurring', 'uses' => 'AdminPaypalController@payWithPaypalRecurrring',));
                Route::get('paypal-invoice-download/{id}', array('as' => 'paypal.invoice-download', 'uses' => 'AdminPaypalController@paypalInvoiceDownload',));
                Route::get('paypal-invoice', array('as' => 'paypal-invoice', 'uses' => 'AdminPaypalController@createInvoice'));

                // route for view/blade file
                Route::get('paywithpaypal', array('as' => 'paywithpaypal', 'uses' => 'AdminPaypalController@payWithPaypal'));
                // route for post request
                Route::get('paypal/{packageId}/{type}', array('as' => 'paypal', 'uses' => 'AdminPaypalController@paymentWithpaypal'));
                Route::get('paypal/cancel-agreement', array('as' => 'paypal.cancel-agreement', 'uses' => 'AdminPaypalController@cancelAgreement'));
                // route for check status responce
                Route::get('paypal', array('as' => 'status', 'uses' => 'AdminPaypalController@getPaymentStatus'));
                
                
//                Route::post('billing/chargebee-payment-submit', 'AdminBillingController@chargebeePaymentSubmit')->name('billing.chargebee-payment-submit');
//                Route::get('billing/chargebee-payment-submit', 'AdminBillingController@chargebeePaymentSubmit')->name('billing.chargebee-payment-submit');
            });
            
            
            Route::post('billing/chargebee-payment-submit', 'ManageAccountSetupController@chargebeePaymentSubmit')->name('billing.chargebee-payment-submit');
            Route::get('billing/chargebee-payment-submit', 'ManageAccountSetupController@chargebeePaymentSubmit')->name('billing.chargebee-payment-submit');
            Route::put('account-setup/module-setting-update', 'ManageAccountSetupController@moduleSettingUpdate')->name('account-setup.module-setting-update');
            
            Route::post('account-setup/completed/{id}', 'ManageAccountSetupController@completed')->name('account-setup.completed');
            
            Route::resource('account-setup', 'ManageAccountSetupController');
            Route::put('account-setup/update-invoice/{id}', ['uses' => 'ManageAccountSetupController@updateInvoice'])->name('account-setup.update-invoice');
        }
    );

    // Employee routes
    Route::group(
        ['namespace' => 'Member', 'prefix' => 'member', 'as' => 'member.', 'middleware' => ['role:employee']],
        function () {

            Route::get('dashboard', ['uses' => 'MemberDashboardController@index'])->name('dashboard');

            Route::post('profile/updateOneSignalId', ['uses' => 'MemberProfileController@updateOneSignalId'])->name('profile.updateOneSignalId');
            Route::get('language/change-language', ['uses' => 'MemberProfileController@changeLanguage'])->name('language.change-language');
            Route::resource('profile', 'MemberProfileController');
              //bitsclan code here
            Route::resource('quickbooks', 'MemberQuickbooksController');
            //Route::post('quickbooks/{id}', 'MemberQuickbooksController@update');


            //code ends here




            Route::post('projects/gantt-task-update/{id}', ['uses' => 'MemberProjectsController@updateTaskDuration'])->name('projects.gantt-task-update');


            // Added by Rehan
            Route::post('projects/project-duration-update/{id}', ['uses' => 'ManageAllTasksController@updateProjectDuration'])->name('projects.project-duration-update');

            Route::get('projects/ajaxCreate/{columnId}', ['uses' => 'MemberProjectsController@ajaxCreate'])->name('projects.ajaxCreate');
            Route::get('projects/ganttData/{projectId?}', ['uses' => 'MemberProjectsController@ganttData'])->name('projects.ganttData');
            Route::get('projects/gantt/{projectId?}', ['uses' => 'MemberProjectsController@gantt'])->name('projects.gantt');
            Route::get('projects/data', ['uses' => 'MemberProjectsController@data'])->name('projects.data');
            Route::get('projects/discussion-replies/{projectId}/{discussionId}', ['uses' => 'MemberProjectsController@discussionReplies'])->name('projects.discussionReplies');
            Route::get('projects/discussion/{projectId}', ['uses' => 'MemberProjectsController@discussion'])->name('projects.discussion');
            
            Route::get('projects/burndown/{projectId?}', ['uses' => 'MemberProjectsController@burndownChart'])->name('projects.burndown-chart');
            Route::get('projects/free-flow-gantt', ['uses' => 'MemberProjectsController@freeFlowGantt'])->name('projects.free-flow-gantt');
            Route::resource('projects', 'MemberProjectsController');

            Route::get('project-template/data', ['uses' => 'ProjectTemplateController@data'])->name('project-template.data');
            Route::resource('project-template', 'ProjectTemplateController');

            Route::post('project-template-members/save-group', ['uses' => 'ProjectMemberTemplateController@storeGroup'])->name('project-template-members.storeGroup');
            Route::resource('project-template-member', 'ProjectMemberTemplateController');
            
            
            Route::get('project-template-task/data/{templateId?}', ['uses' => 'ProjectTemplateTaskController@data'])->name('project-template-task.data');
            Route::get('project-template-task/detail/{id?}', ['uses' => 'ProjectTemplateTaskController@taskDetail'])->name('project-template-task.detail');
            Route::resource('project-template-task', 'ProjectTemplateTaskController');
            
            Route::get('project-template-milestone/data/{templateId?}', ['uses' => 'ProjectTemplateMilestoneController@data'])->name('project-template-milestone.data');
            Route::get('project-template-milestone/detail/{id?}', ['uses' => 'ProjectTemplateMilestoneController@milestoneDetail'])->name('project-template-milestone.detail');
            Route::resource('project-template-milestone', 'ProjectTemplateMilestoneController');


            Route::get('leads/data', ['uses' => 'MemberLeadController@data'])->name('leads.data');
            Route::post('leads/change-status', ['uses' => 'MemberLeadController@changeStatus'])->name('leads.change-status');
            Route::get('leads/follow-up/{leadID}', ['uses' => 'MemberLeadController@followUpCreate'])->name('leads.follow-up');
            Route::get('leads/followup/{leadID}', ['uses' => 'MemberLeadController@followUpShow'])->name('leads.followup');
            Route::post('leads/follow-up-store', ['uses' => 'MemberLeadController@followUpStore'])->name('leads.follow-up-store');
            Route::get('leads/follow-up-edit/{id?}', ['uses' => 'MemberLeadController@editFollow'])->name('leads.follow-up-edit');
            Route::post('leads/follow-up-update', ['uses' => 'MemberLeadController@UpdateFollow'])->name('leads.follow-up-update');
            Route::post('leads/follow-up-delete/{id}', ['uses' => 'MemberLeadController@deleteFollow'])->name('leads.follow-up-delete');
            Route::get('leads/follow-up-sort', ['uses' => 'MemberLeadController@followUpSort'])->name('leads.follow-up-sort');
            
            Route::get('leads/archive', ['uses' => 'MemberLeadController@archive'])->name('leads.archive');
            Route::get('leads/archive-data', ['uses' => 'MemberLeadController@archiveData'])->name('leads.archive-data');
            
            Route::resource('leads', 'MemberLeadController');

            // Lead Files
            Route::get('lead-files/download/{id}', ['uses' => 'LeadFilesController@download'])->name('lead-files.download');
            Route::get('lead-files/thumbnail', ['uses' => 'LeadFilesController@thumbnailShow'])->name('lead-files.thumbnail');
            Route::resource('lead-files', 'LeadFilesController');
            
            //Pinned route
            Route::resource('pinned', 'MemberPinnedController', ['only' => ['store', 'destroy']]);

            // Proposal routes
            Route::get('proposals/data/{id?}', ['uses' => 'MemberProposalController@data'])->name('proposals.data');
            Route::get('proposals/download/{id}', ['uses' => 'MemberProposalController@download'])->name('proposals.download');
            Route::get('proposals/create/{leadID?}', ['uses' => 'MemberProposalController@create'])->name('proposals.create');
            Route::get('proposals/convert-proposal/{id?}', ['uses' => 'MemberProposalController@convertProposal'])->name('proposals.convert-proposal');
            Route::resource('proposals', 'MemberProposalController', ['expect' => ['create']]);

            Route::group(
                ['prefix' => 'projects'],
                function () {
                    Route::resource('project-members', 'MemberProjectsMemberController');

                    Route::post('tasks/sort', ['uses' => 'MemberTasksController@sort'])->name('tasks.sort');
                    Route::post('tasks/change-status', ['uses' => 'MemberTasksController@changeStatus'])->name('tasks.changeStatus');
                    Route::get('tasks/check-task/{taskID}', ['uses' => 'MemberTasksController@checkTask'])->name('tasks.checkTask');
                    Route::post('tasks/data/{startDate?}/{endDate?}/{hideCompleted?}/{projectId?}', ['uses' => 'MemberTasksController@data'])->name('tasks.data');
                    Route::resource('tasks', 'MemberTasksController');

                    Route::get('files/download/{id}', ['uses' => 'MemberProjectFilesController@download'])->name('files.download');
                    Route::get('files/thumbnail', ['uses' => 'MemberProjectFilesController@thumbnailShow'])->name('files.thumbnail');
                    Route::post('files/multiple-upload', ['uses' => 'MemberProjectFilesController@storeMultiple'])->name('files.multiple-upload');
                    
                    Route::post('files/create_folder', ['uses' => 'MemberProjectFilesController@create_folder'])->name('files.create_folder');
                    Route::any('files/delete_folder/{folder_id}', ['uses' => 'MemberProjectFilesController@delete_folder'])->name('files.delete_folder');

                    Route::resource('files', 'MemberProjectFilesController');

                    Route::get('time-log/show-log/{id}', ['uses' => 'MemberTimeLogController@showTomeLog'])->name('time-log.show-log');
                    Route::get('time-log/data/{id}', ['uses' => 'MemberTimeLogController@data'])->name('time-log.data');
                    Route::post('time-log/store-time-log', ['uses' => 'MemberTimeLogController@storeTimeLog'])->name('time-log.store-time-log');
                    Route::post('time-log/update-time-log/{id}', ['uses' => 'MemberTimeLogController@updateTimeLog'])->name('time-log.update-time-log');
                    Route::resource('time-log', 'MemberTimeLogController');
                    
                    
                    Route::get('products-project/data/{id}', ['uses' => 'MemberProjectProductsController@data'])->name('products-project.data');
                    Route::resource('products-project', 'MemberProjectProductsController');
                    
                    Route::get('estimates-project/data/{id}', ['uses' => 'MemberProjectEstimatesController@data'])->name('estimates-project.data');
                    Route::resource('estimates-project', 'MemberProjectEstimatesController');
                    
                    Route::get('purchase-orders-project/data/{id}', ['uses' => 'MemberProjectPurchaseOrdersController@data'])->name('purchase-orders-project.data');
                    Route::get('purchase-orders-project/archive-delete/{id?}', ['uses' => 'MemberProjectPurchaseOrdersController@archiveDestroy'])->name('purchase-orders-project.archive-delete');
                    Route::resource('purchase-orders-project', 'MemberProjectPurchaseOrdersController');
                    
                    Route::get('milestones/detail/{id}', ['uses' => 'MemberProjectMilestonesController@detail'])->name('milestones.detail');
                    Route::get('milestones/data/{id}', ['uses' => 'MemberProjectMilestonesController@data'])->name('milestones.data');
                    Route::resource('milestones', 'MemberProjectMilestonesController');
                    
                    Route::get('invoices-project/data/{id}', ['uses' => 'MemberProjectInvoicesController@data'])->name('invoices-project.data');
                    Route::resource('invoices-project', 'MemberProjectInvoicesController');
                    
                    Route::get('rooms/detail/{id}', ['uses' => 'MemberProjectRoomsController@detail'])->name('rooms.detail');
                    Route::get('rooms/data/{id}', ['uses' => 'MemberProjectRoomsController@data'])->name('rooms.data');
                    Route::resource('rooms', 'MemberProjectRoomsController');
                    
                    Route::resource('project-expenses', 'MemberProjectExpensesController');
                    Route::resource('project-payments', 'MemberProjectPaymentsController');
                    
                    
                    
                    Route::get('product-review-project/data/{id}', ['uses' => 'MemberProjectProductReviewController@data'])->name('product-review-project.data');
                    Route::get('product-review-project/view-product-notes/{productId?}', ['uses' => 'MemberProjectProductReviewController@viewProductNotes'])->name('product-review-project.view-product-notes');
                    Route::get('product-review-project/view-location-notes/{code_type_id?}', ['uses' => 'MemberProjectProductReviewController@viewLocationNotes'])->name('product-review-project.view-location-notes');
                    Route::post('product-review-project/create-finances', 'MemberProjectProductReviewController@createFinance')->name('product-review-project.create-finances');
                    
                    Route::post('product-review-project/updateSetting/{id}', 'MemberProjectProductReviewController@updateSetting')->name('product-review-project.updateSetting');
                    Route::get('product-review-project/detail/{id}/{pid}', ['uses' => 'MemberProjectProductReviewController@detail'])->name('product-review-project.detail');
                    
                    Route::resource('product-review-project', 'MemberProjectProductReviewController');
                    
                    
                    
                }
            );

            //sticky note
            Route::resource('sticky-note', 'MemberStickyNoteController');

            // User message
            Route::post('message-submit', ['as' => 'user-chat.message-submit', 'uses' => 'MemberChatController@postChatMessage']);
            Route::get('user-search', ['as' => 'user-chat.user-search', 'uses' => 'MemberChatController@getUserSearch']);
            Route::resource('user-chat', 'MemberChatController');

            //Notice
            Route::get('notices/data', ['uses' => 'MemberNoticesController@data'])->name('notices.data');
            Route::resource('notices', 'MemberNoticesController');

            // task routes
            Route::resource('task', 'MemberAllTasksController', ['only' => ['edit', 'update', 'index']]); // hack to make left admin menu item active
            Route::group(
                ['prefix' => 'task'],
                function () {

                    Route::get('all-tasks/dependent-tasks/{projectId}/{taskId?}', ['uses' => 'MemberAllTasksController@dependentTaskLists'])->name('all-tasks.dependent-tasks');
                    Route::post('all-tasks/data/{hideCompleted?}/{projectId?}', ['uses' => 'MemberAllTasksController@data'])->name('all-tasks.data');
                    Route::get('all-tasks/members/{projectId}', ['uses' => 'MemberAllTasksController@membersList'])->name('all-tasks.members');
                    Route::get('all-tasks/ajaxCreate/{columnId}', ['uses' => 'MemberAllTasksController@ajaxCreate'])->name('all-tasks.ajaxCreate');
                    Route::get('all-tasks/reminder/{taskid}', ['uses' => 'MemberAllTasksController@remindForTask'])->name('all-tasks.reminder');
                    Route::get('all-tasks/history/{taskid}', ['uses' => 'MemberAllTasksController@history'])->name('all-tasks.history');
                    Route::get('all-tasks/files/{taskid}', ['uses' => 'MemberAllTasksController@showFiles'])->name('all-tasks.show-files');
                    Route::get('all-tasks/pinned-task', ['uses' => 'MemberAllTasksController@pinnedItem'])->name('all-tasks.pinned-task');
                    
                    
                    Route::post('all-tasks/live-update/{taskid}', ['uses' => 'MemberAllTasksController@liveUpdate'])->name('all-tasks.live-update');
                    Route::post('all-tasks/live-timeLog/{taskid}', ['uses' => 'MemberAllTasksController@liveTimeLog'])->name('all-tasks.live-timeLog');
                    Route::post('all-tasks/live-timeLog-stop/{taskid}', ['uses' => 'MemberAllTasksController@liveTimeLogStop'])->name('all-tasks.live-timeLog-stop');
                    
                    
                    
                    Route::resource('all-tasks', 'MemberAllTasksController');


                    // taskboard resource
                    Route::post('taskboard/updateIndex', ['as' => 'taskboard.updateIndex', 'uses' => 'MemberTaskboardController@updateIndex']);
                    Route::get('taskboard/show/{id}', ['as' => 'taskboard.show', 'uses' => 'MemberTaskboardController@show']); // Added by SB
                    Route::resource('taskboard', 'MemberTaskboardController');

                    // task calendar routes
                    Route::resource('task-calendar', 'MemberCalendarController');

                    Route::get('task-files/download/{id}', ['uses' => 'TaskFilesController@download'])->name('task-files.download');
                    Route::resource('task-files', 'TaskFilesController');
                }
            );


            Route::resource('finance', 'MemberExpensesController', ['only' => ['edit', 'update', 'index']]); // hack to make left admin menu item active
            Route::group(
                ['prefix' => 'finance'],
                function () {



                    // Estimate routes
                    Route::get('estimates/data', ['uses' => 'MemberEstimatesController@data'])->name('estimates.data');
                    Route::get('estimates/download/{id}', ['uses' => 'MemberEstimatesController@download'])->name('estimates.download');
                    Route::post('estimates/send-estimate/{id}', ['uses' => 'MemberEstimatesController@sendEstimate'])->name('estimates.send-estimate');
                    Route::resource('estimates', 'MemberEstimatesController');

                    //Expenses routes
                    Route::get('expenses/data', ['uses' => 'MemberExpensesController@data'])->name('expenses.data');
                    Route::resource('expenses', 'MemberExpensesController');

                    Route::get('expenses/paidvendor','MemberExpensesController@paidVendorInvoice');


                    // All invoices list routes
                    Route::post('file/store', ['uses' => 'MemberAllInvoicesController@storeFile'])->name('invoiceFile.store');
                    Route::delete('file/destroy', ['uses' => 'MemberAllInvoicesController@destroyFile'])->name('invoiceFile.destroy');
                    Route::get('all-invoices/data', ['uses' => 'MemberAllInvoicesController@data'])->name('all-invoices.data');
                    Route::get('all-invoices/download/{id}', ['uses' => 'MemberAllInvoicesController@download'])->name('all-invoices.download');
                    Route::get('all-invoices/convert-estimate/{id}', ['uses' => 'MemberAllInvoicesController@convertEstimate'])->name('all-invoices.convert-estimate');
                    Route::get('all-invoices/update-item', ['uses' => 'MemberAllInvoicesController@addItems'])->name('all-invoices.update-item');
                    Route::get('all-invoices/payment-detail/{invoiceID}', ['uses' => 'MemberAllInvoicesController@paymentDetail'])->name('all-invoices.payment-detail');
                    Route::get('all-invoices/get-client-company/{projectID?}', ['uses' => 'MemberAllInvoicesController@getClientOrCompanyName'])->name('all-invoices.get-client-company');
                    Route::get('all-invoices/check-shipping-address', ['uses' => 'MemberAllInvoicesController@checkShippingAddress'])->name('all-invoices.checkShippingAddress');
                    Route::get('all-invoices/toggle-shipping-address/{invoice}', ['uses' => 'MemberAllInvoicesController@toggleShippingAddress'])->name('all-invoices.toggleShippingAddress');
                    Route::get('all-invoices/shipping-address-modal/{invoice}', ['uses' => 'MemberAllInvoicesController@shippingAddressModal'])->name('all-invoices.shippingAddressModal');
                    Route::post('all-invoices/add-shipping-address/{user}', ['uses' => 'MemberAllInvoicesController@addShippingAddress'])->name('all-invoices.addShippingAddress');
                    Route::get('all-invoices/update-status/{invoiceID}', ['uses' => 'MemberAllInvoicesController@cancelStatus'])->name('all-invoices.update-status');
                    Route::post('all-invoices/send-invoice/{invoiceID}', ['uses' => 'MemberAllInvoicesController@sendInvoice'])->name('all-invoices.send-invoice');
                    Route::get('all-invoices/convert-milestone/{id}', ['uses' => 'MemberAllInvoicesController@convertMilestone'])->name('all-invoices.convert-milestone');
                    
                    Route::get('all-invoices/refund/{invoiceID}', ['uses' => 'MemberAllInvoicesController@refund'])->name('all-invoices.refund');
                    Route::post('all-invoices/refund-update/{invoiceID}', ['uses' => 'MemberAllInvoicesController@refundUpdate'])->name('all-invoices.refund-update');

                    Route::resource('all-invoices', 'MemberAllInvoicesController');

                    //Aqeel code to vendor api



                    // All Credit Note routes
                    Route::post('credit-file/store', ['uses' => 'MemberAllCreditNotesController@storeFile'])->name('creditNoteFile.store');
                    Route::delete('credit-file/destroy', ['uses' => 'MemberAllCreditNotesController@destroyFile'])->name('creditNoteFile.destroy');
                    Route::get('all-credit-notes/data', ['uses' => 'MemberAllCreditNotesController@data'])->name('all-credit-notes.data');
                    Route::get('all-credit-notes/download/{id}', ['uses' => 'MemberAllCreditNotesController@download'])->name('all-credit-notes.download');
                    Route::get('all-credit-notes/convert-invoice/{id}', ['uses' => 'MemberAllCreditNotesController@convertInvoice'])->name('all-credit-notes.convert-invoice');
                    Route::get('all-credit-notes/update-item', ['uses' => 'MemberAllCreditNotesController@addItems'])->name('all-credit-notes.update-item');
                    Route::get('all-credit-notes/payment-detail/{creditNoteID}', ['uses' => 'MemberAllCreditNotesController@paymentDetail'])->name('all-credit-notes.payment-detail');
                    Route::resource('all-credit-notes', 'MemberAllCreditNotesController');

                    //Payments routes
                    Route::get('payments/data', ['uses' => 'MemberPaymentsController@data'])->name('payments.data');
                    Route::get('payments/pay-invoice/{invoiceId}', ['uses' => 'MemberPaymentsController@payInvoice'])->name('payments.payInvoice');
                    Route::resource('payments', 'MemberPaymentsController');
                }
            );

            // Ticket reply template routes
            Route::post('replyTemplates/fetch-template', ['uses' => 'MemberTicketReplyTemplatesController@fetchTemplate'])->name('replyTemplates.fetchTemplate');

            //Tickets routes
            Route::get('tickets/data', ['uses' => 'MemberTicketsController@data'])->name('tickets.data');
            Route::post('tickets/storeAdmin', ['uses' => 'MemberTicketsController@storeAdmin'])->name('tickets.storeAdmin');
            Route::post('tickets/updateAdmin/{id}', ['uses' => 'MemberTicketsController@updateAdmin'])->name('tickets.updateAdmin');
            Route::post('tickets/close-ticket/{id}', ['uses' => 'MemberTicketsController@closeTicket'])->name('tickets.closeTicket');
            Route::post('tickets/open-ticket/{id}', ['uses' => 'MemberTicketsController@reopenTicket'])->name('tickets.reopenTicket');
            Route::get('tickets/admin-data/{startDate?}/{endDate?}/{agentId?}/{status?}/{priority?}/{channelId?}/{typeId?}', ['uses' => 'MemberTicketsController@adminData'])->name('tickets.adminData');
            Route::get('tickets/refresh-count/{startDate?}/{endDate?}/{agentId?}/{status?}/{priority?}/{channelId?}/{typeId?}', ['uses' => 'MemberTicketsController@refreshCount'])->name('tickets.refreshCount');
            Route::get('tickets/reply-delete/{id?}', ['uses' => 'MemberTicketsController@destroyReply'])->name('tickets.reply-delete');
            Route::get('roadmap', ['uses' => 'MemberTicketsController@roadMap'])->name('roadmap');
            
            Route::resource('tickets', 'MemberTicketsController');

            //Ticket agent routes
            Route::get('ticket-agent/data/{startDate?}/{endDate?}/{status?}/{priority?}/{channelId?}/{typeId?}', ['uses' => 'MemberTicketsAgentController@data'])->name('ticket-agent.data');
            Route::get('ticket-agent/refresh-count/{startDate?}/{endDate?}/{status?}/{priority?}/{channelId?}/{typeId?}', ['uses' => 'MemberTicketsAgentController@refreshCount'])->name('ticket-agent.refreshCount');
            Route::post('ticket-agent/fetch-template', ['uses' => 'MemberTicketsAgentController@fetchTemplate'])->name('ticket-agent.fetchTemplate');
            Route::resource('ticket-agent', 'MemberTicketsAgentController');

            Route::get('ticket-files/download/{id}', ['uses' => 'TicketFilesController@download'])->name('ticket-files.download');
            Route::resource('ticket-files', 'TicketFilesController');

            // attendance
            Route::get('attendances/detail', ['uses' => 'MemberAttendanceController@attendanceDetail'])->name('attendances.detail');
            Route::get('attendances/data', ['uses' => 'MemberAttendanceController@data'])->name('attendances.data');
            Route::get('attendances/check-holiday', ['uses' => 'MemberAttendanceController@checkHoliday'])->name('attendances.check-holiday');
            Route::post('attendances/storeAttendance', ['uses' => 'MemberAttendanceController@storeAttendance'])->name('attendances.storeAttendance');
            Route::get('attendances/employeeData/{startDate?}/{endDate?}/{userId?}', ['uses' => 'MemberAttendanceController@employeeData'])->name('attendances.employeeData');
            Route::get('attendances/refresh-count/{startDate?}/{endDate?}/{userId?}', ['uses' => 'MemberAttendanceController@refreshCount'])->name('attendances.refreshCount');
            Route::post('attendances/storeMark', ['uses' => 'MemberAttendanceController@storeMark'])->name('attendances.storeMark');
            Route::get('attendances/mark/{id}/{day}/{month}/{year}', ['uses' => 'MemberAttendanceController@mark'])->name('attendances.mark');
            Route::get('attendances/summary', ['uses' => 'MemberAttendanceController@summary'])->name('attendances.summary');
            Route::post('attendances/summaryData', ['uses' => 'MemberAttendanceController@summaryData'])->name('attendances.summaryData');
            Route::get('attendances/info/{id}', ['uses' => 'MemberAttendanceController@detail'])->name('attendances.info');
            Route::post('attendances/updateDetails/{id}', ['uses' => 'MemberAttendanceController@updateDetails'])->name('attendances.updateDetails');
            Route::resource('attendances', 'MemberAttendanceController');

            // Holidays
            Route::get('holidays/view-holiday/{year?}', 'MemberHolidaysController@viewHoliday')->name('holidays.view-holiday');
            Route::get('holidays/calendar-month', 'MemberHolidaysController@getCalendarMonth')->name('holidays.calendar-month');
            Route::get('holidays/mark_sunday', 'MemberHolidaysController@Sunday')->name('holidays.mark-sunday');
            Route::get('holidays/calendar/{year?}', 'MemberHolidaysController@holidayCalendar')->name('holidays.calendar');
            Route::get('holidays/mark-holiday', 'MemberHolidaysController@markHoliday')->name('holidays.mark-holiday');
            Route::post('holidays/mark-holiday-store', 'MemberHolidaysController@markDayHoliday')->name('holidays.mark-holiday-store');
            Route::resource('holidays', 'MemberHolidaysController');

            // events
            Route::post('events/removeAttendee', ['as' => 'events.removeAttendee', 'uses' => 'MemberEventController@removeAttendee']);
            Route::resource('events', 'MemberEventController');

            // clients
            Route::group(
                ['prefix' => 'clients'],
                function () {
                    Route::get('projects/{id}', ['uses' => 'MemberClientsController@showProjects'])->name('clients.projects');
                    Route::get('invoices/{id}', ['uses' => 'MemberClientsController@showInvoices'])->name('clients.invoices');

                    Route::get('contacts/data/{id}', ['uses' => 'MemberClientContactController@data'])->name('contacts.data');
                    Route::resource('contacts', 'MemberClientContactController');
                }
            );

            Route::get('clients/data', ['uses' => 'MemberClientsController@data'])->name('clients.data');
            Route::get('clients/create/{clientID?}', ['uses' => 'MemberClientsController@create'])->name('clients.create');
            Route::resource('clients', 'MemberClientsController');

            Route::get('employees/docs-create/{id}', ['uses' => 'MemberEmployeesController@docsCreate'])->name('employees.docs-create');
            Route::get('employees/tasks/{userId}/{hideCompleted}', ['uses' => 'MemberEmployeesController@tasks'])->name('employees.tasks');
            Route::get('employees/time-logs/{userId}', ['uses' => 'MemberEmployeesController@timeLogs'])->name('employees.time-logs');
            Route::get('employees/data', ['uses' => 'MemberEmployeesController@data'])->name('employees.data');
            Route::get('employees/export', ['uses' => 'MemberEmployeesController@export'])->name('employees.export');
            Route::post('employees/assignRole', ['uses' => 'MemberEmployeesController@assignRole'])->name('employees.assignRole');
            Route::post('employees/assignProjectAdmin', ['uses' => 'MemberEmployeesController@assignProjectAdmin'])->name('employees.assignProjectAdmin');
            Route::resource('employees', 'MemberEmployeesController');

            Route::get('employee-docs/download/{id}', ['uses' => 'MemberEmployeeDocsController@download'])->name('employee-docs.download');
            Route::resource('employee-docs', 'MemberEmployeeDocsController');

            Route::get('all-time-logs/show-active-timer', ['uses' => 'MemberAllTimeLogController@showActiveTimer'])->name('all-time-logs.show-active-timer');
            Route::post('all-time-logs/stop-timer/{id}', ['uses' => 'MemberAllTimeLogController@stopTimer'])->name('all-time-logs.stopTimer');
            Route::post('all-time-logs/data/{projectId?}/{employee?}', ['uses' => 'MemberAllTimeLogController@data'])->name('all-time-logs.data');
            Route::get('all-time-logs/members/{projectId}', ['uses' => 'MemberAllTimeLogController@membersList'])->name('all-time-logs.members');
            Route::get('all-time-logs/task-members/{taskId}', ['uses' => 'MemberAllTimeLogController@taskMembersList'])->name('all-time-logs.task-members');
            Route::post('all-time-logs/approve-timelog', ['uses' => 'MemberAllTimeLogController@approveTimelog'])->name('all-time-logs.approve-timelog');
            
            Route::post('all-time-logs/calculate-time', ['uses' => 'MemberAllTimeLogController@calculateTime'])->name('all-time-logs.calculate-time');
            Route::resource('all-time-logs', 'MemberAllTimeLogController');

            Route::post('leaves/leaveAction', ['as' => 'leaves.leaveAction', 'uses' => 'MemberLeavesController@leaveAction']);
            Route::get('leaves/data', ['as' => 'leaves.data', 'uses' => 'MemberLeavesController@data']);
            Route::resource('leaves', 'MemberLeavesController');

            Route::post('leaves-dashboard/leaveAction', ['as' => 'leaves-dashboard.leaveAction', 'uses' => 'MemberLeaveDashboardController@leaveAction']);
            Route::resource('leaves-dashboard', 'MemberLeaveDashboardController');

            //sub task routes
            Route::post('sub-task/changeStatus', ['as' => 'sub-task.changeStatus', 'uses' => 'MemberSubTaskController@changeStatus']);
            Route::resource('sub-task', 'MemberSubTaskController');

            //task comments
            Route::resource('task-comment', 'MemberTaskCommentController');
            
              //task notes
              Route::resource('task-note', 'MemberTaskNoteController');

            // hide by SB
            //region Products Routes
//            Route::get('products/data', ['uses' => 'MemberProductController@data'])->name('products.data');
//            Route::resource('products', 'MemberProductController');
            //endregion


            //region discussion routes
            Route::post('discussion/setBestAnswer', ['as' => 'discussion.setBestAnswer', 'uses' => 'MemberDiscussionController@setBestAnswer']);
            Route::resource('discussion', 'MemberDiscussionController');
            //endregion

            //region discussion routes
            Route::resource('discussion-reply', 'MemberDiscussionReplyController');
            //endregion
            
            
            // Added by SB
            Route::get('show-file-manager',['uses' => 'FileManagerController@index'])->name('view-file-manager');

            Route::any('/create_folder', ['uses' => 'FileManagerController@create_folder'])->name('create_folder');
            Route::any('/preview_folder/{folder_id}', ['uses' => 'FileManagerController@preview_folder'])->name('preview_folder');
            Route::any('/add_file/{folder_id}', ['uses' => 'FileManagerController@add_file'])->name('add_file');
            Route::any('/delete_folder/{folder_id}', ['uses' => 'FileManagerController@delete_folder'])->name('delete_folder');
            Route::any('/delete_file/{folder_id}/{file_id}', ['uses' => 'FileManagerController@delete_file'])->name('delete_file');
            Route::any('/filestoreMultiple', ['uses' => 'FileManagerController@storeMultiple'])->name('filestoreMultiple');
            Route::any('/storeFile', ['uses' => 'FileManagerController@storeFile'])->name('storeFile');
            Route::post('/check_password', ['uses' => 'FileManagerController@checkPassword'])->name('check_password');
            Route::post('/change_password', ['uses' => 'FileManagerController@changePassword'])->name('change_password');
            
             //region Products Routes
                Route::get('products/download/{id}', ['as' => 'products.download', 'uses' => 'MemberProductController@download']);
                Route::get('products/export', ['uses' => 'MemberProductController@export'])->name('products.export');
                Route::post('products/uploadImage/{id}', ['uses' => 'MemberProductController@uploadImage'])->name('products.uploadImage');
                Route::post('products/removeImage/{id}', ['uses' => 'MemberProductController@removeImage'])->name('products.removeImage');
                Route::get('products/get-sales-category-detail/{code}', ['uses' => 'MemberProductController@getSalesCategoryDetail'])->name('products.get-sales-category-detail');
                
                Route::get('products/download-all', ['uses' => 'MemberProductController@downloadAll'])->name('products.download-all');
                Route::post('products/live-update', ['uses' => 'MemberProductController@liveUpdate'])->name('products.live-update');
                Route::get('products/filter-products', ['uses' => 'MemberProductController@filterProducts'])->name('products.filter-products');
                Route::post('products/send-rfq', ['uses' => 'MemberProductController@sendRFQ'])->name('products.send-rfq');
                
                
                Route::resource('products', 'MemberProductController');
            //endregion
                
                //Added by Sb
                Route::get('vendor/create', ['uses' => 'MemberVendorController@create'])->name('vendor.create');
                Route::get('vendor/store', ['uses' => 'MemberVendorController@store'])->name('vendor.store');
                Route::post('vendor/update', ['uses' => 'MemberVendorController@update'])->name('vendor.update');
                Route::delete('vendor/destroy', ['uses' => 'MemberVendorController@destroy'])->name('vendor.destroy');
                Route::get('vendor/edit/{id}', ['uses' => 'MemberVendorController@edit'])->name('vendor.edit');
                Route::get('vendor/export/{status?}/{vendor?}', ['uses' => 'MemberVendorController@export'])->name('vendor.export');
                Route::get('vendor/showVendor/{id}', ['uses' => 'MemberVendorController@showVendor'])->name('vendor.showVendor');
                Route::post('vendor/sendEmail', ['uses' => 'MemberVendorController@sendEmail'])->name('vendor.sendEmail');
                Route::get('vendor/download/template', ['uses' => 'MemberVendorController@downloadTemplate'])->name('vendor.download-template');
                Route::post('vendor/import', ['uses' => 'MemberVendorController@import'])->name('vendor.import');
                
                
                Route::get('vendor/create-vendor', ['uses' => 'MemberVendorController@createVendor'])->name('vendor.create-vendor');
                Route::post('vendor/store-vendor', ['uses' => 'MemberVendorController@storeVendor'])->name('vendor.store-vendor');
                
                Route::resource('vendor', 'MemberVendorController');
                // End.
                
                
                
                
                Route::get('purchase-orders/update-item', ['uses' => 'MemberPurchaseOrdersController@addItems'])->name('purchase-orders.update-item');
                Route::get('purchase-orders/create', 'MemberPurchaseOrdersController@create')->name('purchase-orders.create');
                Route::get('purchase-orders/edit/{id}', ['uses' => 'MemberPurchaseOrdersController@edit'])->name('purchase-orders.edit');
                Route::get('purchase-orders/get-vendor-detail/{vendorid}', ['uses' => 'MemberPurchaseOrdersController@getVendorDetail'])->name('purchase-orders.get-vendor-detail');
                Route::get('purchase-orders/download/{id}', ['uses' => 'MemberPurchaseOrdersController@download'])->name('purchase-orders.download');
                Route::post('purchase-orders/change-status', ['uses' => 'MemberPurchaseOrdersController@changeStatus'])->name('purchase-orders.change-status');
                Route::post('purchase-orders/sendpdf/{id}', ['uses' => 'MemberPurchaseOrdersController@sendPdf'])->name('purchase-orders.sendpdf');
                Route::get('purchase-orders/archive-delete/{id?}', ['uses' => 'MemberProjectPurchaseOrdersController@archiveDestroy'])->name('purchase-orders.archive-delete');

                Route::resource('purchase-orders', 'MemberPurchaseOrdersController');
                
                Route::resource('visionboard', 'MemberVisionboardController');
            
            
            // SB

        }
    );

    // Client routes
    Route::group(
        ['namespace' => 'Client', 'prefix' => 'client', 'as' => 'client.', 'middleware' => []],
        function () {

            Route::resource('dashboard', 'ClientDashboardController');

            Route::resource('profile', 'ClientProfileController');

            // Project section
            Route::get('projects/data', ['uses' => 'ClientProjectsController@data'])->name('projects.data');
            
            Route::get('projects/discussion-replies/{projectId}/{discussionId}', ['uses' => 'ClientProjectsController@discussionReplies'])->name('projects.discussionReplies');
            Route::get('projects/discussion/{projectId}', ['uses' => 'ClientProjectsController@discussion'])->name('projects.discussion');
            
            Route::resource('projects', 'ClientProjectsController');

            Route::group(
                ['prefix' => 'projects'],
                function () {

                    Route::resource('project-members', 'ClientProjectMembersController');

                    Route::post('tasks/data/{projectId?}', ['uses' => 'ClientTasksController@data'])->name('tasks.data');
                    Route::get('tasks/ajax-edit/{taskId?}', ['uses' => 'ClientTasksController@ajaxEdit'])->name('tasks.ajax-edit');
                    Route::get('tasks/check-task/{taskID}', ['uses' => 'ClientTasksController@checkTask'])->name('tasks.checkTask');
                    Route::resource('tasks', 'ClientTasksController');
                    
                    Route::post('product-review-project/data/{projectId?}', ['uses' => 'ClientProductReviewController@data'])->name('product-review-project.data');
                    
                    Route::get('product-review-project/create-product-notes/{productId?}', ['uses' => 'ClientProductReviewController@createProductNotes'])->name('product-review-project.create-product-notes');
                    Route::post('product-review-project/store-product-notes', 'ClientProductReviewController@storeProductNotes')->name('product-review-project.store-product-notes');
                    
                    Route::get('product-review-project/create-location-notes/{codeType?}', ['uses' => 'ClientProductReviewController@createLocationNotes'])->name('product-review-project.create-location-notes');
                    Route::post('product-review-project/store-location-notes', 'ClientProductReviewController@storeLocationNotes')->name('product-review-project.store-location-notes');
                    
                    Route::get('product-review-project/detail/{id}/{pid}', ['uses' => 'ClientProductReviewController@detail'])->name('product-review-project.detail');
                    Route::resource('product-review-project', 'ClientProductReviewController');
                    
                    

                    Route::get('files/download/{id}', ['uses' => 'ClientFilesController@download'])->name('files.download');
                    Route::get('files/thumbnail', ['uses' => 'ClientFilesController@thumbnailShow'])->name('files.thumbnail');
                    Route::resource('files', 'ClientFilesController');

                    Route::get('time-log/data/{id}', ['uses' => 'ClientTimeLogController@data'])->name('time-log.data');
                    Route::resource('time-log', 'ClientTimeLogController');

                    Route::get('project-invoice/download/{id}', ['uses' => 'ClientProjectInvoicesController@download'])->name('project-invoice.download');
                    Route::resource('project-invoice', 'ClientProjectInvoicesController');
                }
            );

            //region Products Routes
            Route::get('products/data', ['uses' => 'ClientProductController@data'])->name('products.data');
            Route::get('products/update-item', ['uses' => 'ClientProductController@addItems'])->name('products.update-item');

            Route::resource('products', 'ClientProductController');

            //sticky note
            Route::resource('sticky-note', 'ClientStickyNoteController');

            // Invoice Section
            Route::get('invoices/download/{id}', ['uses' => 'ClientInvoicesController@download'])->name('invoices.download');
            Route::get('invoices/offline-payment', 'ClientInvoicesController@offlinePayment')->name('invoices.offline-payment');
            Route::post('invoices/offline-payment-submit', 'ClientInvoicesController@offlinePaymentSubmit')->name('invoices.offline-payment-submit');

            Route::get('invoices/deposit/{id}', ['uses' => 'ClientInvoicesController@deposit'])->name('invoices.deposit');
            
            Route::resource('invoices', 'ClientInvoicesController');

            // Estimate Section
            Route::get('estimates/download/{id}', ['uses' => 'ClientEstimateController@download'])->name('estimates.download');
            Route::resource('estimates', 'ClientEstimateController');

            //Payments section
            Route::get('payments/data', ['uses' => 'ClientPaymentsController@data'])->name('payments.data');
            Route::resource('payments', 'ClientPaymentsController');

            // Issues section
            Route::get('my-issues/data', ['uses' => 'ClientMyIssuesController@data'])->name('my-issues.data');
            Route::resource('my-issues', 'ClientMyIssuesController');

            // route for view/blade file
            Route::get('paywithpaypal', array('as' => 'paywithpaypal', 'uses' => 'PaypalController@payWithPaypal',));

            // change language
            Route::get('language/change-language', ['uses' => 'ClientProfileController@changeLanguage'])->name('language.change-language');
            // change company
            Route::get('company/change-company', ['uses' => 'ClientProfileController@changeCompany'])->name('company.change-company');
            // login admin
            Route::get('company/login-admin', ['uses' => 'ClientProfileController@loginAdmin'])->name('company.login-admin');

            Route::get('ticket-files/download/{id}', ['uses' => 'TicketFilesController@download'])->name('ticket-files.download');
            Route::resource('ticket-files', 'TicketFilesController');

            //Tickets routes
            Route::get('tickets/data', ['uses' => 'ClientTicketsController@data'])->name('tickets.data');
            Route::post('tickets/close-ticket/{id}', ['uses' => 'ClientTicketsController@closeTicket'])->name('tickets.closeTicket');
            Route::post('tickets/open-ticket/{id}', ['uses' => 'ClientTicketsController@reopenTicket'])->name('tickets.reopenTicket');
            Route::resource('tickets', 'ClientTicketsController');

            Route::resource('events', 'ClientEventController');

            Route::post('gdpr/update-consent', ['uses' => 'ClientGdprController@updateConsent'])->name('gdpr.update-consent');
            Route::get('gdpr/consent', ['uses' => 'ClientGdprController@consent'])->name('gdpr.consent');
            Route::get('gdpr/download', ['uses' => 'ClientGdprController@downloadJSON'])->name('gdpr.download-json');
            Route::post('gdpr/remove-request', ['uses' => 'ClientGdprController@removeRequest'])->name('gdpr.remove-request');
            Route::get('privacy-policy', ['uses' => 'ClientGdprController@privacy'])->name('gdpr.privacy');
            Route::get('terms-and-condition', ['uses' => 'ClientGdprController@terms'])->name('gdpr.terms');
            Route::resource('gdpr', 'ClientGdprController');

            // User message
            Route::post('message-submit', ['as' => 'user-chat.message-submit', 'uses' => 'ClientChatController@postChatMessage']);
            Route::get('user-search', ['as' => 'user-chat.user-search', 'uses' => 'ClientChatController@getUserSearch']);
            Route::resource('user-chat', 'ClientChatController');

            //task comments
            Route::resource('task-comment', 'ClientTaskCommentController');

            Route::post('pay-with-razorpay', array('as' => 'pay-with-razorpay', 'uses' => 'RazorPayController@payWithRazorPay',));

            //region contracts routes
            Route::get('contracts/data', ['as' => 'contracts.data', 'uses' => 'ClientContractController@data']);
            Route::get('contracts/download/{id}', ['as' => 'contracts.download', 'uses' => 'ClientContractController@download']);
            Route::get('contracts/sign/{id}', ['as' => 'contracts.sign-modal', 'uses' => 'ClientContractController@signModal']);
            Route::post('contracts/sign/{id}', ['as' => 'contracts.sign', 'uses' => 'ClientContractController@sign']);
            Route::post('contracts/add-discussion/{id}', ['as' => 'contracts.add-discussion', 'uses' => 'ClientContractController@addDiscussion']);
            Route::get('contracts/edit-discussion/{id}', ['as' => 'contracts.edit-discussion', 'uses' => 'ClientContractController@editDiscussion']);
            Route::post('contracts/update-discussion/{id}', ['as' => 'contracts.update-discussion', 'uses' => 'ClientContractController@updateDiscussion']);
            Route::post('contracts/remove-discussion/{id}', ['as' => 'contracts.remove-discussion', 'uses' => 'ClientContractController@removeDiscussion']);
            Route::resource('contracts', 'ClientContractController');
            //endregion

            //Notice
            Route::get('notices/data', ['uses' => 'ClientNoticesController@data'])->name('notices.data');
            Route::resource('notices', 'ClientNoticesController');
            
            
             //region discussion routes
            Route::post('discussion/setBestAnswer', ['as' => 'discussion.setBestAnswer', 'uses' => 'ClientDiscussionController@setBestAnswer']);
            Route::resource('discussion', 'ClientDiscussionController');
            //endregion

            //region discussion routes
            Route::resource('discussion-reply', 'ClientDiscussionReplyController');
            //endregion
            
            
        }
    );


    // Mark all notifications as readu
    Route::post('show-admin-notifications', ['uses' => 'NotificationController@showAdminNotifications'])->name('show-admin-notifications');
    Route::post('show-user-notifications', ['uses' => 'NotificationController@showUserNotifications'])->name('show-user-notifications');
    Route::post('show-client-notifications', ['uses' => 'NotificationController@showClientNotifications'])->name('show-client-notifications');
    Route::post('mark-notification-read', ['uses' => 'NotificationController@markAllRead'])->name('mark-notification-read');
    Route::get('show-all-member-notifications', ['uses' => 'NotificationController@showAllMemberNotifications'])->name('show-all-member-notifications');
    Route::get('show-all-client-notifications', ['uses' => 'NotificationController@showAllClientNotifications'])->name('show-all-client-notifications');
    Route::get('show-all-admin-notifications', ['uses' => 'NotificationController@showAllAdminNotifications'])->name('show-all-admin-notifications');

    Route::post('show-superadmin-user-notifications', ['uses' => 'SuperAdmin\NotificationController@showUserNotifications'])->name('show-superadmin-user-notifications');
    Route::post('mark-superadmin-notification-read', ['uses' => 'SuperAdmin\NotificationController@markAllRead'])->name('mark-superadmin-notification-read');
    Route::get('show-all-super-admin-notifications', ['uses' => 'SuperAdmin\NotificationController@showAllSuperAdminNotifications'])->name('show-all-super-admin-notifications');


    Route::group(['prefix' => 'laravel-filemanager', 'middleware' => ['web', 'auth']], function () {
        \UniSharp\LaravelFilemanager\Lfm::routes();
    });

//    Route::get('show-file-manager',['uses' => 'FileManagerController@index'])->name('view-file-manager');
//
//    Route::any('/create_folder', ['uses' => 'FileManagerController@create_folder'])->name('create_folder');
//    Route::any('/preview_folder/{folder_id}', ['uses' => 'FileManagerController@preview_folder'])->name('preview_folder');
//    Route::any('/add_file/{folder_id}', ['uses' => 'FileManagerController@add_file'])->name('add_file');
//    Route::any('/delete_folder/{folder_id}', ['uses' => 'FileManagerController@delete_folder'])->name('delete_folder');
//    Route::any('/delete_file/{folder_id}/{file_id}', ['uses' => 'FileManagerController@delete_file'])->name('delete_file');
//    Route::any('/filestoreMultiple', ['uses' => 'FileManagerController@storeMultiple'])->name('filestoreMultiple');
//    Route::any('/storeFile', ['uses' => 'FileManagerController@storeFile'])->name('storeFile');
});

Route::get('/parsing-link', ['uses' => 'Parsing\ParsingController@link', 'middleware' => 'auth']);
Route::get('form/save/{key}', ['uses' => 'ParsingController@form'])->name('parse-form-store');