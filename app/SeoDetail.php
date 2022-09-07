<?php

namespace App;

class SeoDetail extends BaseModel
{
    protected $table = 'seo_details';
    protected $fillable = ['page_name','seo_title','seo_author','seo_description','seo_keywords'];
}
