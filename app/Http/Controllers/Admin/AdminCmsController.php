<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CmsPage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AdminCmsController extends Controller
{
    public function Technology()
    {
        return view('admin.technology.list');
    }

    public function privacyPolicy()
    {
        $data['row'] = CmsPage::where('page_name', 'Privacy Policy')->first();

        return view('admin.cmspages.cms_page', $data);
    }

    public function termsAndCondition()
    {
        $data['row'] = CmsPage::where('page_name', 'Terms & Condition')->first();

        return view('admin.cmspages.cms_page', $data);
    }

    public function coloPhone()
    {
        $data['row'] = CmsPage::where('page_name', 'Colophone')->first();

        return view('admin.cmspages.cms_page', $data);
    }

    public function cookieStatement()
    {
        $data['row'] = CmsPage::where('page_name', 'Cookie Statement')->first();

        return view('admin.cmspages.cms_page', $data);
    }

    public function alcoholLaw()
    {
        $data['row'] = CmsPage::where('page_name', 'Guaranteed Working Method Alcohol Law')->first();

        return view('admin.cmspages.cms_page', $data);
    }

    public function saveCms(Request $request)
    {
        $rules = [
            'description' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()
                ->json([
                    'status' => false,
                    'type'   => 'validation',
                    'errors' => $validator->errors(),
                ]);
        } else {
            /** @var CmsPage $cms */
            $cms = CmsPage::findOrFail($request->id);
            $cmsid = $request->id;
            $message = 'Description Updated Succesfully';
            if ($request->languange == 0) {
                $cms->page_content_eng = $request->description;
            } else {
                $cms->page_content_dutch = $request->description;
            }

            $cms->save();

            return response()
                ->json([
                    'status'  => true,
                    // 'page'    => 'admin/cms/'.$cmsid,
                    'message' => $message,
                ]);
        }
    }

    public function getCmsDetail(Request $request)
    {
        $id = $request->id;
        $language = $request->language;
        $cms = CmsPage::find($id);
        $data['content'] = $language == 0 ? $cms['page_content_eng'] : $cms['page_content_dutch'];

        return response()
            ->json([
                'status'  => true,
                'content' => $data['content'],
            ]);
    }
}
