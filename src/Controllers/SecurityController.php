<?php

namespace Hanoivip\Userinfo\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Exception;
use Hanoivip\Userinfo\UserSecure;
use Hanoivip\Userinfo\Services\SecureService;
use Hanoivip\Userinfo\Requests\SecureEmail;
use Hanoivip\Userinfo\Requests\ResendEmail;
use Hanoivip\Userinfo\Requests\UpdatePass2;
use Hanoivip\Userinfo\Requests\UpdateQna;
use Carbon\Carbon;

class SecurityController extends Controller
{
    protected $secureService;
    
    public function __construct(SecureService $secure)
    {
        $this->middleware('auth');
        
        $this->secureService = $secure;
    }

    public function infoUI()
    {
        $uid = Auth::user()->id;
        $secure = $this->secureService->getInfo($uid);
        return view('hanoivip::secure-info', ['info' => $secure]);
    }
    
    public function updateEmailUI()
    {
        $uid = Auth::user()->id;
        $secure = $this->secureService->getInfo($uid);
        return view('hanoivip::secure-update-email', ['info' => $secure]);
    }
    
    public function doUpdateEmail(SecureEmail $request)
    {
        $uid = Auth::user()->id;
        $newmail = $request->input('newmail');
        $phone = $request->input('phone');
        $message = '';
        $error_message = '';
        
        if (config('id.sms.enabled', false))
        {
            if (!empty($phone))
            {
                // 2 steps authentication by sms
                $error_message = 'Sms not supported yet!';
            }
        }
        else 
        {
            try
            {
                $result = $this->secureService->updateEmail($uid, $newmail);
                if (gettype($result) == "boolean")
                {
                    if ($result)
                    {
                        $message = __('secure.email.update.success');
                        Auth::guard()->logout();
                        $request->session()->invalidate();
                    }
                    else
                        $error_message = __('secure.email.update.fail');
                }
                else 
                    $error_message = $result;
            }
            catch (Exception $ex)
            {
                Log::error("Secure update email exception. Msg:" . $ex->getMessage());
                $error_message = __('secure.email.update.exception');
            }
        }
        return view('hanoivip::secure-update-email-result', ['message' => $message, 'error_message' => $error_message]);
    }
    
    protected function isTooFast($uid)
    {
        $interval = config('id.email.toofast');
        $secure = UserSecure::find($uid);
        if (!empty($secure->last_email_validation))
        {
            $now = Carbon::now();
            $lastEmail = new Carbon($secure->last_email_validation);
            $diff = $now->diffInSeconds($lastEmail);
            return $diff < $interval;
        }
        return false;
    }
    
    public function resendEmail()
    {
        $uid = Auth::user()->id;
        $message = '';
        $error_message = '';
        try 
        {
            if ($this->isTooFast($uid))
            {
                $error_message = __('secure.email.resend.toofast');
            }
            else if ($this->secureService->resendEmail($uid))
                $message = __('secure.email.resend.success');
            else
                $error_message = __('secure.email.resend.fail');
        }
        catch (Exception $ex)
        {
            Log::error("Secure resend email exception. Msg:" . $ex->getMessage());
            $error_message = __('secure.email.resend.exception');
        }
        return view('hanoivip::secure-resend-email-result', ['message' => $message, 'error_message' => $error_message ]);
    }
    
    public function updatePass2()
    {
        $uid = Auth::user()->id;
        $secure = $this->secureService->getInfo($uid);
        return view('hanoivip::secure-update-pass2', ['info' => $secure]);
    }
    
    public function doUpdatePass2(UpdatePass2 $request)
    {
        $uid = Auth::user()->id;
        $newpass2 = $request->input('newpass2');
        $message = '';
        $error_message = '';
        try 
        {
            $result = $this->secureService->updatePass2($uid, $newpass2);
            if (gettype($result) == "boolean")
            {
                if ($result)
                {
                    $message = __('secure.pass2.update.success');
                    Auth::guard()->logout();
                    $request->session()->invalidate();
                }
                else 
                    $error_message = __('secure.pass2.update.fail');
            }
            else
                $error_message = $result;
        } 
        catch (Exception $ex) 
        {
            Log::error("Secure update pass2 exception. Msg:" . $ex->getMessage());
            $error_message = __('secure.pass2.update.exception');
        }
        return view('hanoivip::secure-update-pass2-result', ['message' => $message, 'error_message' => $error_message]);
    }
    
    public function updateQna()
    {
        $uid = Auth::user()->id;
        $secure = $this->secureService->getInfo($uid);
        $questions = [];
        for ($i=1; $i<=20; ++$i)
            $questions[] = [ $i, __('secure.qna.question' . $i) ];
        return view('hanoivip::secure-update-qna', ['info' => $secure, 'questions' => $questions]);
    }
    
    public function doUpdateQna(UpdateQna $request)
    {
        $uid = Auth::user()->id;
        $question = $request->input('newquestion');
        $answer = $request->input('newanswer');
        $message = '';
        $error_message = '';
        try 
        {
            $result = $this->secureService->updateQna($uid, $question, $answer);
            if (gettype($result) == "boolean")
            {
                if ($result)
                {
                    $message = __('secure.qna.update.success');
                    Auth::guard()->logout();
                    $request->session()->invalidate();
                }
                else
                    $error_message = __('secure.qna.update.fail');
            }
            else 
                $error_message = $result;
        }
        catch (Exception $ex)
        {
            Log::error("Secure update qna exception. Msg:" . $ex->getMessage());
            $error_message = __('secure.qna.update.exception');
        }
        return view('hanoivip::secure-update-qna-result', ['message' => $message, 'error_message' => $error_message]);
    }
}
