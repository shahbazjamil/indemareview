<?php

    $picture = 'img/default-product.png';
    $pictureArr = [];
    if (!empty($product->picture)) {
        $pictures = json_decode($product->picture);
        if (isset($pictures[0])) {
            $picture = 'user-uploads/products/' . $product->id . '/' . $pictures[0];
            
            $counter = 0;
            foreach ($pictures as $pic) {
            //if ($counter++ == 0) continue;
               $pictureArr[] =  'user-uploads/products/' . $product->id . '/' . $pic;
            }
        }
    }
    $codes = $product->codes;                                          
    $locations = '';
    if($codes) {
        foreach ($codes as $code) {
            if($locations == '') {
                $locations = $code->code->location_name;
            } else {
                $locations .= ', '.$code->code->location_name;
            }
        }
    }
    
?>
    <div class="modal-content">
        <div class="modal-body">
            <div class="row">
                <div class="col-md-7">
                    <div class="slide-img"><img id="main-img"  class="img-responsive modal-img-src" src="{{ asset($picture) }}" /></div>
                    <div class="slide-thumbs">
                        <?php if($pictureArr) { foreach ($pictureArr as $p) { ?>
                        <a class="child-link" href="javascript:void(0)"><img  class="img-responsive modal-img-src child-img" src="{{ asset($p) }}" /></a>
                        <?php }} ?>
                    </div><!--end of slide-thumbs-->
                </div><!--end of col-7-->
                <div class="col-md-5">
                    <h2>Details</h2>
                    <p>
                        @if($project->show_product_name == 1)
                            <span><b>Product Name:</b> {{ $product->name }}</span><br>
                        @endif
                        @if($project->show_description == 1)
                            <span><b>Client-Facing Description :</b> {{ $product->itemObj->description ?? "" }}</span>
                        @endif
                    </p>
                    <p>
                        @if($project->show_material == 1)
                            <span><b>Material:</b> {{ $product->materials }}</span> <br>
                        @endif
                        @if($project->show_finish_color == 1)
                            <span><b>Finish/Color:</b> {{ $product->finish_color }}</span> <br>
                        @endif
                        @if($project->show_dimensions == 1)
                            <span><b>Dimensions:</b> {{ $product->dimensions }}</span> <br>
                        @endif
                        @if($project->show_location == 1)
                            <span><b>Location:</b> {{ $locations }}</span> <br>
                        @endif
                        @if($project->show_url == 1)
                            <span><b>URL:</b> @if(!is_null($product->url)) <a href="{{ $product->url }}" target="_blank">{{ $product->url }}</a> @endif </span>
                        @endif                    
                        
                    </p>
                    @if($project->show_sale_price == 1)
                        @if(!is_null($product->total_sale) && $product->total_sale !=0)
                        <p><b>Customer Price:</b> ${{ $product->total_sale }}</p> 
                        @else
                        <p><b>Customer Price:</b> ${{ $product->msrp }}</p> 
                        @endif
                        
                    @endif     
                    
                </div><!--end of col-5-->
            </div><!--end of row-->

        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
    </div>

<script>
    $('body').on('click', '.child-img', function () {
        var img_src = $(this).attr("src"); 
        $('#main-img').attr('src',img_src);
    });
</script>

