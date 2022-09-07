<!doctype html>
<html class="no-js" lang="">

<head>
    <meta charset="utf-8">
    <title></title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">


    <link rel="stylesheet" type="text/css" href="/css/parsing/main.css">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@300&display=swap" rel="stylesheet">

    <!-- Place favicon.ico in the root directory -->

    <meta name="theme-color" content="#fafafa">

</head>

<body>
<button class="btn btn-default btn-xs btn-circle btn-outline filter-section-close"><i class="fa fa-chevron-left"></i></button>
<button class="btn btn-default btn-xs btn-outline btn-circle m-t-5 filter-section-show"><i class="fa fa-chevron-right"></i></button>
<div class="bookmarklet-form indemia_loader_block" id="indemia_loader_block" ><div class="indemia_loader" id="indemia_loader_block_l"></div></div>


<form class="indemaCont" id="new_item"  style="display: none">
    <div class="logoImage"> <img src="/img/indema_lowercase.png"></div>
    <h5 class="productTitle">Product Image :</h5>
    <div class="productImage preview">
        <div class="pickerAndInputDisplay">
            <img src="" id="bookmarklet-image-preview" alt="">
            <input type="hidden" id="bookmarklet-image-input" value="" name="image"/>
            <div><img class ="pickerImage" @click="selectImage()" src="/img/picker.svg"/></div>
        </div>

    </div>
    <div class="productsmallImages">
        <div class="pickerAndInputDisplay">
            <img src="" id="bookmarklet-image-preview1" alt="">
            <input type="hidden" id="bookmarklet-image-input1" value="" name="image1"/>
            <div><img class ="pickerImage" @click="selectImage1()" src="/img/picker.svg"/></div>
        </div>
        <div class="pickerAndInputDisplay">
            <img src="" id="bookmarklet-image-preview2" alt="">
            <input type="hidden" id="bookmarklet-image-input2" value="" name="image2"/>
            <div><img class ="pickerImage" @click="selectImage2()" src="/img/picker.svg"/></div>
        </div>

    </div>
    <div class="inputAndLabel">
        <label class="labelStyles"> Product Name :</label>
        <div class="pickerAndInputDisplay">
            <input class="inputsStyiles" type="text" name="name" id="item_name"/>
            <div><img class ="pickerImage" @click="selectTitle()" src="/img/picker.svg"/></div>
        </div>
    </div>
    <div class="inputAndLabel">
        <label class="labelStyles"> Vendor :</label>
        <div class="pickerAndInputDisplay">
            <input class="inputsStyiles" type="text" name="vendor" id="item_vendor"/>
            <div></div>
        </div>
    </div>
    <div class="inputAndLabel">
        <label class="labelStyles"> Project :</label>
        <div class="pickerAndInputDisplay">
             <select name="project" id="item_project" class="inputsStyiles" >
                <option value="">Select Project</option>
                @foreach ($projects as $project)
                    <option value="{{$project->id}}"  >{{ucfirst($project->project_name)}}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="inputAndLabel">
        <label class="labelStyles"> Sales Category :</label>
        <div class="pickerAndInputDisplay">
             <select name="category" id="item_category" class="inputsStyiles" >
                <option value="">Select Category</option>
                @forelse($salescategories as $salescategory)
                    <option value="{{ $salescategory->salescategory_code }}" >{{ucfirst($salescategory->salescategory_name)}}</option>
                @empty
                    <option value="">No Category Added</option>
                @endforelse
            </select>
            
        </div>
    </div>
    <div class="inputAndLabel">
        <label class="labelStyles"> Location CODE :</label>
        <div class="pickerAndInputDisplay">
             <select name="location" id="item_location" class="inputsStyiles" >
                <option value="">Select location CODE</option>
                @forelse($codetypes as $codetype)
                <option value="{{ $codetype->id }}" >{{ucfirst($codetype->location_name)}}</option>
                @empty
                <option value="">No Location Added</option>
                @endforelse
            </select>
        </div>
    </div>
    
    <div class="inputAndLabel">
        <label class="labelStyles"> Product Number :</label>
        <div class="pickerAndInputDisplay">
            <input class="inputsStyiles" type="text" name="product_number" id="item_sku"/>
            <div><img class ="pickerImage" @click="selectSku()" src="/img/picker.svg"/></div>
        </div>
    </div>
    <div class="inputAndLabel">
        <label class="labelStyles"> URL :</label>
        <div class="pickerAndInputDisplay">
            <input class="inputsStyiles" type="text" name="url" id="item_url"/>
            <div><img class ="pickerImage" @click="selectUrl()" src="/img/picker.svg"/></div>
        </div>
    </div>
    <div class="displayInputAndLabel">
        <div class="priceMarkupGrid">
            <label class="labelStyles"> Cost Per Unit :</label>
            <div class="pickerAndInputDisplay">
                <input class="inputsStyiles uPriceinputsStyiles" type="number" name="cost_per_unit" id="item_unit_price" v-model="priceValue"/>
                <div><img class ="pickerImage" @click="getPrice()" src="/img/picker.svg"/></div>
            </div>
        </div>
        <div class="priceMarkupGrid">
            <label class="labelStyles"> Default Markup % :</label>
            <input class=" inputsStyiles markupStyiles" type="number" value="0.0" name="default_markup" v-model="markupValue" id="item_markup"/>
        </div>
        <div class="priceMarkupGrid">
            <label class="labelStyles"> Total $ :</label>
            <input class="inputsStyiles cPriceStyiles" type="number" name="msrp" id="item_price" :value="markupData"/>
        </div>
    </div>
    <div class="inputAndLabel">
        <label class="labelStyles"> QTY :</label>
        <div class="pickerAndInputDisplay">
            <input class="inputsStyiles" type="text" name="quantity" id="item_quantity"/>
        </div>
    </div>
    <div class="inputAndLabel">
        <label class="labelStyles">Client-Facing Description :</label>
        <div class="pickerAndInputDisplay">
            <textarea class="inputsStyiles testAreaStyle" name="client_description" id="item_client_description" ></textarea>
            <div><img class ="pickerImage" @click="selectClientDescription()" src="/img/picker.svg"/></div>
        </div>
    </div>
    <div class="inputAndLabel">
        <label class="labelStyles">Vendor Description :</label>
        <div class="pickerAndInputDisplay">
            <textarea class="inputsStyiles testAreaStyle" name="vendor_description" id="item_description" ></textarea>
            <div><img class ="pickerImage" @click="selectDescription()" src="/img/picker.svg"/></div>
        </div>
    </div>
    <div class="inputAndLabel">
        <label class="labelStyles"> Manufacturer:</label>
        <div class="pickerAndInputDisplay">
            <input class="inputsStyiles" type="text" name="manufacturer" id="item_manufacturer"/>
            <div><img class ="pickerImage" @click="selectManufacturer()" src="/img/picker.svg"/></div>
        </div>
    </div>
    <div class="inputAndLabel">
        <label class="labelStyles"> Material:</label>
        <div class="pickerAndInputDisplay">
            <input class="inputsStyiles" type="text" name="materials" id="item_material"/>
            <div><img class ="pickerImage" @click="selectMaterial()" src="/img/picker.svg"/></div>
        </div>
    </div>
    <div class="inputAndLabel">
        <label class="labelStyles">Dimension :</label>
        <div class="pickerAndInputDisplay">
            <input class="inputsStyiles" type="text" name="dimensions" id="item_dimensions"/>
            <div><img class ="pickerImage" @click="selectDimensions()" src="/img/picker.svg"/></div>
        </div>
    </div>
    <div class="inputAndLabel">
        <label class="labelStyles">Finish/Color :</label>
        <div class="pickerAndInputDisplay">
            <input class="inputsStyiles" type="text" name="finish_color" id="item_finish"/>
            <div><img class ="pickerImage" @click="selectFinish()" src="/img/picker.svg"/></div>
        </div>
    </div>
    <div class="saveAndCancel">
        <button class="saveStyles" @click="saveForm($event)">Save</button>
        <button class="canselStyles" @click="closeModal">Cancel</button>
    </div>
</form>
<div id="success-message" style="display: none" class="indemaCont">
    Your product  successfully saved
</div>
<script>
    window.uuid = '{{$uuid}}';
</script>
<script src="https://code.jquery.com/jquery-3.4.1.min.js" type="text/javascript"></script>
<script src="/js/app_vue.js?v=123444"></script>

</body>

</html>
