<?php

namespace App\DataTables\Admin;

use App\DataTables\BaseDataTable;
use App\Role;
use App\User;
use Carbon\Carbon;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use App\RoleUser;

class EmployeesDataTable extends BaseDataTable
{
    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function dataTable($query)
    {
        $roles = Role::where('name', '<>', 'client')->orderBy('name')->get();
        $firstAdmin = User::firstAdmin();
        return datatables()
            ->eloquent($query)
            ->addColumn('role', function ($row) use ($roles, $firstAdmin) {
                $roleRow = '';
                if ($row->id != user()->id) {

                    $status = '<div class="btn-group dropdown">';
                    $status .= '<button aria-expanded="true" data-toggle="dropdown" class="btn ' . (($row->roleId == 1) ? 'btn-danger' : 'btn-info') . ' dropdown-toggle waves-effect waves-light btn-xs" type="button">' . (($row->roleId != 1) ? $row->current_role_name : __('app.' . $row->roleName)) . ' <span class="caret"></span></button>';
                    $status .= '<ul role="menu" class="dropdown-menu pull-right">';
                    foreach ($roles as $role) {
                        if ($role->id <= 3) {
                            $status .= '<li><a href="javascript:;" data-user-id="' . $row->id . '" class="assign_role" data-role-id="' . $role->id . '">' . __('app.' . $role->name) . '</a></li>';
                        } else {
                            $status .= '<li><a href="javascript:;" data-user-id="' . $row->id . '" class="assign_role" data-role-id="' . $role->id . '">' . ucwords($role->name) . '</a></li>';
                        }
                    }
                    $status .= '</ul>';
                    $status .= '</div>';
                    return $status;
                } else {
                    return __('messages.roleCannotChange');
                }

            })
            ->addColumn('employeeRole', function ($row) use ($roles, $firstAdmin) {
                
                $role_name = '';
                
                if($row->roleId != 1) {
                    $user_roles = RoleUser::where('user_id', $row->id)->get();
                    if($user_roles) {
                        foreach ($user_roles as $user_role) {
                            $rr = Role::where('id', $user_role->role_id)->first();
                            if($rr) {
                                if($role_name == '') {
                                    $role_name .=  ucfirst($rr->name);
                                }else {
                                    $role_name .=  ','.ucfirst($rr->name);
                                }
                            }
                        }
                    }
                } else {
                    $role_name =  __('app.' . $row->roleName);
                }
                
                return $role_name;
                
                //return (($row->roleId != 1) ? $row->current_role_name : __('app.' . $row->roleName));

            })
            ->addColumn('action', function ($row) {

                $action = '<div class="btn-group dropdown m-r-10">
                <button aria-expanded="false" data-toggle="dropdown" class="btn dropdown-toggle waves-effect waves-light" type="button"><i class="ti-more"></i></button>
                    <ul role="menu" class="dropdown-menu pull-right">
                    <li><a href="' . route('admin.employees.edit', [$row->id]) . '"><i class="fa fa-pencil" aria-hidden="true"></i> ' . trans('app.edit') . '</a></li>
                  <li><a href="' . route('admin.employees.show', [$row->id]) . '"><i class="fa fa-search" aria-hidden="true"></i> ' . __('app.view') . '</a></li>';
                if($this->user->id !== $row->id){
                    $action .= '<li><a href="javascript:;"  data-user-id="' . $row->id . '"  class="sa-params"><i class="fa fa-times" aria-hidden="true"></i> ' . trans('app.delete') . '</a></li>';
                }
                $action .= '</ul> </div>';

                return $action;


            })
            ->editColumn(
                'created_at',
                function ($row) {
                    return Carbon::parse($row->created_at)->format($this->global->date_format);
                }
            )
            ->editColumn(
                'status',
                function ($row) {
                    if ($row->status == 'active') {
                        return '<label class="label label-success">' . __('app.active') . '</label>';
                    } else {
                        return '<label class="label label-danger">' . __('app.inactive') . '</label>';
                    }
                }
            )
            ->editColumn('name', function ($row) use ($roles) {

                if($row->image) {
                    $image = '<img src="' . $row->image_url . '"alt="user" class="img-circle" width="30" height="30"> ';
                } else {
                    $image = '<span class="nameletter">'.company_initials().'</span>';
                }
                

                $designation = ($row->designation_name) ? ucwords($row->designation_name) : ' ';

                return  '<div class="row truncate"><div class="col-sm-3 col-xs-4">' . $image . '</div><div class="col-sm-9 col-xs-8"><a href="' . route('admin.employees.show', $row->id) . '">' . ucwords($row->name) . '</a><br><span class="text-muted font-12">' . $designation . '</span></div></div>';
            })
            ->addIndexColumn()
            ->rawColumns(['name', 'action', 'role', 'status'])
            ->removeColumn('roleId')
            ->removeColumn('roleName')
            ->removeColumn('current_role');
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Product $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(User $model)
    {
        $request = $this->request();
        if ($request->role != 'all' && $request->role != '') {
            $userRoles = Role::findOrFail($request->role);
        }

        $users = User::with('role')
            ->withoutGlobalScope('active')
            ->join('role_user', 'role_user.user_id', '=', 'users.id')
            ->leftJoin('employee_details', 'employee_details.user_id', '=', 'users.id')
            ->leftJoin('designations', 'employee_details.designation_id', '=', 'designations.id')
            ->join('roles', 'roles.id', '=', 'role_user.role_id')
            ->select('users.id', 'users.name', 'users.email', 'users.created_at', 'roles.name as roleName', 'roles.id as roleId', 'users.image', 'users.status', \DB::raw("(select user_roles.role_id from role_user as user_roles where user_roles.user_id = users.id ORDER BY user_roles.role_id DESC limit 1) as `current_role`"), \DB::raw("(select roles.name from roles as roles where roles.id = current_role limit 1) as `current_role_name`"), 'designations.name as designation_name')
            ->where('roles.name', '<>', 'client');

        if ($request->status != 'all' && $request->status != '') {
            $users = $users->where('users.status', $request->status);
        }

        if ($request->employee != 'all' && $request->employee != '') {
            $users = $users->where('users.id', $request->employee);
        }

        if ($request->designation != 'all' && $request->designation != '') {
            $users = $users->where('employee_details.designation_id', $request->designation);
        }

        if ($request->department != 'all' && $request->department != '') {
            $users = $users->where('employee_details.department_id', $request->department);
        }
        
        if ($request->role != 'all' && $request->role != '' && $userRoles) {
            if ($userRoles->name == 'admin') {
                $users = $users->where('roles.id', $request->role);
            } elseif ($userRoles->name == 'employee') {
                $users =  $users->where(\DB::raw("(select user_roles.role_id from role_user as user_roles where user_roles.user_id = users.id ORDER BY user_roles.role_id DESC limit 1)"), $request->role)
                    ->having('roleName', '<>', 'admin');
            } else {
                $users = $users->where(\DB::raw("(select user_roles.role_id from role_user as user_roles where user_roles.user_id = users.id ORDER BY user_roles.role_id DESC limit 1)"), $request->role);
            }
        }
        
        if ((is_array($request->skill) && $request->skill[0] != 'all') && $request->skill != '' && $request->skill != null && $request->skill != 'null') {
            $users =  $users->join('employee_skills', 'employee_skills.user_id', '=', 'users.id')
                ->whereIn('employee_skills.skill_id', $request->skill);
        }

        return $users->groupBy('users.id');
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        return $this->builder()
            ->setTableId('employees-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->dom("<'row'<'col-md-6'l><'col-md-6'Bf>><'row'<'col-sm-12'tr>><'row'<'col-sm-5'i><'col-sm-7'p>>")
            ->destroy(true)
            ->orderBy(0)
            ->responsive(true)
            ->serverSide(true)
            ->stateSave(true)
            ->processing(true)
            ->language(__("app.datatable"))
            ->parameters([
                'initComplete' => 'function () {
                   window.LaravelDataTables["employees-table"].buttons().container()
                    .appendTo( ".bg-title .text-right")
                }',
                'fnDrawCallback' => 'function( oSettings ) {
                    $("body").tooltip({
                        selector: \'[data-toggle="tooltip"]\'
                    })
                }',
            ])
            ->buttons(
                Button::make(['extend' => 'export', 'buttons' => ['excel', 'csv'], 'text' => '<i class="fa fa-download"></i> ' . trans('app.exportExcel') . '&nbsp;<span class="caret"></span>'])
            );
    }

    /**
     * Get columns.
     *
     * @return array
     */
    protected function getColumns()
    {
        return [
           // __('app.id') => ['data' => 'id', 'name' => 'id', 'visible' => false, 'exportable' => false],
            '#' => ['data' => 'DT_RowIndex', 'orderable' => false, 'searchable' => false ],
            __('app.name') => ['data' => 'name', 'name' => 'name'],
            __('app.email') => ['data' => 'email', 'name' => 'email'],
            __('app.role') => ['data' => 'role', 'name' => 'role', 'width' => '20%', 'exportable' => false],
            __('app.roleAssigned') => ['data' => 'employeeRole', 'name' => 'employeeRole', 'width' => '20%'],
            __('app.status') => ['data' => 'status', 'name' => 'status'],
            Column::computed('action', __('app.action'))
                ->exportable(false)
                ->printable(false)
                ->orderable(false)
                ->searchable(false)
                ->width(150)
                ->addClass('text-center')
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'employees_' . date('YmdHis');
    }

    public function pdf()
    {
        set_time_limit(0);
        if ('snappy' == config('datatables-buttons.pdf_generator', 'snappy')) {
            return $this->snappyPdf();
        }

        $pdf = app('dompdf.wrapper');
        $pdf->loadView('datatables::print', ['data' => $this->getDataForPrint()]);

        return $pdf->download($this->getFilename() . '.pdf');
    }
}
