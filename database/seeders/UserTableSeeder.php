<?php


namespace Database\Seeders;


use App\Models\User;
use App\Models\UserProfile;
use App\Models\UserRole;
use Database\Factories\FolderFactory;
use Database\Factories\ProgramFactory;
use Database\Factories\UserProfileFactory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserTableSeeder extends Seeder
{
    public function run()
    {
        $userAdminRole = UserRole::where('name', UserRole::ROLE_ADMIN)->first();
        $user = new User([
            'login' => 'admin',
            'password' => Hash::make('admin')
        ]);
        $user->role()->associate($userAdminRole);
        $user->save();

        $user->profile()->save(new UserProfile([
            'name' => 'Administrator',
            'email' => 'admin@mail.fake'
        ]));

        $userRole = UserRole::where('name', UserRole::ROLE_USER)->first();
        User::factory()
            ->count(30)
            ->make()
            ->each(function ($user) use ($userRole) {
                $userProfile = new UserProfileFactory();
                $user->role()->associate($userRole);
                $user->save();
                $user->profile()->save($userProfile->make());

                $folderFactory = new FolderFactory();
                $folders = $folderFactory
                    ->count(20)
                    ->make();

                $user->folders()->saveMany($folders);

//                $folders->each(function ($folder) use ($user) {
//                    $programs = (new ProgramFactory())
//                        ->count(100)
//                        ->make();
//                    $folder->programs()->saveMany($programs);
//                });
            });
    }
}
