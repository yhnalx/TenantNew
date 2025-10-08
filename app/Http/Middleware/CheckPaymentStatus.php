<!-- <?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\User;
use Carbon\Carbon;

class CheckPaymentStatus
{
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();

        // Only check for tenants
        if ($user && $user->role === 'tenant') {
            $user->updatePaymentStatuses();
        }

        return $next($request);
    }
} -->
