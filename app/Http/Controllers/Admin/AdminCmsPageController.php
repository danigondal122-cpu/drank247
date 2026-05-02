<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CmsPage;

class AdminCmsPageController extends Controller
{
    public function privacyPolicy($id)
    {

        $row = CmsPage::where('page_name', 'Privacy Policy')->first();
        $data['content'] = $id == '1' ? $row['page_content_dutch'] : $row['page_content_eng'];

        return view('cms_page', $data);
    }

    public function termsAndCondition($id)
    {

        $row = CmsPage::where('page_name', 'Terms & Condition')->first();
        $data['content'] = $id == '1' ? $row['page_content_dutch'] : $row['page_content_eng'];

        return view('cms_page', $data);
    }

    public function coloPhone($id)
    {

        $row = CmsPage::where('page_name', 'Colophone')->first();
        $data['content'] = $id == '1' ? $row['page_content_dutch'] : $row['page_content_eng'];

        return view('cms_page', $data);
    }

    public function cookieStatement($id)
    {

        $row = CmsPage::where('page_name', 'Cookie Statement')->first();
        $data['content'] = $id == '1' ? $row['page_content_dutch'] : $row['page_content_eng'];

        return view('cms_page', $data);
    }

    public function alcoholLaw($id)
    {

        $row = CmsPage::where('page_name', 'Guaranteed Working Method Alcohol Law')->first();
        $data['content'] = $id == '1' ? $row['page_content_dutch'] : $row['page_content_eng'];

        return view('cms_page', $data);
    }
}
