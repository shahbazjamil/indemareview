<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Company;
use App\Faq;
use App\FaqCategory;
use App\Helper\Reply;

use App\Http\Requests\SuperAdmin\Faq\StoreRequest;
use App\Http\Requests\SuperAdmin\Faq\UpdateRequest;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\Request;
use App\Helper\Files;
use Intervention\Image\ImageManagerStatic as Image;


class SuperAdminFaqController extends SuperAdminBaseController
{
    /**
     * AdminProductController constructor.
     */
    public function __construct() {
        parent::__construct();
        $this->pageTitle = 'app.menu.faq';
        $this->pageIcon = 'icon-basket';
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($faqCategoryId)
    {
        $this->faqCategoryId = $faqCategoryId;
        $this->faq = new Faq();

        return view('super-admin.faq-category.add-edit-faq', $this->data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreRequest $request, $faqCategoryId)
    {
        
        $message = $request->description;
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $dom->loadHtml($message, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        $images = $dom->getElementsByTagName('img');
        // foreach <img> in the submited message
        foreach ($images as $img) {
            $src = $img->getAttribute('src');

            // if the img source is 'data-url'
            if (preg_match('/data:image/', $src)) {

                // get the mimetype
                preg_match('/data:image\/(?<mime>.*?)\;/', $src, $groups);
                $mimetype = $groups['mime'];

                // Generating a random filename
                $filename = uniqid();
                $filepath = "/user-uploads/faqs/$filename.$mimetype";

                // @see http://image.intervention.io/api/
                $image = Image::make($src)
                        // resize if required
                        /* ->resize(300, 200) */
                        ->encode($mimetype, 100)  // encode file to the specified mimetype
                        ->save(public_path($filepath));

                $new_src = asset($filepath);
                $img->removeAttribute('src');
                $img->setAttribute('src', $new_src);
            } // <!--endif
        } // <!--endforeach
        // End
        
        $faq = new Faq();
        $faq->title = $request->title;
        //$faq->description = $request->description;
        $faq->description = $dom->saveHTML();
        $faq->faq_category_id = $request->faq_category_id;
        $faq->save();

        return Reply::success( 'messages.createSuccess');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($faqCategoryId, $id)
    {
        $this->faqCategoryId = $faqCategoryId;
        $this->faq = Faq::find($id);

        return view('super-admin.faq-category.add-edit-faq', $this->data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateRequest $request, $faqCategoryId, $id)
    {
        $faq = Faq::find($id);
        
        // added by SB
        $message = $request->description;
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $dom->loadHtml($message, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        $images = $dom->getElementsByTagName('img');
        // foreach <img> in the submited message
        foreach ($images as $img) {
            $src = $img->getAttribute('src');

            // if the img source is 'data-url'
            if (preg_match('/data:image/', $src)) {

                // get the mimetype
                preg_match('/data:image\/(?<mime>.*?)\;/', $src, $groups);
                $mimetype = $groups['mime'];

                // Generating a random filename
                $filename = uniqid();
                $filepath = "/user-uploads/faqs/$filename.$mimetype";

                // @see http://image.intervention.io/api/
                $image = Image::make($src)
                        // resize if required
                        /* ->resize(300, 200) */
                        ->encode($mimetype, 100)  // encode file to the specified mimetype
                        ->save(public_path($filepath));

                $new_src = asset($filepath);
                $img->removeAttribute('src');
                $img->setAttribute('src', $new_src);
            } // <!--endif
        } // <!--endforeach
        // End

        $faq->title = $request->title;
        //$faq->description = $request->description;
        $faq->description = $dom->saveHTML();
        $faq->faq_category_id = $request->faq_category_id;
        $faq->save();

        return Reply::success('messages.updateSuccess');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($faqCategoryId, $id)
    {
        Faq::destroy($id);

        return Reply::success('messages.deleteSuccess');
    }
}
