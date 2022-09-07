                
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
                     
                    
	 <div id="paginationsAll">
        {{ $products->links() }}
    </div>      
	
