@extends('layouts.app')
@section('title', 'Edit Profile')

@section('content')
<div class="page-header">
    <h1 class="page-title">My Profile</h1>
</div>

@if(session('status') === 'profile-updated')
    <div class="alert alert-success" style="margin-bottom: 1rem">Profile updated successfully.</div>
@endif

{{-- Update Profile Info --}}
<div class="card" style="margin-bottom: 1.5rem">
    <h2 class="card-title" style="margin-bottom: 1rem">Profile Information</h2>
    <form method="POST" action="{{ route('profile.update') }}" class="form">
        @csrf @method('PATCH')

        <div class="form-group">
            <label class="form-label">Full Name</label>
            <input type="text" name="name" value="{{ old('name', $user->name) }}"
                   class="form-input @error('name') is-error @enderror" required>
            @error('name')<span class="error-message">{{ $message }}</span>@enderror
        </div>

        <div class="form-group">
            <label class="form-label">Email Address</label>
            <input type="email" name="email" value="{{ old('email', $user->email) }}"
                   class="form-input @error('email') is-error @enderror" required>
            @error('email')<span class="error-message">{{ $message }}</span>@enderror
        </div>

        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Phone (optional)</label>
                <input type="text" name="phone" value="{{ old('phone', $user->phone) }}"
                       class="form-input" placeholder="09XXXXXXXXX">
            </div>
            <div class="form-group">
                <label class="form-label">Gender (optional)</label>
                <select name="gender" class="form-input">
                    <option value="">— Select —</option>
                    <option value="male"   @selected(old('gender', $user->gender) == 'male')>Male</option>
                    <option value="female" @selected(old('gender', $user->gender) == 'female')>Female</option>
                    <option value="other"  @selected(old('gender', $user->gender) == 'other')>Other</option>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">Date of Birth (optional)</label>
            <input type="date" name="date_of_birth"
                   value="{{ old('date_of_birth', optional($user->date_of_birth)->format('Y-m-d')) }}"
                   class="form-input">
        </div>

        <button type="submit" class="btn btn-primary">Save Changes</button>
    </form>
</div>

{{-- Update Password --}}
<div class="card" style="margin-bottom: 1.5rem">
    <h2 class="card-title" style="margin-bottom: 1rem">Change Password</h2>
    <form method="POST" action="{{ route('password.update') }}" class="form">
        @csrf @method('PUT')

        <div class="form-group">
            <label class="form-label">Current Password</label>
            <input type="password" name="current_password"
                   class="form-input @error('current_password', 'updatePassword') is-error @enderror">
            @error('current_password', 'updatePassword')
                <span class="error-message">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-row">
            <div class="form-group">
                <label class="form-label">New Password</label>
                <input type="password" name="password"
                       class="form-input @error('password', 'updatePassword') is-error @enderror">
                @error('password', 'updatePassword')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>
            <div class="form-group">
                <label class="form-label">Confirm New Password</label>
                <input type="password" name="password_confirmation" class="form-input">
            </div>
        </div>

        <button type="submit" class="btn btn-primary">Update Password</button>
    </form>
</div>

{{-- Delete Account --}}
<div class="card">
    <h2 class="card-title" style="margin-bottom: 0.5rem">Delete Account</h2>
    <p style="color: var(--color-text-secondary); margin-bottom: 1rem">
        Once deleted, your account and all data cannot be recovered.
    </p>
    <form method="POST" action="{{ route('profile.destroy') }}"
          onsubmit="return confirm('Are you sure? This cannot be undone.')">
        @csrf @method('DELETE')

        <div class="form-group">
            <label class="form-label">Confirm your password to delete</label>
            <input type="password" name="password"
                   class="form-input @error('password', 'userDeletion') is-error @enderror">
            @error('password', 'userDeletion')
                <span class="error-message">{{ $message }}</span>
            @enderror
        </div>

        <button type="submit" class="btn btn-danger">Delete My Account</button>
    </form>
</div>

@endsection