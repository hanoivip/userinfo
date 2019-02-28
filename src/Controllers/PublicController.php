<?php

namespace Hanoivip\Userinfo\Controllers;

use Illuminate\Support\Facades\Log;
use Exception;
use Hanoivip\Userinfo\Services\CredentialService;
use App\User;
use Hanoivip\Userinfo\Services\SecureService;

class PublicController extends Controller
{
    protected $credentials;
    
    protected $secures;
    
    public function __construct(CredentialService $credentials,
        SecureService $secures)
    {
        $this->credentials = $credentials;
        $this->secures = $secures;
    }
    
    /**
     * Xác thực email đăng nhập
     * 
     * @param string $token
     */
    public function verifyEmail($token)
    {
        $message = '';
        $error_message = '';
        try 
        {
            $affectedUser = new User();
            $result = $this->credentials->verify($token, $affectedUser);
            if (gettype($result) == "boolean")
            {
                if ($result)
                {
                    if (empty($affectedUser))
                        throw new Exception('Verify affected user not set.');
                    $message = __("email.verify.success", ['username' => $affectedUser->name]);
                }
                else
                    $error_message = __("email.verify.fail");
            }
            else 
                $error_message = $result;
        }
        catch (Exception $ex)
        {
            Log::error("Verify login email exception. Msg:" . $ex->getMessage());
            $error_message = __("email.verify.exception");
        }
        return view("verify-login-result", ['message' => $message, 'error_message' => $error_message]);
    }
    
    /**
     * Xác thực email bảo mật
     * 
     * @param string $token
     */
    public function verifySecureEmail($token)
    {
        $message = '';
        $error_message = '';
        try
        {
            $affectedUser = new User();
            $result = $this->secures->verify($token, $affectedUser);
            if (gettype($result) == "boolean")
            {
                if ($result)
                {
                    if (empty($affectedUser))
                        throw new Exception('Verify affected user not set.');
                    $message = __("secure.email.verify.success", ['username' => $affectedUser->name]);
                }
                else
                    $error_message = __("secure.email.verify.fail");
            }
            else
                $error_message = $result;
        }
        catch (Exception $ex)
        {
            Log::error("Verify login email exception. Msg:" . $ex->getMessage());
            $error_message = __("secure.email.verify.exception");
        }
        return view("verify-secure-result", ['message' => $message, 'error_message' => $error_message]);
    }
}
