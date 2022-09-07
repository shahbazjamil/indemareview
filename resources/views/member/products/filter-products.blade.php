<table class="table">
    <thead>
        <tr>
            <th>Select</th>
            <th>@lang('app.product.tabs.picture')</th>
            <th>@lang('modules.invoices.item')</th>
            <th>Project Name</th>
            <th>Vendor</th>
        </tr>
    </thead>
    <tbody>
        @forelse($products as $item)
        <?php
        $project_name = '';
        foreach ($item->projects as $project) {
            if($project->project) {
                if ($project_name == '') {
                    $project_name .= ucfirst($project->project->project_name);
                } else {
                    $project_name .= ', ' . ucfirst($project->project->project_name);
                }
            }
        }
        $vendor_name = '';
        if (!is_null($item->vendor_id)) {
            if ($item->vendor) {
                $vendor_name = $item->vendor->company_name;
            }
        }
        ?>
        <tr>
            <td width="5%" class="al-center bt-border">
                <input type="checkbox" value="{{$item->id}}" name="select_product" id="select_product" class="form-control">
            </td>

            <td width="10%" class="al-center bt-border">

                <?php
                
                $picture1 = '';
                if (!empty($item->picture)) {
                    $pictures = json_decode($item->picture);
                    if(isset($pictures[0])) {
                        $picture1 = $pictures[0];
                    }
                }
                
                if (!empty($picture1)) {
                    $pictures = json_decode($item->picture);
                    ?>
                    <p class="form-control-static"><img src="{{ asset('user-uploads/products/'.$item->id.'/'.$picture1.'') }}" alt="product" width="100" height="100"></p>

                <?php } else { ?>
                    <p class="form-control-static"><img src="{{ asset('img/img-dummy.jpg') }}" alt="product" width="100" height="100"></p>
                <?php }
                ?>

            </td>

            <td width="25%" class="al-center bt-border">
                {{ $item->name }}
            </td>
            <td width="35%" class="al-center bt-border">
                {{ $project_name }}
            </td>
            <td width="25%" class="al-center bt-border">
                {{ $vendor_name }}
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="5">No Products</td>
        </tr>
        @endforelse
    </tbody>
</table>