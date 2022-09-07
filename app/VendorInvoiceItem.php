<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class VendorInvoiceItem extends Model
{
    protected $fillable = [
      'invoice_id','item_name','quantity','unit_price','amount',
    ];
}
