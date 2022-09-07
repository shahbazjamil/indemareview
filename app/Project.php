<?php

namespace App;

use App\Observers\ProjectObserver;
use App\Scopes\CompanyScope;
use App\Traits\CustomFieldsTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends BaseModel
{
    use CustomFieldsTrait; use SoftDeletes;

    protected $dates = ['start_date', 'deadline'];

    protected $guarded = ['id'];

    protected $appends = ['isProjectAdmin'];

    protected static function boot()
    {
        parent::boot();
        static::observe(ProjectObserver::class);
        static::addGlobalScope(new CompanyScope);
    }

    public function category()
    {
        return $this->belongsTo(ProjectCategory::class, 'category_id');
    }

    public function client()
    {
        return $this->belongsTo(ClientDetails::class, 'client_id', 'user_id');
    }
    
    public function clients()
    {
        return $this->hasMany(ProjectClient::class, 'project_id');
    }

    public function clientdetails()
    {
        return $this->belongsTo(ClientDetails::class, 'client_id', 'user_id');
    }

    public function members()
    {
        return $this->hasMany(ProjectMember::class, 'project_id');
    }
    
    public function products()
    {
        return $this->hasMany(Product::class, 'project_id');
    }

    public function members_many()
    {
        return $this->belongsToMany(User::class, 'project_members');
    }
    
    public function clients_many()
    {
        return $this->belongsToMany(User::class, 'project_clients', null, 'client_id');
    }

    public function tasks()
    {
        return $this->hasMany(Task::class, 'project_id')->orderBy('id', 'desc');
    }

    public function files()
    {
        return $this->hasMany(ProjectFile::class, 'project_id')->orderBy('id', 'desc');
    }
    public function folders()
    {
        return $this->hasMany(ProjectFolder::class, 'project_id')->orderBy('id', 'desc');
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'project_id')->orderBy('id', 'desc');
    }

    public function issues()
    {
        return $this->hasMany(Issue::class, 'project_id')->orderBy('id', 'desc');
    }

    public function times()
    {
        return $this->hasMany(ProjectTimeLog::class, 'project_id')->orderBy('id', 'desc');
    }

    public function milestones()
    {
        return $this->hasMany(ProjectMilestone::class, 'project_id');
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class, 'currency_id');
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class, 'project_id')->orderBy('id', 'desc');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'project_id')->orderBy('id', 'desc');
    }

    /**
     * @return bool
     */
    public function checkProjectUser()
    {
        $project = ProjectMember::where('project_id', $this->id)
            ->where('user_id', auth()->user()->id)
            ->count();

        if ($project > 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @return bool
     */
    public function checkProjectClient()
    {
        
        $project_ids[] = 0;
        $projectClients = ProjectClient::where('client_id', auth()->user()->id)->get();
        if($projectClients) {
            foreach ($projectClients as $projectClient) {
                $project_ids[] = $projectClient->project_id;
            }
        }
        
        $project = Project::where('id', $this->id)
            ->where('client_id', auth()->user()->id)
            ->orWhereIn('projects.id', $project_ids)
            ->count();

        if ($project > 0) {
            return true;
        } else {
            return false;
        }
    }

    public static function clientProjects($clientId)
    {
        $project_ids[] = 0;
        $projectClients = ProjectClient::where('client_id', $clientId)->get();
        if($projectClients) {
            foreach ($projectClients as $projectClient) {
                $project_ids[] = $projectClient->project_id;
            }
        }
        $projects = Project::where('client_id', $clientId)->orWhereIn('projects.id', $project_ids)->get();
        
        return $projects;
    }
    
    public static function allProjects()
    {
        return cache()->remember(
            'all-projects', 60*60*24, function () {
                return Project::orderBy('project_name', 'asc')->get();
            }
        );
    }

    public static function byEmployee($employeeId)
    {
        return Project::join('project_members', 'project_members.project_id', '=', 'projects.id')
            ->where('project_members.user_id', $employeeId)
            ->get();
    }

    public function scopeCompleted($query)
    {
        return $query->where('completion_percent', '100');
    }

    public function scopeInProcess($query)
    {
        return $query->where('status', 'in progress');
    }

    public function scopeOnHold($query)
    {
        return $query->where('status', 'on hold');
    }

    public function scopeFinished($query)
    {
        return $query->where('status', 'finished');
    }

    public function scopeNotStarted($query)
    {
        return $query->where('status', 'not started');
    }

    public function scopeCanceled($query)
    {
        return $query->where('status', 'canceled');
    }

    public function scopeOverdue($query)
    {
        $setting = Setting::first();
        return $query->where('completion_percent', '<>', '100')
            ->where('deadline', '<', Carbon::today()->timezone($setting->timezone));
    }

    public function getIsProjectAdminAttribute()
    {
        if (isset(auth()->user()->id) && $this->project_admin == auth()->user()->id) {
            return true;
        }
        return false;
    }

    public static function projectNames() {
        return Project::select("id", "project_name")->where('company_id', user()->company_id)->get();
    }
    
     public function getTotalPurchaseOrder(){
       $total = PurchaseOrder::where('project_id', $this->id)->sum('total_amount');
       return  number_format((float)$total, 2, '.', '');
     }
     public function getTotalWeeklyLogedHours(){
         
         
        $setting = Setting::first();
        // this week
        $startDate = Carbon::now()->timezone($setting->timezone)->startOfWeek();
        $endDate = Carbon::now()->timezone($setting->timezone);
         
       $timeLog = ProjectTimeLog::where('project_id', $this->id)
               ->where('created_at', '>=', $startDate->format('Y-m-d'))
               ->where('created_at', '<=', $endDate->format('Y-m-d'))
               ->sum('total_minutes');
       $timeLog = intdiv($timeLog, 60);
       return $timeLog;
       
     }
     
    //aqeel
//    public function allCompanyProjects($companyid)
//    {
//        return Project::all()->where('company_id',$companyid);
//    }

}
