
                    
                    
                    
                    <div class="col-md-12 d-i-new" id="view-details">
                
                     @foreach($products as $row)
                     
                        <?php

                            $image = asset('img/default-product.png');
                            if(!empty($row->picture)) {
                                $pictures = json_decode($row->picture);
                                if($pictures) {
                                    $image =  asset('user-uploads/products/'.$row->id.'/'.$pictures[0].'');
                                }
                            }
                            
                                $name = '';
                                if(isset($row->name) && !empty($row->name)) {
                                    $name = ucfirst($row->name);
                                }
                                $url = '';
                                if(isset($row->url) && !empty($row->url)) {
                                    $url = $row->url;
                                }
                                
                               
                        $style = '';
                        
                        if(isset($row->product_status_id) && !empty($row->product_status_id)) {
                            $style = 'color: '.$row->productStatus->status_color.' !important; border: 1px solid '.$row->productStatus->status_color.' !important;';
                        }
                        
//                        $client_status_cls = 'pending';
//                        $client_status_text = 'Pending';
                        $client_status_cls = '';
                        $client_status_text = ' ';
                        if($row->is_approved == 1) {
                            $client_status_text = 'Approved';
                            $client_status_cls = 'approved';
                        } else if (!is_null($row->is_approved) && $row->is_approved == 0){
                            $client_status_text = 'Declined';
                            $client_status_cls = 'declined';
                        }
                        
                        
                        // new logic assign multiple projects to products
                        $project_name = ' ';
                        if($row->projects) { 
                             foreach ($row->projects as $project) {
                                 $projectDT = \App\Project::where('id', $project->project_id)->first();
                                 if($projectDT) {
                                   $project_name = ucfirst($projectDT->project_name);
                                   break;
                                }
                             }
                         }
                         
                        $category = ' ';
                        $item = json_decode($row->item);
                        if(isset($item->salesCategory) && !empty($item->salesCategory)) {
                            $salesCategory = \App\SalescategoryType::where('salescategory_code', $item->salesCategory)->first();
                            if($salesCategory) {
                                $category =  ucfirst($salesCategory->salescategory_name);
                            }
                        }
                        
                        $locationCode = ' ';
                        if($row->codes) {
                            foreach ($row->codes as $code) {
                                if($code->code) {
                                    $locationCode = ucfirst($code->code->location_name);
                                    break;
                                }
                            }
                        }
                        
                        
                       

                        ?>
                     
                        <div class="d-flex border">
                                   <div class="col-auto img">
                                           <img src="{{$image}}" alt="product" width="75" height="75">
                                   </div>
                                   <div class="col-auto name">					
                                           <div><b onblur="saveData(this, '<?php echo $row->id; ?>', 'name');" contenteditable="true">{{$name}}</b> Product Name</div>
                                           <div><b onblur="saveData(this, '<?php echo $row->id; ?>', 'url');" contenteditable="true">{{$url}}</b> Product Url</div>
                                           <div><b onblur="saveData(this, '<?php echo $row->id; ?>', 'spec_number');" contenteditable="true">{{$row->spec_number}}</b> Spec Number</div>
                                   </div>
                                   <div class="col-auto status">					
                                           <div>
                                               <select id="product-status-color-<?php echo $row->id; ?>"  onchange="saveData(this, '<?php echo $row->id; ?>', 'product_status_id');" style="<?php echo $style ?>">
                                                   <option value="">Select</option>
                                                    <?php foreach ($productStatuses as $status) {
                                                        $selected = '';
                                                        if($status->id == $row->product_status_id) {
                                                            $selected = 'selected="selected"';
                                                        }
                                                        
                                                        ?>
                                                        <option data-color="{{$status->status_color}}" <?php echo $selected; ?> value="{{$status->id}}">{{$status->status_name}}</option>
                                                    <?php } ?>
                                               </select> Product Status
                                           </div>
                                       <div><b contenteditable="false" class="status <?php echo $client_status_cls; ?>">{{$client_status_text}}</b> Client Status</div>
                                   </div>
                                   <div class="col-auto other">					
                                       <div><b contenteditable="false">{{$project_name}}</b> Project</div>
                                           
                                           <div>
                                               <select onchange="saveData(this, '<?php echo $row->id; ?>', 'vendor_id');">
                                                   <option value="">Select</option>
                                                   <?php foreach ($clientVendors as $vendor) {
                                                        $selected = '';
                                                        if($vendor->id == $row->vendor_id) {
                                                            $selected = 'selected="selected"';
                                                        }
                                                        ?>
                                                        <option <?php echo $selected; ?> value="{{$vendor->id}}">{{$vendor->company_name}}</option>
                                                    <?php } ?>
                                                   
                                                   <option value="deciding">Vendor</option>
                                               </select> 
                                               Vendor
                                           </div>
                                           <div><b  onblur="saveData(this, '<?php echo $row->id; ?>', 'materials');" contenteditable="true">{{$row->materials}}</b> Material</div>
                                           <div><b  onblur="saveData(this, '<?php echo $row->id; ?>', 'acknowledgement');" contenteditable="true">{{$row->acknowledgement}}</b> Acknowledgement</div>
                                           <div><b  contenteditable="false">{{$category}}</b> Product Category</div>
                                           <div><b  onblur="saveData(this, '<?php echo $row->id; ?>', 'manufacturer');" contenteditable="true">{{ucfirst($row->manufacturer)}}</b> Manufacturer</div>
                                           <div><b  onkeyup="saveData(this, '<?php echo $row->id; ?>', 'product_number');" contenteditable="true">{{$row->product_number}}</b> Product Number</div>
                                           <div><b  onblur="saveData(this, '<?php echo $row->id; ?>', 'finish_color');" contenteditable="true">{{ucfirst($row->finish_color)}}</b> Finish/Color</div>
                                           <div><b  contenteditable="false">{{$locationCode}}</b> Location Code</div> 
                                           <div><b  onblur="saveData(this, '<?php echo $row->id; ?>', 'dimensions');" contenteditable="true">{{$row->dimensions}}</b> Dimensions</div>
                                           <div><b  onblur="saveData(this, '<?php echo $row->id; ?>', 'tracking_number');" contenteditable="true">{{$row->tracking_number}}</b> Tracking Number</div>
                                           <div><b  onblur="saveData(this, '<?php echo $row->id; ?>', 'lead_time');" contenteditable="true">{{$row->lead_time}}</b> Lead Time</div>
                                           
                                   </div>
                                   <div class="col note">					
                                           <div>Notes<b  onblur="saveData(this, '<?php echo $row->id; ?>', 'notes');" contenteditable="true" class="notes">{{$row->notes}}</b></div>
                                   </div>

                                           <div class="col-auto last-action">
                                               <?php
                                               
                                                    $action = '<div class="btn-group dropdown m-r-10">
                                                    <button aria-expanded="false" data-toggle="dropdown" class="btn dropdown-toggle waves-effect waves-light" type="button"><i class="ti-more"></i></button>
                                                    <ul role="menu" class="dropdown-menu pull-right">
                                                      <li><a href="' . route('admin.products.edit', [$row->id]). '"><i class="fa fa-pencil" aria-hidden="true"></i> ' . trans('app.edit') . '</a></li>
                                                        <li><a href="' . route('admin.products.edit', [$row->id, "copy"=>1]). '"><i class="fa fa-copy" aria-hidden="true"></i> ' . trans('app.copy') . '</a></li>
                                                      <li><a href="javascript:;"  data-user-id="' . $row->id . '"  class="sa-params"><i class="fa fa-times" aria-hidden="true"></i> ' . trans('app.delete') . '</a></li>
                                                      <li><a href="'. route("admin.products.download", $row->id).'"><i class="fa fa-file-pdf-o"></i> ' . trans('app.download') . '</a></li>';

                                                    $action .= ' <li><a href="javascript:" data-user-id="' . $row->id . '" class="pa-rfq" data-toggle="modal" data-target="#productRFQModal"><i class="fa fa-send"></i> RFQ </a></li>';


                                                    $action .= '</ul> </div>';
                                                    echo $action;
                                               ?>
                                           
                                           </div>
                           </div>
                      @endforeach
                     
                    
			       <div id="paginations">
              <?php
                if($_GET){
                  ?>
                  {{ $products->appends($_GET) }}
              <?php
                }else{
                  ?>
                  {{ $products->links() }}  
                  <?php
                }
              ?>
            </div>
			
		</div><!--end of col-12-->
               
                
                <div class="col-md-12 d-i-new" id="view-others" style="display:none">
                    
                     @foreach($products as $row)
                     
                        <?php

                            $image = asset('img/default-product.png');
                            if(!empty($row->picture)) {
                                $pictures = json_decode($row->picture);
                                if($pictures) {
                                    $image =  asset('user-uploads/products/'.$row->id.'/'.$pictures[0].'');
                                }
                            }
                            
                            $name = '';
                            if(isset($row->name) && !empty($row->name)) {
                                $name = ucfirst($row->name);
                            }
                            $url = '';
                            if(isset($row->url) && !empty($row->url)) {
                                $url = $row->url;
                            }
                            
                            $cost_per_unit = '';
                            if(isset($row->cost_per_unit) && !empty($row->cost_per_unit) && is_numeric($row->cost_per_unit)) {
                            $cost_per_unit =  $row->cost_per_unit? number_format($row->cost_per_unit, 2) : '';
                            }
                            $markup_fix = '';
                            if(isset($row->markup_fix) && !empty($row->markup_fix) && is_numeric($row->markup_fix)) {
                                $markup_fix =  $row->markup_fix? number_format($row->markup_fix, 2) : '';
                            }
                            
                            $markup_per = '';
                            if(isset($row->markup_per) && !empty($row->markup_per) && is_numeric($row->markup_per)) {
                                $markup_per = number_format($row->markup_per, 2);
                            }
                            
                            $default_markup_fix = '-';
                            if(isset($row->default_markup_fix) && !empty($row->default_markup_fix) && is_numeric($row->default_markup_fix)) {
                                $default_markup_fix = $row->default_markup_fix ? number_format($row->default_markup_fix, 2) : '';
                            }
                            
                            $freight = '';
                            if(isset($row->freight) && !empty($row->freight) && is_numeric($row->freight)) {
                                $freight =  $row->freight ? number_format($row->freight, 2) : '';
                            }
                            
                            $msrp = '';
                            if(isset($row->msrp) && !empty($row->msrp) && is_numeric($row->msrp)) {
                                $msrp =  $row->msrp ? number_format($row->msrp, 2) : '';
                            }
                            
                            $total_sale = ' ';
                            if(isset($row->total_sale) && !empty($row->total_sale) && is_numeric($row->total_sale)) {
                                $total_sale = $row->total_sale? number_format($row->total_sale, 2) : '';
                            }
                            
                            $est_ship_date = '';
                            if(!is_null($row->est_ship_date)) {
                                $est_ship_date = \Carbon\Carbon::parse($row->est_ship_date)->format($global->date_format);
                            }
                            $act_ship_date = '';
                            if(!is_null($row->act_ship_date)) {
                                $act_ship_date =  \Carbon\Carbon::parse($row->act_ship_date)->format($global->date_format);
                            }
                            $est_receive_date = '';
                            if(!is_null($row->est_receive_date)) {
                                $est_receive_date = \Carbon\Carbon::parse($row->est_receive_date)->format($global->date_format);
                            }
                            
                            $act_receive_date = '';
                            if(!is_null($row->act_receive_date)) {
                                $act_receive_date  = \Carbon\Carbon::parse($row->act_receive_date)->format($global->date_format);
                            }
                            $est_install_date = '';
                            if(!is_null($row->est_ship_date)) {
                                $est_install_date = \Carbon\Carbon::parse($row->est_install_date)->format($global->date_format);
                            }
                            $act_Install_date = '';
                            if(!is_null($row->act_Install_date)) {
                                $act_Install_date  = \Carbon\Carbon::parse($row->act_Install_date)->format($global->date_format);
                            }
                            
                            $po_sent_date = '';
                            if(!is_null($row->po_sent_date)) {
                                $po_sent_date  = \Carbon\Carbon::parse($row->po_sent_date)->format($global->date_format);
                            }
                            $cfa_approved_date = '';
                            if(!is_null($row->cfa_approved_date)) {
                                $cfa_approved_date  = \Carbon\Carbon::parse($row->cfa_approved_date)->format($global->date_format);
                            }
                            
                            $rfq_sent_date = '';
                            if(!is_null($row->rfq_sent_date)) {
                                $rfq_sent_date  = \Carbon\Carbon::parse($row->rfq_sent_date)->format($global->date_format);
                            }
                            
                            $received_date = '';
                            if(!is_null($row->received_date)) {
                                $received_date  = \Carbon\Carbon::parse($row->received_date)->format($global->date_format);
                            }
                            $ordered_date = '';
                            if(!is_null($row->ordered_date)) {
                                $ordered_date  = \Carbon\Carbon::parse($row->ordered_date)->format($global->date_format);
                            }
                            
                            $quote_received = '';
                            if(!is_null($row->quote_received)) {
                                $quote_received  = \Carbon\Carbon::parse($row->quote_received)->format($global->date_format);
                            }
                            
                            $locationCode = ' ';
                            if($row->codes) {
                                foreach ($row->codes as $code) {
                                    if($code->code) {
                                        $locationCode = ucfirst($code->code->location_name);
                                        break;
                                    }
                                }
                            }
                            
                           
                            

                        ?>
			
                            <div class="d-flex border">
                                    <div class="col-auto img">
                                            <img src="{{$image}}" alt="product" width="75" height="75">
                                    </div>
                                    <div class="col-auto name">					
                                            <div><b onblur="saveData(this, '<?php echo $row->id; ?>', 'name');" contenteditable="true">{{$name}}</b> Product Name</div>
                                            <div><b onblur="saveData(this, '<?php echo $row->id; ?>', 'url');" contenteditable="true">{{$url}}</b> Product Url</div>
                                            <div><b onblur="saveData(this, '<?php echo $row->id; ?>', 'spec_number');" contenteditable="true">{{$row->spec_number}}</b> Spec Number</div>
                                    </div>
                                    <div class="col-auto price">					
                                            <div><b id="cost-per-unit-<?php echo $row->id; ?>" onblur="saveData(this, '<?php echo $row->id; ?>', 'cost_per_unit');" contenteditable="true">{{$cost_per_unit}}</b> Per Unit Price</div>
                                            <div><b  onblur="saveData(this, '<?php echo $row->id; ?>', 'quantity');" contenteditable="true">{{$row->quantity}}</b> Quantity</div>
                                            <div><b id="markup-fix-<?php echo $row->id; ?>" onblur="saveData(this, '<?php echo $row->id; ?>', 'markup_fix');" contenteditable="true">{{$markup_fix}}</b> Markup {{$global->currency->currency_symbol}}</div>
                                            <div><b id="markup-per-<?php echo $row->id; ?>" onblur="saveData(this, '<?php echo $row->id; ?>', 'markup_per');" contenteditable="true">{{$markup_per}}</b> Markup %</div>
                                            <div><b id="default-markup_fix-<?php echo $row->id; ?>" onblur="saveData(this, '<?php echo $row->id; ?>', 'spec_number');" contenteditable="false">{{$default_markup_fix}}</b> Markup % Total</div>
                                            <div><b id="freight-fix-<?php echo $row->id; ?>" onblur="saveData(this, '<?php echo $row->id; ?>', 'freight');" contenteditable="true">{{$freight}}</b> Freight</div>
                                            <div><b id="msrp-<?php echo $row->id; ?>" onblur="saveData(this, '<?php echo $row->id; ?>', 'msrp');" contenteditable="true">{{$msrp}}</b> MSRP</div>
                                            <div><b id="total-sale-<?php echo $row->id; ?>" contenteditable="false">{{$total_sale}}</b> Total Sale</div>
                                    </div>

                                    <div class="col-auto date">					
                                            <div><input onblur="saveData(this, '<?php echo $row->id; ?>', 'po_sent_date');" type="text" class="datepicker" value="{{$po_sent_date}}"> PO Sent</div>
                                            <div><input onblur="saveData(this, '<?php echo $row->id; ?>', 'cfa_approved_date');" type="text" class="datepicker" value="{{$cfa_approved_date}}"> CFA Approved</div>
                                            <div><input onblur="saveData(this, '<?php echo $row->id; ?>', 'act_ship_date');" type="text" class="datepicker" value="{{$act_ship_date}}"> Act Ship Date</div>
                                            <div><input onblur="saveData(this, '<?php echo $row->id; ?>', 'rfq_sent_date');" type="text" class="datepicker" value="{{$rfq_sent_date}}"> RFQ Sent</div>
                                            <div><input onblur="saveData(this, '<?php echo $row->id; ?>', 'ordered_date');" type="text" class="datepicker" value="{{$ordered_date}}"> Ordered</div>
                                            <div><input onblur="saveData(this, '<?php echo $row->id; ?>', 'est_receive_date');" type="text" class="datepicker" value="{{$est_receive_date}}"> Est Received Date</div>
                                            <div><input onblur="saveData(this, '<?php echo $row->id; ?>', 'quote_received');" type="text" class="datepicker" value="{{$quote_received}}"> Quote Received</div>
                                            <div><input onblur="saveData(this, '<?php echo $row->id; ?>', 'est_ship_date');" type="text" class="datepicker" value="{{$est_ship_date}}"> Est Ship Date</div>
                                            <div><input onblur="saveData(this, '<?php echo $row->id; ?>', 'est_install_date');" type="text" class="datepicker" value="{{$est_install_date}}"> Est Install Date</div>
                                    </div>
                                    <div class="col-auto received">					
                                            <div><input onblur="saveData(this, '<?php echo $row->id; ?>', 'received_date');" type="text" class="datepicker" value="{{$received_date}}"> Received</div>
                                            <div><b onblur="saveData(this, '<?php echo $row->id; ?>', 'received_by');" contenteditable="true">{{$row->received_by}}</b> Received By</div>
                                            <div><b onblur="saveData(this, '<?php echo $row->id; ?>', 'warehouse');"  contenteditable="true">{{ucfirst($row->warehouse)}}</b> Warehouse</div> 
                                    </div>
                                    <div class="col note">					
                                            <div>Expediting<b onblur="saveData(this, '<?php echo $row->id; ?>', 'expediting');" contenteditable="true" class="notes">{{$row->expediting}}</b></div>
                                    </div>

                                    <div class="col-auto last-action">
                                        <?php

                                            $action = '<div class="btn-group dropdown m-r-10">
                                            <button aria-expanded="false" data-toggle="dropdown" class="btn dropdown-toggle waves-effect waves-light" type="button"><i class="ti-more"></i></button>
                                            <ul role="menu" class="dropdown-menu pull-right">
                                              <li><a href="' . route('admin.products.edit', [$row->id]). '"><i class="fa fa-pencil" aria-hidden="true"></i> ' . trans('app.edit') . '</a></li>
                                                <li><a href="' . route('admin.products.edit', [$row->id, "copy"=>1]). '"><i class="fa fa-copy" aria-hidden="true"></i> ' . trans('app.copy') . '</a></li>
                                              <li><a href="javascript:;"  data-user-id="' . $row->id . '"  class="sa-params"><i class="fa fa-times" aria-hidden="true"></i> ' . trans('app.delete') . '</a></li>
                                              <li><a href="'. route("admin.products.download", $row->id).'"><i class="fa fa-file-pdf-o"></i> ' . trans('app.download') . '</a></li>';

                                            $action .= ' <li><a href="javascript:" data-user-id="' . $row->id . '" class="pa-rfq" data-toggle="modal" data-target="#productRFQModal"><i class="fa fa-send"></i> RFQ </a></li>';


                                            $action .= '</ul> </div>';
                                            echo $action;
                                       ?>
                                        
                                    </div>
                            </div>
                        
                        @endforeach

            <div id="pagination">
              <?php
                if($_GET){
                  ?>
                  {{ $products->appends($_GET) }}
              <?php
                }else{
                  ?>
                  {{ $products->links() }}  
                  <?php
                }
              ?>
            </div>
		    
		</div><!--end of col-12-->
                    
                    
                    
                    
               