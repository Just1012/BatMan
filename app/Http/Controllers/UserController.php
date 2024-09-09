<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateUserRequest;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use App\Services\UserService;
use App\Http\Requests\UserRequest;
use App\Models\Category;
use Brian2694\Toastr\Facades\Toastr;

class UserController extends Controller
{
    protected $UserService;

    public function __construct(UserService $UserService)
    {
        $this->UserService = $UserService;
        $this->middleware(['auth', 'admin']);
    }

    public function index(Role $role)
    {
        if ($role) {
            return view('dashboard.user.index', ['role' => $role]);
        }
    }

    public function datatable($id)
    {
        $data = User::where('role_id', '=', $id)->get();
        return response()->json([
            'data' => $data,
            'message' => 'found data'
        ]);
    }

    public function create()
    {
        $role = Role::all();
        $category = Category::all();
        return view('dashboard.user.create', ['type_page' => 'create', 'role' => $role, 'category' => $category]);
    }
    public function edit(User $user)
    {
        $user = User::find($user->id);
        if($user->id != 1){
            $role = Role::all();
            $category = Category::all();
            $engineerCategory = $user->engineerCategory;
            return view('dashboard.user.create', ['type_page' => '', 'data' => $user, 'role' => $role, 'category' => $category, 'engineerCategory' => $engineerCategory]);
        }else{
            Toastr::info('لا يمكن تعديل هذا المستخدم', ' خطأ');
            return redirect()->back();
        }
    }

    public function store(UserRequest $UserRequest)
    {
        $result = $this->UserService->storeUser($UserRequest);
        return redirect()->route('user.index', ['role' => $result['1']]);
    }
    public function update(UpdateUserRequest $updateUserRequest)
    {
        $result = $this->UserService->storeUser($updateUserRequest);
        return redirect()->route('user.index', ['role' => $result['1']]);
    }

    public function delete(User $user)
    {
        try {
            $user = User::find($user->id);
            if ($user->id != 1) {
                $user->delete();
                toastr()->success('تم حذف المستخدم', 'تم بنجاح');
                return redirect()->back();
            }
            Toastr::info('لا يمكن حذف المستخدم', ' خطأ');
            return redirect()->back();
        } catch (\Throwable $th) {
            toastr()->error('أعد المحاولة', 'خطاء');
            return redirect()->back();
        }
    }
}
