<?php

namespace App\Http\Controllers\Api;
use App\Models\User;
use App\Models\Userimage;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
// use Validator;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Auth;

class AuthController extends Controller
{
        
    /**
     * Create User
     * @param Request $request
     * @return User
     */
    public function createUser(Request $request)
    {

    try{

        $validateUser= Validator::make($request->all(),
        [
            'name'=>'required',
            'email'=>'required|email|unique:users,email',
            'password'=>'required'
        ]);


        if($validateUser->fails()){
            return response()->json(
                [
                    'status'=>false,
                    'message'=>'validation error',
                    'errors'=>$validateUser->errors()
                ],401);
        }
        $user=User::create([
            'name'=>$request->name,
            'email'=>$request->email,
            'password'=>Hash::make($request->password)
        ]);
        $user['token']=$user->createToken("API TOKEN")->plainTextToken;
        return response()->json([
            
            'status'=>true,
            'message'=>'acount create sucessful',
            'data'=>$user,
            'token'=>$user->createToken("API TOKEN")->plainTextToken
        ],200);



    }catch(\Throwable $th){
        return response()->json(
            [
                'status'=>false,
                'message'=>$th->getMessage(),
                
            ],500);

    }
    }

    /**
     * Create User
     * @param Request $request
     * @return User
     */
    public function loginUser(Request $request){

        try{
            
        $validateUser= Validator::make($request->all(),
        [
            'email'=>'required|email',
            'password'=>'required'
        ]);


        if($validateUser->fails()){
            return response()->json(
                [
                    'status'=>false,
                    'message'=>'validation error',
                    'errors'=>$validateUser->errors()
                ],401);
        }

        if(!Auth::attempt($request->only(['email','password']))){
        return   response()->json(
                [
                    'status'=>false,
                    'message'=>'email or password invalide',
                    'errors'=>$validateUser->errors()
                ],401);
        }

        $user=User::where('email',$request->email)->first();
    $user['token']=$user->createToken("API TOKEN")->plainTextToken;
        return response()->json([
            'status'=>true,
            'message'=>'User logged In successfully',
            'data'=>$user,
            'token'=>$user->createToken("API TOKEN")->plainTextToken
        ],200);


        }catch(\Throwable $th){
            return response()->json(
                [
                    'status'=>false,
                    'message'=>$th->getMessage(),
                    
                ],500);
        
        }
    }

    
    public function getAccount()
    {
        $users=User::all();
        // $data=[];
        for ($i=0; $i < count($users); $i++) { 
            $userimage=Userimage::where('user_id',$users[$i]->id)->get();
            $users[$i]['file_url']=$userimage??[];
        }
        
        //     $userimage=Userimage::where('user_id',$users[$i]->id)->first();
            
        //     $users[$i]['file_url']=$userimage??[];
            
        // }
        // // $data['user']=$posts;
    
        return response()->json(
            [
                'status'=>true, 
                'message'=>'list of users',
                'data'=> $users
            
            ],200);
    }

}


