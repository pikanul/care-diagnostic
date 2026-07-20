<?php

use App\Models\Role;
use App\Models\User;
use App\Support\AdminFoundationInstaller;
use App\Support\AdminRoles;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Validator;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('admin:create-super {email} {--name=Super Admin} {--password=}', function () {
    AdminFoundationInstaller::syncRolesAndPermissions();

    $email = (string) $this->argument('email');
    $name = (string) $this->option('name');
    $password = (string) $this->option('password');

    if ($password === '') {
        $password = (string) $this->secret('Password');
        $confirmation = (string) $this->secret('Confirm password');

        if ($password !== $confirmation) {
            $this->error('Passwords do not match.');

            return 1;
        }
    }

    $validator = Validator::make([
        'name' => $name,
        'email' => $email,
        'password' => $password,
    ], [
        'name' => ['required', 'string', 'max:255'],
        'email' => ['required', 'email', 'max:255'],
        'password' => ['required', 'string', 'min:8'],
    ]);

    if ($validator->fails()) {
        foreach ($validator->errors()->all() as $error) {
            $this->error($error);
        }

        return 1;
    }

    $user = User::updateOrCreate(
        ['email' => $email],
        [
            'name' => $name,
            'password' => $password,
            'is_active' => true,
        ]
    );

    $role = Role::where('slug', AdminRoles::SUPER_ADMIN)->firstOrFail();
    $user->roles()->syncWithoutDetaching([$role->id]);

    $this->info("Super Admin is ready: {$user->email}");

    return 0;
})->purpose('Create or update the first Super Admin user');
