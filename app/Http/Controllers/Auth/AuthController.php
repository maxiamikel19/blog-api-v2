<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use PhpParser\Node\Stmt\TryCatch;

class AuthController extends Controller
{
    /**
 * @OA\Post(
 *     path="/api/register",
 *     summary="Create a new user",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(ref="#/components/schemas/User")
 *     ),
 *     @OA\Response(
 *         response="201",
 *         description="User and the user token",
 *         @OA\JsonContent(ref="#/components/schemas/User")
 *     ),
 *     @OA\Response(
 *         response="422",
 *         description="Validations input",
 *         @OA\JsonContent(ref="#/components/schemas/User")
 *     )
 * )
 */
    public function register(Request $request){
        $validator = Validator::make($request->all() ,[
            'name' => 'required|string|max:255',
            'email' => 'required|string|max:255|unique:users',
            'password' => 'required|max:10|min:4'
        ]);

        if($validator->fails()){
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = User::create([
                'name' => $request['name'],
                'email' => $request['email'],
                'password' => Hash::make($request['password'])
            ]);

            $token = $user->createToken('register-' .$request->email)->plainTextToken;

            return response()->json([
                'user' => $user,
                'token' => $token
            ], 201);
        } catch (\Exception $ex) {
            return response()->json([
                'error' => $ex->getMessage()
            ], 500);
        }
    }


 /**
 * @OA\Post(
 *     path="/api/login",
 *     summary="Log in the user",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             @OA\Property(property="email", type="string"),
 *             @OA\Property(property="password", type="string")
 *         )
 *     ),
 *     @OA\Response(
 *         response="200",
 *         description="User object and Token",
 *         @OA\JsonContent(
 *             @OA\Property(property="token", type="string", description="Token")
 *         )
 *     ),
 *     @OA\Response(
 *         response="401",
 *         description="User credentials incorrect"
 *     ),
 *     @OA\Response(
 *         response="422",
 *         description="Input check validations message"
 *     ),
 *     @OA\Response(
 *         response="500",
 *         description="Internal server error"
 *     )
 * )
 */
    public function login(Request $request ){
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users',
            'password' => 'required'
        ]);

        if($validator->fails()){
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        try {

        $user = User::where('email', $request['email'])->first();

        if(!$user || !Hash::check($request['password'],$user->password)){
            return response()->json([
                'errors' => [
                    'email' => 'User credentials incorrect'
                ]
            ], 401);
        }

        $token = $user->createToken($user->name)->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token
        ], 200);
        } catch (\Exception $ex) {
            return response()->json([
                'error' => $ex->getMessage()
            ], 500);
        }
    }


    /**
     * @OA\Post(
     *     path="/api/logout",
     *     summary="Logged out the user (revoke the token)",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response="200",
     *         description="Logged out successfully"
     *     ),
     *     @OA\Response(
     *         response="401",
     *         description="Invalid token"
     *     )
     * )
     */
    public function logout(Request $request){
        $user = $request->user();
        try {
            $user->tokens()->delete();

            return response()->json([
                'message' => 'Successfully logged out'
            ], 200);

        } catch (\Exception $th) {
            return response()->json([
                'message' => $th->getMessage()
            ], 401);
        }

    }
}
