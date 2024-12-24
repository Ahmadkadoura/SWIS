<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Repositories\AuthRepository;
use App\Http\Requests\Auth\CompleteProfileRequest;
use App\Http\Requests\Auth\LoginRequests;
use App\Http\Requests\Auth\registerRequests;
use App\Http\Responses\Response;
use App\Mail\PasswordResetCodeMail;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Throwable;

class AuthController extends Controller
{

    private AuthRepository $authRepository;
    public function __construct(AuthRepository $authRepository)
    {
        $this->authRepository = $authRepository;
        $this->middleware(['auth:sanctum'])
            ->only(['logout','requestResetPassword','resetWithCode','completeProfile']);
    }

    public function login(LoginRequests $request): JsonResponse
    {

        $userData = $this->authRepository->login($request->validated());

        return Response::Success($userData['User'],  __($userData['message']), $userData['code']);
    }

    public function logout(): JsonResponse
    {

        $userData = $this->authRepository->logout();
        return Response::Success($userData['User'],  __($userData['message']));
    }

    public function requestResetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);
        $user = User::where('email', $request->email)->first();

        // Generate a random code
        $resetCode = mt_rand(100000, 999999);
        $user->password_reset_code = $resetCode;
        $user->password_reset_expires_at = now()->addMinutes(15); // Code is valid for 15 minutes
        $user->save();

        // Send the reset code via email
        Mail::to($user->contact_email)->send(new PasswordResetCodeMail($resetCode));
        return response()->json(['message' => __('Reset code sent to your email.')], 200);
    }

    public function resetWithCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'password' => 'required|string|min:8|confirmed',
            'reset_code' => 'required|integer',
        ]);

        $user = User::where('email', $request->email)
            ->where('password_reset_code', $request->reset_code)
            ->where('password_reset_expires_at', '>', now())
            ->first();

        if (!$user) {
            return response()->json(['message' => __('Invalid or expired reset code.')], 422);
        }

        // Reset the password
        $user->password = Hash::make($request->password);
        $user->password_reset_code = null; // Clear the reset code
        $user->password_reset_expires_at = null;
        $user->save();

        return response()->json(['message' => __('Password reset successfully.')], 200);
    }



    public function completeProfile(CompleteProfileRequest $request): JsonResponse
    {
        $nowData = $request->validated();
        $userData = $this->authRepository->completeProfile($nowData);

        return Response::Success($userData['User'], __($userData['message']), $userData['code']);
    }

    //    public function register(registerRequests $request): JsonResponse
//    {
//        $userData = $this->authRepository->register($request->validated());
//        return Response::Success($userData['User'], $userData['message']);
//    }
}
