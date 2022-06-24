<?php

namespace App\Http\Controllers;

use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Twilio\Rest\Client;

class UserController extends Controller
{
    public function index()
    {
        $title = 'All Users';
        $users = User::where('role', '!=', 'admin')->latest()->paginate(10);
        return view('users.index',compact('title','users'));
    }

    public function create()
    {
        $title = 'Create New User';
        return view('users.create',compact('title'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|between:2,35',
            'phone' => 'required|unique:users',
            'password' => 'required|string|confirmed|min:6',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = $request->except(['password']);
        $data['password'] = bcrypt($request->password);

        $pin_code = random_int(1111, 9999);
        $data['pin_code'] = $pin_code;

        // for send sms pin code
        $receiverNumber = $request->phone;
        $message = "Verification code is : ( " . $pin_code . " )";

        try {

            $account_sid = getenv("TWILIO_SID");
            $auth_token = getenv("TWILIO_TOKEN");
            $twilio_number = getenv("TWILIO_FROM");

            $client = new Client($account_sid, $auth_token);
            $client->messages->create($receiverNumber,
                [
                    'from' => $twilio_number,
                    'body' => $message
                ]);

        } catch (Exception $e) {
//            return redirect()->back()->withErrors($e->getMessage())->withInput();
            return redirect()->back()->withErrors('There was an error in your Phone Number Please try again later')->withInput();
        }

        $user = User::create($data);

        return redirect()->route('users.index');
    }

    public function show(User $user)
    {
        if ($user->id === auth()->user()->id OR auth()->user()->role === 'admin') {
            $title = 'User Details';
            return view('users.show', compact('title', 'user'));
        }
        return abort(404);
    }

    public function edit(User $user)
    {
        if ($user->id === auth()->user()->id OR auth()->user()->role === 'admin') {
            $title = 'Edit User';
            $user = User::findOrFail($user->id);
            return view('users.edit',compact('title','user'));
        }
        return abort(404);
    }

    public function update(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|between:2,35',
            'phone' => 'required|unique:users,phone,'. $user->id,
            'password' => 'sometimes|nullable|string|confirmed|min:6',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = $request->except(['password']);
        if ($request->password != null){
            $data['password'] = bcrypt($request->password);
        }

        $user->update($data);

        return redirect()->route('users.index');
    }

    public function destroy(User $user)
    {
        $user->delete();
        session()->flash('status', 'User Deleted Successfully');
        return redirect()->back();
    }

    public function showVerifyUser ()
    {
        return view('verify_user');
    }

    public function doVerify(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'pin_code' => 'required',
            'phone' => 'required'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $user = User::where('pin_code', $request->pin_code)
            ->where('pin_code', '!=', 0)
            ->where('phone', $request->phone)
            ->first();

        if ($user) {
            $user->pin_code = null;
            $user->verified = 'yes';
            if ($user->save()) {
                return redirect()->route('login');
            } else {
                return redirect()->back()->withErrors('Error while verifying user, please try again later.')->withInput();
            }
        } else {
            return redirect()->back()->withErrors('Error your pin code is not true.')->withInput();
        }
    }
}
