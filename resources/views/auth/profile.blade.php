@extends('layouts.app')
@section('title', 'My Profile')

@section('content')
<div class="page-header">
    <h1 class="page-title">My Profile</h1>
</div>

<div class="card" style="max-width:540px">
    <form method="POST" action="{{ route('profile.update') }}" class="form">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label class="form-label">Full Name</label>
            <input type="text" name="name" value="{{ old('name', $user->name) }}"
                   class="form-input @error('name') is-error @enderror" required>
        </div>

        <div class="form-group">
            <label class="form-label">Email Address</label>
            <input type="email" value="{{ $user->email }}" class="form-input" disabled>
            <small class="form-hint">Email cannot be changed.</small>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Phone</label>
                <input type="text" name="phone" value="{{ old('phone', $user->phone) }}"
                       class="form-input" placeholder="09XXXXXXXXX">
            </div>
            <div class="form-group">
                <label class="form-label">Gender</label>
                <select name="gender" class="form-input">
                    <option value="">— Select —</option>
                    <option value="male"   @selected(old('gender',$user->gender)=='male')>Male</option>
                    <option value="female" @selected(old('gender',$user->gender)=='female')>Female</option>
                    <option value="other"  @selected(old('gender',$user->gender)=='other')>Other</option>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">Date of Birth</label>
            <input type="date" name="date_of_birth"
                   value="{{ old('date_of_birth', $user->date_of_birth?->format('Y-m-d')) }}"
                   class="form-input">
        </div>

        <div class="form-group">
            <label class="form-label">Role</label>
            <input type="text" value="{{ ucfirst($user->role) }}" class="form-input" disabled>
        </div>

        <button type="submit" class="btn btn-primary">Save Changes</button>
    </form>
</div>
@endsection
