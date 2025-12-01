<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PasswordResetController extends Controller
{
    /**
     * Mostrar formulario para solicitar recuperación de contraseña
     */
    public function showForgotPasswordForm()
    {
        return view('auth.passwords.forgot');
    }

    /**
     * Send password reset link (Web)
     */
    public function sendResetLink(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => [
                'required',
                'email',
                'exists:usuarios,email',
                function ($attribute, $value, $fail) {
                    if (preg_match('/[<>\"\'&;]/', $value)) {
                        $fail('El campo ' . $attribute . ' no puede contener símbolos peligrosos.');
                    }
                }
            ],
        ], [
            'email.required' => 'El email es obligatorio',
            'email.email' => 'El formato del email no es válido',
            'email.exists' => 'No existe una cuenta con este email',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Check if user has made too many reset requests
        $email = $request->email;
        $key = 'password_reset_attempts:' . $email;

        if (cache()->has($key) && cache()->get($key) >= 3) {
            return back()->with('error', 'Has excedido el límite de solicitudes de recuperación. Intenta de nuevo en 1 hora.');
        }

        // Increment attempts counter
        cache()->put($key, (cache()->get($key, 0) + 1), 3600); // 1 hour

        // Generate reset token
        $token = Str::random(64);
        $expiresAt = Carbon::now()->addMinutes(60);

        // Store token in database
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $email],
            [
                'token' => Hash::make($token),
                'created_at' => Carbon::now(),
                'expires_at' => $expiresAt
            ]
        );

        // Send email
        try {
            $resetUrl = url('/password/reset/' . $token . '?email=' . urlencode($email));

            Mail::send('emails.password-reset', [
                'token' => $token,
                'email' => $email,
                'resetUrl' => $resetUrl,
                'expiresAt' => $expiresAt
            ], function ($message) use ($email) {
                $message->to($email)
                        ->subject('Recuperación de Contraseña - NAWI');
            });

            return back()->with('status', 'Se ha enviado un enlace de recuperación a tu email. Revisa tu bandeja de entrada.');
        } catch (\Exception $e) {
            \Log::error('Error al enviar email de recuperación', [
                'email' => $email,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // En desarrollo, mostrar el error real para debugging
            if (config('app.debug')) {
                return back()->with('error', 'Error al enviar email: ' . $e->getMessage() . ' | Token para testing: ' . $token);
            }
            return back()->with('status', 'Se ha enviado un enlace de recuperación a tu email.');
        }
    }

    /**
     * API: Send password reset link
     */
    public function sendResetLinkApi(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => [
                'required',
                'email',
                'exists:usuarios,email',
                function ($attribute, $value, $fail) {
                    if (preg_match('/[<>\"\'&;]/', $value)) {
                        $fail('El campo ' . $attribute . ' no puede contener símbolos peligrosos.');
                    }
                }
            ],
        ], [
            'email.required' => 'El email es obligatorio',
            'email.email' => 'El formato del email no es válido',
            'email.exists' => 'No existe una cuenta con este email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos inválidos',
                'errors' => $validator->errors()
            ], 422);
        }

        // Check if user has made too many reset requests
        $email = $request->email;
        $key = 'password_reset_attempts:' . $email;

        if (cache()->has($key) && cache()->get($key) >= 3) {
            return response()->json([
                'success' => false,
                'message' => 'Has excedido el límite de solicitudes de recuperación. Intenta de nuevo en 1 hora.',
                'code' => 'TOO_MANY_REQUESTS'
            ], 429);
        }

        // Increment attempts counter
        cache()->put($key, (cache()->get($key, 0) + 1), 3600); // 1 hour

        // Generate reset token
        $token = Str::random(64);
        $expiresAt = Carbon::now()->addMinutes(60);

        // Store token in database
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $email],
            [
                'token' => Hash::make($token),
                'created_at' => Carbon::now(),
                'expires_at' => $expiresAt
            ]
        );

        // Send email
        try {
            $resetUrl = url('/password/reset/' . $token . '?email=' . urlencode($email));

            Mail::send('emails.password-reset', [
                'token' => $token,
                'email' => $email,
                'resetUrl' => $resetUrl,
                'expiresAt' => $expiresAt
            ], function ($message) use ($email) {
                $message->to($email)
                        ->subject('Recuperación de Contraseña - NAWI');
            });

            return response()->json([
                'success' => true,
                'message' => 'Se ha enviado un enlace de recuperación a tu email',
                'expires_at' => $expiresAt->toISOString()
            ]);
        } catch (\Exception $e) {
            \Log::error('Error al enviar email de recuperación', [
                'email' => $email,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // En desarrollo, mostrar el error real
            if (config('app.debug')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al enviar email: ' . $e->getMessage(),
                    'token' => $token, // Solo en debug para testing
                    'expires_at' => $expiresAt->toISOString()
                ], 500);
            }

            return response()->json([
                'success' => true,
                'message' => 'Se ha enviado un enlace de recuperación a tu email',
                'expires_at' => $expiresAt->toISOString()
            ]);
        }
    }

    /**
     * Mostrar formulario de reset de contraseña
     */
    public function showResetForm(Request $request, $token = null)
    {
        $email = $request->query('email');
        return view('auth.passwords.reset', [
            'token' => $token,
            'email' => $email
        ]);
    }

    /**
     * Reset password with token (Web)
     */
    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:usuarios,email',
            'token' => 'required|string|size:64',
            'password' => [
                'required',
                'string',
                'min:8',
                'max:128',
                'confirmed',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&#])[A-Za-z\d@$!%*?&#]+$/',
                function ($attribute, $value, $fail) {
                    if (preg_match('/[<>]/', $value)) {
                        $fail('El campo ' . $attribute . ' no puede contener símbolos peligrosos como < o >.');
                    }
                }
            ],
        ], [
            'email.required' => 'El email es obligatorio',
            'email.email' => 'El formato del email no es válido',
            'email.exists' => 'No existe una cuenta con este email',
            'token.required' => 'El token es obligatorio',
            'token.size' => 'El token debe tener 64 caracteres',
            'password.required' => 'La contraseña es obligatoria',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres',
            'password.confirmed' => 'Las contraseñas no coinciden',
            'password.regex' => 'La contraseña debe contener al menos una letra minúscula, una mayúscula, un número y un símbolo especial',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Find valid reset token
        $resetRecord = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->where('expires_at', '>', Carbon::now())
            ->first();

        if (!$resetRecord || !Hash::check($request->token, $resetRecord->token)) {
            return back()->with('error', 'El enlace de recuperación es inválido o ha expirado. Por favor, solicita uno nuevo.');
        }

        // Update user password
        $usuario = Usuario::where('email', $request->email)->first();
        if (!$usuario) {
            return back()->with('error', 'Usuario no encontrado.');
        }

        $usuario->password = Hash::make($request->password);
        $usuario->save();

        // Delete used token
        DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->delete();

        // Clear reset attempts cache
        cache()->forget('password_reset_attempts:' . $request->email);

        return redirect()->route('login')->with('status', 'Contraseña actualizada exitosamente. Ya puedes iniciar sesión.');
    }

    /**
     * API: Reset password with token
     */
    public function resetPasswordApi(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:usuarios,email',
            'token' => 'required|string|size:64',
            'password' => [
                'required',
                'string',
                'min:8',
                'max:128',
                'confirmed',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&#])[A-Za-z\d@$!%*?&#]+$/',
                function ($attribute, $value, $fail) {
                    if (preg_match('/[<>]/', $value)) {
                        $fail('El campo ' . $attribute . ' no puede contener símbolos peligrosos como < o >.');
                    }
                }
            ],
        ], [
            'email.required' => 'El email es obligatorio',
            'email.email' => 'El formato del email no es válido',
            'email.exists' => 'No existe una cuenta con este email',
            'token.required' => 'El token es obligatorio',
            'token.size' => 'El token debe tener 64 caracteres',
            'password.required' => 'La contraseña es obligatoria',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres',
            'password.confirmed' => 'Las contraseñas no coinciden',
            'password.regex' => 'La contraseña debe contener al menos una letra minúscula, una mayúscula, un número y un símbolo especial',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos inválidos',
                'errors' => $validator->errors()
            ], 422);
        }

        // Find valid reset token
        $resetRecord = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->where('expires_at', '>', Carbon::now())
            ->first();

        if (!$resetRecord || !Hash::check($request->token, $resetRecord->token)) {
            return response()->json([
                'success' => false,
                'message' => 'Token inválido o expirado',
                'code' => 'INVALID_TOKEN'
            ], 400);
        }

        // Update user password
        $usuario = Usuario::where('email', $request->email)->first();
        if (!$usuario) {
            return response()->json([
                'success' => false,
                'message' => 'Usuario no encontrado'
            ], 404);
        }

        $usuario->password = Hash::make($request->password);
        $usuario->save();

        // Delete used token
        DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->delete();

        // Clear reset attempts cache
        cache()->forget('password_reset_attempts:' . $request->email);

        return response()->json([
            'success' => true,
            'message' => 'Contraseña actualizada exitosamente'
        ]);
    }

    /**
     * Verify reset token (API)
     */
    public function verifyToken(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:usuarios,email',
            'token' => 'required|string|size:64',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos inválidos',
                'errors' => $validator->errors()
            ], 422);
        }

        $resetRecord = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->where('expires_at', '>', Carbon::now())
            ->first();

        if (!$resetRecord || !Hash::check($request->token, $resetRecord->token)) {
            return response()->json([
                'success' => false,
                'message' => 'Token inválido o expirado',
                'code' => 'INVALID_TOKEN'
            ], 400);
        }

        return response()->json([
            'success' => true,
            'message' => 'Token válido',
            'expires_at' => $resetRecord->expires_at
        ]);
    }
}
