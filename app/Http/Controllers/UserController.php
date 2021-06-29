<?php

namespace App\Http\Controllers;
use Log;
use Auth;
use App\Models\User;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Contracts\Services\User\UserServiceInterface;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Config;
use App\Rules\MatchOldPassword;
use Illuminate\Support\Facades\Hash;
use File;
use Illuminate\Support\Facades\Session;
class UserController extends Controller
{

    public $userService;
    public function __construct(UserServiceInterface $user_service_interface)
    {
      $this->userService = $user_service_interface;
    } 
   //$photo_destination_path=Config::get('constants.options.profile_photo_destination_path');
    public function photo_store(Request $request)
    {
        
        if($request->hasFile('profile_photo'))
        {               // Get image file
            $image = $request->file('profile_photo'); 
            $destinationPath = 'storage/images/'; // upload path
            $profileImage = date('YmdHis') . "_Profile." . $image->getClientOriginalExtension();
            $file_full_path=$destinationPath.'/'.$profileImage;
            $image->move($destinationPath, $profileImage); 

        return $profileImage;
        // $image->move(public_path('images'), $imageName);//store in public
        }
        else
        {
            $profileImage="";

        }
        return $profileImage;
    }
    
    /*
    * User Detail Form
    *@return ..resources\views\user\show.blade.php
    */
    public function show($id)
    {   
        $user=$this->userService->getUserInfo($id);

        return view('user.show', compact('user'));
    }

     /**
     * Show All Users
     *
     * @return ..resources\views\user\index.blade.php
     */  
    public function index(Request $request)
    {
        
        $userData=$this->userService->getUserList($request);
        //dd($userData);

        $name=$request->input('name');
        $email=$request->input('email');
        $created_from_date=$request->input('created_from_date');
        $created_to_date=$request->input('created_to_date');
        
        return view('user.index',compact('userData'))
        ->with('k',(request()->input('page',1)-1)*5)
        ->with('name',$name)
        ->with('email',$email)
        ->with('created_from_date',$created_from_date)
        ->with('created_to_date',$created_to_date);
  
    }

    /**
     *  Common Menu Form     
     * @return ..resources\views\user\common.blade.php
     */
    public function common()
    {
        return view('user.common');
    }

    /**
     * Profile Form 
     *  @return ..resources\views\user\profile.blade.php
     */
    public function profile($id)
    {
        $userData=$this->userService->getUserbyId($id);
        
        return view('user.profile',compact('userData'));
    }

    /**
     *  Create User  Form  
     *  @return ..resources\views\user\create.blade.php
     */
    public function create()
    {
        return view('user.create');
    }

  
    /**
     * Create User 
     * Validation
     *  @return ..resources\views\user\create_confirm.blade.php
     */
    public function create_user(Request $request)
    {     

     $request->validate([
        'name'=>'required|unique:users',
        'email' => 'required|email|unique:users,email,regex:/^(?=.*[A-Z])(?=.*\d)[a-zA-Z\d]{8,}$/',        
        'profile_photo' => 'required|image|mimes:jpeg,png,jpg,jfif,gif,svg|max:2048',//|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        'date_of_birth'=>'required|date',
        'password' => 'required|min:8|confirmed',
        'password_confirmation' => 'required| min:8'
        ],
        [
        'name.required'=>'Name is required.',
        'email.required'=>'Email is required',
        'profile_photo.required'=>'Profile photo is required.',
        'password.required'=>'Password is required.',
        'password_confirmation.required'=>'Confirm Password is required.',
        'date_of_birth.required'=>'Date of birth is required.',
        'name.unique'=>'The user name is already exist.',
        'email.email'=>'Email must be a valid email address.',
        'email.unique'=>'The user with this email already exist',
        'password.confirmed'=>'Password and confirm password must be the same.',
        'password.min'=>'Password must be more than 8 characters long.',   
        'password_confirmation.min'=>'Confirm password must be more than 8 characters long.'
        ]);

        $profileImage=$this->photo_store($request);
        $userData=$request;
        return view('user.create_confirm',compact('userData'))->with('image',$profileImage);  
    }

    /**
     * Store User in db
     *  @return ..resources\views\user\index.blade.php
     * 
     */
    public function confirm_user(Request $request)
    {    
        $this->userService->saveUser($request);
        return redirect()->route('showAllUsers')->with('success','New user is created successfully.');
    }

    /**
     *  Update User Form    
     * GET Method
     */
    public function update($id)
    {
        $userData=$this->userService->getUserbyId($id);
        return view('user.update',compact('userData'));
    }

    public function update_user(Request $request,$id)
    {
        $request->validate([
                'name'=>['required',Rule::unique('users')->ignore($id)],
                'email' => ['required','email', Rule::unique('users')->ignore($id)],
                'date_of_birth'=>'required|date'
            ],
            [
                'name.required'=>'Name is required.',
                'email.required'=>'Email is required',
                'date_of_birth.required'=>'Date of birth is required.',
                'name.unique'=>'The user name is already exist.',
                'email.email'=>'Email must be a valid email address.',
                'email.unique'=>'The user with this email already exist'
            ]);
        if($request->hasFile('profile_photo'))//new profile
        { 
            $old_photo=$request->old_photo;
            File::delete('storage/images/'.$old_photo);
            $profileImage=$this->photo_store($request);
                
        }
        else //old profile
        {
        $profileImage=$request->old_photo;       
        }
        $userData=$request;  

        return view('user.update_confirm',compact('userData'))->with('image',$profileImage);
    }

    /**
     * Update user in db
     *@return ..resources\views\user\index.blade.php
     */
    public function update_confirm_user(Request $request,$id)
    {        
        $this->userService->updateUser($request,$id);     
        return redirect()->route('showAllUsers')->with('success','User is updated successfully.');      
    }

    /**
     *  Delete User Form
     * @parameter $id
     *  @return ..resources\views\user\delete.blade.php
     * 
     */
    public function delete($id)
    {
        $data=$this->userService->getUserbyId($id);    
        $checkUserPosted=$this->userService->checkUserPosted($id);
        if($checkUserPosted)
        {
            return "The user is created post and cannot be deleted.";

        }
        else
        {
            return view('user.delete',compact('data'));
        }      
           
    }

    /**
     * Delete user in db
     */
    public function delete_user(Request $request)
    {
        $name=$request->name; 
      
       $is_deleted=$this->userService->deleteUser($request->id);
      if($is_deleted==1)
      {
          $message="User has been deleted successfully.";
          return redirect()->route('showAllUsers')->with('success',$message);
      }
      else
      {
        $message="User delete fail !";
        return redirect()->route('showAllUsers')->with('fail',$message);
      }
      
    }

    /**
     * Change Password Form
     * @return ..resources\views\user\change_password.blade.php
     */
    public function changePasswordForm($id)
    {
      //  Session::forget(['old_value','new_value','new_confirm_value']);
        $userData=$this->userService->getUserbyId($id);
        return view('user.change_password',compact('userData'));   
    }

    /**
     * Change Password 
     * Post Method
     */
    public function changePassword(Request $request)
    {       
        // session(['old_value' => $request->old_password]);
        // session(['new_value' => $request->new_password]);
        // session(['new_confirm_value' => $request->new_confirm_password]);

         $request->validate([
        'old_password' =>['required',new MatchOldPassword($request->password)],
        'new_password' => ['required','min:8','different:old_password','regex:/^(?=.*[A-Z])(?=.*\d)[a-zA-Z\d]{8,}$/'],
        'new_confirm_password' => ['required','min:8','same:new_password']
         ],
         [
         'old_password.required'=>'Old Password is required.',
         'new_password.required'=>'New Password is required.',
         'new_confirm_password.required'=>'Confirm password is required.',
         'new_password.different'=>'New password must be different from old password.',
         'new_confirm_password.same'=>'New password and new confirm password must be the same' ,
         'new_password.regex'=>'Must contain at least 1 Uppercase letter and 1 numeric.'      

         ]);    
        
        $is_change_password=$this->userService->changePassword($request->id,$request->new_password);
  
     // return redirect()->route('showAllUsers')->with('success','Change Password Successful.');
        // Session::forget(['old_value','new_value','new_confirm_value']);
        return redirect()->route('update', ['id' => $request->id]);

    }
}

