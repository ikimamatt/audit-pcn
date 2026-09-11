# Panduan setup client NUSA DAYA EXCELLENCE TOOLS (SSO)

Penggunaan framework Laravel 8+.

## File .env

Tambahkan baris code berikut pada file `.env`

```env
SSO_CHECK_TOKEN_URL=https://sso.plnnusadaya.co.id/api/sso/check-token
SSO_REDIRECT_URL=https://sso.plnnusadaya.co.id
SSO_CLIENT_ID=
```

Bagian `SSO_CLIENT_ID` nanti akan diberikan. Saat deploy ke server terdapat kegagalan kemungkinan diperlukan jalankan perintah `php artisan config:clear` setelah merubah `.env`

## File config

Tambahkan baris berikut di bagian bawah pada file `config/services.php`

```php
'sso_pln_nd' => [ 
    'client_id' => env('SSO_CLIENT_ID'), 
    'check_token_url' => env('SSO_CHECK_TOKEN_URL'), 
    'redirect_url' => env('SSO_REDIRECT_URL'), 
],
```

## Repository SSO

Buat class `SSORepository` untuk menampung fungsi terkait sso.

```php
<?php 
 
namespace App\Repositories; 
 
use Illuminate\Support\Facades\Http; 
 
class SSORepository
{ 
    /** 
     * Check token validity to main sso server 
     *  
     * @param string $token 
     */ 
    public function checkTokenValidityToServer(string $token) 
    { 
        $response = Http::post(config('services.sso_pln_nd.check_token_url'), [ 
            'website_id' => config('services.sso_pln_nd.client_id'), 
            'token' => $token 
        ]); 
 
        return $response->json(); 
    } 
}
```

## Controller SSO

Buat class `APIController` untuk menampung fungsi untuk response api

```php
<?php 
 
namespace App\Http\Controllers; 
 
use Illuminate\Foundation\Auth\Access\AuthorizesRequests; 
use Illuminate\Foundation\Bus\DispatchesJobs; 
use Illuminate\Foundation\Validation\ValidatesRequests; 
use Illuminate\Routing\Controller as BaseController; 
 
class APIController extends BaseController
{ 
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests; 
 
    public function sendSuccess($data, $message, int $code = 200){ 
 
        return response()->json([ 
            'status' => true, 
            'message' => $message, 
            'data' => $data 
        ], $code); 
    } 
 
    public function sendError($message, int $code = 400){ 
 
        return response()->json([ 
            'status' => false, 
            'message' => $message, 
        ], $code); 
    } 
}
```

Selanjutnya buat class `SSOApiController` untuk menampung request yang akan dikirim oleh main-server sso.

```php
<?php 
 
namespace App\Http\Controllers\API\SSO; 
 
use App\Http\Controllers\APIController; 
use App\Models\User; 
use App\Repositories\SSORepository; 
use Illuminate\Http\Request; 
 
class SSOApiController extends APIController
{ 
    /** 
     * Used to mark website that user have 
     */ 
    public function checkUserExistsByNip(Request $request, SSORepository $ssoRepository) 
    { 
        $request->validate([ 
            'nip' => 'required', 
            'token' => 'required' 
        ]); 
 
        $checkToken = $ssoRepository->checkTokenValidityToServer($request->token); 
 
        if ($checkToken['status'] && $checkToken['data']) { 
            $user = User::where('nip', $request->nip)->first(); 
            return $this->sendSuccess($user, 'Hasil pencarian user.'); 
        } 
 
        return $this->sendError($checkToken['message']); 
    } 
 
    /** 
     * Used to login the user 
     */ 
    public function login(Request $request, SSORepository $ssoRepository) 
    { 
        $request->validate([ 
            'nip' => 'required|exists:users,nip', 
            'token' => 'required' 
        ]); 
 
        $checkToken = $ssoRepository->checkTokenValidityToServer($request->token); 
        try { 
            if ($checkToken['status'] && $checkToken['data']) { 
                $user = User::where('nip', $request->nip)->first(); 
                if ($user) { 
                    auth()->login($user); 
                    return redirect('/login'); 
                } 
            } 
        } catch (\Exception $e) { 
            info('Exception: ' . $e->getMessage()); 
            // Handle exception 
        } 
        return redirect()->away(config('services.sso_pln_nd.redirect_url')); 
    } 
}
```

> ⚠️ Pada bagian `return redirect('/login')`, url dapat diganti menjadi route login utama sesuai aplikasi. Lalu pada bagian `use App\Models\User` juga bisa disesuaikan dengan penempatan model pada aplikasi.

## Route callback api

Pada file `routes/api.php` tambahkan baris berikut

```php
Route::post('sso/check-user', [SSOApiController::class, 'checkUserExistsByNip']);
```

## Route login

Pada file `routes/web.php` tambahkan baris berikut, pastikan tidak dibungkus oleh middleware.

```php
Route::get('sso/callback', [SSOApiController::class, 'login'])->name('login-sso');
```

> ⚠️ Penulisan controller bisa disesuaikan dengan versi laravel. Penulisan nip bisa disesuaikan dengan penamaan field pada masing-masing sistem.
