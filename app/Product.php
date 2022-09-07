<?php

namespace App;

use App\Observers\ProductObserver;
use App\Scopes\CompanyScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Product extends BaseModel
{
    protected $table = 'products';

    // protected $fillable = ['name', 'price', 'tax_id'];
    // protected $appends = ['total_amount'];

    protected static function boot()
    {
        parent::boot();

        static::observe(ProductObserver::class);

        static::addGlobalScope(new CompanyScope);
    }

    public function afterLoad() {
        $this->itemObj = json_decode($this->item);
        $this->purchaseOrderObj = json_decode($this->purchaseOrder);
        $this->specificationObj = json_decode($this->specification);
        $this->pricingObj = json_decode($this->pricing);
        $this->pictureObj = json_decode($this->picture);
        $this->workroomObj = json_decode($this->workroom);

        if ($this->id == null) $this->id = 0;

        if ($this->itemObj == null) $this->itemObj = new BaseModel();
        if ($this->purchaseOrderObj == null) $this->purchaseOrderObj = new BaseModel();
        if ($this->specificationObj == null) {
            $this->specificationObj = new BaseModel();
            $attrs = array();
            for ($i = 0; $i < 10; $i ++) $attrs[$i] = (object)array('title' => '', 'description' => '');
            $this->specificationObj->attrs = (object)$attrs;
        }
        if ($this->pricingObj == null) {
            $this->pricingObj = new BaseModel();
            $types = array('merchandise', 'freight', 'crati', 'installLabor', 'designFee', 'time', 'total');
            $pricing = (object)array();
            foreach ($types as $type) $pricing->{$type} = new BaseModel();
            $this->pricingObj->pricing = $pricing;
        }
        if ($this->pictureObj == null) $this->pictureObj = new BaseModel();
        if ($this->workroomObj == null) $this->workroomObj = new BaseModel();
    }
    
    public function projects()
    {
        return $this->hasMany(ProductProject::class, 'product_id');
    }
    public function codes()
    {
        return $this->hasMany(ProductCodeType::class, 'product_id');
    }
    public function vendor()
    {
        return $this->belongsTo(ClientVendorDetails::class, 'vendor_id');
    }
    
    public function comments()
    {
        return $this->hasMany(ProductNote::class, 'product_id')->orderBy('id', 'desc');
    }
    
     public function productStatus()
    {
        return $this->belongsTo(ProductStatus::class, 'product_status_id');
    }
    
    

    /*public function tax()
    {
        return $this->belongsTo(Tax::class);
    }

    public static function taxbyid($id) {
        return Tax::where('id', $id);
    }

    public function getTotalAmountAttribute(){

        if(!is_null($this->price) && !is_null($this->tax)){
            return $this->price + ($this->price * ($this->tax->rate_percent/100));
        }

        return "";
    }*/
}
