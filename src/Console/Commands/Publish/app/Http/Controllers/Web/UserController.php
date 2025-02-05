<?php

namespace App\Http\Controllers\Web;

use AlanGiacomin\LaravelBasePack\Controllers\Controller;
use App\Commands\User\RemoveUserRole;
use App\Models\User\Contracts\IUserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\RouteAttributes\Attributes\Get;
use Spatie\RouteAttributes\Attributes\Post;
use Spatie\RouteAttributes\Attributes\Prefix;

#[Prefix('user')]
class UserController extends Controller
{
    public function __construct(
        protected IUserRepository $userRepository
    ) {}

    #[Get('loadUser')]
    public function loadUser(Request $request)
    {
        return $request->user();
    }

    #[Post('login')]
    public function login(Request $request)
    {
        $loggedUser = $request->user();
        if (!Auth::check()) {
            $credentials = [
                'email' => $request->input('email'),
                'password' => $request->input('password'),
            ];
            $request->validate([
                'email' => 'required|email',
                'password' => 'required',
            ]);

            if (Auth::attempt($credentials)) {
                $request->session()->regenerate();
                $loggedUser = Auth::user();
            } else {
                return response()->json(['error' => 'Unauthorized'], 401);
            }
        }

        return $loggedUser;
    }

    #[Post('logout')]
    public function logout(Request $request)
    {
        if (Auth::check()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        return null;
    }

    #[Get('all')]
    public function all()
    {
        $users = $this->userRepository->getAll();

        return $users;
    }

    #[Post('removeRole')]
    public function removeRole(Request $request)
    {
        return $this->executeCommand(new RemoveUserRole($request->input()));
    }
}
