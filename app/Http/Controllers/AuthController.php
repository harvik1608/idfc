<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use App\Mail\WelcomeMail;
use App\Models\User;
use Auth;

class AuthController extends Controller
{
    public function index()
    {
        return view('login');
    }

    public function checkLogin(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
        if(Auth::attempt($request->only('email', 'password'))) {
            $request->session()->regenerate();
            return redirect()->intended('/dashboard');
        }
        return back()->withErrors([
            'error' => 'Invalid email or password.',
        ]);
    }

    public function forgetPassword()
    {
        return view('forget_password');
    }

    public function profile()
    {
        $user = User::find(Auth::user()->id);
        return view('profile',compact('user'));
    }

    public function submitProfile(Request $request)
    {
        try {
            $post = $request->all();

            $user = User::where('email',$post["email"])->where('id','!=',Auth::user()->id)->count();
            if($user > 0) {
                return response()->json(['success' => false,'message' => "Email is already used."], 200);
            }

            $phone = User::where('phone',$post["phone"])->where('id','!=',Auth::user()->id)->count();
            if($phone > 0) {
                return response()->json(['success' => false,'message' => "Mobile No. is already used."], 200);
            }

            $row = User::find(Auth::user()->id);
            $row->name = trim($post['name']);
            $row->email = trim($post['email']);
            $row->phone = trim($post['phone']);
            $row->updated_at = date("Y-m-d H:i:s");
            $row->save();

            return response()->json(['success' => true,'message' => "Profile updated successfully."], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false,'message' => $e->getMessage()], 200);
        }
    }

    public function changePassword()
    {
        $user = User::find(Auth::user()->id);
        return view('change_password',compact('user'));
    }

    public function submitChangePassword(Request $request)
    {
        $user = auth()->user();

        if(!Hash::check($request->old_password, $user->password)) {
            return response()->json(['success' => false,'message' => "Old password is incorrect."], 200);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return response()->json(['success' => true,'message' => "Password updated successfully."], 200);        
    }

    public function resetPassword(Request $request)
    {
        $token = $request->query('token');
        
        $user = User::select("id")->where("link",$token)->first();
        if($user) {
            return view('reset_password',compact('token'));
        } else {
            return redirect('/')->withErrors('error', 'Token is expired or invalid.');
        }
    }

    public function submitResetPassword(Request $request)
    {
        $post = $request->all();
        
        $user = User::where('link', $request->token)->first();
        if (!$user) {
            return back()->withErrors(['token' => 'Invalid or expired token.']);
        }

        $user->password = Hash::make($request->password);
        $user->link = null; // optional: clear token
        $user->save();

        return redirect()->route('login')->withErrors([
            'success' => 'Password updated successfully.',
        ]);
    }

    public function submitForgetPassword(Request $request)
    {
        $row = User::where("email",$request->email)->first();
        if($row) {
            $token = md5(time());
            
            $user = User::find($row->id);
            $user->link = $token;
            $user->save();

            Mail::to($request->email)->send(
                new WelcomeMail(
                    subjectText: "Reset Your Password",
                    viewFile: 'email_templates.forget_password',
                    data: ['name' => $row->name,"link" => route('reset.password',["token" => $token])]
                )
            );

            return redirect()->route('login')->withErrors([
                'success' => 'Link has been sent to your email.',
            ]);
        } else {
            return back()->withErrors([
                'error' => 'Email not found',
            ]);
        }
    }

    public function logout(Request $request)
    {
        Auth::logout(); // Log out the user

        // Invalidate the session
        $request->session()->invalidate();

        // Regenerate the CSRF token to prevent fixation
        $request->session()->regenerateToken();

        // Redirect to admin login page (or wherever you prefer)
        return redirect('/')->with('success', 'You have been logged out successfully.');
    }
}
