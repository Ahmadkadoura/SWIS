<?php

namespace App\Http\Repositories;

use App\Enums\userType;
use App\Mail\NewUserCredentials;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;

class userRepository extends baseRepository
{
    public function __construct(User $model)
    {
        parent::__construct($model);
    }

    public function index(): LengthAwarePaginator
    {
        $filters = [
            AllowedFilter::exact('type'),
            AllowedFilter::partial('name'),
            AllowedFilter::partial('code'),
        ];
        $sorts = [
            AllowedSort::field('name'),
            AllowedSort::field('code'),
            AllowedSort::field('type'),
            AllowedSort::field('created_at'),
            AllowedSort::field('updated_at'),
        ];

        return $this->filter(User::query(), $filters, $sorts);
    }

    public function create($request): array
    {
        // Generate a random password
        $password = Str::random(10);

        // Extract the user type and other details from the request
        $userType = UserType::from($request['type']); // Assume `user_type` is provided in the request
        $randomNumber = rand(1000, 9999); // Generate a random number
        $username = $request['name']['en'] ?? reset($request['name']);

        // Determine the email address based on user type
        switch ($userType) {
            case UserType::keeper:
                $email = "{$username}{$randomNumber}@keeper.swis.com";
                $role = 'keeper';

                break;
            case UserType::donor:
                $email = "{$username}{$randomNumber}@donor.swis.com";
                $role = 'donor';

                break;
            case UserType::admin:
                $email = "{$username}{$randomNumber}@admin.swis.com";
                $role = 'admin';

                break;
            default:
                // Handle invalid user type
                return ['message' => 'Invalid user role'];
        }

        // Update request with the new email and hashed password
        $request['email'] = $email;
        $request['password'] = Hash::make($password);

        // Create the user
        $data = User::create($request);

        $data->assignRole($role);

        // Prepare email details
        $message = "User created successfully";
        $details = [
            'email' => $email,
            'password' => $password,
        ];

        // Send email to the generated email address
        Mail::to($data->contact_email)->send(new NewUserCredentials($details));

        return ['message' => $message, 'User' => $data];
    }


}
