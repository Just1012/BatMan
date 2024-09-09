<?php

namespace App\Http\Controllers;

use App\Http\Requests\SettingRequest;
use App\Models\Setting;
use App\Models\Term;
use Illuminate\Http\Request;
use App\Services\SettingService;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Validator;

class SettingController extends Controller
{
    protected $SettingService;

    public function __construct(SettingService $SettingService)
    {
        $this->SettingService = $SettingService;
        $this->middleware(['auth','admin']);
    }

    public function index()
    {
        return view('dashboard.setting.index');
    }

    public function datatable()
    {
        $data = Setting::all();
        return response()->json([
            'data' => $data,
            'message' => 'found data'
        ]);
    }

    public function create()
    {
        return view('dashboard.setting.create', ['type_page'=>'create']);
    }
    public function edit(Setting $setting)
    {
        return view('dashboard.setting.create', ['type_page'=>'','data'=>$setting]);
    }

    public function store(Request $SettingRequest)
    {
        $result = $this->SettingService->storeSetting($SettingRequest);
        return redirect()->route('setting.index') ;
    }

    
    public function editTerms()
    {
        $terms = Term::first();
        return view('dashboard.setting.terms', compact('terms'));
    }

    public function updateTerms(Request $request)
    {
        try {
            // Define validation rules
            $rules = [
                'terms_ar' => 'required|string',
                'terms_en' => 'required|string',
            ];

            // Validate the request data
            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            // Update the data
            $terms = Term::first(); // Assuming you have only one row
            if (!$terms) {
                $terms = new Term(); // Create a new instance if no row exists
            }

            $terms->terms_ar = $request->terms_ar;
            $terms->terms_en = $request->terms_en;
            $terms->save();

            toastr()->success(__('Privacy Policy Updated Successfully'), __('Success'));
            return redirect()->back();
        } catch (\Exception $e) {
            toastr()->error(__('An error occurred. Try Again'), __('Error'));
            return redirect()->back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }
}
