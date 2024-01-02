<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Recordings;
use App\Http\Traits\CommonMethods;

use Illuminate\Support\Facades\Storage;
use App\File;
use Illuminate\Support\Facades\URL;
use Carbon\Carbon;

class UserController extends Controller
{
    use CommonMethods;

    public function show(Request $request, $userId)
    {
        $user = $request->user();
        $user = Auth::user();

        if($user && $userId == $user->id) {

            if( $user->profile_pic )
                $user->profile_pic  = url($user->folder.$user->profile_pic);

            $return = [
                "code"  =>  $this->errorCodes("success"),
                'message' => 'Success!',
                'data'  =>  $user
            ];
            return response()->json($return);
        }

        $return = [
            "code"  =>  $this->errorCodes("failed"),
            'message' => 'User not found!'
        ];

        return response()->json($return, $this->errorCodes("failed"));
    }

    public function changePassword(Request $request)
    {
        $user = auth()->user();

        $return = [ 'code'   =>  $this->errorCodes("failed") ];

        if(!$user){
            $return['message']  =   "User not found!";
            return response()->json( $return , $this->errorCodes("failed") );
        }

        $rules = [
            'old_password' => 'required|min:6',
            'password' => 'required|min:6'
        ];

        $validation = \Validator::make($request->all(), $rules);
        
        if ($validation->fails()) {
            $return['message']  =   $validation->messages()->all()[0];
            return response()->json( $return , $this->errorCodes("failed") );
        }

        if ((Hash::check($request->old_password, Auth::user()->password)) == false) {
            $return['message']  =   "Check your old password!";
            return response()->json( $return , $this->errorCodes("failed") );
        } else if ((Hash::check(request('password'), Auth::user()->password)) == true) {
            $return['message']  =   "Please enter a password which is not similar then current password.";
            return response()->json( $return , $this->errorCodes("failed") );
        }

        $user->password = bcrypt($request->password);
        $user->save();

        $return = [
            'message' => 'Success',
            "code"  =>  $this->errorCodes("success")
        ];

        return response()->json($return, 200);
    }

    public function editProfile (   Request $request    )
    {
        $user = auth()->user();

        $return = [ 'code'   =>  $this->errorCodes("failed") ];

        if(!$user){
            $return['message']  =   "User not found!";
            return response()->json( $return , $this->errorCodes("failed") );
        }

        if( isset(   $request->first_name    ) &&    !empty(  $request->first_name    ) ){
            $user->first_name = $request->first_name;
            $user->save();
        }

        if($request->hasFile('profile_pic'))
        {
            $rules = [
                'profile_pic' => 'required|image|mimes:jpg,png,jpeg,gif,svg|max:2048'
            ];
    
            $validation = \Validator::make($request->all(), $rules);
            
            if ($validation->fails()) {
                $return['message']  =   $validation->messages()->all()[0];
                return response()->json( $return , $this->errorCodes("failed") );
            }

            // required|image|mimes:jpg,png,jpeg,gif,svg|max:2048
            $uniqueid=uniqid();
            $original_name=$request->file('profile_pic')->getClientOriginalName();
            $size=$request->file('profile_pic')->getSize();
            $extension=$request->file('profile_pic')->getClientOriginalExtension();
            $filename=Carbon::now()->format('Ymd').'_'.$uniqueid.'.'.$extension;
            // $filepath=url(SELF::$AUDEIO_STORAGE.$filename);
            $path=$request->file('profile_pic')->storeAs('public/uploads/profile_pic/',$filename);
            // $all_files=$filepath;
            if( $user->profile_pic ){
                Storage::delete('public/uploads/profile_pic/'.$user->profile_pic);
            }

            $user->profile_pic = $filename;
            $user->folder = SELF::$PROFILE_PIC;
            $user->save();
        }

        if( $user->profile_pic )
            $user->profile_pic  = url($user->folder.$user->profile_pic);

        $return = [
            'message' => 'Success',
            "code"  =>  $this->errorCodes("success"),
            "user"  =>  $user
        ];

        return response()->json($return, 200);
    }

    public function deleteAccoutPermanentaly ()
    {
        $user = auth()->user();

        $return = [ 'code'   =>  $this->errorCodes("failed") ];

        if(!$user){
            $return['message']  =   "User not found!";
            return response()->json( $return , $this->errorCodes("failed") );
        }
        //remove profile pic from server.
        if( $user->profile_pic ){
            Storage::delete('public/uploads/profile_pic/'.$user->profile_pic);
        }

        //Remove recordings from the server
        $where = [  'user_id'   =>  $user->id   ];
        $recordings =   Recordings::where(  $where  );

        if( $recordings->count() ){
            $records    =   $recordings->get();

            foreach( $records   as  $record ){
                if( $record->recording  )
                    Storage::delete('public/uploads/recordings/'.$record->recording);
            }
        }

        $user->delete();

        $return = [
            'message' => 'This Account has been deleted permanentaly.',
            "code"  =>  $this->errorCodes("success")
        ];

        return response()->json($return, 200);
    }
}
