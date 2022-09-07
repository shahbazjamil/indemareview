<?php

ApiRoute::group(['namespace' => 'Modules\RestAPI\Http\Controllers'], function () {

    ApiRoute::get('app', ['as' => 'api.app', 'uses' => 'AppController@app']);

    // Forgot Password
    ApiRoute::post(
        'auth/forgot-password',
        ['as' => 'api.auth.forgotPassword', 'uses' => 'AuthController@forgotPassword']
    );

    // Auth routes
    ApiRoute::post('auth/login', ['as' => 'api.auth.login', 'uses' => 'AuthController@login']);
    ApiRoute::post('auth/logout', ['as' => 'api.auth.logout', 'uses' => 'AuthController@logout']);
    ApiRoute::post('auth/reset-password', ['as' => 'api.auth.resetPassword', 'uses' => 'AuthController@resetPassword']);
    ApiRoute::get('auth/refresh', ['as' => 'api.auth.refresh', 'uses' => 'AuthController@refresh']);
    
    ApiRoute::post('auth/loginbyuid', ['as' => 'api.auth.loginbyuid', 'uses' => 'AuthController@loginByUID']); //added by SB
    
    // File view does not require Auth
    ApiRoute::get('/file/{name}', ['as' => 'file.show', 'uses' => 'FileController@download']);
    
     // get all users  does not require Auth
    //ApiRoute::post('/mood-board/users', ['as' => 'mood-board.users', 'uses' => 'MoodBoardController@allUsers']); // no need this api 
    
    ApiRoute::post('/mood-board/products', ['as' => 'mood-board.products', 'uses' => 'MoodBoardController@productsByCompnayID']);
    
    

    // We public file uploads, but only for certain types, which we will check in request
    ApiRoute::post('/file', ['as' => 'file.store', 'uses' => 'FileController@upload']);
    ApiRoute::get('/lang', ['as' => 'lang', 'uses' => 'LanguageController@lang']);
});

ApiRoute::group(['namespace' => 'Modules\RestAPI\Http\Controllers','middleware' => 'api.auth'], function () {

    ApiRoute::get('/project/me', ['as' => 'project.me', 'uses' => 'ProjectController@me']);

    ApiRoute::get('company', ['as' => 'api.app', 'uses' => 'CompanyController@company']);
    ApiRoute::post('/project/{project_id}/members', ['as' => 'project.member', 'uses' => 'ProjectController@members']);
    ApiRoute::delete(
        '/project/{project_id}/member/{id}',
        [
            'as' => 'project.member.delete',
            'uses' => 'ProjectController@memberRemove'
        ]
    );
    ApiRoute::resource('project', 'ProjectController');
    ApiRoute::resource('project-category', 'ProjectCategoryController');
    ApiRoute::resource('currency', 'CurrencyController');

    ApiRoute::get('/task/me', ['as' => 'task.me', 'uses' => 'TaskController@me']);
    ApiRoute::get('/task/remind/{id}', ['as' => 'task.remind', 'uses' => 'TaskController@remind']);

    ApiRoute::resource('/task/{task_id}/subtask', 'SubTaskController');
    ApiRoute::resource('task', 'TaskController');
    ApiRoute::resource('task-category', 'TaskCategoryController');
    ApiRoute::resource('taskboard-columns', 'TaskboardColumnController');


    ApiRoute::resource('lead', 'LeadController');
    ApiRoute::resource('client', 'ClientController');
    ApiRoute::resource('department', 'DepartmentController');
    ApiRoute::resource('designation', 'DesignationController');

    ApiRoute::resource('holiday', 'HolidayController');

    ApiRoute::resource('contract-type', 'ContractTypeController');
    ApiRoute::resource('contract', 'ContractController');

    ApiRoute::resource('notice', 'NoticeController');
    ApiRoute::resource('event', 'EventController');
    ApiRoute::get('/me/calendar', 'EventController@me');

    ApiRoute::get('/estimate/send/{id}', ['as' => 'estimate.send', 'uses' => 'EstimateController@sendEstimate']);
    ApiRoute::resource('estimate', 'EstimateController');

    ApiRoute::get('/invoice/send/{id}', ['as' => 'invoice.send', 'uses' => 'InvoiceController@sendInvoice']);
    ApiRoute::resource('invoice', 'InvoiceController');

    ApiRoute::resource('ticket', 'TicketController');
    ApiRoute::resource('ticket-reply', 'TicketReplyController');
    ApiRoute::resource('ticket-group', 'TicketGroupController', ['only' => ['index']]);
    ApiRoute::resource('ticket-channel', 'TicketChannelController', ['only' => ['index']]);
    ApiRoute::resource('ticket-type', 'TicketTypeController', ['only' => ['index']]);

    ApiRoute::get('product/vendors', ['as' => 'product.vendors', 'uses' => 'ProductController@getVendors']);
    ApiRoute::get('product/sale-categories', ['as' => 'product.sale-categories', 'uses' => 'ProductController@getSaleCategories']);
    ApiRoute::get('product/location-codes', ['as' => 'product.location-codes', 'uses' => 'ProductController@getLocationCodes']);
    ApiRoute::get('product/projects', ['as' => 'product.projects', 'uses' => 'ProductController@getProjects']);
    ApiRoute::get('product/filter', ['as' => 'product.filter', 'uses' => 'ProductController@filterProducts']);
    
    
    ApiRoute::resource('product', 'ProductController');
    ApiRoute::get(
        '/employee/last-employee-id',
        [
            'as' => 'employee.last-employee-id',
            'uses' => 'EmployeeController@lastEmployeeID'
        ]
    );
    ApiRoute::resource('employee', 'EmployeeController');

    ApiRoute::resource('user', 'UserController', ['only' => ['index']]);

    ApiRoute::resource('expense', 'ExpenseController');

    ApiRoute::resource('leave', 'LeaveController');
    ApiRoute::get('leave-type', 'LeaveTypeController@index');

    ApiRoute::post('/device/register', ['as' => 'device.register', 'uses' => 'DeviceController@register']);
    ApiRoute::post('/device/unregister', ['as' => 'device.unregister', 'uses' => 'DeviceController@unregister']);


    ApiRoute::get('/attendance/today', ['as' => 'attendance.today', 'uses' => 'AttendanceController@today']);
    ApiRoute::post('/attendance/clock-in', ['as' => 'attendance.clockIn', 'uses' => 'AttendanceController@clockIn']);
    ApiRoute::post(
        '/attendance/clock-out/{attendance}',
        [
            'as' => 'attendance.clockOut',
            'uses' => 'AttendanceController@clockOut'
        ]
    );
    ApiRoute::resource('/attendance', 'AttendanceController');

    ApiRoute::resource('/tax', 'TaxController', ['only' => ['index']]);
});
