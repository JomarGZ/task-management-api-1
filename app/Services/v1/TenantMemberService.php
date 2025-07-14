<?php
namespace App\Services\v1;

use App\Models\User;
use App\Notifications\AddMemberNotification;
use Exception;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class TenantMemberService {

    public function getValidSortDirection($direction)
    {
        if (!in_array($direction, ['asc', 'desc'])) {
            $direction = 'asc'; 
        }
        return $direction;
    }
    public function getValidSortColumn($column)
    {
        $validColumns = ['name', 'email', 'created_at'];
        if (!in_array($column, $validColumns)) {
            $column = 'created_at';
        }
        
        return $column;
    }

    public function addMember(array $data)
    {
        $password = $this->generatePassword() ?? 'password';
        $newMember =  User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'role' => $data['role'],
            'password' => Hash::make($password),
        ]);
        if ($newMember) {
            $this->notifyNewMember($newMember, $password);
        }
        return $newMember;
    }

    public function notifyNewMember($member, $plainPassword)
    {
        if (empty($member) || empty($plainPassword)) {
            throw new Exception('All parameters are required to process');
        }
        $data = $member->toArray();
        $data['plain_password'] = $plainPassword;
        $member->notify(new AddMemberNotification($data));
    }

    public function generatePassword()
    {
        return Str::random(12);
    }
}