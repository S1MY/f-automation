<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

use Illuminate\Support\Facades\Auth;
use App\Models\TgAccount;

use danog\MadelineProto\API;
use danog\MadelineProto\Settings;
use danog\MadelineProto\Settings\AppInfo;
use danog\MadelineProto\Logger;
use danog\MadelineProto\Settings\Logger as LoggerSettings;

class TgAuthController extends Controller
{
    public function tgUserAuth(Request $request)
    {
        $rules = [
            'tg_phone' => 'required|string',
            'tg_code' => 'required|string',
            'tg_first_name' => 'required|string',
            'tg_password' => 'required|string',
        ];
        $messages = [
            'tg_phone.required' => 'Обязательное поле',
            'tg_phone.string' => 'Не является строкой',
            'tg_code.required' => 'Обязательное поле',
            'tg_code.string' => 'Не является строкой',
            'tg_first_name.required' => 'Обязательное поле',
            'tg_first_name.string' => 'Не является строкой',
            'tg_password.required' => 'Обязательное поле',
            'tg_password.string' => 'Не является строкой',
        ];
        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            $errors = $validator->getMessageBag()->toArray();
            $firstErrorKey = array_key_first($errors);
            return $this->generateJsonResponse('input', $firstErrorKey, $errors[$firstErrorKey][0]);
        }
        $validatedData = $validator->validated();
        $searchAccount = TgAccount::where('phone', '=', $validatedData['tg_phone'])->first();
        if($searchAccount){
            Storage::disk('public')->deleteDirectory('app/public/files/users/' . $userName . '/tg_session');
            return $this->generateJsonResponse('error', 'Ошибка авторизации', 'Данный аккаунт уже имеется в нашей базе данных!');
        }
        $apiId = 20077871;
        $apiHash = 'c3e50b327a806382305eb0ef048676d6';
        $userId = Auth::user()->id;
        $userName = Auth::user()->login;
        $fileLoadUrl = 'files/users/' . $userName . '/tg_session';
        if (!Storage::disk('public')->exists($fileLoadUrl.'/madeline.log')) {
            Storage::disk('public')->put($fileLoadUrl.'/madeline.log', '');
            chmod(storage_path('app/public/' . $fileLoadUrl.'/madeline.log'), 0777);
        }
        $logger = (new LoggerSettings)
            ->setType(Logger::FILE_LOGGER)
            ->setExtra(storage_path('app/public/'.$fileLoadUrl.'/madeline.log'))
            ->setLevel(Logger::FATAL_ERROR )
            ->setMaxSize(2048);
        $settings = 
        (new Settings())
        ->setAppInfo((new AppInfo)->setApiId($apiId)->setApiHash($apiHash))
        ->setLogger($logger);
        $sessionUrl = storage_path('app/public/' . $fileLoadUrl . '/session.madeline');
        $MadelineProto = new API($sessionUrl, $settings);
        if($validatedData['tg_phone'] !== 'empty'){
            $MadelineProto->phoneLogin($validatedData['tg_phone']);
            return $this->generateJsonResponse('next', 'tg_code', 'Введите код, пришедший в Телеграм');
        }
        if($validatedData['tg_code'] !== 'empty'){
            $authorization = $MadelineProto->completePhoneLogin($validatedData['tg_code']);
            if ($authorization['_'] === 'account.noPassword') {
                Storage::deleteDirectory(storage_path('app/public/files/users/' . $userName . '/tg_session'));
                return $this->generateJsonResponse('error', 'Ошибка авторизации', '2FA включен, но пароль не установлен!');
            }
            if ($authorization['_'] === 'account.needSignup') {
                return $this->generateJsonResponse('next', 'tg_first_name', 'Введите никнейм для Telegram');
            }
            if ($authorization['_'] === 'account.password') {
                return $this->generateJsonResponse('next', 'tg_password', 'Введите свой 2FA пароль');
            }
        }
        if($validatedData['tg_first_name'] !== 'empty'){
            $authorization = $MadelineProto->completeSignup($validatedData['tg_first_name'], '');
        }
        if($validatedData['tg_password'] !== 'empty'){
            $authorization = $MadelineProto->complete2falogin($validatedData['tg_password']);
        }
        $me = $MadelineProto->getSelf();
        return $this->generateJsonResponse('success', 'Иди нахуй', $me);
        if (isset($me['first_name'])) {
            $name = $me['first_name'];
        } else {
            if (isset($me['username'])) {
                $name = $me['username'];
            } else {
                $name = 'Не определено';
            }
        }
        $phone = $me['phone'];
        if (isset($me['photo'])) {
            $photo = $me['photo'];
            $photoInfo = $MadelineProto->getPropicInfo($me);
            $MadelineProto->downloadToFile($photoInfo, 'app/public/'.$fileLoadUrl . '/avatar.png');
            $avatar = $fileLoadUrl . '/avatar.png';
        } else {
            $avatar = '/files/avatar.jpg';
        }
        TgAccount::create([
            'uid' => $userId,
            'name' => $name,
            'avatar' => $avatar,
            'phone' => $phone
        ]);
        return $this->generateJsonResponse('success', 'Всё прошло успешно', 'Аккаунт - <span>' . $name . '</span> - успешно привязан.<br> Страница автоматически перегрузится, чтобы изменения применились.');
    }
}
