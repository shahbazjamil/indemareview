<?php

namespace App;

use App\Notifications\EmailVerification;
use App\Notifications\NewUser;
use App\Observers\CompanyObserver;
use App\Scopes\CompanyScope;
use GuzzleHttp\Client;
use Illuminate\Notifications\Notifiable;
use Laravel\Cashier\Billable;
use Laravel\Cashier\Invoice;
use Stripe\Invoice as StripeInvoice;

class Company extends BaseModel
{
    protected $table = 'companies';
    protected $dates = ['trial_ends_at', 'licence_expire_on', 'created_at', 'updated_at', 'last_login'];
    protected $fillable = ['last_login', 'company_name', 'company_email', 'company_phone', 'website', 'address', 'currency_id', 'timezone', 'locale', 'date_format', 'time_format', 'week_start', 'longitude', 'latitude'];
    protected $appends = ['logo_url', 'login_background_url', 'gmail_login_background_url', 'gmail_logo_url'];
    use Notifiable, Billable;

    // public function findInvoice($id)
    // {
    //     try {
    //         $stripeInvoice = StripeInvoice::retrieve(
    //             $id,
    //             $this->getStripeKey()
    //         );

    //         $stripeInvoice->lines = StripeInvoice::retrieve($id, $this->getStripeKey())
    //             ->lines
    //             ->all(['limit' => 1000]);

    //         $stripeInvoice->date = $stripeInvoice->created;
    //         return new Invoice($this, $stripeInvoice);

    //     } catch (\Exception $e) {
    //         //
    //     }


    // }

    public static function boot()
    {
        parent::boot();
        static::observe(CompanyObserver::class);
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class, 'currency_id');
    }

    public function package()
    {
        return $this->belongsTo(Package::class, 'package_id');
    }

    public function employees()
    {
        return $this->hasMany(User::class)
            ->join('employee_details', 'employee_details.user_id', 'users.id');
    }
    
     public function projects()
    {
        return $this->hasMany(User::class)
            ->join('projects', 'projects.company_id', 'users.company_id');
    }

    public function file_storage()
    {
        return $this->hasMany(FileStorage::class, 'company_id');
    }

    public function getLogoUrlAttribute()
    {
        if (is_null($this->logo)) {
            $global = global_settings();
            return $global->logo_url;
        }
        return asset_url('app-logo/' . $this->logo);
    }

    public function getLoginBackgroundUrlAttribute()
    {
        if (is_null($this->login_background) || $this->login_background == 'login-background.jpg') {
            return asset('img/login-bg.jpg');
        }

        return asset_url('login-background/' . $this->login_background);
    }

    public function validateGoogleRecaptcha($googleRecaptchaResponse)
    {
        $global = global_settings();
        $client = new Client();
        $response = $client->post(
            'https://www.google.com/recaptcha/api/siteverify',
            ['form_params' =>
            [
                'secret' => $global->google_recaptcha_secret,
                'response' => $googleRecaptchaResponse,
                'remoteip' => $_SERVER['REMOTE_ADDR']
            ]]
        );

        $body = json_decode((string) $response->getBody());

        return $body->success;
    }
    public function addUser($company, $request)
    {
        // Save Admin
        $user = User::withoutGlobalScopes([CompanyScope::class, 'active'])->where('email', $request->email)->first();
        if (is_null($user)) {
            $user = new User();
        }
        $user->company_id = $company->id;
        $user->name = 'admin';
        $user->email = $request->email;
        $user->password = bcrypt($request->password);
        $user->status = 'active';
        $user->email_verification_code = str_random(40);
        $user->lm_data = $request->lm_data?$request->lm_data:null;
        $user->save();

        return $user;
    }
    public function addEmployeeDetails($user)
    {
        $employee = new EmployeeDetails();
        $employee->user_id = $user->id;
        $employee->employee_id = 'emp-' . $user->id;
        $employee->company_id = $user->company_id;
        $employee->address = 'address';
        $employee->hourly_rate = '50';
        $employee->save();

        $global = global_settings();

        if ($global->email_verification == 1) {
            // Send verification mail
            $user->notify(new EmailVerification($user));
            $user->status = 'deactive';
            $user->save();

            $message = __('messages.signUpThankYouVerify');
        } else {

            $user->notify(new NewUser(request()->password));
            $message = __('messages.signUpThankYou') . ' <a href="' . route('login') . '">Login Now</a>.';
        }
        return $message;
    }
    public function recaptchaValidate($request)
    {
        $global = global_settings();
        if (!is_null($global->google_recaptcha_key)) {
            $gRecaptchaResponseInput = 'g-recaptcha-response';
            $gRecaptchaResponse = $request->{$gRecaptchaResponseInput};
            $validateRecaptcha = $this->validateGoogleRecaptcha($gRecaptchaResponse);
            if (!$validateRecaptcha) {
                return false;
            }
        }
        return true;
    }

    public function assignRoles($user)
    {

        // Assign roles even before verification
        $adminRole = Role::where('name', 'admin')->where('company_id', $user->company_id)->first();
        $user->roles()->attach($adminRole->id);

        $employeeRole = Role::where('name', 'employee')->where('company_id', $user->company_id)->first();
        $user->roles()->attach($employeeRole->id);

        return $user;
    }

    public function setSubDomainAttribute($value)
    {
        // domain is added in the request Class
        $this->attributes['sub_domain'] = strtolower($value);
    }

    public function getGmailLogoUrlAttribute()
    {
        if (is_null($this->logo)) {
            $global = global_settings();
            return $global->logo_url;
        }
        return public_path().'/user-uploads/app-logo/' . $this->logo;
    }

    public function getGmailLoginBackgroundUrlAttribute()
    {
        if (is_null($this->login_background) || $this->login_background == 'login-background.jpg') {
            return public_path().'/img/login-bg.jpg';
        }

        return public_path().'/user-uploads/login-background/' . $this->login_background;
    }
}
