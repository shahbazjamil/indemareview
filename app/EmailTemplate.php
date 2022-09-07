<?php

namespace App;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Database\Eloquent\Model;

class EmailTemplate extends Model
{
    /**
     * @var string
     */
    public $table = 'email_templates';

    /**
     * @var array
     */
    public $fillable = [
        'user_id',
        'company_id',
        'template_name',
        'subject',
        'body',
        'variable',
        'email_type',
        'file_name',
        'file_extension'
    ];

    protected $appends = ['file_url', 'gmail_file_url'];

    protected $casts = [
        'body' => 'string',
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'template_name' => 'required',
        'subject' => 'required|max:150',
        'body'    => 'required',
    ];

    /**
     * @return Application|UrlGenerator|string
     */
    public function getFileUrlAttribute()
    {
        return ($this->file_name) ? asset_url('email-templates/'.$this->id .'/'. $this->file_name) : asset('img/img-dummy.jpg');
    }

    /**
     * @return Application|UrlGenerator|string
     */
    public function getGmailFileUrlAttribute()
    {
        return ($this->file_name) ? public_path().'/user-uploads/email-templates/'.$this->id .'/'. $this->file_name : public_path().'/img/img-dummy.jpg';
    }
}
