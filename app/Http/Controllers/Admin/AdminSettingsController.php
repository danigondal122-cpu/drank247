<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Laravel\Facades\Image;

class AdminSettingsController extends Controller
{
    public function web_banner()
    {
        $data['row'] = Banner::orderBy('id', 'asc')->get();

        return view('admin.settings.banner', $data);
    }

    public function settings()
    {
        $data['row'] = Setting::findOrFail(1);

        return view('admin.settings.create', $data);
    }

    public function update(Request $request)
    {
        $rules = [
            'email'      => 'required',
            'address'    => 'required',
            'contact_no' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);
        $settings = Setting::find('1');

        if ($validator->fails()) {
            return response()
                ->json([
                    'status' => false,
                    'type'   => 'validation',
                    'errors' => $validator->errors(),
                ]);
        } else {

            $settings->email = $request->email;
            $settings->address = $request->address;
            $settings->contact_no = $request->contact_no;
            $settings->email_show = ($request->input('email_show') == 'on') ? '1' : '0';
            $settings->save();

            return response()
                ->json([
                    'status' => true,
                    'page'   => 'admin/settings',
                    'msg'    => 'succesvol bijgewerkt',
                ]);
        }
    }

    public function updateBanner(Request $request)
    {
        if ($request->hasFile('banner')) {
            foreach ($request->file('banner') as $key => $image) {
                $imagename = time().'_'.$key.$image->getClientOriginalName();
                $img = Image::read($image->path());
                $img->resize(100, 100, function ($constraint) {
                    $constraint->aspectRatio();
                })->save(public_path('uploads/banner/thumb').'/'.$imagename);
                $image->move(public_path('uploads/banner/'), $imagename);
                $banner = new Banner;
                $banner->image = $imagename;
                $banner->save();
            }
        }

        return response()
            ->json([
                'status' => true,
                'msg'    => 'Banner Updated !',
                'page'   => 'admin/settings/banner',
            ]);
    }

    public function deleteBanner(Request $request)
    {
        $rules = [
            'id' => 'required',
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

            Banner::where('id', $request->id)->delete();

            return response()
                ->json([
                    'status' => true,
                    'msg'    => 'Banner deleted !',
                ]);
        }
    }
}
