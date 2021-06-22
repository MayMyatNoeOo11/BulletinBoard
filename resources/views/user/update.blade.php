@extends('layouts.app')

@section('content')

    
        <div class="col-md-8 offset-md-2" style="padding-top:4px">
            <div class="card">
                <div class="card-header bg-light" >
                    <h4 class="text-center">Edit User</h4>
                </div>

                <div class="card-body">
                    <form method="POST" action="{{ route('updateUser') }}">
                        @csrf
                        <div class="row">
                            <div class="col-md-10">                        
                                    <div class="form-group row">
                                        <label for="name" class="col-md-3 col-form-label text-md-right">User Name</label>

                                        <div class="col-md-6">
                                            <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ $userData->name}}" required autocomplete="name" autofocus>

                                            @error('name')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="email" class="col-md-3 col-form-label text-md-right">Email</label>

                                        <div class="col-md-6">
                                            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{$userData->email }}" required autocomplete="email">

                                            @error('email')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>


                                    <div class="form-group row">
                                        <label for="type" class="col-md-3 col-form-label text-md-right">User Type</label>

                                        <div class="col-md-6">  
                                            <select name="type" id="type" class="form-control">
                                                <option value="1" {{ $userData->type == '1' ? 'selected' : '' }}>Admin</option>
                                                <option value="0" {{ $userData->type == '0' ? 'selected' : '' }}>User</option>
                                            </select>
                                     
                                            @error('type')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="phone" class="col-md-3 col-form-label text-md-right">Phone</label>

                                        <div class="col-md-6">
                                            <input id="phone" type="text" class="form-control @error('phone') is-invalid @enderror" name="phone" value="{{$userData->phone }}" required autocomplete="phone" autofocus>

                                            @error('phone')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div> 

                                    <div class="form-group row">
                                        <label for="date_of_birth" class="col-md-3 col-form-label text-md-right">Date of Birth</label>

                                        <div class="col-md-6">

                                        <input class="form-control" id="date_of_birth" name="date_of_birth" value="{{$userData->date_of_birth}}" type="date"/>
                            

                                            @error('date_of_birth')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    
                                    <div class="form-group row">
                                        <label for="address" class="col-md-3 col-form-label text-md-right">Address</label>

                                        <div class="col-md-6">
                                            <textarea id="address" class="form-control" >{{$userData->address }}</textarea>

                                            @error('address')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="profile" class="col-md-3 col-form-label text-md-right">Profile Photo</label>

                                        <div class="col-md-6">
                                            <input id="profile_photo" type="file"  accept=".png, .jpg, .jpeg" value="pic1.jpg" onchange="validateFileType(this.event)" class="form-control @error('profile_photo') is-invalid @enderror" name="profile_photo"  required autocomplete="profile_photo" autofocus>

                                            @error('profile_photo')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        
                                        <div class="col-md-7 offset-md-3">
                                            <img class="profile_preview" src="{{URL::asset('/images/profile.jpeg')}}" id="preview_image"/>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        
                                        <div class="col-md-3">
                                            <a href="{{route('changePassword',Auth::id())}}" >Change Password</a>
                                        </div>
                                    </div>

                                    <div class="form-group row mb-0">
                                        <div class="col-md-7 offset-md-4">
                                            <button type="submit" class="btn btn-primary">
                                                Update
                                            </button>
                                            <button type="button" class="btn btn-danger">
                                                Cancel
                                            </button>
                                        </div>
                                    </div>                                                
                            </div>
                            <div class="col-md-2">
                                <img class="profile_preview" src="{{URL::asset('/images/profile.jpeg')}}" id="old_profile"/>
                                <label for="lbl"> Old Profile</label>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
   
<script type="text/javascript">


</script>    
@endsection